<?php
include "session.php";
include "dbconnect.php";
include "menu.php";


$fields=array(
	"recnum",
	"kdnr",
	"firma",
	"vorname",
	"nachname",
	"strasse",
	"plz",
	"ort",
	"tel_privat", 
	"tel_dienst", 
	"tel_mobil",
	"mail_privat",
	"mail_dienst"
);



	$recnum="";
	$kdnr="";
	$firma="";
	$vorname="";
	$nachname="";
	$strasse="";	
	$plz="";
	$ort="";
	$tel_privat=""; 
	$tel_dienst=""; 
	$tel_mobil="";
	$mail_privat="";
	$mail_dienst="";
	$info_text="";

/*
function clearFields() {
	$recnum="";
	$kdnr="";
	$firma="";
	$vorname="";
	$nachname="";
	$strasse="";	
	$plz="";
	$ort="";
	$tel_privat=""; 
	$tel_dienst=""; 
	$tel_mobil="";
	$mail_privat="";
	$mail_dienst="";

	$info_text="";
}
clearFields();
	*/	
function mask($s) { // Apostrophe werden somit nicht zum Fehler
	return str_replace("'","\'",$s);
}

/*
if (isset($_POST['kdnr']) ) {
	$kdnr=$_POST['kdnr'];
}
if (isset($_POST['firma'])) {
	$firma=$_POST['firma'];
}
if (isset($_POST['vorname'])) {
	$vorname=$_POST['vorname'];
}
if (isset($_POST['nachname']) ) {
	$nachname=$_POST['nachname'];
}
if (isset($_POST['strasse'])) {
	$strasse=$_POST['strasse'];
}
if (isset($_POST['plz'])) {
	$plz=$_POST['plz'];
}
if (isset($_POST['ort'])) {
	$ort=$_POST['ort'];
}
if (isset($_POST['tel_privat'])) {
	$tel_privat=$_POST['tel_privat'];
}
if (isset($_POST['tel_dienst'])) {
	$tel_dienst=$_POST['tel_dienst'];
}
if (isset($_POST['tel_mobil'])) {
	$tel_mobil=$_POST['tel_mobil'];
}
if (isset($_POST['mail_privat'])) {
	$mail_privat=$_POST['mail_privat'];
}
if (isset($_POST['mail_dienst'])) {
	$mail_dienst=$_POST['mail_dienst'];
}
if (isset($_POST['recnum']) ) {
	$recnum=$_POST['recnum'];
}
*/
$recnum=0;
if (isset($_POST['recnum'])) {
	$recnum=$_POST['recnum'];
}

$msgok="";
$msgerr="";

if (isset($_POST['save'])) {
	$row=mask($_POST); // gepostete Felder
	$set="";
	$f=$fields;        // Felder inklusive recnum
	array_shift($f);   // Recnum eleminieren
	
	
	if ($recnum>0) {
		//UPDATE
		foreach ($f as $k) {
			if ($set) {
				$set.=",";
			}
			$v=$row[$k];
			$set.="`".$k."`='".$v."'";
		}		
		$request="update `bu_kunden` set $set where recnum=$recnum";
		$msgok="Kunde ".$row['vorname']." ".$row['nachname']." wurde geändert";
	} else {		
		// INSERT
		$values=array();
		foreach ($f as $k) {
			$values[]=$row[$k];		
		}
		$request="insert into `bu_kunden` (`".join("`,`",$f)."`,`auftraggeber`) values ('".join("','",$values)."','".$_SESSION['firmanr']."')";
		$msgok="Kunde ".$row['vorname']." ".$row['nachname']." wurde neu angelegt";
	}
		
	// echo $request;
	
	$result = $db->query($request);
	if (!$result) {
		$msgerr="Kunde konnte nicht bearbeitet/geändert werden<br>";
	}
	

	// für die nachbearbeitung
	if (empty($recnum)) {
		$recnum=$db->insert_id;
	}		
		
	
}

$found=false;

if (isset($_POST['find_kdnr'])) {
	$kdnr=mask($_POST['kdnr']);
	
	$request="select * from `bu_kunden` where `kdnr` = '".$kdnr."' and auftraggeber=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}
if (isset($_POST['find_name'])) {
	$vorname=mask($_POST['vorname']);
	$nachname=mask($_POST['nachname']);
	
	$request="select * from `bu_kunden` where `vorname` like '%".$vorname."%' and `nachname` like '%".$nachname."%' and auftraggeber=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}

if ($found) {
	$recnum=     $row['recnum'];
	$kdnr=       $row['kdnr'];
	$firma=      $row['firma'];
	$vorname=    $row['vorname'];
	$nachname=   $row['nachname'];
	$strasse=    $row['strasse'];	
	$plz=        $row['plz'];
	$ort=        $row['ort'];
	$tel_privat= $row['tel_privat']; 
	$tel_dienst= $row['tel_dienst']; 
	$tel_mobil=  $row['tel_mobil'];
	$mail_privat=$row['mail_privat'];
	$mail_dienst=$row['mail_dienst'];
}
showHeader("Kunden");


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

	
<form action="kunde.php" method="POST";>
<input type="hidden" name="recnum" value="<?php echo $recnum ?>">
<table>
<tr><th>Kundennummer</th><td>      <input type="text" name="kdnr"        size="10" value="<?php echo $kdnr ?>"><input type="submit" name="find_kdnr" value="Suchen"></td></tr>
<tr><th>Firma</th><td>             <input type="text" name="firma"       size="50" value="<?php echo $firma ?>"></td></tr>
<tr><th>Vorname</th><td>           <input type="text" name="vorname"     size="50" value="<?php echo $vorname ?>"></td></tr>
<tr><th>Nachname</th><td>          <input type="text" name="nachname"    size="50" value="<?php echo $nachname ?>"><input type="submit" name="find_name" value="Name Suchen"></td></tr>
<tr><th>Straße</th><td>            <input type="text" name="strasse"     size="50" value="<?php echo $strasse ?>"></td></tr>
<tr><th>PLZ</th><td>               <input type="text" name="plz"         size="10" value="<?php echo $plz ?>"></td></tr>
<tr><th>Ort</th><td>               <input type="text" name="ort"         size="50" value="<?php echo $ort ?>"></td></tr>
<tr><th>Telefon privat</th><td>    <input type="text" name="tel_privat"  size="20" value="<?php echo $tel_privat ?>"></td></tr>
<tr><th>Telefon dienstlich</th><td><input type="text" name="tel_dienst"  size="20" value="<?php echo $tel_dienst ?>"></td></tr>
<tr><th>Telefon mobil</th><td>     <input type="text" name="tel_mobil"   size="20" value="<?php echo $tel_mobil ?>"></td></tr>
<tr><th>E-Mail privat</th><td>     <input type="text" name="mail_privat" size="50" value="<?php echo $mail_privat ?>"></td></tr>
<tr><th>E-Mail dienstlich</th><td> <input type="text" name="mail_dienst" size="50" value="<?php echo $mail_dienst ?>"></td></tr>
<tr><td colspan=2>
<!-- if recnum= 0 then "anlegen" if recnum >0 then "ändern" "neu anlegen" -->
<?php
  if ($recnum > 0) {
	  $value = "übernehmen";
  } else {
	  $value = "anlegen";
  }

  echo '<input type = "submit" name="save" value = "'.$value.'">';

?>


<!-- input type = "submit" name="find" value = "Suchen" --></td></tr>
</table>
<br>
<!-- input type="submit" name="zurueck" value="Menü" formaction="index.php" -->

</form>
</center>
<?php
showBottom();
?>

