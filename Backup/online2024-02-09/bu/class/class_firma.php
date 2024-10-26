<?php
/*
	Daten Tabelle: bu_firma
*/
class firma {
	private $feld;
	public $row;
	
	public $db;
	
	function __construct($db="") {
		if ($db != "") {
			$this->db=$db;
		}
		
		$this->feld = &$row; // alias
		$this->row = array();
		
	}
		
	function init() {
		// $this->feld = array();
	}
	
	function load($recnum=0) {
		if ($recnum == 0) {
			$recnum=$_SESSION['firmanr'];
		}
		$request='select * from bu_firma where recnum='.$_SESSION['firmanr'];
		$result = $this->db->query($request);	
		$this->row = $result->fetch_assoc();

	}
	
	
	function get($fieldname) {
		if (isset($this->row[$fieldname])) {
			return $this->row[$fieldname];
		} else {
			return "";
		}
	}

	function set($fieldname,$content="") {
		if (isset($fieldname)) {
			$this->row[$fieldname]=$content;
			return 1;
		} else {
			return 0;
		}
	}
}
?>
