<?php
$recnum="";
$kdnr="";
$vorname="";
$nachname="";
$strasse="";	
$plz="";
$ort="";
$tel_privat=""; 
$tel_diesnt=""; 
$tel_mobil="";
$mail_privat="";
$mail_dienst="";

$info_text="";
	
function mask($s) { // Apostrophe werden somit nicht zum Fehler
	return str_replace("'","\'",$s);
}
	
	
if (isset($_POST['kdnr']) && $_POST['kdnr']) {
	$kdnr=$_POST['kdnr'];
}
if (isset($_POST['vorname']) && $_POST['vorname']) {
	$vorname=$_POST['vorname'];
}
if (isset($_POST['nachname']) && $_POST['nachname']) {
	$nachname=$_POST['nachname'];
}
if (isset($_POST['strasse']) && $_POST['strasse']) {
	$strasse=$_POST['strasse'];
}
if (isset($_POST['plz']) && $_POST['plz']) {
	$plz=$_POST['plz'];
}
if (isset($_POST['ort']) && $_POST['ort']) {
	$ort=$_POST['ort'];
}
if (isset($_POST['tel_privat']) && $_POST['tel_privat']) {
	$tel_privat=$_POST['tel_privat'];
}
if (isset($_POST['tel_dienst']) && $_POST['tel_dienst']) {
	$tel_dienst=$_POST['tel_dienst'];
}
if (isset($_POST['tel_mobil']) && $_POST['tel_mobil']) {
	$tel_mobil=$_POST['tel_mobil'];
}
if (isset($_POST['mail_privat']) && $_POST['mail_privat']) {
	$mail_privat=$_POST['mail_privat'];
}
if (isset($_POST['mail_dienst']) && $_POST['mail_dienst']) {
	$mail_dienst=$_POST['mail_dienst'];
}
if (isset($_POST['recnum']) && $_POST['recnum']) {
	$recnum=$_POST['recnum'];
}

if (isset($_POST['save'])) {
	if ($recnum>0) {
		//UPDAATE
		$row=mask($_POST);
		array_shift($row);		
		
		$set="";
		foreach ($row as $k=>$v) {
			if ($set) {
				$set.=",";
			}
			$set.="`".$k."`='".$v."'";
		}
		$request="update `BU_kunde` set $set where recnum=$recnum";
		echo $request;
		
	}	else {
		// INSERT
		$row=mask($_POST);
		array_shift($row);		
		
		$set="";
		$keys=array();
		foreach ($row as $k=>$v) {
			$keys[]=$k;
		}
		$request="insert into `BU_kunde` (`".join("`,`",$keys)."`) values ('".join("','",$row)."')";
		echo $request;
	}
	
	
}

if (isset($_POST['find'])) {

}


?>	
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
<link rel="stylesheet" href="standart.css">
</head>
<body><center>
<h1>Buchhaltung</h1>
<h2>Kunden anlegen/ändern</h2>

<?php
// 	include "menu.php";
?>
<form action="kunde_set.php" method="POST";>
<input type="hidden" name="recnum" value="<?php echo $recnum ?>">
<h1>Kundendatenbank: Kunden eingeben/ändern</h1>
<table>
<tr><th>Kundennummer</th><td>      <input type="text" name="kdnr"        value="<?php echo $kdnr ?>"></td></tr>
<tr><th>Vorname</th><td>           <input type="text" name="vorname"     value="<?php echo $vorname ?>"></td></tr>
<tr><th>Nachname</th><td>          <input type="text" name="nachname"    value="<?php echo $nachname ?>"></td></tr>
<tr><th>Straße</th><td>            <input type="text" name="strasse"     value="<?php echo $strasse ?>"></td></tr>
<tr><th>PLZ</th><td>               <input type="text" name="plz"         value="<?php echo $plz ?>"></td></tr>
<tr><th>Ort</th><td>               <input type="text" name="ort"         value="<?php echo $ort ?>"></td></tr>
<tr><th>Telefon privat</th><td>    <input type="text" name="tel_privat"  value="<?php echo $tel_privat ?>"></td></tr>
<tr><th>Telefon dienstlich</th><td><input type="text" name="tel_dienst"  value="<?php echo $tel_diesnt ?>"></td></tr>
<tr><th>Telefon mobil</th><td>     <input type="text" name="tel_mobil"   value="<?php echo $tel_mobil ?>"></td></tr>
<tr><th>E-Mail privat</th><td>     <input type="text" name="mail_privat" value="<?php echo $mail_privat ?>"></td></tr>
<tr><th>E-Mail dienstlich</th><td> <input type="text" name="mail_dienst" value="<?php echo $mail_dienst ?>"></td></tr>
<tr><td colspan=2>
<!-- if recnum= 0 then "anlegen" if recnum >0 then "ändern" "neu anlegen" -->

<input type = "submit" name="save" value = "Sichern">

<input type = "submit" name="find" value = "Suchen"></td></tr>
</table>

</form>
</center></body>
