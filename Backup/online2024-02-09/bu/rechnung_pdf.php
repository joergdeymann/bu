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
	'inhaber'   => "",
	'bankname'  => "",
	'iban'      => "",
	'bic'       => "",
	'hrname'    => "",
	'hra'       => "",
	'ustid'     => "",
	'betriebsnr'=> ""
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
	$re['leistung']=$monate[date("n",$t)]." '".date("y",$t);
	
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

foreach($abs as $k => $v) {
	$abs[$k]=$rechnung->row_firma[$k];
}

/*
	Layout einstellungen
*/
$layout['logo']=$io->getBase64Image($rechnung->getLogo()); 
$layout['anrede']=$rechnung->row_layout['retext'];
$layout['css']=$rechnung->layout['css'];

/*
	Empfängerdaten
*/
foreach($empf as $k => $v) {
	$empf[$k]=$rechnung->row_kunde[$k];
}


/*
	Posten
*/

	
/* 
		Outpoiut generieren
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

/*
	Rechnungsinfoirmationen
*/
foreach($re as $k => $v) {
	$xkey="\$re['$k']";
	$content=str_replace($xkey,$v,$content);
}


// $rechnung->senden(2); //Versandart 2 = DRUCKEN

// echo $content;
$filename="R".$re['renr'];
// echo "<html><head><title>Hallo</title></head><body>XXX</body></html>";
// echo $content;

$output=new PDF();
$output->setContent($content);
$output->download($filename);
// echo "PDF";

// $output->destruct();
	
?>
	
	
