<?php
include "session.php";
include "dbconnect.php";
include "menu.php";

showHeader("Rechnungen");
echo "<center>";
// Hier muss eine neue Rechnung oder Mahnung raus

$firmanr=$_SESSION['firmanr'];
$request="SELECT *,bu_re.datum as re_datum,bu_re.faellig as bu_faellig,bu_mahn.datum as mahn_datum,bu_mahn.faellig as mahn_faellig 
FROM `bu_re` 
left join bu_mahn on  bu_mahn.firmanr=bu_re.firmanr and bu_mahn.mahnstufe=bu_re.mahnstufe
left join bu_kunden on bu_kunden.auftraggeber=bu_re.firmanr
WHERE bu_re.firmanr='$firmanr' and bu_re.bezahlt is null and now() > bu_mahn.faellig";
// echo $request;
$result = $db->query($request) or die(mysql_fehler()); 
if (mysqli_num_rows($result) > 0) {
	echo '<div id="red"><h1>Es muss was getan werden !</h1></div>';
}
	
while($row = $result->fetch_assoc()) {
	$name=$row['vorname']." ".$row['nachname'];

	echo '<form action="rechnung_aktion.php" method="POST">';

	echo "Der Kunde ";
	if (trim($name)) {
		echo $name." ";
	}
	$firma=$row['firma'];
	if ($firma) {
		echo "der Firma $firma ";
	}
	echo "hat noch nicht bezahlt! ";
	echo '<input id="red" type=submit value="Bearbeiten">';
	echo '<input type=hidden name="renr" value="'.$row['renr'].'">';
	echo '</form>';
	echo '<br>';
}
	
	

// falls datensatz leer muss nichts getan werden

echo "</center>";
showBottom();
?>
