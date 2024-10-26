<?php
class Projekt_aufgabe extends Table {
	public function __construct(&$db) {
		$this->db=$db;
		$this->name="bu_projekt_aufgabe";
		// $this->transfer=true;
		
		$this->format=array(
			"recnum" 			=> array("typ" => "hidden", 	"style" => "width:10em;"			 	,"label" => ""), 		
			"firma_recnum" 		=> array("typ" => "hidden", 	"style" => "width:10em;"			 	,"label" => "Firma Recnum"),     // Eigene Firma, 0 = Kunde für alle sichtbar
			"projekt_recnum" 	=> array("typ" => "hidden", 	"style" => "width:10em;"			 	,"label" => "Projekt Recnum"),   // Projekt
			"name" 				=> array("typ" => "string", 	"style" => "width:60em;"			 	,"label" => "Einsatzbereich<br>TL, PL, Aufbau"), 
			"text"				=> array("typ" => "textarea", 	"style" => "width:70em;height:40em;"	,"label" => "Aufgabenbeschreibung")
		);
	}
}
?>