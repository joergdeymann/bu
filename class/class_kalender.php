<?php
/*
Beispiel:
$name="Jörg Deymann";
$name2="Susi Veit";

$k=new Kalender();
$k->new($name);

$k->add("2023-06-01",2);
$k->add("2023-06-02",2,20);
$k->add("2023-06-03",2,99);
$k->add("2023-06-04",2);
$k->add("2023-06-05",2);
$k->add("2023-06-06",4);
$k->add("2023-06-07",4);
$k->add("2023-06-08",2);
$k->add("2023-07-09",2);
$k->add("2023-07-10",2);
$k->add("2023-07-11",2);


$k->new($name2);
$k->add("2023-06-01",3);
$k->add("2023-06-02",3);
$k->add("2023-06-03",3,50);
$k->add("2023-06-04",3);
$k->add("2023-07-09",2);
$k->add("2023-07-10",2);
$k->add("2023-07-11",2);

$k->set($name);
$k->setStart();
echo $k->getHeader();
echo $k->getBody();

$k->set($name2);
$k->setStart("2023-06-10");
echo $k->getBody();
echo $k->getFooter();


$k->nextMonth();
$k->set($name);
echo "<br>";
echo $k->getHeader();
echo $k->getBody();
$k->set($name2);
echo $k->getBody();
echo $k->getFooter();

echo $k->legende();
*/


/*
Kalender

*/
class Kalender {
	private $kuerzel=array(
		""  => 0,//  0. transparent/Schwarz/"": Wochenende/Feiertag
		"!" => 1,//  1. Weiß/Schwarz/!(Fett): Fehlzeit
		"z" => 2,//  2. HellGrün/Schwarz/Stunden: Arbeitszeit
		"ü" => 3,//  3. HellGrün/Orange/Stunden:  Arbeitszeit mit überstunden
		"U" => 4,//  4. Gelb/Schwarz/+: Urlaub bezahlt
		"-" => 5,//  5. Gelb/Schwarz/-: Urlaub unbezahlt
		"?" => 6,//  6. Gelb/Schwarz/?: Urlaub beantragt, falls nicht in AZ (AZ hat bei allen Prio)
		"M" => 7,//  7. Gelb/Schwarz/M: Mutterurlaub
		"S" => 8,//  8. Gelb/Schwarz/S: Sonderurlaub
		"K" => 9,//  9. Hellrot/Schwarz/K: Krank
		"X" =>10,// 10. Hellrot/Schwarz/X: Krank ohne Krankmeldung (AU)
		"F" =>11 // 11. Orange/Schwarz/F: Feiertag
	);

	private $colors=array(
	 array("transparent", "#000000","",         "Freier Tag/Wochende" ),          // Fehlzeit
	 array("#ffffff",     "#000000","<b>!</b>", "Fehlzeit" ),          // Fehlzeit
	 array("#99FF99",     "#000000","8",        "Arbeitszeit"),        // AZ normal
	 array("#99ff99",     "orange", "<b>10</b>","Arbeitszeit mit Überstunden"), // AZ + ÜS
	 array("lightyellow", "#000000","U",        "Urlaub"),             // Urlaub normal
	 array("lightyellow", "#000000","-",        "unbezahlter Urlaub"), // Urlaub unbezahlt
	 array("lightyellow", "#000000","?",        "beantragter Urlaub"), // Urlaub beantragt
	 array("lightyellow", "#000000","M",        "Mutterschutz"),       // Mutterurlaub
	 array("lightyellow", "#000000","S",        "Sonderurlaub"),       // Sonderurlaub
	 array("red",         "#FFFFFF","K",        "Krank"),              // Krank normal
	 array("#FF9999",     "#FFFFFF","X",        "Krank ohne AU"),      // Krank ohne Krankmeldung 
	 array("orange",      "#FFFFFF","<b>F</b>",        "Feiertag")            // Krank normal
	);

	// Anzeigematrix für $colors
	private $matrix=array(
	 0,4, 8,
	 1,5, 9,
	 2,6,10,
	 3,7,11
	);


