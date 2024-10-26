<?php
class Table {
	// protected  $table; // eigentlich $table
	protected  $db = "";
	protected  $name;  // Table Name

	public $insert=false;
	public $update=false;
	public $row = array();
	public $result;
	protected $transfer=false; // Automatischer Transfer zu $_POST bei loadByRecnum
	
	public $format;	
	public $input=array(); // Eingabe Felder müssen gelich sein mit der Tabelle Recnum, darf abweichen,weil er übergeben wird
	
	// Zum Speichern von Leeren Datum
	// Typ 0 = standart
	//     1 = Datum
	// Feldtyp festlegen
	protected $typ=array(); // Typen der Felder

	public $err=false;
	public $msg="";

	public function setTransfer($t=true) {
		$this->transfer=$t;
	}

	public function setChoice($fieldname,$choice) {
		$this->format[$fieldname]["wahl"]=$choice;
	}
	
	protected function prepare_date($liste) {
		foreach($liste as $k) {
			$this->typ[$k]=1;
		}
	}

	// Fülle Eingabefelder ,mit Hilfe des Recnums
	// Überschreiben ist nicht erlaubt
	public function fillInput($recnum,$overwrite=false) {
		// echo $_POST[$recnum]."Y<BR>";
		if (empty($_POST[$recnum])) {
			return false;
		}
		if ($this->loadByRecnum($_POST[$recnum])) {
			if ($overwrite==false) {
				// echo "overwrite false";
				foreach ($this->input as $k) {
					if (!isset($_POST[$k])) $_POST[$k]=$this->row[$k]; // isset ist muss damit leere eingaben nichtr überschrieben werden
				}
				// print_r($_POST);
			} else {
				foreach ($this->input as $k) {
					$_POST[$k]=$this->row[$k]; // alles überschreiben
				}
			}
			return true;
		} else {
			return false;
		}
	}
	
	
	private function mysql_error(&$request) {
		echo '<div style="display:inlne-box;margin:10px;padding:5px; border:red solid 2px;background-color: #EEEEEE;color:black;">';
		echo "Tabelle:".$this->name."<br>";
		echo "Script:". $_SERVER["SCRIPT_NAME"]."<br>";
		echo "Fehler:". $this->db->errno.":".$this->db->error."<br>";
		echo "Request:<br>";
		echo $request."<br>";
		echo "</div>";
	}
	private function mysql_warning(&$request) {
		echo '<div style="display:inlne-box;margin:10px;padding:5px; border:red solid 2px;background-color: #EEEEEE;color:black;">';
		echo "Tabelle:".$this->name."<br>";
		echo "Script:". $_SERVER["SCRIPT_NAME"]."<br>";
		$e = mysqli_get_warnings($this->db);
		do {
		   echo "Warning: $e->errno: $e->message <br>"; // $e->line $e-getLine();
		} while ($e->next());
		echo "Request:<br>";
		echo $request."<br>";
		echo "</div>";
	}
	
	public function query(&$request) {
		$this->result=$this->query_raw($request);
		return $this->result;
		
		/*
		try  {
			$this->result = $this->db->query($request) or die($this->mysql_error($request));
			if (mysqli_warning_count($this->db)) {
			   $this->mysql_warning($request);
			   return false;
			}			
			return $this->result;
		} catch (Exception $e) {
			$this->mysql_error($request);
			return false;
		}
		*/		
	}

	protected function query_raw(&$request) {
		try  {
			$result = $this->db->query($request) or die($this->mysql_error($request));
			if (mysqli_warning_count($this->db)) {
			   $this->mysql_warning($request);
			   return false;
			}			
			return $result;
		} catch (Exception $e) {
			$this->mysql_error($request);
			return false;
		}	
	}
	
	public function __construct(&$db) {
		$this->db=$db;		
	}

	public function getErrCode() {
		return $this->db->errno;
	}
	
	public function matched() {
		list($matched, $changed, $warnings) = sscanf($this->db->info, "Rows matched: %d Changed: %d Warnings: %d");
		return $matched;
	}
	
