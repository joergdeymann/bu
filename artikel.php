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

$texte->add(array (
	'btn_suchen' 			=> 'Suchen',
	'ok_add'     			=> 'Artikel $data wurde neu angelegt!',
	'ok_change'  			=> 'Artikel $data wurde geändert!',
	'err_change'  			=> 'Artikel $data konnte nicht bearbeitet werden!',
	'artikelnr'  			=> 'Artikelnummer',
	'artikel'   			=> 'Artikel',
	'artikelnr_ek'  		=> 'Artikelnummer Einkauf',
	'name'          		=> 'Bezeichnung',
	're_text' 				=> 'Rechnungstext',
	're_beschreibung'  		=> 'Beschreibung',
	'netto'	 				=> 'Verkauf Nettoeinzelpreis',
	'mwst' 					=> 'Verkauf MwSt Satz',
	'miet_netto' 			=> 'Miet Nettopreis',
	'miet_mwst' 			=> 'Miet MwSt',
	'einheit_einzahl' 		=> 'Einheit Einzahl',
	'einheit_mehrzahl' 		=> 'Einheit Mehrzahl',
	'einheit_anzahl' 		=> 'Anzahl pro Einheit',
	'lagerbestand' 			=> 'Lagerbestand',
	'ean' 					=> 'EAN',
	'sn' 					=> 'Seriennummer',
	'hersteller' 			=> 'Hersteller',
	'modell' 				=> 'Modell',
	'typ' 					=> 'Typ',
	'charge' 				=> 'Charge',
	'zuordnung' 			=> 'Crew',
	'gruppe' 				=> 'Gruppe',
	'zustand' 				=> 'Zustand',
	'nutzbar' 				=> 'Nutzbar',
	'aktiv' 				=> 'Aktiv',
	'shopnr' 				=> 'Shop-Nr',
	'header' 				=> "Artikel erfassen/ändern",
	'btn_submit' 			=> "übernehmen",
	'info' 					=> 'Info',
	'reserviert'			=> 'Reserviert',
	'einheit_anzahl_zusatz' => 'z.B. 6 im Karton'
	
	
));
	


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
	
	// Felder die nicht eingegeben wurden muss ich hier eleminieren
	foreach  ($f as $k => $v) {
		if (!isset($row[$v])) {
			unset($f[$k]);
		}
	}
	
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
		// $msgok="Artikel ".$row['artikelnr']." ".$row['name']." wurde geändert";
		$data=$row['artikelnr']." ".$row['name'];
		// $msgok=translate("artikel","Artikel")." ".$row['artikelnr']." ".$row['name']." wurde geändert";
		// $msgok=translate("ok_change",$msgok,$data)."<br>";
		$msgok=$texte->translate("ok_change",$data)."<br>";
	} else {		
		// INSERT
		$values=array();
		foreach ($f as $k) {
			
			$values[]=$row[$k];		
		}
		$request="insert into `bu_artikel` (`".join("`,`",$f)."`,`auftraggeber`) values ('".join("','",$values)."','".$_SESSION['firmanr']."')";
		// $msgok=translate("artikel","Artikel")." ".$row['artikelnr']." ".$row['name']." wurde neu angelegt";
		// $msgok=translate("ok_add",$msgok)."<br>";
		$data=$row['artikelnr']." ".$row['name'];
		$msgok=$texte->translate("ok_add",$data)."<br>";
	}
		
	// echo $request;
	
	$result = $db->query($request);
	if (!$result) {
		// $msgerr=translate("artikel","Artikel")." konnte nicht bearbeitet/geändert werden";		
		// $msgerr=translate("err_change",$msgerr)."<br>";	
		$msgerr=$texte->translate("err_change")."<br>";
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
	$request.=" order by `name` limit 1";
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}

if (isset($_POST['find_artikelnr'])) {
	$artikelnr=mask($_POST['artikelnr']);
	
	$request="select * from `bu_artikel` where `artikelnr` like '%".$artikelnr."%' and auftraggeber=".$_SESSION['firmanr'];
	$request.=" order by `artikelnr` limit 1";
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


if (!empty($_POST['recnum']) and empty($_POST['save'])) {
	$artikel->loadByRecnum($_POST['recnum']);
	// echo $_POST['recnum'];exit; 
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
			// $m=translate("artikel","Artikel")." wurde erfolgreich geändert!";
			// $m=translate("ok_change",$m);
			$m=$texte->translate("ok_change");
			$html.= "<h1>".$m."<h1>";			
		}
	}
}



