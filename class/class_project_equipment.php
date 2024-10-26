<?php
class ProjectEquipment extends Table {
	// public $row=array();        // Array Eine Zeile
	// private static $wahl=array("Ja","Nein");
	
	
	//$adr->format['location']['wahl']=&$firma->row['recnum']
	
	
	public function __construct($db) {
		$this->db=$db;
		$this->name="bu_project_equipment";
		// $this->transfer=true;
		
		
	}
}
?>