<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
// $texte->add(array('header' => 'Rechnungen'));
showHeader('Rechnungen');


echo "<center>";
// Hier muss eine neue Rechnung oder Mahnung raus
// Ich muss das unbedingt hier abkürzen, Zuviele Zeilen

$now=(new DateTime())->format("Y-m-d");

$firmanr=$_SESSION['firmanr'];
$request="SELECT *,bu_re.datum as re_datum,bu_re.faellig as bu_faellig,bu_mahn.datum as mahn_datum,bu_mahn.faellig as mahn_faellig 
FROM `bu_re` 
left join bu_kunden on bu_kunden.auftraggeber=bu_re.firmanr
left join bu_mahn on bu_mahn.firmanr=bu_re.firmanr and bu_mahn.mahnstufe=bu_re.mahnstufe and bu_mahn.renr = bu_re.renr
WHERE bu_re.firmanr='$firmanr' 
and bu_re.bezahlt is null 
and '$now' > bu_mahn.faellig 
and bu_re.versanddatum is not null 
and bu_re.typ = 0 
GROUP by bu_re.renr 
ORDER BY bu_re.firmanr,bu_re.datum;";

// echo $request;
$result = $db->query($request) or die(mysql_fehler()); 
if (mysqli_num_rows($result) > 0) {
	echo '<div id="red"><h1>Es muss was getan werden !</h1></div>';
}
$texte->add(array('todo' => 'Der Kunde $kunde hat die Rechnung $renr nicht bezahlt!'));
$layout=$texte->translate('todo');
while($row = $result->fetch_assoc()) {
	$name=$row['vorname']." ".$row['nachname'];
	if (!empty($row['firma'])) $name=$row['firma'];
	echo '<form action="rechnung_aktion.php" method="POST">';
	$o=str_replace('$kunde',trim($name),$layout);
	$o=str_replace('$renr',$row['renr'],$o);

	echo $o;
	echo '<input id="red" type=submit value="'.$texte->translate('Bearbeiten').'">';
	echo '<input type=hidden name="renr" value="'.$row['renr'].'">';
	echo '</form>';
	echo '<br>';
}
	
	

// falls datensatz leer muss nichts getan werden

echo "</center>";
showBottom();
?>
