<?php
class ProjektTag {
	private $db;
	public $row=array();
	public  $kdnr = 0; 
	
	public $arbeitstyp = array(
		"normaler Tag",
		"bezahlter Offday",
		"kostenloser Offday",
		"Überstunden",
		"doppelter Tagessatz"	
	);

	public function getTyp($nr) {
		return $this->arbeitstyp($nr);
	}
	
	public function __construct(&$db) {
		$this->db=$db;
	}
	
	public function loadByRecnum($recnum) {
		$request="select * from bu_project_day where `recnum`='$recnum'";
		$result=$this->db->query($request);
		$this->row=$result->fetch_assoc();
		return $this->row;
	}
	
	private function insert($row) {
		unset ($row['recnum']);  // zur Sicherheit
	
		
		$values="";
		$keys="";
		foreach($row as $k => $v) {
			if ($values != "") {
				$values.=",";
				$keys.=",";
			}
			$values.= "'".$this->db->real_escape_string($v)."'";
			$keys  .= "`".$k."`";
		}
		
		$request="insert into bu_project_day ($keys) values ($values)";	
		$result = $this->db->query($request);
		if ($result) {
			$this->row['recnum']=$this->db->insert_id;
		} 
		return $result;
	}

	private function update($row) {
		$recnum=$row['recnum'];
		unset($row['recnum']);
		
		$set="";
		foreach($row as $k => $v) {
			if ($set != "") {
				$set.=",";
			}
			$set.="`".$k."`='".$this->db->real_escape_string($v)."'";
		}
		
		$request="update bu_project_day set $set where `recnum`='".$recnum."'";	
		$result = $this->db->query($request);
		
		$this->row['recnum']=$recnum;
		return $result; // Arrayoffset = null ?
		
	}
	
	public function save($row) {
		if (empty($row['recnum'])) {
			$this->insert($row);
		} else {
			$this->update($row);
		}
	}
	public function loadByDate() {
		$dt=    new DateTime();
		$datum= $dt->format("Y-m-d");
		$kdnr=  $this->kdnr;
		
		
		$request='SELECT * from `bu_project_day` where firmanr="'.$_SESSION['firmanr'].'" and kdnr="'.$kdnr.'" and datum = "'.$datum.'"';
		$result = $this->db->query($request);
		if ($result->num_rows > 0) {
			$this->row = $result->fetch_assoc();
			return $this->row;	
		}
		return null;	
	}

	public function saveByRecnum($row) {
		if (isset($this->row['recnum']) and ($this->row['recnum'] > 0)) {
			$row['recnum']=$this->row['recnum'];
			$this->update($row);
		} else {
			$this->insert($row);
		}		
	}
	
}
?>