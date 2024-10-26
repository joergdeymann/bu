<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_output.php";
include "class/class_projekt_aufgabe.php";
include "class/class_projekt.php";


$msg="";
$err="";
$html="";
$out=new Output($db);
$aufgabe=new Projekt_aufgabe($db);
$projekt=new Projekt($db);


if ($aufgabe->isUpdate()) {	

	if ($aufgabe->save()) {
		$msg="Aufgabe füt das Projekt erfolgreich verändert";
		$err=false;
	} else  {
		$msg="Aufgabe für das Projekt konnte nicht geändert werden";
		$err=true;
	}
} else 
if ($aufgabe->isInsert()) {	
	$_POST['firmanr']=$_SESSION['firmanr'];
	if ($aufgabe->save()) {
		$msg="Augfgabe erfolgreich zum Projekt hinzugefügt";
		$err=false;
	} else  {
		$msg="Aufgabee konnte nicht  zum Projekt hinzugefügt werden";
		$err=true;
	}
}

// von Extern
if (isset($_POST['projekt_recnum'])) {
	$projekt->setTransfer(false);
	$projekt->loadByRecnum($_POST['projekt_recnum'],false);	
}

if (isset($_POST['aufgabe_recnum'])) {
	$aufgabe->loadByRecnum($_POST['aufgabe_recnum']);
	$_POST['recnum']=$_POST['aufgabe_recnum'];	
	$_POST['name']=$aufgabe->row['name'];
	$_POST['text']=$aufgabe->row['text'];
	echo "AUFGABE geladen";
}
if (!isset($_POST['firma_recnum'])) {
	$_POST['firma_recnum']=$_SESSION['firmanr'];	
}
/*
if (isset($_POST['find_name'])) {	
	$request ="select * from `bu_adresse`";
	$request.=" where (firmanr=".$_SESSION['firmanr'].")";
	$request.=" and (`name` like '%".$_POST['name']."%'";
	$request.=" or concat (`vorname`,' ',`nachname`) like '%".$_POST['vorname']." ".$_POST['nachname']."%')";

	$adresse->query($request);
	$adresse->next();
	$adresse->transfer();
}
*/

showHeader("Ein Aufgabebereich festlegen");


$layout = '<tr><th>$label</th><td>';
$layout.= '$command';
$layout.= '</td></tr>';
$out->setFormat($aufgabe->format);
$out->setLayout($layout);
$out->getHidden("aufgabe_recnum");
$out->getHidden("projekt_recnum");

echo "<center>";
echo $out->msg($msg,$err);
echo $out->formStart;

echo "<table>";
echo "<tr><th>Projekt:</th><td>";
echo $projekt->row['name']." (".$projekt->row['nr'].")";
echo '</td></tr>';

echo $out->printField("recnum" );
echo $out->printField("firma_recnum" );
echo $out->printField("projekt_recnum" );

// $lo=str_replace('$command','$command '.$out->getSubmit("find_name","Suche").' '.$out->getSubmit("liste","Liste","adresse_liste.php"),$layout);
// $out->setLayout($lo);
// echo $out->printField("name"	);
// $out->setLayout($layout);
echo $out->printField("name"	);
echo $out->printField("text"	);
echo "</table>";

echo $out->getAutoButton();
if (!empty($_POST['recnum'])) {
	// echo " ";
	// echo $out->getSubmit("insert","hinzufügen");
}
echo "<br><br>".$out->getSubmit("find_recnum","zum Projekt","projekt.php");

echo $out->formEnd;
showBottom();

?>


