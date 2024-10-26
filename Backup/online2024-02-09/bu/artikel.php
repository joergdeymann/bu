<?php
include "session.php";
include "dbconnect.php";
include "menu.php";


$fields=array(
	"recnum",
// 	"auftraggeber",
	"artikelnr",
	"artikelnr_ek",
	"name",
	"re_text",          /* Auch mit [] Felder zum einlesen */
	"re_beschreibung",           /* Auch mit []nFelder zum einlesen */
	"netto",
	"mwst",
	"einheit_einzahl",
	"einheit_mehrzahl",
	// "einheit_bezug",        
	/* Kann später vielleicht weg" */
	"einheit_anzahl",
	// "einheit_umrechnung",   
	/* Kann später vielleicht weg */ 
	"lagerbestand",
	"reserviert"	
);


	$recnum="";
	// $auftraggeber="";
	$artikelnr="";
	$artikelnr_ek="";
	$name="";
	$re_text="";          		/* Auch mit [] Felder zum einlesen */
	$re_beschreibung="";        /* Auch mit []nFelder zum einlesen */
	$netto="";
	$mwst="";
	$einheit_einzahl="";
	$einheit_mehrzahl="";
	// $einheit_bezug="";        /* Kann später vielleicht weg" */
	$einheit_anzahl="";
	// $einheit_umrechnung="";   /* Kann später vielleicht weg */ 
	$lagerbestand="";
	$reserviert="";	


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

/*
	Aufruf auch von Artikel_liste
*/
if (isset($_POST['find_artikelnr'])) {
	$artikelnr=mask($_POST['artikelnr']);
	
	$request="select * from `bu_artikel` where `artikelnr` = '".$artikelnr."' and auftraggeber=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}

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
	$recnum=			$row['recnum'];
	// $auftraggeber=		$row['auftraggeber'];
	$artikelnr=			$row['artikelnr'];
	$artikelnr_ek=		$row['artikelnr_ek'];
	$name=				$row['name'];
	$re_text=			$row['re_text'];          
	$re_beschreibung=	$row['re_beschreibung'];  
	$netto=				$row['netto'];
	$mwst=				$row['mwst'];
	$einheit_einzahl=	$row['einheit_einzahl'];
	$einheit_mehrzahl=	$row['einheit_mehrzahl'];
	$einheit_anzahl=	$row['einheit_anzahl'];
	$lagerbestand=		$row['lagerbestand'];
	$reserviert=		$row['reserviert'];	
}
showHeader("Artikel erfassen/ändern");


?>	
<center>
<?php
	if (isset($_POST['save'])) {
		if ($msgerr) {
			echo "<h1>$msgerr</h1>";
		} else { 
			if ($msgok) {
				echo "<h1>$msgok</h1>";
			} else {
				echo "<h1>Die Kundendaten wurden erfolgreich geändert!<h1>";
			}
			// clearFields();		
		
			// echo "<a href=\"index.php\">Weiter zum Menu</a>";
			// exit;
		}
	}
	
	
	
?>

	
<form action="artikel.php" method="POST";>
<input type="hidden" name="recnum" value="<?php echo $recnum ?>">
<table>
<tr><th>Artikelnummer</th><td>           <input type="text" name="artikelnr"         size="20" value="<?php echo $artikelnr ?>"><input type="submit" name="find_artikelnr" value="Suchen"></td></tr>
<tr><th>Bezeichnung</th><td>             <input type="text" name="name"              size="50" value="<?php echo $name ?>"><input type="submit" name="find_name" value="Suchen"></td></tr>
<tr><th>Rechnugstext</th><td>            <input type="text" name="re_text"     size="50" value="<?php echo $re_text ?>"></td></tr>
<tr><th>Beschreibung</th><td><textarea rows="10" cols="80"  name="re_beschreibung"><?php echo $re_beschreibung ?></textarea></td></tr>
<tr><th>Nettopreis</th><td>              <input type="text" name="netto"             size="20" value="<?php echo $netto ?>"></td></tr>
<tr><th>MwSt</th><td>                    <input type="text" name="mwst"              size="10" value="<?php echo $mwst ?>"></td></tr>
<tr><th>Einheit Einzahl</th><td>         <input type="text" name="einheit_einzahl"   size="50" value="<?php echo $einheit_einzahl ?>"></td></tr>
<tr><th>Einheit Mehrzahl</th><td>        <input type="text" name="einheit_mehrzahl"  size="50" value="<?php echo $einheit_mehrzahl ?>"></td></tr>
<tr><th>Einheit Anzahl</th><td>          <input type="text" name="einheit_anzahl"    size="10" value="<?php echo $einheit_anzahl ?>"></td></tr>
<tr><th>Lagerbestand</th><td>            <input type="text" name="lagerbestand"      size="10" value="<?php echo $lagerbestand ?>"></td></tr>
<tr><th>Reserviert</th><td>              <input type="text" name="reserviert"        size="10" value="<?php echo $reserviert ?>"></td></tr>
<tr><th>Bezug auf Einkauf</th><td>       <input type="text" name="artikelnr_ek"      size="10" value="<?php echo $artikelnr_ek ?>"></td></tr>
<tr><td colspan=2>
<!-- if recnum= 0 then "anlegen" if recnum >0 then "ändern" "neu anlegen" -->

<input type = "submit" name="save" value = "übernehmen">

<!-- input type = "submit" name="find" value = "Suchen" --></td></tr>
</table>
<br>
<!-- input type="submit" name="zurueck" value="Menü" formaction="index.php" -->

</form>
</center>
<?php
showBottom();



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

$sig = "<h1 style='background-color:green'>Signatur</h1><br>";
$sig.= '<table><tr><td><a href="https://www.die-deymanns.de/VA/leistungen.html"><img src="img/logo.png" alt="Logo"></a></td><td>Jörg Deymann</td></tr></table>';
*/

?>


