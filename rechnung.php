<?php
// ===========================================================================================
// Vorbereitungen
// ===========================================================================================
// ini_set('max_execution_time', '600');
include_once "session.php";
include_once "dbconnect.php";
include_once "menu.php";
include_once "class/class_firma.php";
include_once "class/class_DB_Posten.php";
include_once "class/class_datum.php";
include_once "class/class_rechnung_extra.php";
// print_r($_POST);

$texte->add(array (
	'header' 				=> "Rechnung erstellen/ändern",
	'btn_addArtikel' 		=> "Artikel hinzufügen",
));
// showHeader($texte->translate('header'));


// include "class_rechnung.php";
/*
		$string ="abc 12345DE";
		preg_match("/(.*?)[^0-9]+$/",$string,$result);
		print_r( $result);exit;
*/

// ===========================================================================================
// lokale Unterprogramme
// ===========================================================================================
/* 
	Automatisch das Fälligkeitdatum setzen:

	TODO: 1: ist das Zahlungszil Global oder für jede Firma gesondert
	TODO: 2. Wurde der Kunde manuell geändert ? dann Zahlunsziel anpassen und anzeigen ?
	TODO: 3. Option: Eingabe des Zahlungsziel ermöglichen
	
	Voraussetzung:
	+ 'faellig' ist egal, da man ja normal über auswählen geht und dann erst die 2. Eingaben macht, die man ändern könnte
	
	+ 'kdnr' Kundennummer ist vorhanden
	+ 'firma.faellig_status' = Kunde !!!noch machen: 
	   status 1: "Kunde"  = bu_kunde.zahlungsziel
	          0: "Global" = bu_firma.zahlungsziel
	+ Abfrage ermöglichen oder nicht auch als Option anbieten
	
	
*/	

function getReSumHTML($k,$v="") {
	if ($v == "") {
		$v=$k;
		$k="";
	}
	$html = '<span style="width:6em; text-align:right;display:inline-block;">'.sprintf("%.2f €",$v).'</span>';
	if ($k) {
		$html.= ' ('.sprintf("%2d",$k).'% MwSt)<br>';
	}
	return $html;
}

function setFaellig() {
	// Falls Rechnungsnummer da ist nicht neu berechnen, wurde dann schon geladen  ! Falscher Ansatz
	// if (!empty($_POST['renr'])) {
	// 	return; 
	// }

	// Nur überprüfen wenn fällig noch nihct gesetzt wurde
	if (!empty($_POST['faellig']) and $_POST['faellig'] != "0000-00-00") {
		return; 
	}
	
	global $db;
	
	$msg="";
	// if (empty($_POST['faellig'])) {
		if (!empty($_POST['kdnr'])) {
			$request="select zahlungsziel from bu_kunden where auftraggeber='".$_SESSION['firmanr']."' and kdnr='".$_POST['kdnr']."'";
			$result = $db->query($request) or die(mysql_fehler()); 
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$z=new DateTime($_POST['datum'].'+'.$row['zahlungsziel'].' days');
				$_POST['faellig']=$z->format("Y-m-d");

			} else {
				$msg="Kunde mit der Kundennnummer ".$_POST['kdnr']." nicht vorhanden";
			}
		}
	// }		
	return $msg;
}


/*
	HTML: Eingabe Leistungswoche
*/
function showLeistungsWoche() {	
	/* Anpassen in kunde_suchen.php, wenn fertig */
	/* 
		Wie bei den anderen:
		falls isset = falsch generieren aus Datum der eingabe
	*/  
	$dt = new DateTime($_POST['datum']);
	$woche=$dt->format("W");
	if (isset($_POST['woche'])) {
		$woche=(int)$_POST['woche'];
	}
	$html =  '<tr><th>Leistungswoche</th><td>';
	$html.=  '<input size=5 type="number"  name="woche" value="'.$woche.'">';
	return $html;
}



/*
	HTML: Eingabe Leistungsmonat
*/
function showLeistungsMonat() {
	$monate = array(
		"Januar",
		"Februar",
		"März",
		"Apri",
		"Mai",
		"Juni",
		"Juli",
		"August",
		"September",
		"Oktober",
		"November",
		"Dezember"
	);

	$dt    = new DateTime($_POST['datum']);
	$month = (int)$dt->format("m");
	
	if (isset($_POST['leistungsmonat'])) {
		$month=(int)$_POST['leistungsmonat'];
	}

	$html= '<tr><th>Leistungsmonat</th><td>';
	$html.='<select name="leistungsmonat">';

	$i=0;
	foreach($monate as $v) {		
		$i++;
		// echo $month.":".$i."-".$v."<br>";
		$opt="";
		if ($i == $month) {
			$opt="selected";
		} else {
			$opt="";
		}
		$html.='<option '.$opt.' value="'.$i.'">'.$v.'</option>';
		
	}
	$html.= '</select>';
	return $html;
}
		
