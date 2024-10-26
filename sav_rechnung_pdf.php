<?php
// include 'dbconnect.php';
include 'session.php';
include 'class/class_pdf.php';
include 'class/class_rechnung.php';
include 'class/class_io.php';

$msg=""; //Meldungen zum, Benutzer 
$re = array();

$abs = array(
	'firma'     => "",
	'strasse'   => "",
	'plz'       => "",
	'ort'       => "",
	'vorname'   => "",
	'nachname'  => "",
	'web'       => "",
	'inhaber'   => "",
	'bankname'  => "",
	'iban'      => "",
	'bic'       => "",
	'paypal_link' => "",
	'hrname'    => "",
	'hra'       => "",
	'ustid'     => "",
	'betriebsnr'=> "",
	'imail'     => "",
	'rmail'     => "",
	'amail'     => "",
	'itel'      => "",
	'atel'      => "",
	'rtel'      => ""
);	
$empf = array(
	'firma'     => "",
	'strasse'   => "",
	'plz'       => "",
	'ort'       => "",
	'vorname'   => "",
	'nachname'  => ""
);	
$layout = array(
	'logo' 	 		=> "",  // Dateiname HTTP-Link oder absolut/relativer name
	'anrede' 		=> "",  // Anrede in der Rechnung
	'css'    		=> "",	 // Ort und Name des CSS-Files aus der Layout HTML
	'retext'	 	=> "",	 // Rechnungstext
	'ueberschrift' 	=> "",	 // "Rechnung" oder "M A H N U N G";
	'schlusswort'   => ""
	
);

/*
	Einheiten für eine Postenzeile
*/
$einheiten_value=array(
 "Stunde",
 "Tour",
 "Frachtstück"
);

$einheiten_mz=array(
 "Stunden",
 "Touren",
 "Frachtstücke"
);

$zuschlag_value=array(
 "",
 "Nachtzuschlag",
 "Sonntagszuschlag"
);


$km_value=array(
  "",
 "pro KM",
 "Gesamt"
);


// $_GET['renr']="20220023";



//-----------------------------------------------------------------------------------
/* 
	Angebot Ja / Nein --> Typ=1 Angebot 
*/
if (isset($_GET['typ']) && $_GET['typ']) {
	$_POST['typ']=$_GET['typ'];
} else {
	if (empty($_POST['typ'])) {
		$_POST['typ']=0;
	}
}

/*
echo "Typ";
echo $_POST['typ'];
exit;
*/ 


/* 
	Rechnungsnummer uebergeben
*/
if (isset($_GET['renr']) && $_GET['renr']) {
	$_POST['renr']=$_GET['renr'];
}
if (isset($_POST['renr']) && $_POST['renr']) {
} else {
	$msg="Keine Rechnung angegeben";
	echo $msg;
	exit;
}

/* 
	Mahnstufe uebergeben
*/
if (isset($_GET['mahnstufe']) && $_GET['mahnstufe']) {
	$_POST['mahnstufe']=$_GET['mahnstufe'];
} 
if (isset($_POST['mahnstufe']) && $_POST['mahnstufe']) {
} else {
	$_POST['mahnstufe']="0";
}
/* 
	Firmennummer(sich selbst als Firma) uebergeben
*/
if (isset($_GET['firmanr']) && $_GET['firmanr']) {
	$_POST['firmanr']=$_GET['firmanr'];
} 
if (isset($_POST['firmanr']) && $_POST['firmanr']) {
} else {
	if ($_SESSION['firmanr']) {
		
		$_POST['firmanr']=$_SESSION['firmanr'];
	} else {
		$_POST['firmanr']="0";
	}
}

$rechnung=new Rechnung();
$rechnung->typ=$_POST['typ'];
$rechnung->setFirma($_POST['firmanr']);
$rechnung->setReNr($_POST['renr']);
$rechnung->setMahnstufe($_POST['mahnstufe']);

// in der Klasse
// 1. setReplaceContent()
// 2. $content=$rechnung->getHTML();             // HTML Vorlage laden
// 3. $content=$rechnung->PostenGetAll($re);     // Posten samt getauschter Inhalt hinzufügen
// 4. replaceContent()
// 5. get filename

//===========================================================================================================
// START: TEIL1: $content übersetzen: Namen der Felder mit Inhalten belegen
//===========================================================================================================

