<?php
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
?>