/*
	HTML: Eingabe Leistungsjahr
*/
function showLeistungsJahr() {
	
	$html = '<label><select name="leistungsjahr">';
	$dt = new DateTime($_POST['datum']);
	$jahr=$dt->format("Y");
	if (isset($_POST['leistungsjahr'])) {
		$jahr=(int)$_POST['leistungsjahr'];
	}
	$html.= "<option value=".($jahr-2).">".($jahr-2)."</option>";
	$html.= "<option value=".($jahr-1).">".($jahr-1)."</option>";
	$html.= "<option value=".($jahr)  ." selected>".($jahr)  ."</option>";
	$html.= "<option value=".($jahr+1).">".($jahr+1)."</option>";	
	$html.= "<option value=".($jahr+2).">".($jahr+2)."</option>";	
	$html.= "</select></label></td></tr>";
	return $html;
	
}

/*
	HTML: Eingabe Datum von - bis
*/	
function showLeistungsZeitraum() {
	$von="";
	$bis="";

	if (isset($_POST["leistung"])) {
		$von=$_POST["leistung"];
	} else {
		$dt=new DateTime($_POST['datum'].'-1 month');
		$von= $dt->format("Y-m-d");
	}
	
	if (isset($_POST["leistungbis"])) {
		$bis=$_POST["leistungbis"];
	} else {
		$dt=new DateTime($_POST['datum'].'-1 day');
		$bis= $dt->format("Y-m-d");
	}
		
	
	$html = '<tr><th>Leistungszeitraum</th>';
	$html.= '<td><nobr><span style="width:2em;display: inline-block;">von:</span><input type="date"  name="leistung" value="'.$von.'"></nobr>';
	$html.= '<br><nobr><span style="width:2em;display: inline-block;">bis:</span><input type="date"  name="leistungbis" value="'.$bis.'"></nobr>';
	$html.= '</td></tr>';
	return $html;
}	
/*
	HTML: Eingabe Datum 
*/	
function showLeistungsDatum() {
	$von="";
	$bis="";

	if (isset($_POST["leistung"])) {
		$von=$_POST["leistung"];
	} else {
		$dt=new DateTime($_POST['datum'].'-1 month');
		$von= $dt->format("Y-m-d");
	}
			
	
	$html = '<tr><th>Leistungsdatum</th>';
	$html.= '<td><nobr><span style="width:2em;display: inline-block;">von:</span><input type="date"  name="leistung" value="'.$von.'"></nobr>';
	$html.= '</td></tr>';
	return $html;
}	


// ===========================================================================================
// PDF einstellungen
// ===========================================================================================
function pdf() {
	/* 
		function printPDF()
		Aufruf: Button 
		die Namen kontrollieren: 
			NAME: renr kdnr
			ID: PRINTED PDF
	*/

	
	if (empty($_POST['renr'])) {
		return;
	}


	echo "<script>";


	echo '
	function setPDF(pdf) {
		var ifr=document.getElementById("PDF");
		ifr.style.display="initial";  //"none"; // initial;
		ifr.style.border="0px";
		ifr.contentWindow.location.replace(pdf);
	}
	
	function printPDF() {
	 	document.getElementById("PDF").contentWindow.print();
		
		var renr=document.getElementsByName("renr")[0].value;
		var kdnr=document.getElementsByName("kdnr")[0].value;
		var file="rechnung_printed.php?renr="+renr+"&kdnr="+kdnr;		
		var printed=document.getElementById("PRINTED");		
		/* alert(file); */
		printed.contentWindow.location.replace(file);
	}
	
	
	function closePDF() {
	 	document.getElementById("PDF").contentWindow.close();	
	}	
	';
	echo "</script>";
	
	
	
}