	public function changed() {
		list($matched, $changed, $warnings) = sscanf($this->db->info, "Rows matched: %d Changed: %d Warnings: %d");
		return $changed;
	}
		
	public function loadAll($order) {
		$request="select * from ".$this->name;
		if (!empty($order)) {
			$request.=" order by $order";
		}
		$this->result = $this->db->query($request);
		return $this->result;
	}
	
	public function loadByRecnum($recnum=0) {
		if (($recnum==0) and !empty($this->row['recnum'])) {
			$recnum=$this->row['recnum'];
		}
		$request="select * from ".$this->name." where recnum='".$recnum."'";

		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		
		if ($this->transfer and ($result->num_rows > 0)) {
			$this->transfer();
		}
		return $this->row;
	}

    public function getFieldByRecnum($recnum,$fieldname) {
		if (empty($_POST[$recnum])) {
			$_POST[$recnum]="";
			return "";
		}
		$request="select * from ".$this->name." where recnum='".$_POST[$recnum]."'";

		$result = $this->db->query($request);
		$row = $result->fetch_assoc();
		// $_POST[$fieldname]=$row[$fieldname];
		if (empty($row[$fieldname])) $row[$fieldname]=""; 
		return $row[$fieldname];	
	}

	public function transfer() {
		if (is_array($this->row)) {
			foreach($this->row as $k => $v) {
				$_POST[$k]=$v;
			}
		}
	}
		

	/*
		Daten einfügen
	*/
	
	public function insert($row="") {
		if (empty($row)) {
			$row=$this->row;
		}
		// $recnum=$row['recnum'];
		unset ($row['recnum']);  // zur Sicherheit
		
		$this->row=$row;
		$values="";
		$keys="";
		foreach($row as $k => $v) {
			$this->row[$k]=$v; 
			if ($values != "") {
				$values.=",";
				$keys.=",";
			}
			if (empty($v) and !empty($this->typ[$k]) and $this->typ[$k]==1) {
				$values.="NULL";
			} else
			if ($v=="NULL") {
				$values.="NULL";
			} else {
				$values.= "'".$this->db->real_escape_string($v)."'";
			}
			$keys  .= "`".$k."`";
		}

		$request="insert into ".$this->name." ($keys) values ($values)";	
		//echo $request."<br>";			
		try  {
			$result = $this->db->query($request);
			if ($result) {
				$this->row['recnum']=$this->db->insert_id;
				$_POST['recnum']=$this->db->insert_id;
				
				// echo "ID=".$this->row['recnum']."<br>";				
			} 
			return $result;
		} catch (Exception $e) {
			if ($this->db->errno == 1062) {  // Duplicate Entry
				return false;
			}
			echo '<div style="display:inlne-box;margin:10px;padding:5px; border:red solid 2px;background-color: #EEEEEE;color:black;">';
			echo "Tabelle:".$this->name."<br>";
			echo "Script:". $_SERVER["SCRIPT_NAME"]."<br>";
			echo "Fehler:". $this->db->errno.":".$this->db->error."<br>";
			echo "Request:<br>";
			echo $request."<br>";
			echo "</div>";
			
			
			return false;
		}
	}

	/*
		Daten verändern
	*/
	public function update($row="") {
		if (empty($row)) {
			$row=$this->row;
		}

		$recnum=0;
		if (isset($row['recnum'])) {
			$recnum=$row['recnum'];
		} else 
		if (isset($this->row['recnum'])) {
			$recnum=$this->row['recnum'];
		}
		if ($recnum == 0) {
			return false; // Update ohne recnum nicht möglich
		}
		
		unset($row['recnum']);
		
		$set="";
		foreach($row as $k => $v) {
			$this->row[$k]=$v; 
			
			if ($set != "") {
				$set.=",";
			}
			if (empty($v) and !empty($this->typ[$k]) and $this->typ[$k]==1) {
				$values="NULL";
			} else
			if ($v=="NULL") {
				$values="NULL";
			} else {

				// echo "k/v:".$k."/".$v."<br>";				
				$values= "'".$this->db->real_escape_string($v)."'";
			}
			
			
			$set.="`".$k."`=$values";
		}
		
		$request="update ".$this->name." set $set where `recnum`='".$recnum."'";	
		$result = $this->query($request);
		
		$this->row['recnum']=$recnum;
		return $result; // Arrayoffset = null ?
		
	}
	
