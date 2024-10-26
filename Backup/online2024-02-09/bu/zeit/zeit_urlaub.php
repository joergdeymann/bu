<?php
include "../class/dbconnect.php";
include "../class/class_mitarbeiter.php";
include "../class/class_firma.php";
include "../class/class_urlaub.php";
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

	
if (empty($_POST['info'])) {
	$_POST['info']="";
}
if (empty($_POST['von'])) {
	$_POST['von']=$dt->format("Y-m-d");
}
if (empty($_POST['bis'])) {
	$_POST['bis']=$_POST['von'];
}

if (isset($_POST['btn_urlaub'])) {
	if ($_POST['von'] > $_POST['bis']) {
		$msg="Datum von ist grösser als bis!";
	} else {
		$row=array();
		$row['von']  = $_POST['von'];
		$row['bis']  = $_POST['bis'];
		$row['info'] = $_POST['info'];
		$row['mitarbeiternr'] = $m->row['nr'];
		$row['firmanr']       = $m->row['firmanr'];
		$row['art']    = 0;
		$row['status'] = 0;
		if (isset($_POST['recnum'])) {
			$row['recnum']=$_POST['recnum'];
		}

		if ($u->save($row)) {
			$msg="Erfolgreich angefragt";
		} else {
			$msg="Urlaubsantrag fehlgeschlagen";
		}		
	}
}
	
if (isset($_POST['recnum'])) {
	$msg="Urlaub vorgeladen";
}

headers();

echo '<body><center>';
echo '<h1>Urlaub '.$dt->format("Y").'</h1>';
if (!empty($msg)) {
	echo '<div id="animiert">'.$msg.'</div>';
}
echo '<h2>Resturlaub</h2>';
echo '<b>'.$m->row['resturlaub'].' von '.$m->row['jahresurlaub'].' Tage </b><br><br>';
echo '<h2>Übersicht</h2>';  
//echo '<p>';
echo '<table id="zeiten">';
echo '<tr><th>Von</th><th>Bis</th><th>Tage</th><th style="width:6em;"><div id="pop">Genehmigt</div></th><th>Aktion</th></tr>';

$u->loadByUrlaubZeitraum($dt->format("Y-01-01 00:00:00"),$dt->format("Y-12-31 23:59:59"));
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
	// echo $u->row['info'];
	// echo "<style>div#pop:hover::after {content: '\A ".$u->row['von']."'}</style>";
	

	echo '<tr>';
	echo '<td>'.(new DateTime($u->row['von']))->format("d.m.Y").'</td>';
	echo '<td>'.(new DateTime($u->row['bis']))->format("d.m.Y").'</td>';
	echo '<td>'.$m->getUrlaub($u->row['von'],$u->row['bis']).'</td>';
	// echo '<td><div id="pop">'.$status.'</div></td>';	
	// echo '<td><div id="tooltip">'.$status.'</div></td>';	
	echo '<td><div id="tooltip">'.$status.'<span id="tooltiptext">'.$u->row['info'].'</span></div></td>';	

	if ($u->row['status'] == 1) {
		echo '<td>&nbsp;</td>';
	} else {
		echo '<form action="zeit_urlaub.php" method="POST">';
		echo '<td>';
		echo '<input type="hidden" name="recnum" value="'.$u->row['recnum'].'">';
		echo '<input type="submit" name="change" value="ändern">';
		echo '</td>';
		echo '</form>';
	}

	echo '</tr>';
}
echo '</table><br>';
// echo '</p>';

if (isset($_POST['recnum'])) {
	$u->loadbyRecnum($_POST['recnum']);
	$_POST['von']=(new DateTime($u->row['von']))->format("Y-m-d");
	$_POST['bis']=(new DateTime($u->row['bis']))->format("Y-m-d");
	$_POST['info']=$u->row['info'];
}

echo '<form name="formurlaub" action="zeit_urlaub.php" method="POST">';
echo '<h2>Urlaubswunsch</h2>'; 
echo '<table>';
echo '<tr><th>von:</th><td><input name="von" value="'.$_POST['von'].'" type="date" ></td></tr>';
echo '<tr><th>bis:</th><td><input name="bis" value="'.$_POST['bis'].'" type="date" ></td></tr>';
echo '</table>';

echo '<textarea name="info" style="width:90%;height:4rem;">'.$_POST['info'].'</textarea><br>';
if (empty($_POST['recnum'])) {
	$e="Einreichen";
} else {
	echo '<input type="hidden" name="recnum" value="'.$_POST['recnum'].'">';
	$e="ändern";
}
echo '<button id="button" name="btn_urlaub" type="submit" value="senden">'.$e.'</button>';
echo '</form>';
echo '<div id="menu" style="border-top:red solid 4px;"><a id="button" href="zeiterfassung.php">Zeiten</a><a id="button" href="zeit_urlaub.php">Urlaub</a><a id="button" href="zeit_krank.php">Krank</a></div>';
echo "</center></body></html>";


function headers() {
	echo '<html lang="de"><head>
		<meta charset="utf-8">
		<meta name="description" 
			  content="Anzeige des diesjährigen Urlaubs und Beantragung von Urlaub">
		<meta name="keywords" content="Stempeluhr, stempeln, Zeiterfassung, Krankheitserfassung, Urlaubserfassung">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Ulaubsanfrage und Information</title>
		<link rel="stylesheet" href="zeiterfassung.css">
		</head>
	';
}

?>