// ===========================================================================================
// $_POST / Eingabefelder aus der Datenbak: bu_re vorladen
// ===========================================================================================
function prepareFromDB(&$row) {
	global 	$dbfirma;
	if (!empty($row['datum']) and empty($_POST['datum'])) {
		$_POST['datum'] = $row['datum'];
	}

	if($row['leistung'] != "0000-00-00") {			
		switch($dbfirma->get("re_input_leistung"))  {
			case 1:
				if (!empty($row['leistung'])) {
					$dt=new DateTime($row['leistung']);
					if (empty($_POST['leistungsmonat'])) 	$_POST['leistungsmonat']=$dt->format("m");
					if (empty($_POST['leistungsjahr']))  	$_POST['leistungsjahr']=$dt->format("Y");
				}
				break;
				
			case 2:
				if (!empty($row['leistung'])) {
					$dt=new DateTime($row['leistung']);
					if (empty($_POST['woche'])) 			$_POST['woche']=$dt->format("W");
					if (empty($_POST['leistungsjahr']))  	$_POST['leistungsjahr']=$dt->format("Y");
				}
				break;
				
			case 3:
				if (!empty($row['leistung']) and empty($_POST['leistung'])) {
					$_POST['leistung']=$row['leistung'];
				}
				if (!empty($row['leistungbis']) and empty($_POST['leistungbis'])) {
					$_POST['leistungbis']=$row['leistungbis'];
				}
				break;

			case 4:
				if (!empty($row['leistung']) and empty($_POST['leistung'])) {
					$_POST['leistung']=$row['leistung'];
				}
				break;
				
		}
	}
	if (!empty($row['kdnr']) and empty($_POST['kdnr'])) {
		// echo "POST KDNR überschrieben";
		$_POST['kdnr'] = $row['kdnr'];
	}

	if (!empty($row['faellig']) and empty($_POST['faellig'])) {
		$_POST['faellig'] = $row['faellig'];
	}

	if (!empty($row['layout']) and empty($_POST['layout'])) {
		$_POST['layout'] = $row['layout'];
	}
}


// ===========================================================================================
// Firmendaten (login) vorladen
// ===========================================================================================
$dbfirma = new firma();
$dbfirma->db = &$db;
$dbfirma->load();


// ===========================================================================================
// Variablen
// ===========================================================================================
$msg="";
if (!empty($_POST['err'])) {
	$msg=$_POST['err'];
}

/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/
// ===========================================================================================
// Javascript Vorbereitung
// ===========================================================================================
pdf();

// ===========================================================================================
// POST:CHANGE oder SAVE: Rechnungsdaten vorbereiten
// ===========================================================================================
$save=false;
if (isset($_POST['change']) || isset($_POST['save'])) {
	$save=true;
	if (empty($_POST['faellig'])) {
		$save=false;
		$msg="Bitte gib das Fälligkeitsdatum an.";
	}
	if (empty($_POST['kdnr'])) {
		$save=false;
		$msg="Bitte gib die Kundennummer an.";
	}
}

if ($save) {	
	$d=new Datum();
	
	// 1. Felder die gespeichert werden können
	// 2. Eingabefelder 
	///$selected_input_fields=array();
	
	$db_re_output_fields=array();

	$db_re_output_fields['datum']=$_POST['datum'];
	$db_re_output_fields['kdnr']=$_POST['kdnr'];
	$db_re_output_fields['faellig']=$_POST['faellig'];

	switch($dbfirma->get("re_input_leistung"))  {
		case 1:
			$db_re_output_fields['leistung'] = $d->setMonat($_POST['leistungsmonat'],$_POST['leistungsjahr']);
			break;
			
		case 2:
			$db_re_output_fields['leistung'] = $d->setWoche($_POST['woche'],$_POST['leistungsjahr']);
			break;
			
		case 3:
			$db_re_output_fields['leistung']   = $_POST['leistung'];
			$db_re_output_fields['leistungbis']= $_POST['leistungbis'];
			break;

		case 4:
			$db_re_output_fields['leistung']   = $_POST['leistung'];
			break;
			
			
	}
	
	if ($dbfirma->get("re_input_layout"))  {
		if ($_POST['layout']=="") {
			$_POST['layout']="0";
		}
		
		$db_re_output_fields['layout'] = $_POST['layout'];
	}
}

