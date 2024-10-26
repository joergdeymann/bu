<?php
// include 'dbconnect.php';
include 'class/class_pdf.php';
include 'class/class_rechnung.php';
include 'class/class_io.php';

// var_dump($_GET);


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
	'itel'      => "",
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
	'logo' 	 => "",  // Dateiname HTTP-Link oder absolut/relativer name
	'anrede' => "",  // Anrede in der Rechnung
	'retext'	 	=> "",	 // Rechnungstext
	'ueberschrift' 	=> "",	 // "Rechnung" oder "M A H N U N G";
	
	'css'    => ""	 // Ort und Name des CSS-Files aus der Layout HTML
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

if ($rechnung->row_re['renr']) {
	$re['renr']     =$rechnung->row_re['renr'];
	$re['datum']    =$rechnung->getDate('datum');

	$re['kdnr']     =$rechnung->row_re['kdnr'];
	$re['faellig']  =$rechnung->getDate('faellig');
	
	$t=strtotime($rechnung->row_re['leistung']);
	$monate = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	$re['leistung']="";
	$re['leistung_text']="";
	
	$dt_von=new DateTime($rechnung->row_re['leistung']);
	$dt_bis=new DateTime($rechnung->row_re['leistungbis']);
	
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


/*
       Firmendaten
*/

foreach($rechnung->row_firma as $k => $v) {
	$abs[$k]=$rechnung->row_firma[$k];
}

/*
	Layout einstellungen
*/
foreach($rechnung->row_layout as $k => $v) {
	$layout[$k]=$v;
}

$layout['logo']=$io->getBase64Image($rechnung->getLogo()); 
$layout['logo_trans']=$io->getBase64Image($rechnung->getLogo("trans")); 

$layout['retext']=$rechnung->row_layout['retext'];
$layout['ueberschrift']=$rechnung->row_layout['ueberschrift'];
$layout['anrede']=$rechnung->row_layout['retext']; // Anrede !! 
$layout['css']=$rechnung->layout['css'];

/*
	Empfängerdaten
*/
/*
var_dump($empf);
var_dump($rechnung->row_kunde);
*/
foreach($empf as $k => $v) {
	$empf[$k]=$rechnung->row_kunde[$k];
}


/*
	Felder die nicht in der Datei sind:
	nettosumme
	nettogesamt
	bruttosumme
	bruttogesamt
	mwstsatz    = mwstsatz[$mwst]
	mwstsumme   = mwstsumme[$mwst]
	
	
	Umprogramieren
	Header
	<posten>
	zb:
	<tr>
		<td>$re['pos']</td>
		<td>$re['anz'] $re['einheit']</td>
		<td>$re['re_text']</td>
		<td>$re['netto']</td>
		<td>$re['nettosumme']</td>
	
		<td>$re
	</posten>
*/





	
/* 
		Output generieren
*/
$content=$rechnung->getHTML();
$content=$rechnung->PostenGetAll($re);

/*
	Layout Felder
*/
foreach($layout as $k => $v) {
		$xkey='$layout[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}

/*
	Absender infos
*/
if (empty($abs['rname'])) {
	if (!empty($abs['vorname']) and !empty($abs['nachname'])) {
		$abs['rname']=$abs['vorname']." ".$abs['nachname'];
	} else 
	if (!empty($abs['vorname'])) {
		$abs['rname']=$abs['vorname'];
	} else
	if (!empty($abs['nachname'])) {
		$abs['rname']=$abs['nachname'];
	} else {
		$abs['rname']="";
	}
}
if (empty($abs['imail'])) $abs['imail']="";
if (empty($abs['itel'])) $abs['itel']="";
if (empty($abs['rmail'])) $abs['rmail']=$abs['imail'];
if (empty($abs['rtel'])) $abs['rtel']=$abs['itel'];

foreach($abs as $k => $v) {
		$xkey='$abs[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}

/*
	empfaenger infos
*/
foreach($empf as $k => $v) {
		$xkey='$empf[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}

if (isset($empf['vorname']) && isset($empf['nachname']) && $empf['vorname'] && $empf['nachname'] ) {
	$re['name']=$empf['vorname']." ".$empf['nachname'];
} else {
	$re['name']="";
}

foreach($re as $k => $v) {
		$xkey="\$re['$k']";
		$content=str_replace($xkey,$v,$content);
}


// $rechnung->senden(2); //Versandart 2 = DRUCKEN

// echo $content;

$filename=$re['renr'];
if ($_POST['mahnstufe'] > 0) $filename.="-M".$_POST['mahnstufe'];
if ($_POST['typ'] == 1) {
	$filename=$re['renr']."-AN";
} 




$output=new PDF();
$output->setContent($content);
$output->print();
// $output->destruct();
	
?>
	
	
