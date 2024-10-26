<?php

// include 'dbconnect.php';
include 'session.php';
include 'class/class_pdf.php';
include 'class/class_rechnung.php';
// include 'class/class_io.php';

$msg=""; //Meldungen zum, Benutzer 

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

$content=$rechnung->getContent();
$filename=$rechnung->getFilename();


if (headers_sent()) {
	echo "Irgendwo wurde schon was geschrieben. HEADER already sent!";
	exit;
}	
//  echo "rechnung_pdf:exit";exit;

$pdf=new PDF();
$pdf->setContent($content);
$pdf->download($filename);
// echo "PDF";
?>
	
	