// ===========================================================================================
// POST:CHANGE: Rechnungsdaten ändern
// ===========================================================================================
if (isset($_POST['change']) && $save) {
	$r="";	
	foreach ($db_re_output_fields as $k => $v) {
		if ($r) {
			$r.=",";
		}
		$r.= "`".$k."`='".$v."'";
	}
		
	// UPDATE Mahnung
	$request="update `bu_mahn` set `datum`='".$_POST['datum']."',`faellig`='".$_POST['faellig']."' where firmanr='".$_SESSION['firmanr']."' and renr='".$_POST['renr']."' and mahnstufe=0";
	// echo $request."<br>";
	$result = $db->query($request) or die(mysql_fehler()); 

	
	// UPDATE Rechnung
	$request="update `bu_re` set $r where firmanr='".$_SESSION['firmanr']."' and renr='".$_POST['renr']."'";
	// echo $request."<br>";exit;
	$result = $db->query($request) or die(mysql_fehler()); 
	if ($result) {
		$msg="Rechnungsdaten wurde geändert.<br>";
	}
}


// ===========================================================================================
// POST:SAVE/Weiter: Rechnungsdaten anlegen
// ===========================================================================================
if (isset($_POST['save']) && $save) {
	$f=array();
	$values=array();
		
	foreach ($db_re_output_fields as $k => $v) {
		$f[]=$k;
		$values[] = $v;
	}

	/*
		neue Rechnungsnummer vergeben
		Standart: YYYYXXXX
		
	*/
	$rex=new Rechnung_extra($db);
	$renr=$rex->getNextRenr(); // get next Invoice Number
/*	
	$request="select max(renr) as renr from `bu_re` where firmanr='".$_SESSION['firmanr']."'";
	$result = $db->query($request);
	$r = $result->fetch_assoc();
	if (isset($r['renr']) && strlen($r['renr']) == 8) {
		$d=date("Y"); // 
		$c=sprintf("%04d",(substr($r['renr'],4,5)+1));
		if (substr($r['renr'],0,4) < $d) {
			$c="0001";
		}
		$renr=$d.$c;	
	} else {
		$renr=date("Y")."0001";
	}
*/
	$f[]="renr";
	$values[]=$renr;
	$_POST["renr"]=$renr;
	$f[]="firmanr";
	$values[]=$_SESSION["firmanr"];

	// $values['renr']=$renr;
	// Doppelte verhindern würde man vorher testen ob es den Datensatz gibt
	
	
	// INSERT Mahnung
	$request="insert into `bu_mahn` (`firmanr`,`renr`,`mahnstufe`,`datum`,`faellig`) values ('".$_SESSION['firmanr']."','".$renr."','0','".$_POST['datum']."','".$_POST['faellig']."')";
	// echo $request."<br>";
	$result = $db->query($request) or die(mysql_fehler()); 

	// INSERT Rechnung
	// $f = Datei $fields
	// $values = aus $f die Feldinhalte von $_POST
	
	$request="insert into `bu_re` (`".implode("`,`",$f)."`) values ('".implode("','",$values)."')";
	// echo $request."<br>";
	$result = $db->query($request) or die(mysql_fehler()); 
	
	if ($result) {
		$msg="Rechnung wurde neu angelegt. Jetzt nur noch die Posten hinzufügen<br>";
	}
}


// ===========================================================================================
// POST Abfangen ausserhalb: Posten ändern
// ===========================================================================================
// ein zusätzliches Feld mit infos als text
// "feld1", "inhalt1","feld2", "inhalt2",
// Oder nur die Menge als Spezielles feld [anz] [anz:DATEI], was bedeutent wegiger aufwendig ist.
// solche Felder sind dann reine autmatische Felder aus der Datei.
// --
// [anz]
// abgleich mit allen geladenen Feldern 
// Aendern beim Erstellen
// bleibt so in der Liste


