<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_artikel.php";
include "class/class_output.php";


$msg="";
$html="";
$msgok="";
$msgerr="";
$out=new Output($db);
$artikel=new Artikel($db);

$fields=array(
	"recnum",
// 	"auftraggeber",
	"artikelnr",
	"artikelnr_ek",              /* hier nur Recnum und Auswahl Eigene Einkaufstabelle erstellen später, mehrere Shops möglich, zugriffe hier verknüpfen */
	"name",                      /* Anzeige Text, für den User, eigenname */
	"re_text",                   /* Text für die Rechnung,                Auch mit [] Felder zum einlesen */
	"re_beschreibung",           /* Artikelbeschreibung für die Rechnung, Auch mit []nFelder zum einlesen */
	"netto",                     /* Netto Verkaufspreis für Rechnung */
	"mwst",                      /* MWSTR Verkaufspreis für Rechnung */
	"einheit_einzahl",           /* Einheit km € Stück, Tag */
	"einheit_mehrzahl",          /* Einheit km € Stück, Tage */
	// "einheit_bezug",        
	/* Kann später vielleicht weg" */
	"einheit_anzahl",            /* Anzhal 
	// "einheit_umrechnung",   
	/* Kann später vielleicht weg */ 
	"lagerbestand",
	"reserviert",

    "hersteller",                // Marke:; Ford, Alan & Heath, Shure 
	"modell",                    // Modell: Focus, Rivage, X100 
	"typ",                       // Kleinwagen, Compact, usw 
	"charge",                    // Chargenummer 
	"ean",                       // EAN-13 Code international 
	"info",                      // Info für den User nicht bestimmt für die Rechnung 
	"aktiv",                     // Ja / Nein Aktiver Artikel oder nicht benutzt 
	"gruppe",                    // Arktikelverkauf, Artikelverleih, Rechnungswert -> bu_artikelgruppe Neu anlegen 1 = XX 2= YY 3=ZZ wird bei bedarf geladen
	"zuordnung",                  // Zuordnung Bereich:  Lichttechnik,Tontechnik, Rowdy, wie bei Gruppe
	"miet_netto",                // Mietpreis netto
	"miet_mwst",                 // Steuern für die Miete
	"shopnr",                    // Artikelnummer im externen shop
	"zustand",                   // 0/1 = neu, 2=gebraucht, 3=beschädigt, 4=unbrauchbar
    "nutzbar",                   // 0/1 = Ja 2=Nein
	"sn"                         // Seriennummer
	
	
	
);


//--------------------------------------------------------------------------------------------------------------------
function mask($s) { // Apostrophe werden somit nicht zum Fehler
	return str_replace("'","\'",$s);
}

$msgok="";
$msgerr="";

if (isset($_POST['save'])) {
	$row=mask($_POST); // gepostete Felder
	$set="";
	$f=$fields;        // Felder inklusive recnum

	$recnum=$_POST['recnum'];  // Hidden Recnum falls geladen

	array_shift($f);   // Recnum eleminieren
	
	if ($recnum>0) {
		//UPDAATE
		foreach ($f as $k) {
			if ($set) {
				$set.=",";
			}
			$v=$row[$k];
			$set.="`".$k."`='".$v."'";
		}		
		$request="update `bu_artikel` set $set where recnum=$recnum";
		$msgok="Artikel ".$row['artikelnr']." ".$row['name']." wurde geändert";
	} else {		
		// INSERT
		$values=array();
		foreach ($f as $k) {
			
			$values[]=$row[$k];		
		}
		$request="insert into `bu_artikel` (`".join("`,`",$f)."`,`auftraggeber`) values ('".join("','",$values)."','".$_SESSION['firmanr']."')";
		$msgok="Artikel ".$row['artikelnr']." ".$row['name']." wurde neu angelegt";
	}
		
	// echo $request;
	
	$result = $db->query($request);
	if (!$result) {
		$msgerr="Artikel konnte nicht bearbeitet/geändert werden<br>";
	}
	

	// für die nachbearbeitung
	if (empty($recnum)) {
		$recnum=$db->insert_id;
	}		
		
	
}

$found=false;


