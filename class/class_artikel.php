<?php
class Artikel extends Table {
	// public $row=array();        // Array Eine Zeile
	
	public function __construct($db) {
		$this->db=$db;
		$this->name="bu_artikel";
	}
}
?>