if (isset($_POST['posten_change'])) {
	$posten=new DB_Posten($db);
/*	
	echo "========================================================<br>";
	echo "<pre>";
	var_dump($_POST);
	echo "</pre>";
	echo "========================================================<br>";
	// $posten->update($fields);
*/	
	/*
		1. Eingabefelder eintragen
	*/
	$f=array();
	foreach($_POST as $k => $v) {
		if ((substr($k,0,4) == "SEL_") && ($k != "SEL_") && isset($v)) {
			$k=substr($k,4);	
			$suche="/"."\[.*?\:".$k."\:.*?\]"."/i";
			// echo "Suche:".$suche."<br>";
			// echo "K:".$k."<br>";
			
			/*
				Wenn es eine Standart-Eingabe gibt, 
				wird diese in der Datei:bu_re_posten oder bu_artikel gespeichert
				und von dort aus in der Rechnung geladen
				Hier muss das Eingabefeld genauso heissen wie in der Datei
				['anz'] bleibt somit hier unberührt
			*/				
			if (!isset($_POST[$k]) && !empty($v)) {
				$s="/^(....)-(..)-(..)$/i";
				$ersetze = "$3.$2.$1";
				$v= preg_replace($s,$ersetze,$v);
				
				$_POST['beschreibung']=preg_replace($suche,$v,$_POST['beschreibung']);
			}
			// echo $_POST['beschreibung']."<br>";
			// echo $x."<br>";
		}
	}
	
	$f['beschreibung'] = $_POST['beschreibung'];
	$f['anz']          = $_POST['anz'];
	$f['renr']         = $_POST['renr'];	
	$f['pos']          = $_POST['pos'];	
	$posten=new DB_Posten($db);
	$posten->update($f);
	
	
	// echo $_POST['beschreibung']."<br>";

	
}

if (isset($_POST['posten_delete'])) {
	$f['renr']         = $_POST['renr'];	
	$f['pos']          = $_POST['pos'];	
	$posten=new DB_Posten($db);
	$posten->delete($f);
}

// ===========================================================================================
// POST Abfangen ausserhalb: Rechnung laden
// ===========================================================================================

if (isset($_POST['renr']) && !empty($_POST['renr'])) {
	$request='select * from bu_re where renr="'.$_POST['renr'].'" and firmanr="'.$_SESSION['firmanr'].'" and typ=0';
	$result = $db->query($request) or die(mysql_fehler());
	/*
		Eingabefelder Hauptteil vorbereiten
	*/	
	if ($result->num_rows > 0) {
		$row=$result->fetch_assoc();
		prepareFromDB($row); // Rechnungskopf
	
		/*
			POSTEN: alle Posten anzeigen
			hierhin: fehlt noch
		*/
		
	} else {
		$msg="Rechnung nicht gefunden!";
	}
	
}
// ===========================================================================================
// POST Abfangen ausserhalb: Artikel erstellen laden
// ===========================================================================================
if (isset($_POST['return_artikelnr'])) {
	// echo "POST von ausserhalb";
	
	$f = array();
	$f['renr']      = $_POST['renr'];
	$f['firmanr']   = $_SESSION['firmanr'];
	$f['artikelnr'] = $_POST['artikelnr'];	
	
	$f['netto']     = $_POST['netto'];	
	$f['mwst']      = $_POST['mwst'];	
	$f['anz']       = 1;
	$posten=new DB_Posten($db);
	$posten->insert($f);
	
	
	// Weiss nihct ob das rein muss

/*
	if (empty($_POST['faellig'])) {
		$request='select * from bu_re where renr="'.$_POST['renr'].'" and firmanr="'.$_SESSION['firmanr'].'" and typ=0';
		$result = $db->query($request) or die(mysql_fehler());
		/*
			Eingabefelder Hauptteil vorbereiten
		* /	
		if ($result->num_rows > 0) {
			$row=$result->fetch_assoc();
			$_POST['faellig']=$row['faellig'];
			$f['faellig'] = $_POST['faellig'];
		}
		
	}
*/	
	// $_POST['betrag']=$f['netto'];
	// $_POST['anz']=$f['anz'];
	
}
if (isset($_POST['kdnr'])) {
	// echo "KDNR".$_POST['kdnr'];
	// $_POST['']
}

// ===========================================================================================
// POST abfangen (Eingaben) - Hauptbereich
// ===========================================================================================
if (empty($_POST['renr']) ) {
	$renr="";
	$re_text='<b id="rand">Neue Rechnung</b>';
} else {
	$renr=$_POST['renr'];
	$re_text='<b id="rand">'.$_POST['renr'].'</b>';
}

if (empty($_POST['datum']) ) {
	$dt = new DateTime();
	$_POST['datum']=$dt->format('Y-m-d');
}

if (empty($_POST['kdnr']) ) {
	$dt = new DateTime();
	$_POST['kdnr']="";
}

$msg.=setFaellig(); // Fälligkeit setzen wenn Daten vorhanden;

// ===========================================================================================
// Vorbereiten (Eingaben) - Hauptbereich
// ===========================================================================================

