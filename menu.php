<?php
// include "session.php";
include "dbconnect.php";
include "class/class_texte.php";
// echo basename($_SERVER['SCRIPT_NAME']);
$texte=new Texte($db);
// $texte->insert();
$texte->add(array(
	'Buchhaltung' 			 => 'DD-Office',
	'menu_Home'              => 'Home'      ,
	'menu_Firma'             => 'Firma'      ,
	'menu_Mitarbeiter'       => 'Mitarbeiter',
	'menu_Kunden'            => 'Kunden'     ,
	'menu_Artikel'           => 'Artikel'    ,
	'menu_Rechnungen'        => 'Rechnungen' ,
	'menu_Projekte'          => 'Projekte'   ,
	'menu_Berichte'          => 'Berichte'   ,
	'menu_Statistiken'	     => 'Statistiken',
	'menu_Adressen'          => 'Adressen'   ,
	'menu_Einstellungen'     => 'Einstellungen',
	'Home'             	    => 'Home'      ,
	'Firma'             	=> 'Firma'      ,
	'Mitarbeiter'       	=> 'Mitarbeiter',
	'Kunden'            	=> 'Kunden'     ,
	'Artikel'          	 	=> 'Artikel'    ,
	'Rechnungen'        	=> 'Rechnungen' ,
	'Projekte'          	=> 'Projekte'   ,
	'Berichte'          	=> 'Berichte'   ,
	'Statistiken'	     	=> 'Statistiken',
	'Adressen'          => 'Adressen'   ,
	'Einstellungen'     => 'Einstellungen',
	'firma_edit'    	=> 'Firma anlegen/änderen',    
	'firma_liste'   	=> 'Firmenliste',              
	'benutzer_edit' 	=> 'Benutzer anlegen/änderen', 
	'benutzer_liste' 	=> 'Benutzerliste',            
	'ma_edit'           => 'Mitarbeiter anlegen/änderen',
	'ma_liste'          => 'Mitarbeiterliste',           
	'kunde_edit'        => 'Kunden anlegen/änderen',     
	'kunde_liste'       => 'Kundenliste',                
	'artikel_edit'      => 'Artikel anlegen/änderen',    
	'artikel_liste'     => 'Artikelliste',
	'angebot_edit'		=> 'Angebote anlegen/änderen',
	'angebot_liste'		=> 'Liste aller Angebote',
	're_edit'			=> 'Rechnung anlegen/änderen',
	're_liste'			=> 'Liste aller Rechnungen',
	'user_edit'			=> 'Benutzerverwaltung',
	're_layout'			=> 'Rechnungslayout',
	're_input'			=> 'Rechnungseingaben',
	'projekt_edit'		=> 'Projekt anlegen/änderen',
	'projekt_liste'		=> 'Projektliste',
	'projekt_tag'		=> 'Tagesangaben',
	'adresse_edit'		=> 'Adresse anlegen/änderen',
	'adresse_liste'		=> 'Adressliste',
	'User:'				=> 'User:',
	'Firma:'			=> 'Firma:',
	'Firmanr:'			=> 'Firmanr:',
	'Bearbeiten'        => 'Bearbeiten'

));



















