<?php
if (!class_exists('Rechnung_extra')) {
class Rechnung_extra {
	private $db;
	
	public function __construct(&$db) {
		$this->db=$db;
	}
	
	
	// Zukunft:
	// hole das format aus den KundenSetup 
	// Bearbeite es, finde die höchste nummer raus
	// Speicherung der Kundennummer get dann so:
	// int kdnr; Nur nummer wie gewünscht anfangen
	// dann aus Vorlage zb: "[Y-m-d]000000" oder "RE0000000" oder "RE[Y]0000";
	// Filtern von "[]" als DateTime angaben auch mehrfach möglich "RE[Y]0000[m:s]" z.B.
	//             "0"  Laufende nummer
	//                  alles andere Bleibt wie es ist
	// als Standart "[Y]0000" nehmen
	// SELECT *  FROM `bu_adresse` WHERE  name REGEXP 'De.*?nn'
	// a)
	// 1. Filter als momentane lönge merken
	// Buchstaben am ende abscheneiden
	// Nach der Länge des strings suchen
	// letzten Nummer filtern
	// diese Numemr am ende + 1
	// und das ganze wieder neu Zusammensetzten nach neuen format
	
	// Postion der Zahlen merken und länge  / ??REGXP(.*?[0-9].*
	// SUBSTR(Pos,länge)
	// kdnr als nurmmer speichern, ist so einfacher aufzufinden
	// 
	// REGXP_SUBSTR(renr,".*?([^0-9].*$)
	
	public function getNextRenr() {
		$db=$this->db;
		/*
			neue Rechnungsnummer vergeben
			Standart: YYYYXXXX
			
		*/
		$request="select renr_aufbau from bu_firma where recnum='".$_SESSION['firmanr']."'";
		$result = $db->query($request);
		$r = $result->fetch_assoc();
		if (empty($r['renr_aufbau'])) $r['renr_aufbau']="";
		$renr_aufbau=$r['renr_aufbau'];

		$len=strlen($renr_aufbau);
		if ($len == 0) $len=8;
		$request="select max(renr) as renr from `bu_re` 
			where firmanr='".$_SESSION['firmanr']."'
			and length(renr) = ".strlen($renr_aufbau);

			// hinzufügen
		
		$result = $db->query($request);
		$r = $result->fetch_assoc();

		$pre="";
		$nr=1;

		switch($renr_aufbau) {
			case "RE0000":
				$pre="RE";
				if (isset($r['renr']) and strlen($r['renr']) == strlen($renr_aufbau)) {
					$nr=(int)substr($r['renr'],2,4)+1;					
					if ($nr>9999) $nr=0;
				}
				$c=sprintf("%04d",$nr);
				$renr=$pre.$c;	
				return $renr;
			
			case "REYYYY0000":
				$pre="RE";
				$d=date("Y");
				$nr=1;				
				if (isset($r['renr']) and strlen($r['renr']) == strlen($renr_aufbau)) {
					$nr=(int)substr($r['renr'],6,4)+1;
					$jahr=substr($r['renr'],2,4);					
					if ($nr>9999) $nr=0;
					if ($jahr < $d) {
						$nr=1;
					}
	
				}
				
				$c=sprintf("%04d",$nr);
				$renr=$pre.$d.$c;	
				return $renr;
				
				
			case "YYYY0000":
				$d=date("Y");
				$nr=1;				
				if (isset($r['renr']) and strlen($r['renr']) == strlen($renr_aufbau)) {
					$nr=(int)substr($r['renr'],4,4)+1;
					$jahr=substr($r['renr'],0,4);					
					if ($nr>9999) $nr=0;
					if ($jahr < $d) {
						$nr=1;
					}
	
				}
				
				$c=sprintf("%04d",$nr);
				$renr=$pre.$d.$c;	
				return $renr;

			default:
				$renr_aufbau="YYYY0000";
				$d=date("Y"); 
				$nr=1;
// echo "DEFAULT";
// echo "-".strlen($r['renr'])."-";
// strlen($renr_aufbau)
				if (isset($r['renr']) and strlen($r['renr']) == strlen($renr_aufbau)) {
					$nr=(int)substr($r['renr'],4,4)+1;
					$jahr=substr($r['renr'],0,4);
					
					if ($nr>9999) $nr=0;
					if ($jahr < $d) {
						$nr=1;
					}
				}
				
				$c=sprintf("%04d",$nr);
				$renr=$pre.$d.$c;
				return $renr;
			
		}
		
	}
}
}
?>