// $felderpanel = new rechnung();
$html_leistung="";
switch($dbfirma->get("re_input_leistung"))  {
	case 1:
		// $felderpanel->use("leistungsmonat");
		// $felderpanel->use("leistungsjahr");	
		$html_leistung = showLeistungsMonat().showLeistungsJahr();
		break;
		
	case 2:
		// $felderpanel->use("leistungswoche");
		// $felderpanel->use("leistungsjahr");
		$html_leistung = showLeistungsWoche().showLeistungsJahr();
		break;
		
	case 3:
		// $felderpanel->use("leistungsdatumvon");
		// $felderpanel->use("leistungsdatumbis");
		$html_leistung =  showLeistungsZeitraum();
		break;

	case 4:
		$html_leistung =  showLeistungsDatum();
		break;
		
}




// ===========================================================================================
// Anzeige der HTML
// ===========================================================================================

showHeader("Rechnung erstellen/ändern",1);
// pdf();
/*
echo '
<iframe id="PDF" style="position:absolute;right:-100px;bottom:-300px;display:none;width:420px;height:600px;transform:scale(0.5);origin:bottom left;"></iframe>
<!-- Dieser Frame ist für das Speichern über PHP -->
<iframe id="PRINTED"  style="display:none;" ></iframe>
';
*/
/*
	vorher top:250px;
*/
/*
echo '
<iframe id="PDF" style="position:absolute;right:-100px;top:0px;display:none;width:420px;height:600px;transform:scale(0.5);origin:bottom left;"></iframe>
<!-- Dieser Frame ist für das Speichern über PHP -->
<iframe id="PRINTED"  style="display:none;" ></iframe>
';
*/
echo '
<iframe id="PDF" style="position:absolute;right:-100px;top:0px;display:none;width:420px;height:600px;transform:scale(0.5);"></iframe>
<!-- Dieser Frame ist für das Speichern über PHP -->
<iframe id="PRINTED"  style="display:none;" ></iframe>
';

// showHeader("Rechnungsangaben festlegen");
echo '<center>';
echo '<h3>'.$msg.'</h3>';
echo '<form action="rechnung.php" method="POST">'; 
echo '<table>';
echo '<tr><th>Rechnungsnummer</th><td><input type="hidden" name="renr" size="15" value="'.$renr.'">';
echo $re_text; // Rechnungsnummer oder "Neue Rechnung"
echo '<input type="submit" name="find" value="Rechnungsliste" formaction="rechnung_suchen.php"></td></tr>';
echo '<tr><th>Rechnungsdatum</th><td><input type="date"  name="datum" value="'.$_POST['datum'].'"></td></tr>';

echo $html_leistung;

echo '<tr><th>Kundennummer</th><td><input type="text"  name="kdnr" size="15" value="'.$_POST['kdnr'].'">';
echo '<input type="submit" name="findkunde" value="Kundenliste" formmethod="POST" formaction="kunde_suchen.php">';
echo '</td></tr>';

if (!empty($_POST['faellig'])) {
	echo '<tr><th>Fälligkeitsdatum</th><td><input type="date"  name="faellig" value="'.$_POST['faellig'].'"></td></tr>';
}


/*
	Individuell:
	0 oder 1: Nein joder Ja
*/

if ($dbfirma->get("re_input_individuell")) {
	$layout="";
	if (!empty($_POST['layout'])) {
		$layout=$_POST['layout'];
	}
	echo '<tr><th>Rechnungsformular/Layout</th><td> <input type="text"  name="layout"         size="5"   value="'.$layout.'"></td></tr>';
}

echo '<tr><td colspan=2 style="text-align:right;">';

if (empty($_POST['renr']) ) {
	echo '<input type = "submit" name="save" value = "Weiter" style="font-size:1.5em;">';
} else {
	echo '<input type = "submit" name="change" value = "Ändern" style="font-size:1.5em;">';
}

echo '</td></tr>';
echo '</table>';
echo '</form>';
echo '<br>';


/*
	Posten
*/
	$summe_netto=0;
	$summe_brutto=0;

