<?php
class Projekt_einteilung extends Table {	
	
	public function __construct($db) {
		$this->db=$db;
		$this->name="bu_project_division";
		$this->id="id";
	}
}
?>