//------------------------------------------------------------------------------------------------------
//	Rechnungs Daten vorbereiten: RE
//------------------------------------------------------------------------------------------------------
foreach($rechnung->row_re as $k => $v) {
	$re[$k]=$rechnung->row_re[$k];
}

if ($rechnung->row_re['renr']) {
	$re['renr']     =$rechnung->row_re['renr'];
	$re['datum']    =$rechnung->getDate('datum');

	$re['kdnr']     =$rechnung->row_re['kdnr'];
	$re['faellig']  =$rechnung->getDate('faellig');
	
	$t=strtotime($rechnung->row_re['leistung']);
	$monate = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	$re['leistung']="";
	$re['leistung_text']="";
	if (!empty($rechnung->row_re['leistung'])) {		
		$dt_von=new DateTime($rechnung->row_re['leistung']);
	}
	if (!empty($rechnung->row_re['leistungbis'])) {		
		$dt_bis=new DateTime($rechnung->row_re['leistungbis']);
	}
	
	switch($rechnung->row_firma['re_input_leistung']) {
		case 0: 
				$re['leistung']="";
				$re['leistung_text']="";
				break;
		case 1: 
				$re['leistung']=$monate[date("n",$t)]." '".date("y",$t);
				$re['leistung_text']="vom". $monate[date("n",$t)]." '".date("y",$t);
				break;
		case 2: 
				$re['leistung']=date("W",$t)." ".date("Y",$t);
				$re['leistung_text']="der Woche ".date("W",$t)." ".date("Y",$t);
				break;
		case 3: 
				$re['leistung']=$dt_von->format("d.m.Y")." bis ".$dt_bis->format("d.m.Y");
				$re['leistung_text']="von ".$dt_von->format("d.m.Y")." bis ".$dt_bis->format("d.m.Y");
				$re['leistung_von']=$dt_von->format("d.m.Y");
				$re['leistung_bis']=$dt_bis->format("d.m.Y");
				
				break;
		case 4: 
				$re['leistung']=$dt_von->format("d.m.Y");
				$re['leistung_text']="vom ".$dt_von->format("d.m.Y");
				break;
	}
	
	$re['layout']=$rechnung->row_re['layout'];
	$re['firmanr']=$rechnung->row_re['firmanr'];
} else {
	$msg="Rechnung ".$_POST['renr']." nicht vorhanden!";
	echo $msg;
	exit;
}


//------------------------------------------------------------------------------------------------------
//	Firmendaten vorbereiten: ABS
//------------------------------------------------------------------------------------------------------
foreach($rechnung->row_firma as $k => $v) {
	$abs[$k]=$v;
}



if (empty($abs['iname'])) {
	if (!empty($abs['vorname']) and !empty($abs['nachname'])) {
		$abs['iname']=trim($abs['vorname']." ".$abs['nachname']);
	} else 
	if (!empty($abs['vorname'])) {
		$abs['iname']=$abs['vorname'];
	} else
	if (!empty($abs['nachname'])) {
		$abs['iname']=$abs['nachname'];
	} else {
		$abs['iname']="";
	}
}


if (empty($abs['rname'])) {
	if (!empty($abs['iname'])) {
		$abs['rname']=$abs['iname'];
	} else 
	if (!empty($abs['aname'])) {
		$abs['rname']=$abs['aname'];
	} else {
		$abs['rname']="";
	}
}
// Ansprechpartner versuchen zu ermitteln
if (empty($abs['aname'])) {
	if (!empty($abs['rname'])) {
		$abs['aname']=$abs['rname'];
	} else 
	if (!empty($abs['iname'])) {
		$abs['aname']=$abs['iname'];
	} else {
		$abs['aname']="";
	}
}

$abs['inhaber']=$abs['iname'];

// #### Mails kann man noch optimieren
if (empty($abs['imail'])) $abs['imail']="";
if (empty($abs['itel'])) $abs['itel']="";
if (empty($abs['rmail'])) $abs['rmail']=$abs['imail'];
if (empty($abs['rtel'])) $abs['rtel']=$abs['itel'];
if (empty($abs['rmail'])) $abs['rmail']=$abs['amail'];
if (empty($abs['rtel'])) $abs['rtel']=$abs['atel'];

