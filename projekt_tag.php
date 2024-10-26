<?php
include "dbconnect.php";
include "class/class_mitarbeiter.php";
include "class/class_firma.php";
// include "class/class_project_tag.php";
include "session_zeiterfassung.php";

$m=new mitarbeiter($db);
$m->loadByRecnum($_SESSION['usernr']);

$f=new firma($db);
$f->load($m->row['firmanr']);
$_SESSION['firmanr']=$m->row['firmanr'];

$pt=new project_tag($db);
$pt->mitarbeiternr=$m->row['nr'];
$pt->loadByDate();

$save=array();

$msg="";
/* 
	KM
*/
if (empty($_POST['km'])) {
	if (empty($pt->row['km'])) {
		$_POST['km']="0";
	} else {		
		$_POST['km']=$pt->row['km'];
	} 
} else {
	if (empty($pt->row['km'])) {
		$save['km']=$_POST['km'];
		$msg.="KM angelegt<br>";
	} else 
	if ($pt->row['km'] != $_POST['km']) {		
		$save['km']=$_POST['km'];
		$msg.="KM verändert<br>";
	}
}
/* 
	info
*/
if (empty($_POST['info'])) {
	if (empty($pt->row['info'])) {
		$_POST['info']="";
	} else {		
		$_POST['info']=$pt->row['info'];
	}
} else {
	if (empty($pt->row['info'])) {
		$save['info']=$_POST['info'];
		$msg.="Text angelegt<br>";
	} else 
	if ($pt->row['info'] != $_POST['info']) {	
		// $msg.=$pt->row['info']."<br>";
		// $msg.=$_POST['info']."<br>";
		
		$save['info']=$_POST['info'];
		$msg.="Text verändert<br>";
	}
}
/* 
	arbeitstyp
*/
if (empty($_POST['arbeitstyp'])) {
	if (empty($pt->row['arbeitstyp'])) {
		$_POST['arbeitstyp']="0";
	} else {		
		$_POST['arbeitstyp']=$pt->row['arbeitstyp'];
	}
} else {
	if (empty($pt->row['arbeitstyp'])) {
		$save['arbeitstyp']=$_POST['arbeitstyp'];
		$msg.="Arbeitstyp angelegt<br>";
		
	} else 
	if (($pt->row['arbeitstyp']) != $_POST['arbeitstyp']) {		
		$save['arbeitstyp']=$_POST['arbeitstyp'];
		
		// $msg.=$pt->row['arbeitstyp']."<br>";
		// $msg.=$_POST['arbeitstyp']."<br>";
		
		$msg.="Arbeitstyp verändert<br>";
	}
}


if (sizeof($save) > 0) {
	$dt=new DateTime();
	$save['mitarbeiternr']  = $m->row['nr'];
	$save['datum']          = $dt->format("Y-m-d");
	$save['firmanr']        = $m->row['firmanr'];
	
	if ($pt->saveByRecnum($save)) {
		$msg='<p style="color:white; font-weight:900;">'.$msg.'</p>';
	} else {
		$msg='<p style="color:red;font-weight:900;">Projektinfo konnte nicht gesichert werden werden</p>'; 
	}
}	


headers();
echo '<body><center>';
echo '<form action="APP_projeKt.php" method="POST">';
echo '<h1>Projektinfo</h1>';
if (!empty($msg)) {
	echo '<div id="animiert">'.$msg.'</div>';
}
$dt=new Datetime();
echo '<h2>KM heute ('.$dt->format("d.m.Y").')</h2>';  // wunsch mit einer großen Box
echo '<p>';
echo '<input type="number" name="km" size="6" value="'.$_POST['km'].'"><br>';
// echo '<button id="button" name="btn_km" type="submit" value="senden">senden</button>';
echo '</p>';
echo '<br>';
echo '<h2>Arbeitszeit</h2>';
$checked=array("","","","","","");
$i=0;
// erst aus datei laden, falls nicht vohanden, diesen vorschlag machen
// heir abfragen: 
// 0 Arbeitszeit  bis  10h    = normale Arbeitszet
// 1 Arbeitszeit  0           = offday bezahlt
// 2 Arbeitszeit  0           = offday unbezahlt
// 3 Arbeitszeit  12:15 - 16h = überstunde AZ ohne pause
// 4 Arbeitszeit  > 16h       = doppelter Tagessatz 
// diese Zeiten werden in der Kundendatei festgelegt,
// vorbelegung nur wenn Kunde bekannt ist.
// Kunde kann auch in der APP geändert werden

// erst mal standart

if (empty($_POST['arbeitstyp'])) {
	$checked[0]="checked";
} else {
	$i=$_POST['arbeitstyp'];
	$checked[$i]="checked";
}
echo '<div style="font-size: 1.2em;text-align:left;display: inline-block;">';
$i=0;
foreach($pt->arbeitstyp as $v) {
	echo '<label><input type="radio" name="arbeitstyp" value="'.$i.'" '.$checked[$i].'>'.$v.'</label><br>';
	$i++;
}
echo '</div><br>';
echo '<br>';
// echo '<button id="button" name="btn_typ" type="submit" value="senden">senden</button>';
echo '<h2>Info</h2>';
echo '<textarea name="info" rows="10" style="width:90%">'.$_POST['info'].'</textarea>';
echo '<br>';
echo '<button id="button" name="btn_typ" type="submit" value="senden">senden</button>';

