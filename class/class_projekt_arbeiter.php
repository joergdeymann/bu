<?php
class Projekt_arbeiter extends Table {
	public function __construct(&$db) {
		$this->db=$db;
		$this->name="bu_projekt_arbeiter";
		// $this->transfer=true;
		
		$this->format=array(
			"recnum" 				=> array("typ" => "hidden", 	"style" => "width:10em;"			 	,"label" => "Projekt Arbeiter Recnum"), 		
			"firma_recnum" 			=> array("typ" => "hidden", 	"style" => "width:10em;"			 	,"label" => "Firma Recnum"),     // Eigene Firma, 0 = Kunde für alle sichtbar
			"projekt_recnum" 		=> array("typ" => "hidden", 	"style" => "width:30em;"			 	,"label" => "Projekt Recnum"),  // Besser wenn es Name heist 
			"mitarbeiter_recnum" 	=> array("typ" => "hidden", 	"style" => "width:30em;"			 	,"label" => "Mitarbeiter Recnum"),    	// Projekt kürzel
			"start" 				=> array("typ" => "date", 		"style" => ""			 				,"label" => "Erster Tag des Events"), 
			"ende"					=> array("typ" => "date", 		"style" => ""			 				,"label" => "Letzter Tag der Events"),
			"anfahrt" 				=> array("typ" => "date", 		"style" => ""			 				,"label" => "Anfahrt"), 
			"abfahrt"				=> array("typ" => "date", 		"style" => ""			 				,"label" => "Abfahrt"),
			"unterkunft_recnum"		=> array("typ" => "int", 		"style" => "width:10em;"			 	,"label" => "Unterkunft auswählen"),        // Wenn man ein Teil des Projekts ist, nimmt man diesen Wert
			"unterkunft_preis"		=> array("typ" => "euro", 		"style" => "width:10em;"			 	,"label" => "Gesamtpreis der Unterkunft (netto)"),
			"unterkunft_start"		=> array("typ" => "date", 		"style" => ""			 				,"label" => "Unterkunft Checkin"),
			"unterkunft_ende"		=> array("typ" => "date", 		"style" => ""			 				,"label" => "Unterkunft Checkout"),
			"info"					=> array("typ" => "textarea", 	"style" => "width:60em;height:10em;"	,"label" => "Information für den Arbeiter"),
			"km_pauschale"			=> array("typ" => "euro", 	  	"style" => "width:5em;"			 		,"label" => "KM Pauschale"), // Wenn man eine Veranstaltung organisiert wird, ist dies der Wert für Kunde
			"km_weg" 				=> array("typ" => "int", 		"style" => "width:5em;"					,"label" => "Weg zum Event"), 	
			"km_fahrten" 			=> array("typ" => "int", 		"style" => "width:5em;"					,"label" => "Anzahl der Fahrten"), 	
			"tagessatz" 			=> array("typ" => "euro", 		"style" => "width:5em;"			 		,"label" => "Tagessatz"), 	
			"tagessatz_offday" 		=> array("typ" => "euro", 		"style" => "width:5em;"			   		,"label" => "Tagessatz Offday"), 	
			"arbeitszeit" 			=> array("typ" => "int",		"style" => "width:5em;"			 		,"label" => "Standart Arbeitsstunden pro Tag"),
			"ueberstunden_satz" 	=> array("typ" => "euro", 		"style" => "width:5em;"			 		,"label" => "Überstundenstaz"),
		);

	}
	
}
		 
?>