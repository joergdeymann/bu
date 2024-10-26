<?php
class Kunden {
	private $db;
	public $row=array();
	
	public function __construct(&$db) {
		$this->db=$db;
	}
	
	public function loadByKDNR($kdnr) {
		$request="select * from bu_kunden where `kdnr`='$kdnr'";
		$result=$this->db->query($request);
		$this->row=$result->fetch_assoc();
		return $this->row;
	}

	public function loadByRecnum($recnum) {
		$request="select * from bu_kunden where `recnum`='$recnum'";
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
		
		$request="insert into bu_kunden ($keys) values ($values)";	
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
		
		$request="update bu_kunden set $set where `recnum`='".$recnum."'";	
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
}
?>