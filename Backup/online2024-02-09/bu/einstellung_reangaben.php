<?php
// ini_set('max_execution_time', '600');
include "session.php";
include "dbconnect.php";
include "menu.php";
$_POST['firmanr']=$_SESSION['firmanr'];
$msg="";

$row=array();

if (isset($_POST['leistung_button'])) {
	if (isset($_POST['leistung']) or isset($_POST['rechnungslayout'])) {
		if (isset($_POST['rechnungslayout'])) {
			$r=1;
		} else {
			$r=0;
		}
		$request='update bu_firma set re_input_leistung='.$_POST['leistung'].',re_input_individuell='.$r;
		$request.=" where recnum=".$_SESSION['firmanr'];
		$result = $db->query($request);	
		$row['re_input_leistung']=$_POST['leistung'];
		$row['re_input_individuell']=$r;
	}
} else {
	$request='select re_input_leistung, re_input_individuell from bu_firma where recnum='.$_SESSION['firmanr'];
	$result = $db->query($request);	
	$row = $result->fetch_assoc();		
}	


$c[0]="";
$c[1]="";
$c[2]="";
$c[3]="";
$i=$row['re_input_leistung'];
$c[$i] = "checked";

$c2="";
if ($row['re_input_individuell'] == 1) {
	$c2="checked";
}
showHeader("Rechnungsangaben festlegen");

?>
<center>
<form action="einstellung_reangaben.php" method="POST">
<h3 style="background-color:#999999;color:black;padding:10px;display:inline-block;border:1px solid white;">Angaben bei der Eingabe der Rechnungsdaten</h3>
<table>
<tr><th>Abfrage, wann die Leistung stattgefunden hat</th></tr>
<tr><td>
<?php

echo '<label><input '.$c[0].' type="radio" name="leistung" value=0>Keine Abfrage</label><br>';
echo '<label><input '.$c[1].' type="radio" name="leistung" value=1>Leistungsmonat (Monat und Jahr)</label><br>';
echo '<label><input '.$c[2].' type="radio" name="leistung" value=2>Leistungswoche (Woche und Jahr)</label><br>';
echo '<label><input '.$c[3].' type="radio" name="leistung" value=3>Leistungszeitraum (Datum bis Datum)</label><br>';

echo '</td></tr>';

echo '<tr><th>Individuelles Rechnungslayout</th></tr>';
echo '<tr><td>';
echo '<label><input type="checkbox" name="rechnungslayout" '.$c2.'>Individuelles Rechnungslayout ermöglichen</label><br>';
echo '</td></tr>';
echo '<tr><th><input type="submit" name="leistung_button" value="Auswahl bestätigen"></th></tr>';
echo '</table>';
echo '</form>';
echo '</center>';

showBottom();

?>





	

