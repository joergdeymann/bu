<?php
include "dbconnect.php";
include "class/class_mitarbeiter.php";
include "class/class_firma.php";
include "class/class_urlaub.php";

// include "class/class_project_tag.php";
include "session_zeiterfassung.php";

$m=new mitarbeiter($db);
$m->loadByRecnum($_SESSION['usernr']);

$f=new firma($db);
$f->load($m->row['firmanr']);
$_SESSION['firmanr']=$m->row['firmanr'];
$msg="";

$dt=new Datetime();

$u=new Urlaub($db);
$u->setMitarbeiternr($m->row['nr']);
$u->loadByUrlaubZeitraum($dt->format("Y-01-01 00:00:00"),$dt->format("Y-12-31 23:59:59"));




headers();
echo '<body><center>';
echo '<form action="zeit_urlaub.php" method="POST">';
echo '<h1>Urlaub '.$dt->format("Y").'</h1>';
if (!empty($msg)) {
	echo '<div id="animiert">'.$msg.'</div>';
}
$dt=new Datetime();
echo '<h2>Resturlaub</h2>';
echo '<b>'.$m->row['resturlaub'].' von '.$m->row['jahresurlaub'].' Tage </b><br><br>';
echo '<h2>Übersicht</h2>';  
//echo '<p>';
echo '<table id="zeiten">';
echo '<tr><th>Von</th><th>Bis</th><th>Tage</th><th>Genehmigt</th></tr>';
while($row=$u->next()) {
	if ($u->row['status'] == 0) {
		$status="Offen";
	} else 
	if ($u->row['status'] == 1) {
		$status="Ja";
	} else 
	if ($u->row['status'] == 2) {
		$status="Nein";
	} 
	

	echo '<tr>';
	echo '<td>'.(new DateTime($u->row['von']))->format("d.m.Y").'</td>';
	echo '<td>'.(new DateTime($u->row['bis']))->format("d.m.Y").'</td>';
	echo '<td>'.$m->getUrlaub($u->row['von'],$u->row['bis']).'</td>';
	echo '<td>'.$status.'</td>';	
	echo '</tr>';
}
echo '</table><br>';
// echo '</p>';

echo '<form action="zeit_urlaub.php" method="POST">';
echo '<h2>Wunschurlaub</h2>';  // wunsch mit einer großen Box
echo '<table>';
echo '<tr><th>von:</th><td><input name="von" value="'.$dt->format("Y-m-d").'" type="date" ></td></tr>';
echo '<tr><th>bis:</th><td><input name="bis" value="'.$dt->format("Y-m-d").'" type="date" ></td></tr>';
echo '</table>';
echo '<button id="button" name="btn_typ" type="submit" value="senden">Einreichen</button>';
echo '</form>';
echo '<div id="menu" style="border-top:red solid 4px;"><a id="button" href="zeiterfassung.php">Zeiten</a><a id="button" href="zeit_urlaub.php">Urlaub</a><a id="button" href="zeit_krank.php">Krank</a></div>';
echo "</center></body></html>";


function headers() {
	echo '<html lang="de"><head>
		<meta charset="utf-8">
		<meta name="description" 
			  content="tägliche Projekterfassung für die Vorbereitung der Rechnung">
		<meta name="keywords" content="Stempeluhr, stempeln, Zeiterfassung, Krankheitserfassung, Urlaubserfassung">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Projekt Tagesinfo</title>
		<link rel="stylesheet" href="zeiterfassung.css">
		</head>
	';
}

?>
