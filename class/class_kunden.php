<?php
class Kunden extends Table {
	public function __construct(&$db) {
		$this->db=$db;
		$this->name="bu_kunden";
		// $this->transfer=true;
		
		$this->format=array(
			"recnum" 		=> array("typ" => "hidden", "style" => "width:10em;"			 	,"label" => ""), 		
			"auftraggeber" 	=> array("typ" => "hidden", "style" => "width:10em;"			 	,"label" => "Firma Recnum"),     // Eigene Firma, 0 = Kunde für alle sichtbar
			"firma" 		=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "Name des Kunden"),  // Besser wenn es Name heist 
			"kdnr" 			=> array("typ" => "string", "style" => "width:10em;"			 	,"label" => "Kundennummer"), 
			"vorname"		=> array("typ" => "string", "style" => "width:60em;"			 	,"label" => "Ansprechpartner Vorname"),
			"nachname" 		=> array("typ" => "string", "style" => "width:60em;"			 	,"label" => "Ansprechpartner Nachname"), 	
			"strasse" 		=> array("typ" => "string", "style" => "width:60em;"			 	,"label" => "Straße"), 	
			"plz"			=> array("typ" => "string", "style" => "width:10em;"			 	,"label" => "PLZ"),
			"ort" 			=> array("typ" => "string", "style" => "width:60em;"			 	,"label" => "Ort"), 	
			"zahlungsziel" 	=> array("typ" => "int", 	"style" => "width:5em;"			   		,"label" => "Zahlungsziel (std. 14 Tage)"), 	
			"tel_privat" 	=> array("typ" => "string",	"style" => "width:30em;"			 	,"label" => "Telefon privat"),
			"tel_mobil" 	=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "Telefon mobil"),
			"tel_dienst" 	=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "Telefon dienslich"),
			"mail_privat" 	=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "E-Mail privat"),
			"mail_dienst" 	=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "E-Mail dienstlich")	
		);
	}
	
	public function loadByKDNR($kdnr) {
		$request="select * from bu_kunden where `kdnr`='$kdnr'";
		$result=$this->db->query($request);
		$this->row=$result->fetch_assoc();
		return $this->row;
	}
/*	
	public function save($row) {
		if (empty($row['recnum'])) {
			$this->insert($row);
		} else {
			$this->update($row);
		}
	}
*/	
}
?>