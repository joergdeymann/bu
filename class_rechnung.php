<?php
class re_fields {
	public input_value;
	public input_use;
	public output_field;
}
	
// ===========================================================================================
// Klasse fuer Dieses Script: Eingabe und Ausbage POSTS
// ===========================================================================================
class rechnung {
	private $input_fields=array(
		"renr"              => "",
		"rechnungsdatum"    => "",
		"kdnr"              => "",
		"faelligkeit"       => "",
		"layout"            => "",
		"leistungswoche"    => "",  // Kambi Woche / Jahr
		"leistungsmonat"    => "",  // Kombi Monat / Jahr
		"leistungsjahr"     => "",  
		"leistungsdatumvon" => "",
		"leistungsdatumbis" => ""
	);

	private $input_fields_use=array(
		"renr"              => true,
		"rechnungsdatum"    => true,
		"kdnr"              => true,
		"faelligkeit"       => true,
		"layout"            => false,
		"leistungswoche"    => false,  // Kambi Woche / Jahr
		"leistungsmonat"    => false,  // Kombi Monat / Jahr
		"leistungsjahr"     => false,  
		"leistungsdatumvon" => false,
		"leistungsdatumbis" => false
	);

	/*
		felder die gespeichert werden müssen
		Optionale Felder später hinzufügen
	*/
	private $output_fields=array(
		"renr"            => "",
		"rechnungsdatum"  => "",
		"kdnr"            => "",
		"faelligkeit"     => ""
	);
	/*
		"firmanr" = SESSION Variable
		"leistung"        => ""
		"renr",
		"datum",
		"layout", 

		"faellig", -> faelligkeit
	*/	

	private $db_fields = array(	
		"rechnungsnummer"  => "renr",
		"rechnungsdatum"   => "datum",
		"kdnr"             => "kdnr",
		"faelligkeit"      => "faellig",
		"leistung"         => "leistung",
		"leistungbis"      => "leistungbis"		
	);

	/*
		Hier werden die Kopfdaten übertragen, solage die Rechnungskopfdaten noch nicht gespeichert sind
		nur die Felder übertragen die auch in der input listesind, 
		andere per $_POST abfragen, zb. von anderen Scripten
	*/
	public function getPOST_head() {
		foreach($this->input_fields as $k => $v) {
			if (isset($_POST[$v])) {
				$this->input_fields[$k]=$_POST[$v];
			}
		}
	}

	public function use($field,$u=true) {
		$this->input_fields_use[$field]=$u;
		
		
		/* 
			Zusatz aktion zum Speichern der Felder
		*/
		$lei=false;
		$layout=false;
		switch ($field) {
			case "leistungsmonat": 
				$lei=true;
				break;
			case "leistungsjahr": 
				$lei=true;
				break;
			case "leistungswoche": 
				$lei=true;
				break;
			case "leistungsdatumvon": 
				$lei=true;
				$lei2=true;
				break;
			case "leistungsdatumbis": 
				$lei=2;
				break;
			case "layout":
				$layout=true;
				break;
		}
		
		/*
			Leistungsdaten sichern ermöglichen
		*/
		if ($lei) {
			if ($u == true) {
				$this->output_fields["leistung"]="";
			} else if (array_key_exists("leistung", $this->output_fields)) {
				unset($this->output_fields["leistung"]);
				
			}
		}	

		/*
			Leistungsdaten zusätzliches Datum (leistungbis) ermöglichen
		*/
		if ($lei == 2) {
			if ($u == true) {
				$this->output_fields["leistungbis"]="";
			} else if (array_key_exists("leistungbis", $this->output_fields)) {
				unset($this->output_fields["leistungbis"]);
			}
		}	
		/* 
			Layout sichern ermöglichen
		*/
		if ($layout) {
			if ($u == true) {
				$this->output_fields["layout"]="";
			} else if (array_key_exists("layout", $this->output_fields)) {
				unset($this->output_fields["layout"]);
			}
		}			
	}
	
	private function monat2leistung() {
	}
	private function woche2leistung() {
	}
	private function datum2leistung() {
	}
	
}


?>