<?php

/*
Momentan: 
Neu: Benutzer hinzufügen / eigene Info editieren
1. bu_user
   recnum
   benutzername
   pw
   mail
   last_firma (zuletzt eingeloggt)
   
2. bu_rechte
   benutzername
   firmanr
   level
   
 User anlegen:
Name eingeben
last_firma = diese firma

rechte
Name
Firmanummer
level 0
 
*/

	/*
		// Hier Passwort neu einfügen 
		// wird von mitarbeiter.php so benutzt
		// vielleicht besser dort einbauen
		if (empty($row['pw'])) {
			$row['pw']=$row['name'];
		}
		$row['pw']=password_hash($row['pw'], PASSWORD_DEFAULT);
	*/

 
class Database {
	protected  $database;
	protected  $db = "";

	public $row = array();


	public function __construct(&$db) {
		$this->db=$db;		
	}

	public function loadByRecnum($recnum) {
		$request="select * from ".$this->database." where recnum='".$recnum."'";
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		return $this->row;
	}

	/*
		Mitarbeiter einfügen
	*/
	
	public function insert($row) {
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
			$values.= "'".$this->db->real_escape_string($v)."'";
			$keys  .= "`".$k."`";
		}
		
		$request="insert into ".$this->database." ($keys) values ($values)";	
		$result = $this->db->query($request);
		if ($result) {
			$this->row['recnum']=$this->db->insert_id;
		} 
		return $result;
	}

	/*
		Mitarbeiterdaten verändern
	*/
	public function update($row) {
		if (isset($row['recnum'])) {
			$recnum=$row['recnum'];
		} else {
			$recnum=$this->row['recnum'];
		}
		unset($row['recnum']);
		
		$set="";
		foreach($row as $k => $v) {
			$this->row[$k]=$v; 
			
			if ($set != "") {
				$set.=",";
			}
			

			$set.="`".$k."`='".$this->db->real_escape_string($v)."'";
		}
		
		$request="update ".$this->database." set $set where `recnum`='".$recnum."'";	
		$result = $this->db->query($request);
		
		$this->row['recnum']=$recnum;
		return $result; // Arrayoffset = null ?
		
	}
}

class User extends DataBase {
	public function __construct(&$db) {
		$this->db=$db;		
		$this->database="bu_user";		
	}
	
	public function loadByName($name) {
		$request="select * from bu_user where benutzername like '%".$this->db->real_escape_string($name)."%' and last_firma=".$_SESSION['firmanr'];
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		return $this->row;
	}
	/*	
		Passwort neu setzen
	*/
	public function setPassword($pw) {
		$request="update ".$this->database." set `passwort`='".password_hash($pw, PASSWORD_DEFAULT)."' where `recnum`='".$this->row['recnum']."'";	
		$result = $this->db->query($request);
		return $result;
	}

}

class Rechte extends DataBase {
	public function __construct(&$db) {
		$this->db=$db;		
		$this->database="bu_rechte";		
	}
	
	public function loadByName($name) {
		$request="select * from bu_rechte where benutzername like '%".$this->db->real_escape_string($name)."%' and firmanr=".$_SESSION['firmanr'];
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		return $this->row;
	}
	public function loadByUniqueName($name) {
		$request="select * from bu_rechte where benutzername = '".$this->db->real_escape_string($name)."' and firmanr=".$_SESSION['firmanr'];
		$result = $this->db->query($request);
		$this->row = $result->fetch_assoc();
		return $this->row;
	}

	public function update($row) {
		$set="";
		foreach($row as $k => $v) {
			$this->row[$k]=$v; 
			
			if ($set != "") {
				$set.=",";
			}
			$set.="`".$k."`='".$this->db->real_escape_string($v)."'";
		}
		
		$request="update ".$this->database." set $set where `benutzername`='".$row['benutzername']."' and `firmanr`='".$_SESSION['firmanr']."'";	
		$result = $this->db->query($request);
		
		return $result; // Arrayoffset = null ?
		
	}

}

?>