if (!empty($_POST['renr'])) {
	/*
		Eingabefelder fuer die Behandlung der Texteingaben ausschließen
		
		kann man ja später in einer Config festlegen
	*/
	$input_fields = array();
	$input_fields['anz']=true;

	$menu_fields['anz']	 = "Menge";
	$menu_fields['einheit_mehrzahl'] = "Menge";
	$menu_fields['re_text']			 = "Text";
	$menu_fields['beschreibung']	 = "Text";
	$menu_fields['netto']			 = "Betrag";
	$menu_fields['aktion']			 = "Aktion";
	
	
	

	echo '<table border=0 style="min-width:80%">';
	echo '<tr><th width="1">Menge</th><th width="70%">Text</th><th width="1">Einzelpreis</th><th width="1">Gesamtpreis</th><th width="1">Aktion</th></tr>';

	// $f = array("anz","rechnungstext","rechnungsbeschreibung","
	$posten=new DB_Posten($db);
	$posten->startExtend();

	// $summe_netto=0;
	// $summe_brutto=0;

	while ($p=$posten->getNext()) {
		$p['gesamt_netto']   	 = sprintf("%.2f",(float)$p['netto']        * (int)$p['anz']);
		$p['gesamt_mwst_betrag'] = sprintf("%.2f",(float)$p['gesamt_netto'] * (float)$p['mwst'] / 100);
		$p['gesamt_brutto']      = $p['gesamt_netto'] + $p['gesamt_mwst_betrag'];
		
		$mwst_satz  = $p['mwst'];
		$summe_netto += $p['gesamt_netto'];
		$summe_brutto += $p['gesamt_brutto'];

		if (empty($summe_mwst[$mwst_satz])) {
			$summe_mwst[$mwst_satz]=0;
		}		
		$summe_mwst[$mwst_satz] += (float)$p['gesamt_mwst_betrag'];

/*
		if (empty($summe_netto[$mwst_satz])) {
			$summe_netto[$mwst_satz]=0;
		}		
		$summe_netto[$mwst_satz] += (float)$p['gesamt_netto'];
*/


		if (empty($p['beschreibung'])) {
			$p['beschreibung']=$p['re_beschreibung'];
		}
			

		echo '<form action="rechnung.php" method="POST">';
		echo '<input type="hidden" name="pos" value="'.$p['pos'].'">';
		echo '<input type="hidden" name="renr"   value="'.$_POST['renr'].'">';
		
		echo '<tr>';
		echo '<td><input size="5" type="number" name="anz" value="'.$p['anz'].'"><br>';
		// echo $p['einheit_mehrzahl'];
		if ($p['anz'] == 1) {
			echo $p['einheit_einzahl'];
		} else {
			echo $p['einheit_mehrzahl'];
		}
		echo '</td>';


		$html="";
		if (!empty($p['beschreibung'])) {
			$s="/\[(.*?):(.*?):(.*?)\]/i";
			// $sub="[a-zA-Z 0-9äöüßÄÖÜ-]*?";
			// $sub="[^:]*?";
			$sub="[^\]:]*?";
			$s="/\[(".$sub."):(".$sub."):(".$sub.")\]/i";			
			preg_match_all($s,$p['beschreibung'],$matches);	

			// echo "<pre>";
			// var_dump ($matches[0]);
			// echo "</pre>";		
			$i=0;
			foreach ($matches[0] as $s) {
				/*
					Dateifelder automatisch ersetzen
				*/
				$k=$matches[2][$i];
				// echo "K=$k<br>";


				// Eingabefelder nicht in Text abspeichern, da sie aus der Datei geholt werden
				if (!isset($input_fields[$k])) {
					if (!empty($p[$k])) {					
						// needle, replace, Haystack
						$p['beschreibung']=str_ireplace($matches[0][$i],$p[$k],$p['beschreibung']);
					} else {
						/*
							Andere Felder eingeben
						*/
						$size="";
						// echo $matches[3][$i]."=".$matches[2][$i]."<br>";
						if ($matches[3][$i] == "NUMBER") {				
							$size='size="10"';
						}
						if ($matches[3][$i] == "TEXT") {				
							$size='size="60"';
						}
						if ($matches[3][$i] == "DATUM") {				
							$matches[3][$i] = "DATE";
						}
						$html .= '<span style="min-width:10em;font-weight:1000;display:inline-block;">'.$matches[1][$i].':</span><input '.$size.' type="'.$matches[3][$i].'" name="SEL_'.$matches[2][$i].'"><br>';
					}
				}
				$i++;
			}
		}
		echo '<td style="min-width:50%;font-weight:1000;">'.$p['re_text'].'<br><textarea style="width:100%;height:10em;" name="beschreibung">'.$p['beschreibung'].'</textarea>';
		
		echo $html;
		
		echo '</td>';

		echo '<td style="text-align:right;white-space:nowrap;" >'.sprintf("%.2f €",$p['netto'])."</td>";
		echo '<td style="text-align:right;white-space:nowrap;" >'.sprintf("%.2f €",$p['gesamt_netto'])."</td>";
		echo '<td style="vertical-align:bottom;text-align:center;">';
		echo '<input type="submit" name="posten_change" value="anpassen"><br>';
		echo '<input type="submit" name="posten_delete" value="entfernen">';
		echo '</td>';	
		echo '</tr>';
		echo '</form>';
		echo '<tr><td colspan=5 style="height:2px;background-color:gray;margin:0;padding:0"></td></tr>';
	}
	echo '</table>';	
	
	
	
	
	
	/*
		MWST berechungen
	*/
	$htmlMWST   = "";
	$htmlBrutto = "";
	$htmlNetto  = "";
	if (empty($summe_mwst)) {
		$summe_mwst = array();
	}

	$htmlNetto  .= getReSumHTML($summe_netto);
	$htmlBrutto .= getReSumHTML($summe_brutto);

	foreach($summe_mwst as $k => $v) {	
		
		$htmlMWST  .= getReSumHTML($k,$v);
		// secho "html=$htmlMWST.<br>";
		// echo "k=$k v=$v.<br>";
	}
	if ($htmlMWST == "") $htmlMWST=getReSumHTML(0);
	// Hier MWST X% = summe X%
	//      MWST Y% = summe Y%
	
	// <th style="text-align:left;font-weight:300;color:lightgrey;">

	echo '<table style="min-width:80%">';
	echo '<tr><th>Nettosumme:</th>       <th style="text-align:left;font-style:normal;">'.$htmlNetto.'</th></tr>';	
	echo '<tr><th>MwSt:</th>             <th style="text-align:left;font-style:normal;">'.$htmlMWST.'</th></tr>';	
	echo '<tr><th>Rechnungsbetrag:</th>  <th style="text-align:left;font-style:normal;">'.$htmlBrutto.'</th></tr>';	
	echo '</table>';

	/*
		Artikel auswählen
	*/
	echo '<br>';
	echo '<form method="POST" action="artikel_auswahl.php">';
	echo '<input type="hidden" name="renr" value="'.$_POST['renr'].'">';
	// echo '<h2 id="info">Posten</h2><br>';
	echo '<input style="font-size:1.5em;" type="submit" name="findartikel" value="'.$texte->translate('btn_addArtikel').'">';
	echo '<br><br>';
	echo '</form>';		
	
	echo '<form method="POST" action="artikel_auswahl.php">';
	echo '<input type="hidden" name="renr" value="'.$_POST['renr'].'">';
/*	
	echo '<input style="font-size:1.5em;margin-left:1em;margin-right:1em;"  type="submit" name="drucken"     value="Rechnung drucken" >';
	echo '<input style="font-size:1.5em;margin-left:1em;margin-right:1em;"  type="submit" name="mail"        value="Rechnung versenden" >';
	echo '<input style="font-size:1.5em;margin-left:1em;margin-right:1em;"  type="submit" name="view"        value="Rechnung ansehen" >';
*/
	echo '<button type="submit" name="saveas" formaction="rechnung_pdf.php" formmethod="POST" formtarget="_self"><br>Speichern<br>&nbsp;</button>';

	// if ($_SERVER['SERVER_NAME'] == 'localhost') {
		echo '<button type="submit" name="mailto" formaction="rechnung_versenden.php" formmethod="POST" formtarget="_self">per<br>Mail<br>versenden</button>';
	// }
	echo '<button type="button" name="drucken" onClick="this.blur();printPDF(this.form)" formaction="rechnung.php" formmethod="POST">Drucken<br>für<br>Versand</button>';
	echo '<button type="submit" name="copy"   formaction="rechnung_kopie.php" formmethod="POST">Als neue<br>Kopie<br>bearbeiten</button>';

	echo '</form>';		
	echo '<br><br>';


}

echo '</center>'; // </body></html>';
showBottom();

/*
	Dieser Bereich muss am Ende ausgeführt werden bzw, nach der Erstellung des Tags PDF
*/

if (isset($_POST['renr'])) {
	$file="rechnung_print.php?renr=".$_POST['renr']."&mahnstufe=0&firmanr=".$_SESSION['firmanr'];	
	echo "<script>";
	echo "setPDF('$file');";
	echo "</script>";
}

?>