function showHeader($info,$cache=0) {
	global $texte;
	
	$cache=0;
	if ($cache) {
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Datum in der Vergangenheit
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
	
		
	echo '<!doctype html>
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
	// echo $info;
	// $texte->add(array('header' => 'Adressen'));
	// showHeader($texte->translate('header'));
	
	$texte->add(array('menu_header' => $info));
	// showHeader($texte->translate('Adressen'));	
	echo '
	<!-- link rel="stylesheet" href="index.css" -->
	<link rel="stylesheet" href="menu.css">
	<link rel="stylesheet" href="standart.css">

	</head>
	<body>

	<div id="wrapper">
	<header>
	<h1>'.$texte->translate('Buchhaltung').'</h1>
	<h2>'.$texte->translate($info).'</h2>
	</header>
	<div id="mehrspaltig">
	<nav>';
	function display($link,$text) {
		global $texte;
		$t=$texte->translate($text);
		$html="";
		if (!empty($t=$texte->translate($text))) { 
			$html='<a href="'.$link.'">'.$t.'</a><br>';
		}
		return $html;
	}
	$htmlA="";
	$htmlA.=display("../index.php"		,"menu_Home");
	$htmlA.=display("menu_firma.php"		,"menu_Firma");
	$htmlA.=display("menu_mitarbeiter.php"	,'menu_Mitarbeiter');
	$htmlA.=display("menu_kunde.php"  		,'menu_Kunden');
	$htmlA.=display("menu_artikel.php" 		,'menu_Artikel');
	$htmlA.=display("menu_rechnung.php"		,'menu_Rechnungen');
	$htmlA.=display("menu_projekte.php"		,'menu_Projekte');
	$htmlA.=display("menu_adressen.php"		,'menu_Adressen');
	if (!empty($htmlA)) {
		echo $htmlA;
		echo "<hr>";
	}
	$htmlA="";
	$htmlA.=display("menu_berichte.php"		,'menu_Berichte');
	$htmlA.=display("menu_statistiken.php"	,'menu_Statistiken');
	if (!empty($htmlA)) {
		echo $htmlA;
		echo "<hr>";
	}
	$htmlA="";
	$htmlA.=display("menu_einstellung.php"		,'menu_Einstellungen');
	if (!empty($htmlA)) {
		echo $htmlA;
	}
	
	
/*	
	echo
	'<a href="menu_firma.php">'.      $texte->translate('menu_Firma').'</a><br>
	<a href="menu_mitarbeiter.php">'.$texte->translate('menu_Mitarbeiter').'</a><br>
	<a href="menu_kunde.php">'.      $texte->translate('menu_Kunden').'</a><br>
	<a href="menu_artikel.php">'.    $texte->translate('menu_Artikel').'</a><br>
	<a href="menu_rechnung.php">'.	 $texte->translate('menu_Rechnungen').'</a><br>
	<a href="menu_projekte.php">'.	 $texte->translate('menu_Projekte').'</a><br>
	<a href="menu_adressen.php">'.	 $texte->translate('menu_Adressen').'</a><br>
	<hr>
	<a href="menu_berichte.php">'.	 $texte->translate('menu_Berichte').'</a><br>
	<a href="menu_statistiken.php">'.$texte->translate('menu_Statistiken').'</a><br>
	<hr>
	<a href="menu_einstellung.php">'.$texte->translate('menu_Einstellungen').'</a><br>
*/
	echo '
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
	"rechnung_kopie.php",
	"rechnung_extra.php",
	"angebot.php",
	"angebot_liste.php",
	"angebot_suchen.php",
	"angebot_aktion.php",
	"angebot_anlegen.php",
	"angebot_kopie.php",
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
	"projekt.php",
	"projekt_liste.php",
	"projekte_tag.php",
	"projekt_tag_bearbeiten.php",
	"kunde_auswahl.php",
	"mitarbeiter_auswahl.php",
	"menu_projekte.php",
	"menu.php");

	$menuliste10=array(
	"adresse.php",
	"adresse_liste.php",
	"menu_adressen.php",
	"menu.php"
	);
	
	// echo "*".basename($_SERVER['SCRIPT_NAME'])."*";
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste1) !== false) {
		echo '	
		<a href="firma.php">'.$texte->translate('firma_edit').'</a>
		<a href="firma_liste.php">'.$texte->translate('firma_liste').'</a>
		<a href="user_eingabe.php">'.$texte->translate('benutzer_edit').'</a>
		<a href="user_liste.php">'.$texte->translate('benutzer_liste').'</a>
		';
		// Hier der normale Text
	} else
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste8) !== false) {
		echo '	
		<a href="mitarbeiter.php"      >'.$texte->translate('ma_edit').'</a>
		<a href="mitarbeiter_liste.php">'.$texte->translate('ma_liste').'</a>
		';
	} else
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste2) !== false) {
		echo '	
		<a href="kunde.php"      >'.$texte->translate('kunde_edit').'</a>
		<a href="kunde_liste.php">'.$texte->translate('kunde_liste').'</a>
		';	
	}

	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste3) !== false) {
		echo '	
		<a href="artikel.php"      >'.$texte->translate('artikel_edit').'</a>
		<a href="artikel_liste.php">'.$texte->translate('artikel_liste').'</a>
		';	
	}

	
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste4) !== false) {
		echo '	
		<a href="angebot.php"         >'.$texte->translate('angebot_edit').'</a>
		<a href="angebot_liste.php"   >'.$texte->translate('angebot_liste').'</a>
		<a href="rechnung.php"        >'.$texte->translate('re_edit').'</a>
		<a href="rechnung_liste.php"  >'.$texte->translate('re_liste').'</a>
		';	
	}
	
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste7) !== false) {
		echo '	
		<a href="einstellung_user.php"     >'.$texte->translate('user_edit').'</a>
		<a href="einstellung_layout.php"   >'.$texte->translate('re_layout').'</a>
		<a href="einstellung_reangaben.php">'.$texte->translate('re_input').'</a>
		';
	
	} else
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste9) !== false) {
		echo '	
		<a href="projekt.php"      >'.$texte->translate('projekt_edit').'</a>
		<a href="projekt_liste.php">'.$texte->translate('projekt_liste').'</a>
		<a href="projekte_tag.php" >'.$texte->translate('projekt_tag').'</a>
		';
	
	} else
	if (array_search(basename($_SERVER['SCRIPT_NAME']) ,$menuliste10) !== false) {
		echo '	
		<a href="adresse.php"      >'.$texte->translate('adresse_edit').'</a>
		<a href="adresse_liste.php">'.$texte->translate('adresse_liste').'</a>
		';
	}
	echo '</div>';

	echo '<a href="logout.php"><div id="username">';
	if (isset($_SESSION['username'])) {
		echo $texte->translate('User:').$_SESSION['username'].'<br>';
	}
	if (isset($_SESSION['firmaname'])) {
		echo $texte->translate('Firma:').$_SESSION['firmanr'].':'.$_SESSION['firmaname'].'<br>';
	} else 
	if (isset($_SESSION['firmanr'])) {
		echo $texte->translate('Firmanr:').$_SESSION['firmanr'];
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