//------------------------------------------------------------------------------------------------------
//	Layout einstellungen LAYOUT
//------------------------------------------------------------------------------------------------------
foreach($rechnung->row_layout as $k => $v) {
	$layout[$k]=$v;
}
$layout['logo']=$io->getBase64Image($rechnung->getLogo()); 
$layout['logo_trans']=$io->getBase64Image($rechnung->getLogo("trans")); 
// echo $layout['logo'];exit;
$layout['anrede']=$rechnung->row_layout['retext']; // #### Rechnungstext und anrede sollten unterschiedlich sein
$layout['css']=$rechnung->layout['css'];



//------------------------------------------------------------------------------------------------------
//	Empfängerdaten/Kunde einstellungen
//------------------------------------------------------------------------------------------------------
foreach($rechnung->row_kunde as $k => $v) {
	$empf[$k]=$v;
}

if (isset($empf['vorname']) && isset($empf['nachname']) && $empf['vorname'] && $empf['nachname'] ) {
	$re['name']=$empf['vorname']." ".$empf['nachname'];
} else {
	$re['name']="";
}
$empf['name']=$re['name'];

//------------------------------------------------------------------------------------------------------
// Skonto Kunde(empf) / Firma(abs) einstellungen
//------------------------------------------------------------------------------------------------------
if ($empf['skonto_prozent'] == -1) {
	$re['skonto_prozent']=$abs['skonto_prozent'];
	$re['skonto_tage']=$abs['skonto_tage'];	
} else {
	$re['skonto_prozent']=$empf['skonto_prozent'];
	$re['skonto_tage']=$empf['skonto_tage'];
}
if ($re['skonto_prozent']>0) {
	$dt=new DateTime($re['datum']."+".$re['skonto_tage']." days");
	$re['skonto_datum']=$dt->format("d.m.Y");
}
/*
wird direkt in $rechnung->getPosten() umgesetzt
if ($re['skonto_prozent']>0) {
	$re['skonto_betrag']=$re['summe_gesamt_brutto']*(100-$re['skonto_prozent'])/100;
	$re['skonto_text']=sprintf("%,2f",$re['skonto_betrag'])."€";
}
*/
	

//===========================================================================================================
// START: TEIL2: $content übersetzen: Output generieren
//===========================================================================================================

/*
	Posten
*/
$content=$rechnung->getHTML();             // HTML Vorlage laden
$content=$rechnung->PostenGetAll($re);     // Posten samt getauschter Inhalt hinzufügen


/*
	Layout Felder
*/
foreach($layout as $k => $v) {
	if (isset($v)) {
		$xkey='$layout[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
	}
}

/*
	Absender infos
*/
foreach($abs as $k => $v) {
	if (isset($v)) {
		$xkey='$abs[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
	}
}

/*
	empfaenger infos
*/
foreach($empf as $k => $v) {
	if (isset($v)) {
		$xkey='$empf[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
	}
}

/*
	Rechnungsinfoirmationen
*/
foreach($re as $k => $v) {
	if (isset($v)) {
		$xkey="\$re['$k']";
		$content=str_replace($xkey,$v,$content);
	}
}

//===========================================================================================================
// ENDE: $content übersetzen
//===========================================================================================================

// $rechnung->senden(2); //Versandart 2 = DRUCKEN

// echo $content;

// $filename="R".$re['renr'];
//===========================================================================================================
// DATEINAME holenb: $filename 
//===========================================================================================================
$filename=$rechnung->row_re['renr'];
if ($rechnung->getMahnstufe() > 0) $filename.="-M".$rechnung->getMahnstufe();
if ($rechnung->getTyp() == 1) {
	$filename=$rechnung->row_re['renr']."-AN";
} 

// $filename=$re['renr'];
// echo "RechnungPDF315";
// echo $content;
// exit;
// echo "<html><head><title>Hallo</title></head><body>XXX</body></html>";
// echo $content;
// echo "rechnung_pdf";exit;


// $content=$rechnung->getRelpacedContent();
// $filename=$rechnung->getFilename();

if (headers_sent()) {
	echo "Irgendwo wurde schon was geschrieben. HEADER already sent!";
	exit;
}	

$pdf=new PDF();
$pdf->setContent($content);
$pdf->download($filename);
// echo "PDF";

// $output->destruct();
	
?>
	
	