if (isset($_POST['find_name'])) {
	$name=mask($_POST['name']);
	
	$request="select * from `bu_artikel` where `name` like '%".$name."%' and auftraggeber=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}

if (isset($_POST['find_artikelnr'])) {
	$artikelnr=mask($_POST['artikelnr']);
	
	$request="select * from `bu_artikel` where `artikelnr` like '%".$artikelnr."%' and auftraggeber=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}

if ($found) {
	foreach($row as $k => $v) {
		$_POST[$k]=$v;
	}	
}


// ----------------------------------------------------------------------------------------------------------------------


if (isset($_POST['recnum']) and empty($_POST['save'])) {
	$artikel->loadByRecnum($_POST['recnum']);
	foreach($artikel->row as $k => $v) {
		$_POST[$k]=$v;
	}
}


$html.= '<center>';
if (isset($_POST['save'])) {
	if ($msgerr) {
		$html.= "<h1>$msgerr</h1>";
	} else { 
		if ($msgok) {
			$html.= "<h1>$msgok</h1>";
		} else {
			$html.= "<h1>Die Kundendaten wurden erfolgreich geändert!<h1>";
		}
	}
}


$html.= '<form action="artikel.php" method="POST";>';

$html.= $out->getHidden("recnum");
$html.= '<table>';
$html.= '<tr><th>Artikelnummer</th><td>';
$html.= $out->getText("artikelnr","20em");
$html.= $out->getSubmit("find_artikelnr","Suchen");
$html.= '</td></tr>';

$html.= '<tr><th>Artikelnummer Einkauf &nbsp;</th><td>';
$html.= $out->getText("artikelnr_ek","20em");
$html.= '</td></tr>';

$html.= '<tr><th>Bezeichnung</th><td>';
$html.= $out->getText("name","70em");
$html.= $out->getSubmit("find_name","Suchen");
$html.= '</td></tr>';

$html.= '<tr><th>Rechnugstext</th><td>';
$html.= $out->getText("re_text","70em");
$html.= '</td></tr>';

$html.= '<tr><th>Beschreibung</th><td>';
$html.= $out->getTextArea("re_beschreibung","80em","10em");
$html.= '</td></tr>';

$html.= '<tr><th>Verkauf Nettopreis</th><td>';
$html.= $out->getEuro("netto","10em");
$html.= '</td></tr>';

$html.= '<tr><th>Verkauf MwSt</th><td>';
$html.= $out->getEuro("mwst","10em");
$html.= '</td></tr>';

$html.= '<tr><th>Miet Nettopreis</th><td>';
$html.= $out->getEuro("miet_netto","10em");
$html.= '</td></tr>';

$html.= '<tr><th>Miet MwSt</th><td>';
$html.= $out->getEuro("miet_mwst","10em");
$html.= '</td></tr>';

$html.= '<tr><th>Einheit Einzahl</th><td>';
$html.= $out->getText("einheit_einzahl","50em");
$html.= '</td></tr>';

$html.= '<tr><th>Einheit Mehrzahl</th><td>';
$html.= $out->getText("einheit_mehrzahl","50em");
$html.= '</td></tr>';

$html.= '<tr><th>Anzahl pro Einheit</th><td>';
$html.= $out->getNumber("einheit_anzahl","10em").'z.B. 6 im Karton';
$html.= '</td></tr>';

$html.= '<tr><th>Lagerbestand</th><td>';
$html.= $out->getNumber("lagerbestand","10em");
$html.= '</td></tr>';

$html.= '<tr><th>Reserviert</th><td>';
$html.= $out->getNumber("reserviert","10em");
$html.= '</td></tr>';

$html.= '<tr><th>EAN</th><td>';
$html.= $out->getText("ean","15em");
$html.= '</td></tr>';

$html.= '<tr><th>Seriennummer</th><td>';
$html.= $out->getText("sn","20em");
$html.= '</td></tr>';

$html.= '<tr><th>Hersteller</th><td>';
$html.= $out->getText("hersteller","70em");
$html.= '</td></tr>';

$html.= '<tr><th>Modell</th><td>';
$html.= $out->getText("modell","70em");
$html.= '</td></tr>';

$html.= '<tr><th>Typ</th><td>';
$html.= $out->getText("typ","40em");
$html.= '</td></tr>';

$html.= '<tr><th>Charge</th><td>';
$html.= $out->getText("charge","20em");
$html.= '</td></tr>';

$s=array("Lichttechniker","Tontechniker","Rowdy");
$html.= '<tr><th>Charge</th><td>';
$html.= $out->getSelection("zuordnung",$s);
$html.= '</td></tr>';

$s=array("Verkauf","Vermietung","Bestand");
$html.= '<tr><th>Gruppe</th><td>';
$html.= $out->getSelection("gruppe",$s);
$html.= '</td></tr>';

$s=array("neu","gebraucht","beschädigt","unbrauchbar");
$html.= '<tr><th>Zustand</th><td>';
$html.= $out->getSelection("zustand",$s);
$html.= '</td></tr>';

$s=array("Ja","Nein");
$html.= '<tr><th>Nutzbar</th><td>';
$html.= $out->getRadio("nutzbar",$s,1);
$html.= '</td></tr>';

$s=array("Ja","Nein");
$html.= '<tr><th>Aktiv</th><td>';
$html.= $out->getRadio("aktiv",$s,1);
$html.= '</td></tr>';

$html.= '<tr><th>Shop-Nr.</th><td>';
$html.= $out->getText("shopnr","10em");
$html.= '</td></tr>';

$html.= '<tr><th>Info</th><td>';
$html.= $out->getTextArea("info","80em","10em");
$html.= '</td></tr>';

$html.='<tr><td colspan=2 style="text-align:center">';
$html.='<input type = "submit" name="save" value = "übernehmen">';
$html.= '</td></tr>';

$html.= '</form>';



showHeader("Artikel erfassen/ändern");
echo $html;
showBottom();

exit;

/*
$message="";
$message.= "<h1 style='background-color:green'>Kopf</h1>Hello ä ö ü ' `world!<br>";
$message.= 'Hidermit erhalten Sie folgende Rechnung<br>';
$message.= "Rechnunr Nr RE20220523<br>";
$message.= "Datum: 29.05.2023<br>";
$message.= "Ort: LaGa Bad Gandersheim<br>";
$message.= "Projekt: Arbeiten Lichtoperator<br>";
$message.="Zu zahlender Betrag: <b>172,45 €</b> oder 160,00 € mit Skonto";

$message.= "Vielen Dank fü+r die Zusammenarbeit!<br>";
*/

?>


