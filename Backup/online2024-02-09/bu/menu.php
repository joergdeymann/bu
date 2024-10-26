<?php
// echo basename($_SERVER['SCRIPT_NAME']);
function showHeader($info,$cache=0) {
	$cache=0;
	if ($cache) {
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
	
		
	echo '
	<!doctype html>
	<html lang="de">

	<head>
	<meta charset="utf-8">
	';
	if ($cache) {
		// echo "Hallo";
		echo '
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />

	';}
	echo '
	<!-- link rel="stylesheet" href="index.css" -->
	<link rel="stylesheet" href="menu.css">
	<link rel="stylesheet" href="standart.css">

	</head>
	<body>

	<div id="wrapper">
	<header>
	<h1>Buchhaltung</h1>
	<h2>'.$info.'</h2>
	</header>
	<div id="mehrspaltig">
	<nav>

	<a href="menu_firma.php">Firma</a><br>
	<a href="menu_mitarbeiter.php">Mitarbeiter</a><br>
	<a href="menu_kunde.php">Kunden</a><br>
	<a href="menu_artikel.php">Artikel</a><br>
	<a href="menu_rechnung.php">Rechnungen</a><br>
	<a href="menu_projekte.php">Projekte</a><br>
	<a href="menu_berichte.php">Berichte</a><br>
	<a href="menu_statistiken.php">Statistiken</a><br>
	<hr>
	<a href="menu_einstellung.php">Einstellungen</a><br>
	</nav>
	<article><div id="menutop">';

	$menuliste1=array(
	"firma.php",
	"firma_liste.php",
	"user_liste.php",
	"user_eingabe.php",
	"menu_firma.php",
	"menu.php");

	$menuliste8=array(
	"mitarbeiter.php",
	"mitarbeiter_liste.php",
	"mitarbeiter_urlaub.php",
	"mitarbeiter_krank.php",
	"zeiten_liste.php",
	"menu_mitarbeiter.php",
	"menu.php");

	$menuliste2=array(
	"kunde.php",
	"kunde_liste.php",
	"menu_kunde.php",
	"menu.php");

	$menuliste3=array(
	"artikel.php",
	"artikel_liste.php",
	"menu_artikel.php",
	"menu.php");


	$menuliste4=array(
	"rechnung.php",
	"rechnung_liste.php",
	"rechnung_suchen.php",
	"rechnung_aktion.php",
	"kunde_suchen.php",
	"artikel_auswahl",
	"menu_rechnung.php",
	"menu.php");

	$menuliste7=array(
	"einstellung.php", // nicht benutzt
	"einstellung_user.php",
	"einstellung_layout.php",
	"einstellung_layout_liste.php",
	"einstellung_reangaben.php",
	"menu_einstellung.php",
	"menu.php");

	$menuliste9=array(
	"projekte.php",
	"projekte_liste.php",
	"projekte_tag.php",
	"projekt_tag_bearbeiten.php",
	"kunde_auswahl.php",
	"mitarbeiter_auswahl.php",
	"menu_projekte.php",
	"menu.php");

	// echo "*".basename($_SERVER['SCRIPT_NAME'])."*";
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste1) !== false) {
		echo '	
		<a href="firma.php">Firma anlegen/änderen</a>
		<a href="firma_liste.php">Firmenliste</a>
		<a href="user_eingabe.php">Benutzer anlegen/änderen</a>
		<a href="user_liste.php">Benutzerliste</a>
		';
		// Hier der normale Text
	} else
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste8) !== false) {
		echo '	
		<a href="mitarbeiter.php">Mitarbeiter anlegen/änderen</a>
		<a href="mitarbeiter_liste.php">Mitarbeiterliste</a>
		';
	} else
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste2) !== false) {
		echo '	
		<a href="kunde.php">Kunden anlegen/änderen</a>
		<a href="kunde_liste.php">Kundenliste</a>
		';	
	}

	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste3) !== false) {
		echo '	
		<a href="artikel.php">Artikel anlegen/änderen</a>
		<a href="artikel_liste.php">Artikelliste</a>
		';	
	}

	
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste4) !== false) {
		echo '	
		<a href="rechnung.php">Rechnung anlegen/änderen</a>
		<a href="rechnung_liste.php">Liste aller Rechnungen</a>
		';	
	}
	
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste7) !== false) {
		echo '	
		<a href="einstellung_user.php">Benutzerverwaltung</a>
		<a href="einstellung_layout.php">Rechnungslayout</a>
		<a href="einstellung_reangaben.php">Rechnungseingaben</a>
		';
	
	} else
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste9) !== false) {
		echo '	
		<a href="projekte.php">Projekt anlegen/änderen</a>
		<a href="projekte_liste.php">Projektliste</a>
		<a href="projekte_tag.php">Tagesangaben</a>
		';
	}
	echo '</div>';

	echo '<a href="logout.php"><div id="username">';
	if (isset($_SESSION['username'])) {
		echo 'User:'.$_SESSION['username'].'<br>';
	}
	if (isset($_SESSION['firmaname'])) {
		echo 'Firma:'.$_SESSION['firmanr'].':'.$_SESSION['firmaname'].'<br>';
	} else 
	if (isset($_SESSION['firmanr'])) {
		echo 'Firmanr:'.$_SESSION['firmanr'];
	}  
		
	echo '</div></a>';

}

function showBottom() {	

	echo '
	</article>

	<aside>
	<!-- Zusatz -->
	</aside>

	</div>
		<footer><!-- Fußzeile-->&nbsp;</footer>
	</div>	

	</body>
	</html>
	';
}
