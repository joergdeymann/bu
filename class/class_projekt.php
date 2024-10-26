<?php
class Projekt extends Table {
	public function __construct(&$db) {
		$this->db=$db;
		$this->name="bu_projekt";
		// $this->transfer=true;
		
		$this->format=array(
			"recnum" 		=> array("typ" => "hidden", 	"style" => "width:10em;"			 	,"label" => ""), 		
			"firma_recnum" 	=> array("typ" => "hidden", 	"style" => "width:10em;"			 	,"label" => "Firma Recnum"),     // Eigene Firma, 0 = Kunde für alle sichtbar
			"erstell_datum" => array("typ" => "datetime", 	"style" => "width:30em;"			 	,"label" => "Erstelldatum"),  // Besser wenn es Name heist 
			"nr" 		    => array("typ" => "string", 	"style" => "width:30em;"			 	,"label" => "Kürzel/Nr"),    	// Projekt kürzel
			"name" 			=> array("typ" => "string", 	"style" => "width:60em;"			 	,"label" => "Name"),    		// Projekt Bezeichnung lang
			"start" 		=> array("typ" => "date", 		"style" => ""			 				,"label" => "Erster Tag der Veranstaltung"), 
			"ende"			=> array("typ" => "date", 		"style" => ""			 				,"label" => "Letzter Tag der Veranstaltung"),
			"aufbau" 		=> array("typ" => "date", 		"style" => ""			 				,"label" => "Erster Tag des Aufbaus"), 
			"abbau"			=> array("typ" => "date", 		"style" => ""			 				,"label" => "Letzter Tag des Abbaus"),
			"location"		=> array("typ" => "int", 		"style" => "width:10em;"			 	,"label" => "Location"),
			"kunde_recnum"	=> array("typ" => "int", 		"style" => "width:10em;"			 	,"label" => "Kunde"),        // Wenn man ein Teil des Projekts ist, nimmt man diesen Wert
			"stellung"		=> array("typ" => "radio", 		"style" => ""			 				,"label" => "Projektteilnehmer oder Projektführer","wahl" => array("Projektteilnehmer","Projektleitung"),"select" => 1),        // Wenn man ein Teil des Projekts ist, nimmt man diesen Wert
			"info"			=> array("typ" => "textarea", 	"style" => "width:60em;height:10em;"	,"label" => "Allgemeine Auftragsbeschreibung")
			
/*
			"auftraggeber"	=> array("typ" => "int", 	  "style" => "width:10em;"			 	,"label" => "Auftraggeber"), // Wenn man eine Veranstaltung organisiert wird, ist dies der Wert für Kunde
			"anfahrt" 		=> array("typ" => "datetime", "style" => ""			 				,"label" => "Anfahrt"), 	
			"abfahrt" 		=> array("typ" => "datetime", "style" => ""			 				,"label" => "Abfahrt"), 	
			"pl" 			=> array("typ" => "string", "style" => "width:60em;"			 	,"label" => "Ort"), 	
			"tl" 			=> array("typ" => "int", 	"style" => "width:5em;"			   		,"label" => "Zahlungsziel (std. 14 Tage)"), 	
			"km_pauschale" 	=> array("typ" => "string",	"style" => "width:30em;"			 	,"label" => "Telefon privat"),
			"km_weg" 		=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "Telefon mobil"),
			"fahrten" 		=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "Telefon dienslich"),
			"unterkunft" 	=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "E-Mail privat"),        		// Unterbingung 
			"mail_dienst" 	=> array("typ" => "string", "style" => "width:30em;"			 	,"label" => "E-Mail dienstlich")	
*/
		);
	}	
	
}


/*
	später
	projekt_device  
		- typ                1=Eigentum, 2=Arbeiter (leihen), 3=Fremdfirma (leihen)
		- quelle     		 typ: 1: bu_artikel.recnum
							 typ: 2: bu_project_arbeiter.recnum
							 typ: 3: bu_einkauf.recnum
							 
		- ziel               typ: 1: bu_kunde.recnum    // Eigentum verliehen an Kunde, wenn
		- netto         	 Kosten für das leihen / Tag
		- mswt           	 Mehrwertsteuer        / Tag
		- von                Datun verliehen von
		- bis                Datum verliehen bis
		- tage               Anzahl der realen Tage wo  der Gegenstand verliehen wurde, 0= bis-von Das dient zur berechnung der Rechnung
		- info               Zusatzinfo text
		- status             1=geplant, 2=bestäetigt, 3=erhalten
		
		
		muss mir was einfallen lassen: idee 2 Arten machen 
		bu_verleih
		bu_ausleih
		
		
		
		
	
	
	// Deteilinfo für den Arbeiter
	projekt_arbeiter
	 - projekt_recnum           -> Projektnummer
	 - mitarbeiter_recnum       -> ProjektArbeiter  / Fremdfirma steh da in Mtrarbeiter drin
	 - unterkunft_adresse: 	    -> Adresse
	 - unterkunft_preis:    	-> Preis pro Tag
	 - unterkunft_tage:         -> Anzahl Tage
	 - km_pauschale       		-> Preis / KM
	 - km_weg             		-> von Heimatort zur Location
	 - km_fahrten:           	-> Anzahl der Fahrten
	 - tagessatz                -> in Euro
	 - offday                   -> Tagessatz für Offdays
	 - anfahrt 					-> "anfahrt" 		=> array("typ" => "datetime", "style" => ""			 				,"label" => "Anfahrt"), 	
	 - abfahrt					-> "abfahrt" 		=> array("typ" => "datetime", "style" => ""			 				,"label" => "Abfahrt"), 	
		
	// Aufgabenbeschreibung / Funktion	
	projekt_aufgaben
	 - projekt_recnum 		Nummer des Projekts
	 - einsatz:       		TL, PL, Lichttechniker Tontechniker Aufbau
	 - name:          		Aufbau TL
	 - text   				Genaue Aufgabenbeschreibung
	 

	// Einteilung der Arbeiter auf das Projekt
	projekt_einteilung	
	 - projekt_recnum -> Zuordnung zum Projekt
	 - arbeiter  	  -> projekt_arbeiter_recnum    -> Arbeiterspezifische Daten
	 - aufgabe   	  -> projekt_aufgabe_recnum     -> Aufgebabenbeschreibung
		 


	Projekt Daten
	==============
	
	Einteilung: 
	1. aufgabe [bearbeiten]
	   Aufgaben textarea
	   
	   Mitarbeiter Einteilung: 
	   name                 [bearbeiten] [Infos]
	   name                 [bearbeiten] [Infos]
	   name                 [bearbeiten] [Infos]
	   [hinzufügen]

	2. aufgabe [bearbeiten]
	   Aufgaben textarea
	   
	   Mitarbeiter Einteilung: 
	   name                 [bearbeiten] [Infos]
	   name                 [bearbeiten] [Infos]
	   name                 [bearbeiten] [Infos]
	   [hinzufügen]
	   
	Kostenauflistung des Projekts
	==============================
	
	   
	
		 
*/		 
?>