	private $monat=array("","Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
	
	public $liste=array();   // Original Liste mit liste['Name']['datum'] => Wert
	private $stunden=array(); // stunden['Name']['datum']
	
	private $datumliste;    // Referenz auf 1 Mitarbeiter Datum Urlaubstyp
	private $stundenliste;  // Referenz auf 1 Mitarbeiter Stunden
	
	private $name;         // Mitarbeitername
	
	public $start;         // Datum mit anzeigemonat
	
	
	function __construct () {
		$this->start=new DateTime();             //DateTime 
	}
	
	private function getKey($k) {
		return array_flip($this->kuerzel)[$k];
	}

	
	public function new ($name) {
		$this->liste[$name]=array();
		$this->datumliste=&$this->liste[$name];	
		$this->stunden[$name]=array();
		$this->stundenliste=&$this->stunden[$name];
	}
	
	public function set($name) {
		if (!isset($this->liste[$name])) {
			$this->new($name);
			return;
		}
		$this->name=$name;
		$this->datumliste=&$this->liste[$name];		
		$this->stundenliste=&$this->stunden[$name];
	}
	public function nextMonth() {
		$interval = new DateInterval('P1M');
		$this->start->add($interval);
	}
	
		
		
	private function getStartDatum() {
		$d=array_key_first($this->datumliste);
		foreach($this->datumliste as $k => $v) {
			if ($k<$d) {
				$d=$k;
			}
		}
		return new DateTime($d);
	}
	private function getEndDatum() {
		$d=array_key_first($this->datumliste);
		foreach($this->datumliste as $k => $v) {
			if ($k>$d) {
				$d=$k;
			}
		}
		return new DateTime($d);
	}
	
	public function clearKalender() {
		/* nicht die Pointer nehmen */
		$this->liste[$this->name]=array();
		$this->stunden[$this->name]=array();
	}
	
	public function setMitarbeiterName($nr) {
	
	}

	public function setStart($start="") {
		if (empty($start)) {
			$start=$this->getStartDatum();
		} else {
			$start=new DateTime($start);
		}		
		$this->start=new DateTime($start->format('Y-m-01'));
	}
	
/*
	public function set($name,&$datumliste) {
		$this->name=$name;
		$this->datumliste=$datumliste;
	}
*/
	
	public function add($datumliste,$wert=0,$stunden=0) {
		
		if (gettype($wert) == "string") {
			$wert=$this->kuerzel[$wert];
		}
		if (gettype($datumliste) == "object") {
			$datumliste=$datumliste->format("Y-m-d");
		}
				
		if (gettype($datumliste) == "string") {
			$this->datumliste[$datumliste]=$wert;
		}
		if (gettype($datumliste) == "array") {
			$this->datumliste=$datumliste;
		}
		
		if ($stunden>0) {
			$this->stundenliste[$datumliste]=$stunden;
		}
		
	}

	public function getHeader() {
		$html = '<style>
		table#kalender {
			border-collapse: collapse;
			background-color: transparent;
			border:black 1px solid;
		}
		table#kalender tr td {
			vertical-align: middle;	
			text-align: center;
			height:20px;
			width:20px;
			min-width:20px;
			border-bottom:1px solid black;
			border-right:1px solid black;
			border-top:1px solid black;
		}

		table#kalender tr th {
			text-align: center;
			vertical-align:center;
		}
		
		table#kalender tr th#border {
			border: 1px solid black;
		}
		</style>';
		
		$dt_von=$this->start;
		$days=$dt_von->format("t");
		$html.='<table id="kalender" cellspacing=1 cellpadding=1 bgcolor:transparent;>';
		$html.='<tr><th id="border" colspan="'.($days+1).'">';
		if (sizeof($this->liste) == 1) {
			$html.=$this->name;
		} else {
			$html.=$this->monat[$dt_von->format("n")].' '.$dt_von->format("Y");
		}
		$html.='</th></tr>';

		$html.='<tr><th id="border">Name</th>';
		for ($i=1;$i<=$dt_von->format("t");$i++) {
			$dt=new DateTime($dt_von->format("Y-m-$i 00:00:00"));
			if ($dt->format("N") > 5) {
				$c="silver";
			} else {
				$c="grey";
			}
			$html.='<td valign="center" style="color:#000000;background-color:'.$c.'">'.$i.'</td>';	
		}
		$html.='</tr>';
		return $html;

	}
	
	public function getBody() {
		$start=$this->start;
		// $start=new DateTime($start);
		//$dt_von=(new DateTime($start))->format("Y-m-01");
		// $dt_bis=(new DateTime($start))->format("Y-m-t");

		/// $dt_start=DateTime($start." 00:00:00")->format("Y-m-01");
		
		// 
		// eine Zeile
		//
		if (sizeof($this->liste) == 1) {
			echo '<tr><th id="border">'.$this->monat[$this->start->format("n")]." ".$this->start->format("Y").'</th>';
		} else {
			echo '<tr><th id="border">'.$this->name.'</th>';
		}
		for ($i=1;$i<=$start->format("t");$i++) {
			$d=sprintf("%02d",$i);
			$idx=$start->format("Y-m-$d");
			$c=0;
			if (isset($this->datumliste[$idx])) {
				$c=$this->datumliste[$idx];
			}
			$text=$this->colors[$c][2];
			
			if (isset($this->stundenliste[$idx])) {				
				if ($c == 2) {				
					$text=$this->stundenliste[$idx];
				}
				if ($c == 3) {
					$text='<b style="font-weight:1000;">'.$this->stundenliste[$idx]."</b>";
				}
			}
			
			echo '<td valign="center" style="color:'.$this->colors[$c][1].';background-color:'.$this->colors[$c][0].'">'.$text.'</td>';	
		}
		echo '</tr>';
	}

	public function getFooter() {
		return "</table>";
	}

	public function legende() {
		//$html = '<table id="kalender" cellspacing=1 cellpadding=1 style="border:1px black solid;">';
		$html = '<table cellspacing=1 cellpadding=1 style="border:1px black solid;">';
		$html.= '<tr><th colspan=6>Legende</th></tr>';
		$html.= '<tr>';
		
		$rows=1;
		$c=0;
		// foreach($colors as $color) {
		foreach($this->matrix as $m) {
			$color=$this->colors[$m];
			$html.= '<td valign="center" style="color:'.$color[1].';background-color:'.$color[0].'">'.$color[2].'</td>';
			$html.= '<td style="display:inline-block;white-space: nowrap;text-align:left;margin-right:50px;">'.$color[3].'</td>';
			
			$rows++;
			$c++;
			if ($rows>3) {
				$html.= '</tr><tr>';
				$rows=1;
			}		
		}
		$html.= '</tr></table>';
		return $html;
		
	}
}




?>


