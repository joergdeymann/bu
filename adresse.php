<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_output.php";
include "class/class_adresse.php";
include "class/class_kunden.php";

// Werte wiederholen

// print_r($_POST);
popPOST('adresse');
// print_r($_POST);


$msg="";
$err="";
$html="";
$out=new Output($db);
$adresse=new Adresse($db);

if ($adresse->isUpdate()) {	

	if ($adresse->save()) {
		$msg="Adresse erfolgreich verändert";
		$err=false;
	} else  {
		$msg="Adresse konnte nicht geändert werden";
		$err=true;
	}
} else 
if ($adresse->isInsert()) {	
	$_POST['firmanr']=$_SESSION['firmanr'];
	if ($adresse->save()) {
		$msg="Adresse erfolgreich hinzugefügt";
		$err=false;
	} else  {
		$msg="Adresse konnte nicht hinzugefügt werden";
		$err=true;
	}
}
if ($err == false and !empty($msg) and !empty($_SESSION['projekt'])) {
	$_SESSION['projekt']['location']=$adresse->row['recnum'];
}


// von Extern alt
if (isset($_POST['find_adresse'])) {
	$adresse->loadByRecnum($_POST['location']);
}
// von Extern neu und standart
if (isset($_POST['btn_adresse'])) {
	// echo "von Externe";
	$adresse->loadByRecnum($_POST['adresse_recnum']);
}

if (isset($_POST['find_name'])) {	
	$request ="select * from `bu_adresse`";
	$request.=" where (firmanr=".$_SESSION['firmanr'].")";
	$request.=" and (`name` like '%".$_POST['name']."%')";
	// $request.=" or concat (`vorname`,' ',`nachname`) like '%".$_POST['vorname']." ".$_POST['nachname']."%')";

	$adresse->query($request);
	$adresse->next();
	$adresse->transfer();
}

showHeader("Adresse erfassen/ändern");


$layout = '<tr><th>$label</th><td>';
$layout.= '$command';
$layout.= '</td></tr>';
$out->setFormat($adresse->format);
$out->setLayout($layout);

echo "<center>";
echo $out->msg($msg,$err);
echo $out->formStart;
echo "<table>";
echo $out->printField("recnum" );
echo $out->printField("firmanr" );
$lo=str_replace('$command','$command '.$out->getSubmit("find_name","Suche").' '.$out->getSubmit("liste","Liste","adresse_liste.php"),$layout);
$out->setLayout($lo);
echo $out->printField("name"	);
$out->setLayout($layout);
echo $out->printField("name_zusatz"	);
echo $out->printField("anrede"	);
echo $out->printField("vorname"	);
echo $out->printField("nachname");
echo $out->printField("strasse"	);
echo $out->printField("strasse_zusatz"	);
echo $out->printField("plz"	  	);
echo $out->printField("ort"		);
echo $out->printField("tel1"	);
echo $out->printField("tel2"	);
echo $out->printField("mail"	);
echo $out->printField("info"	);
echo $out->printField("location");
echo $out->printField("istfirma");
echo $out->printField("zuordnung");
if (!isset($_POST['kunde_name'])) {
	if (!empty($adresse->row['kunde_recnum'])) {
		$kunde=new Kunden($db);
		$kunde->loadByRecnum($adresse->row['kunde_recnum']);
		$_POST['kunde_name']=$kunde->row['firma'];
		$_POST['kunde_recnum']=$kunde->row['recnum'];
	} else {
		$_POST['kunde_name']="";
	}
}
$lo=str_replace('$command','$command '.$_POST['kunde_name']." ".$out->getSubmit("liste","Liste","kunde_liste.php"),$layout);
$out->setLayout($lo);
echo $out->printField("kunde_recnum");
$out->setLayout($layout);

echo "</table>";

echo $out->getAutoButton();
echo $out->formEnd;
showBottom();

?>


