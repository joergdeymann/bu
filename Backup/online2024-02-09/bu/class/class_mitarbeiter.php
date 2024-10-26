<?php
class mitarbeiter {
	private $db = "";
	public $row = array();
	private $day = array("so","mo","di","mi","do","fr","sa","so");
	
	public function __construct(&$db) {
		$this->db=$db;		
	}

	
	private function updateResturlaub() {
		$dt=new DateTime();
		
		if ($this->row['urlaub_update'] != $dt->format("Y")) {
			$row=array();
			$row['recnum']=$this->row['recnum'];
			$row['urlaub_update']=$dt->format("Y");
			$row['resturlaub'] =$this->row['resturlaub'] + $this->row['jahresurlaub'];
			$this->update($row);
			$this->row['resturlaub'] = $row['resturlaub'];
			$this->row['urlaub_update']=$dt->format("Y");
		}
	}
			
	private function toDateTime($d) {
		if (gettype($d) == 'object') {
			return $d;
		} else {
			return new DateTime($d);
		}
	}

	public function load($name) {
		$request="select * from bu_mitarbeiter where name='".$this->db->real_escape_string($name)."'";
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		return $this->row;
	}
	public function loadByNr($nr) {
		$request="select * from bu_mitarbeiter where nr='".$this->db->real_escape_string($nr)."' and firmanr=".$_SESSION['firmanr'];
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		return $this->row;
	}
	public function loadByName($name) {
		$request="select * from bu_mitarbeiter where name like '%".$this->db->real_escape_string($name)."%' and firmanr=".$_SESSION['firmanr'];
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		return $this->row;
	}
	public function loadByRecnum($recnum) {
		$request="select * from bu_mitarbeiter where recnum='".$recnum."'";
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		$this->updateResturlaub();
		return $this->row;
	}

	/*
		Standart-Arbeitszeitenrückgabe
	*/
	public function getWorkTimes() {
		$row=&$this->row;
		return array($row['so'],$row['mo'],$row['di'],$row['mi'],$row['do'],$row['fr'],$row['sa']);
	}

	// Prüft die Arbeitstage für Urlaub und Krankheit
	public function getUrlaub($von,$bis) {
		$dt_von=$this->toDateTime($von);
		$dt_bis=$this->toDateTime($bis);
		
		$diff=$dt_von->diff($dt_bis);
		$days=$diff->days;
		
		
		/*
		$d=$dt_von->format("W"); // W ?? === Wochentag
		$day=$this->day[$d];
		$hours=$dt_von-$dt_bis;
		if ($hours < $row[$day]) {
			return ($hours/$row[$day]);
		}
		*/ 
		$urlaub=0;
		$c=0;
		$d=$dt_von->format("w"); // w ?? === Wochentag, So = 0 
		while ($c <= $days) {
			$day=$this->day[$d];			
			if ($this->row[$day] > 0) {
				$urlaub++;
			}
			$c++;
			$d++;
			if ($d > 6) {
				$d=0;
			}
		}
		return $urlaub;
	}
	
	
	public function exist($name) {
		$request="select count(*) as sum from bu_mitarbeiter where name='".$this->db->real_escape_string($user)."'";
		$result = $this->db->query($request);
		$row = $result->fetch_assoc();

		if ($row['sum'] > 0) {
			return true;
		}
		return false;
	
	}

	/*
		Mitarbeiter einfügen
	*/
	public function insert($row) {
		unset ($row['recnum']);  // zur Sicherheit
	
		// Hier Passwort neu einfügen 
		// wird von mitarbeiter.php so benutzt
		// vielleicht besser dort einbauen
		if (empty($row['pw'])) {
			$row['pw']=$row['name'];
		}
		$row['pw']=password_hash($row['pw'], PASSWORD_DEFAULT);
		
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
		
		$request="insert into bu_mitarbeiter ($keys) values ($values)";	
		$result = $this->db->query($request);
		if ($result) {
			$this->row['recnum']=$this->db->insert_id;
		} 
		return $result;
	}
	
	/*	
		Passwort neu setzen
	*/
	public function setPassword($pw) {
		$request="update bu_mitarbeiter set `pw`='".password_hash($pw, PASSWORD_DEFAULT)."' where `recnum`='".$this->row['recnum']."'";	
		$result = $this->db->query($request);
		return $result;
	}

	/*
		Mitarbeiterdaten verändern
	*/
	public function update($row) {
		$recnum=$row['recnum'];
		unset($row['recnum']);
		
		$set="";
		foreach($row as $k => $v) {
			if ($set != "") {
				$set.=",";
			}
			$set.="`".$k."`='".$this->db->real_escape_string($v)."'";
		}
		
		$request="update bu_mitarbeiter set $set where `recnum`='".$recnum."'";	
		$result = $this->db->query($request);
		
		$this->row['recnum']=$recnum;
		return $result; // Arrayoffset = null ?
		
	}
	
	
	/*
		Benötigte Variablen
		$record 
			'name'
		$row['firmanr']
		$_POST['user']
		$_SESSION['firmanr'];
		
	*/
	public function add($record="") {
		// $name=$record['name'];
		// $pw=cipher($name,$name);
		// $firmanr=$this->row['firmanr'];
		
		// $request="insert select count(*) as sum from bu_mitarbeiter where name='".$this->db->real_escape_string($user)."'";
		// $result = $this->db->query($request);

		$request  = sprintf("INSERT INTO bu_mitarbeiter(firmanr,name,pw) VALUES('%s','%s','%s');",
					$_SESSION['firmanr'],
					$this->db->real_escape_string($_POST['user']),
					password_hash($_POST['user'], PASSWORD_DEFAULT)
					);
		$result = $this->db->query($request);
		return $result;
		
	}
}
?>