	public function insertupdate($row="") {
		if (empty($row)) {
			$row=$this->row;
		}
		// $recnum=$row['recnum'];
		unset ($row['recnum']);  // zur Sicherheit
		
		$this->row=$row;
		$set="";
		foreach($row as $k => $v) {
			$this->row[$k]=$v; 			
			if ($set != "") {
				$set.=",";
			}
			// $set.="`".$k."`='".$this->db->real_escape_string($v)."'";
			if (empty($v) and !empty($this->typ[$k]) and $this->typ[$k]==1) {
				$values="NULL";
			} else
			if ($v=="NULL") {
				$values="NULL";
			} else {

				// echo "k/v:".$k."/".$v."<br>";				
				$values= "'".$this->db->real_escape_string($v)."'";
			}
			$set.="`".$k."`=$values";
		}

		$request="insert into ".$this->name." SET $set ON DUPLICATE KEY UPDATE $set";	
		//echo $request."<br>";			
		try {
			$result = $this->db->query($request);
		} catch (Exception $e) {
			echo $this->db->errno.":".$this->db->error."<br>";
			return false;
		}

		if ($result) {
			$this->row['recnum']=$this->db->insert_id;
			$this->insert=false;
			$this->update=false;
			if ($this->db->affected_rows == 1) $this->insert=true;
			if ($this->db->affected_rows == 2) $this->update=true;
			// 0 wenn keine änderung
			
		} 
		return $result;
	}
	
	
	protected function where2string($wherestack) {
		if (!is_array($wherestack)) {
			return "";
		}
		$where="";
		foreach ($wherestack as $k => $v) {
			if (!empty($where)) {
				$where.=" AND ";
			} else {
				$where=" WHERE ";
			}
			$where.="`$k` = '$v'";
		}
		return $where;
	}
		
	public function loadByWhere($wherestack="",$order="") {
		if (!empty($order)) {
			$order=" ORDER BY $order";
		}	
		
		$where=$this->where2string($wherestack);
			
		$request="SELECT * FROM ".$this->name." ".$where.$order;
		$this->result = $this->query($request);
		return $this->result;
	}
	public function load($wherestack,$order="") {
		$this->loadByWhere($wherestack,$order);
	}
			
	public function next() {
		$this->row=$this->result->fetch_assoc();
		return $this->row;
	}
	
	public function count() {
		return $this->result->num_rows;
	}
	
	public function delete($recnum) {
		if (is_object($recnum) or is_array($recnum) ) {
			$where=$this->where2string($recnum);
			$request="DELETE FROM ".$this->name." ".$where;
		} else {
			$request="DELETE FROM ".$this->name." WHERE recnum='".$recnum."'";
		}
		$this->result = $this->query($request);
		return $this->result;
	}

	public function isInsert() {
		if (isset($_POST['insert'])) {
			return true;                 
		} else {
			return false;
		}
	}
	public function isUpdate() {
		if ((isset($_POST['update'])) and !empty($_POST['recnum'])) {
			return true;
		} else {
			return false;
		}
	}		
	
	public function save($row=array()) {
		if (count($row) == 0) { 
			// $row=array();
			foreach($this->format as $k => $v) {
				if (isset($_POST[$k])) {
					$row[$k]=$_POST[$k];
					
					if (isset($this->format[$k]['typ']) and ($this->format[$k]['typ']=="int")) {
						$row[$k]=(int)$_POST[$k];
						// echo "$k ist int<br>";
						
					}
					if (isset($this->format[$k]['typ']) and ($this->format[$k]['typ']=="date")) {
						if (empty($row[$k])) {
							$row[$k]="NULL"; // (int)$_POST[$k];
						}
						// echo "$k ist int<br>";
						
					}
				}
			}
		}
		if (isset($_POST['insert'])) {
			return $this->insert($row);
		} else {
			return $this->update($row);
		}
	}
	

}
?>