echo '</form>';
echo '<div id="menu" style="border-top:red solid 4px;"><a id="button" href="zeiterfassung.php">Zeiten</a><a id="button" href="app_projekt_tag.php">Projekt</a><a id="button" href="chat.php">Nachricht</a></div>';
echo "</center></body></html>";


function headers() {
echo '<html lang="de"><head>
	<meta charset="utf-8">
	<meta name="description" 
		  content="tägliche Projekterfassung für die Vorbereitung der Rechnung">
	<meta name="keywords" content="Stempeluhr, stempeln, Zeiterfassung, Krankheitserfassung, Urlaubserfassung">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Projekt Tagesinfo</title>
	<link rel="stylesheet" href="zeiterfassung.css">
	</head>
';
}


/*
bu_project_tag
------------------
Firmanr: Firma, bei der du arbeitest
Kunde: Kunde, der betroffen ist: 0 = keinei info, ansonsten Kundennamen anzeigen

Tag:  Datum der betroffen ist
arbeitstyp: 0 = normalter Tagessatz, 
			1 = bezahlzter Offday, 
			2 = unbezahlter Offday, 
			3 = Überstunden, 
			4 = doppelter Tagessatz
info: Was hast du heute ausgefressen ? = Besonderes
Project Zugehörigeit : bu_project.recnum
*/


class project_tag {
	private $db;
	public  $row  = array();
	public  $kdnr = ""; 
	public  $mitarbeiternr;

	public $arbeitstyp = array(
		"normaler Tag",
		"bezahlter Offday",
		"kostenloser Offday",
		"Überstunden",
		"doppelter Tagessatz"	
	);
	
	
	public function __construct(&$db) {
		$this->db=$db;
	}
	
	public function loadByRecnum($recnum) {
		
	}

	public function loadByDate() {
		$dt=    new DateTime();
		$datum= $dt->format("Y-m-d");
		$kdnr=  $this->kdnr;
		
		$mitarbeiternr=$this->mitarbeiternr;		
		
		$request='SELECT * from `bu_project_day` where firmanr="'.$_SESSION['firmanr'].'" and kdnr="'.$kdnr.'" and datum = "'.$datum.'" and mitarbeiternr = "'.$mitarbeiternr.'"';
		if (empty($kdnr)) {
			$request='SELECT * from `bu_project_day` where firmanr="'.$_SESSION['firmanr'].'" and datum = "'.$datum.'" and mitarbeiternr = "'.$mitarbeiternr.'"';
		}
		
		// echo $request;

		$result = $this->db->query($request);
		if ($result->num_rows > 0) {
			$this->row = $result->fetch_assoc();
			return $this->row;	
		}
		return null;	
	}
	
	public function saveByRecnum($row) {
		$result=false;
		if (isset($this->row['recnum']) and ($this->row['recnum'] > 0)) {
			$row['recnum']=$this->row['recnum'];
			$result=$this->update($row);
		} else {
			$result=$this->add($row);
		}
		return $result;
	}
	

	public function add($row) {
		unset ($row['recnum']);  // zur Sicherheit
		$values="";
		$keys="";
		foreach($row as $k => $v) {
			if ($values != "") {
				$values.=",";
				$keys.=",";
			}
			$values.= "'".$this->db->real_escape_string($v)."'";
			$keys  .= "`".$k."`";
		}
		
		$request="insert into `bu_project_day` ($keys) values ($values)";	
		$result = $this->db->query($request);
		if ($result) {
			$this->row['recnum']=$this->db->insert_id;
		} 
		return $result;
		
	}

	public function update($row) {
		$recnum=$row['recnum'];
		unset($row['recnum']);
		
		$set="";
		foreach($row as $k => $v) {
			if ($set != "") {
				$set.=",";
			}
			$set.="`".$k."`='".$this->db->real_escape_string($v)."'";
		}
		
		$request="update `bu_project_day` set $set where `recnum`='".$recnum."'";	
		$result = $this->db->query($request);
		
		$this->row['recnum']=$recnum;
		return $result; // Arrayoffset = null ?
		
	}
	
	public function getTyp($nr) {
		return $this->arbeitstyp($nr);
	}
}

/*

|Zeiten|Projekt|Nachricht|
---------------------------

Rechnung
---------
heutige KM: ________

Arbeitszeit: 
o normaler Tag
o OFFDAY
o kostenloser OFFDAY
o berechne |____| überstunden
o doppelter Tagessatz

heutige Info:
______________________
______________________
______________________

[Fertig]


  
------------------

bu_project

Projekt
-------
Datum von: ___________
Datum Bis: ___________
Projektnr: ____________
Projektname: ___________
Kontakt Daten:
	Name1  
	Mail(Typ): jd.de@web.de
	Telefon: +491234567
	[aendern]
	-> adress: recnum Firmanr Kundennr Anrede Name, Strasse, PLZ, Ort    
	-> contact: recnum Key (=adress.recnum), Typ (0=mail, 1=Telefon), Typtext (Telefon Mail Dienst, Mail Rechnung, Mail Privat), content
	

	Name2  
	Mail(Typ): jd.de@web.de
	Telefon: +491234567
	Strasse:
	PLZ Ort:
	
	[aendern]

	[hinzufügen]
	
	
	
Info: (width=100% rows=5)
_______________________
_________________________
__________________________

Wichtiges:
___________________________
___________________________
____________________________

[Fertig]


|Zeiten|Projekt|Nachricht|
---------------------------
letzte 5 Nachrichten
Text:
___________________________
___________________________
____________________________

[Senden]

[mehr...]
?>
*/