$html.= '<form action="artikel.php" method="POST">';

$html.= $out->getHidden("recnum");
$html.= '<table>';

$key="btn_suchen";
$btn_suchen=$texte->translate($key);

$key='artikelnr';
$input = $out->getText($key,"20em");
$input.= $out->getSubmit("find_artikelnr",$btn_suchen);
$html.=$texte->output($input,$key);



// $html.= '<tr><th>Artikelnummer</th><td>';
// $html.= $out->getText("artikelnr","20em");
// $html.= $out->getSubmit("find_artikelnr","Suchen");
// $html.= '</td></tr>';

$key='artikelnr_ek';
$input=$out->getText($key,"20em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Artikelnummer Einkauf &nbsp;</th><td>';
// $html.= $out->getText("artikelnr_ek","20em");
// html.= '</td></tr>';

$key='name';
$input = $out->getText($key,"70em");
$input.= $out->getSubmit("find_name",$btn_suchen);
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Bezeichnung</th><td>';
// $html.= $out->getText("name","70em");
// $html.= $out->getSubmit("find_name","Suchen");
// $html.= '</td></tr>';

$text='Rechnungstext';
$key='re_text';
$input = $out->getText($key,"70em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Rechnugstext</th><td>';
// $html.= $out->getText("re_text","70em");
// $html.= '</td></tr>';

$text='Beschreibung';
$key='re_beschreibung';
$input = $out->getTextArea($key,"80em","10em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Beschreibung</th><td>';
// $html.= $out->getTextArea("re_beschreibung","80em","10em");
// $html.= '</td></tr>';


$text='Verkauf Nettoeinzelpreis';
$key='netto';
$input = $out->getEuro($key,"10em")."  €";
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Verkauf Nettopreis</th><td>';
// $html.= $out->getEuro("netto","10em");
// $html.= '</td></tr>';


$key='mwst';
$input = $out->getEuro($key,"10em")." %";
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Verkauf MwSt</th><td>';
// $html.= $out->getEuro("mwst","10em");
// $html.= '</td></tr>';

$text='Miet Nettopreis';
$key='miet_netto';
$input=$out->getEuro($key,"10em");
$html.=$texte->output($input,$key);

$text='Miet MwSt';
$key='miet_mwst';
$input = $out->getEuro($key,"10em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Miet MwSt</th><td>';
// $html.= $out->getEuro("miet_mwst","10em");
// $html.= '</td></tr>';

$text='Einheit Einzahl';
$key='einheit_einzahl';
$input = $out->getText($key,"10em")." <i>(z.B. Stunde oder Tag)</i>";
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Einheit Einzahl</th><td>';
// $html.= $out->getText("einheit_einzahl","50em");
// $html.= '</td></tr>';

$text='Einheit Mehrzahl';
$key='einheit_mehrzahl';
$input = $out->getText($key,"10em")." <i>(z.B. Stunden oder Tage)</i>";
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Einheit Mehrzahl</th><td>';
// $html.= $out->getText("einheit_mehrzahl","50em");
// $html.= '</td></tr>';

$text='Anzahl pro Einheit';
$key='einheit_anzahl';
$text2='z.B. 6 im Karton';
$key2='einheit_anzahl_zusatz';
$input = $out->getNumber($key,"10em")." ".$texte->translate($key2);
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Anzahl pro Einheit</th><td>';
// $html.= $out->getNumber("einheit_anzahl","10em").'z.B. 6 im Karton';
// $html.= '</td></tr>';

$text='Lagerbestand';
$key='lagerbestand';
$input=$out->getNumber($key,"10em");
$html.=$texte->output($input,$key);


$text='Reserviert';
$key='reserviert';
$input = $out->getNumber($key,"10em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Reserviert</th><td>';
// $html.= $out->getNumber("reserviert","10em");
// $html.= '</td></tr>';

$text='EAN';
$key='ean';
$input = $out->getText($key,"15em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>EAN</th><td>';
// $html.= $out->getText("ean","15em");
// $html.= '</td></tr>';


$text='Seriennummer';
$key='sn';
$input = $out->getText($key,"20em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Seriennummer</th><td>';
// $html.= $out->getText("sn","20em");
// $html.= '</td></tr>';

$text='Hersteller';
$key='hersteller';
$input = $out->getText($key,"70em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Hersteller</th><td>';
// $html.= $out->getText("hersteller","70em");
// $html.= '</td></tr>';

$text='Modell';
$key='modell';
$input = $out->getText($key,"70em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Modell</th><td>';
// $html.= $out->getText("modell","70em");
// $html.= '</td></tr>';

$text='Typ';
$key='typ';
$input = $out->getText($key,"40em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Typ</th><td>';
// $html.= $out->getText("typ","40em");
// $html.= '</td></tr>';

$text='Charge';
$key='charge';
$input = $out->getText($key,"20em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Charge</th><td>';
// $html.= $out->getText("charge","20em");
// $html.= '</td></tr>';

$s=array("Lichttechniker","Tontechniker","Rowdy");
$text='Crew';
$key='zuordnung';
$input = $out->getSelection($key,$s);
$html.=$texte->output($input,$key);

// $s=array("Lichttechniker","Tontechniker","Rowdy");
// $html.= '<tr><th>Crew</th><td>';
// $html.= $out->getSelection("zuordnung",$s);
// $html.= '</td></tr>';


$s=array("Verkauf","Vermietung","Bestand");
$text='Gruppe';
$key='gruppe';
$input = $out->getSelection($key,$s);
$html.=$texte->output($input,$key);

// $s=array("Verkauf","Vermietung","Bestand");
// $html.= '<tr><th>Gruppe</th><td>';
// $html.= $out->getSelection("gruppe",$s);
// $html.= '</td></tr>';

$s=array("neu","gebraucht","beschädigt","unbrauchbar");
$text='Zustand';
$key='zustand';
$input = $out->getSelection($key,$s);
$html.=$texte->output($input,$key);

// $s=array("neu","gebraucht","beschädigt","unbrauchbar");
// $html.= '<tr><th>Zustand</th><td>';
// $html.= $out->getSelection("zustand",$s);
// $html.= '</td></tr>';

$s=array($texte->translate("btn_ja"),$texte->translate("btn_nein"));
$text='Nutzbar';
$key='nutzbar';
$input = $out->getRadio($key,$s,1);
$html.=$texte->output($input,$key);

// $s=array("Ja","Nein");
// $html.= '<tr><th>Nutzbar</th><td>';
// $html.= $out->getRadio("nutzbar",$s,1);
// $html.= '</td></tr>';

$s=array($texte->translate("btn_ja"),$texte->translate("btn_nein"));
$text='Aktiv';
$key='aktiv';
$input = $out->getRadio($key,$s,1);
$html.=$texte->output($input,$key);

// $s=array("Ja","Nein");
// $html.= '<tr><th>Aktiv</th><td>';
// $html.= $out->getRadio("aktiv",$s,1);
// $html.= '</td></tr>';

$text='Shop-Nr';
$key='shopnr';
$input = $out->getText($key,"10em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Shop-Nr.</th><td>';
// $html.= $out->getText("shopnr","10em");
// $html.= '</td></tr>';

$text='Info';
$key='info';
$input = $out->getTextArea($key,"80em","10em");
$html.=$texte->output($input,$key);

// $html.= '<tr><th>Info</th><td>';
// $html.= $out->getTextArea("info","80em","10em");
// $html.= '</td></tr>';
if (empty($_POST['recnum'])) {
	$text="anlegen";
} else {
	$text="übernehmen";
}
$key="btn_submit";
$input=$texte->translate($text); //$key

$html.='<tr><td colspan=2 style="text-align:center">';
$html.='<input type = "submit" name="save" value = '.$input.'>';
$html.= '</td></tr>';

$html.= '</table>';
$html.='<br>';
$html.= '</form>';


$text="Artikel erfassen/ändern";
$key="header";
// echo $texte->row[$key].'<br>';
// echo $texte->row_pre[$key].'<br>';

$input=$texte->translate($key);
showHeader($input);
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