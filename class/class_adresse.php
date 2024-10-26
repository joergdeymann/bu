<?php
class Adresse extends Table {
	// public $row=array();        // Array Eine Zeile
	// private static $wahl=array("Ja","Nein");
	
	
	//$adr->format['location']['wahl']=&$firma->row['recnum']
	
	
	public function __construct($db) {
		$this->db=$db;
		$this->name="bu_adresse";
		$this->transfer=true;
		
		$this->format=array(
			"recnum"      	=> array("typ" => "hidden",   "style" => "width:10em;"			 	,"label" => ""),
			"firmanr"      	=> array("typ" => "hidden",   "style" => "width:10em;"			 	,"label" => "Firma Recnum"),
			"name"			=> array("typ" => "string",   "style" => "width:60em;"			 	,"label" => "Location/Firma Name"),
			"name_zusatz"	=> array("typ" => "string",   "style" => "width:60em;"			 	,"label" => "Name Zusatzfeld"),
			"anrede"		=> array("typ" => "string",   "style" => "width:20em;"			 	,"label" => "Anrede (Herr, Frau, Prof., Dr.)"),			
			"vorname"		=> array("typ" => "string",   "style" => "width:60em;"			 	,"label" => "Vorname"),
			"nachname"		=> array("typ" => "string",   "style" => "width:60em;"			 	,"label" => "Nachname"),
			"location"		=> array("typ" => "radio",    "style" => ""           			 	,"label" => "Ist es eine Location"      ,"wahl" => array("Ja","Nein"),"select" => "2"),
			"istfirma"		=> array("typ" => "radio",    "style" => ""           			 	,"label" => "Ist es eine Firma ?"       ,"wahl" => array("Ja","Nein"),"select" => "1"),
			"kunde_recnum"  => array("typ" => "int",   	  "style" => "display: none;"			,"label" => "Kunde"),
			"zuordnung"		=> array("typ" => "selection","style" => "width:20em"           	,"label" => "Art des Eintrages"       ,"wahl" => array("Rechnung","Angebot","Lieferdaten","Firmensitz/Büro","Allgemein","CC"),"select" => "1"),
			"strasse"		=> array("typ" => "string",   "style" => "width:60em;"		 	 	,"label" => "Straße, Nr"),
			"strasse_zusatz"=> array("typ" => "string",   "style" => "width:60em;"			 	,"label" => "Strasse Zusatzfeld"),
			"plz"	  		=> array("typ" => "string",   "style" => "width:10em;"			 	,"label" => "PLZ"),
			"ort"			=> array("typ" => "string",   "style" => "width:60em;"			 	,"label" => "Ort"),
			"mail"			=> array("typ" => "string",   "style" => "width:60em;"				,"label" => "E-Mail"),
			"tel1"			=> array("typ" => "string",   "style" => "width:60em"				,"label" => "Telefon Festnetz"),  //Festnetzt oder Mobil
			"tel2"			=> array("typ" => "string",   "style" => "width:60em"				,"label" => "Telefon Fax oder Mobil"),  //Fax oder Mobil			
			"info"			=> array("typ" => "textarea", "style" => "width:60em;height:10em;"	,"label" => "Info")
		);
		
	}
}
?>