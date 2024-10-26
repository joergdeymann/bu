<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_output.php";
include "class/class_projekt.php";
include "class/class_projekt_arbeiter.php";
include "class/class_projekt_aufgabe.php";
include "class/class_projekt_einteilung.php";
include "class/class_mitarbeiter.php";
include "class/class_kunden.php";
include "class/class_adresse.php";


$msg="";
$err="";
$html="";
$out=new Output($db);
$projekt=new Projekt($db);
$arbeiter=new Projekt_arbeiter($db);
$aufgabe=new Projekt_aufgabe($db);
$einteilung = new Projekt_einteilung($db);
$ma=new Mitarbeiter($db);
$unterkunft=new Adresse($db);
$kunde=new Kunden($db);


if ($arbeiter->isUpdate()) {	
	$_POST['firma_recnum']=$_SESSION['firmanr'];
	

	if ($arbeiter->save()) {
		$msg="Arbeiter Informationen erfolgreich verändert";
		$err=false;
	} else  {
		$msg="Arbeiter Informationen konnten nicht geändert werden";
		$err=true;
	}
} else 
if ($arbeiter->isInsert()) {	
	$_POST['firma_recnum']=$_SESSION['firmanr'];
	if ($arbeiter->save()) {
		$msg="Arbeiter Informationen erfolgreich hinzugefügt";
		$err=false;
	} else  {
		$msg="Arbeiter Informationen konnten nicht hinzugefügt werden";
		$err=true;
	}
	// hier testetn ob einteilung schon vorhanden ist,  wenn nein anlegen
	if ($err == false)  {
		$row=array();
		$row['firma_recnum']=$_SESSION['firmanr'];
		$row['projekt_recnum']=$_POST['projekt_recnum'];
		$row['aufgabe']=$_POST['aufgabe_recnum'];
		$row['arbeiter']=$arbeiter->row['recnum'];
		// print_r($row);
		$einteilung->loadByWhere($row);
		if (count($einteilung->row) == 0) {
			if ($einteilung->insert($row)) {
			} else {
				$msg.="<br>Arbeiter konnte nicht zur Aufgabe hinzugefügt werden";
			}
		}
	}
	
}
// von Extern
if (!empty($_POST['projekt_recnum'])) {
	$projekt->loadByRecnum($_POST['projekt_recnum']);
	$_POST['projekt_name']=$projekt->row['name'];	
} else {
	$_POST['projekt_name']="";
}
if (!empty($_POST['aufgabe_recnum'])) {
	$aufgabe->loadByRecnum($_POST['aufgabe_recnum']);
	$_POST['aufgabe_name']=$aufgabe->row['name'];	
} else {
	$_POST['aufgabe_name']="";
}
if (!empty($_POST['mitarbeiter_recnum'])) {
	$ma->loadByRecnum($_POST['mitarbeiter_recnum']);
	$_POST['mitarbeiter_name']=$ma->row['name'];	
	
} else {
	$_POST['mitarbeiter_name']="";
}


/*
if (isset($_POST['projekt_recnum']) and isset($_POST['aufgabe_recnum'])) {
	$where=array();
	$where['aufgabe_recnum']=$_POST['aufgabe_recnum'];
	$where['projekt_recnum']=$_POST['projekt_recnum'];
	$where['firma_recnun']=$_SESSION['firmanr'];
	$arbeiter->loadByWhere($where);
	
	
}
*/

if (!empty($_POST['arbeiter_recnum']) and empty($_POST['insert']) and empty($_POST['update'])) {
	$arbeiter->loadByRecnum($_POST['arbeiter_recnum']);
	$arbeiter->transfer();
	$ma->loadByRecnum($arbeiter->row['mitarbeiter_recnum']);
	$_POST['mitarbeiter_name']=$ma->row['name'];	
	
	// mitarbeiter anpassen ?
	
	// firma_recnum $_SESSION['firmanr'];
	// projekt_recnum $_POST['projekt_recnum'];
	// mitarbeiter_recnum $_POST['mitarbeiter_recnum']	
}

if (!empty($_POST['location'])) {
	// echo "Location:".$_POST['location'];
	$_POST['unterkunft_recnum']=(int)$_POST['location'];
	// echo "Location:".$_POST['unterkunft_recnum'];
	
}
if (empty($_POST['unterkunft_recnum'])) {
	$_POST['unterkunft_recnum']=(int)0;
}

if (empty($_POST['start']) and (!empty($projekt->row['aufbau']) or !empty($projekt->row['start']))) {
	// Vorbesetzung erst Aufbau da es voher dem Start liegt, sonst start
	if (!empty($projekt->row['aufbau'])) { 
		$_POST['start']=$projekt->row['aufbau'];
	} else {
		$_POST['start']=$projekt->row['start'];
	}
	$dt=new DateTime($_POST['start']);
	$dt->modify("-1 day");
	$_POST['anfahrt']=$dt->format("d.m.Y");
	$_POST['unterkunft_start']=$dt->format("d.m.Y");	
}
if (empty($_POST['ende']) and (!empty($projekt->row['abbau']) or !empty($projekt->row['ende']))) {
	// Vorbesetzung erst Aufbau da es voher dem Start liegt, sonst start
	if (!empty($projekt->row['abbau'])) { 
		$_POST['ende']=$projekt->row['abbau'];
	} else {
		$_POST['ende']=$projekt->row['ende'];
	}
	$dt=new DateTime($_POST['ende']);
	$dt->modify("+1 day");
	$_POST['abfahrt']=$dt->format("d.m.Y");
	$_POST['unterkunft_ende']=$dt->format("d.m.Y");	
}

if (!empty($projekt->row['kunde_recnum'])) {
	$kunde->loadByRecnum($projekt->row['kunde_recnum']);
	
	$key='tagessatz';
	if (empty($_POST[$key] ) and !empty($kunde->row[$key])) {
		$_POST[$key]=$kunde->row[$key];
	}
	$key='tagessatz_offday';
	if (empty($_POST[$key] ) and !empty($kunde->row[$key])) {
		$_POST[$key]=$kunde->row[$key];
	}
	$key='ueberstunden_satz';
	if (empty($_POST[$key] ) and !empty($kunde->row[$key])) {
		$_POST[$key]=$kunde->row[$key];
	}
	$key='standart_arbeitszeit';
	$keyto='arbeitszeit';
	if (empty($_POST[$keyto] ) and !empty($kunde->row[$key])) {
		$_POST[$keyto]=$kunde->row[$key];
	}
	$key='km_pauschale';
	$keyto='';
	if (empty($_POST[$keyto] ) and !empty($kunde->row[$key])) {
		$_POST[$keyto]=$kunde->row[$key];
	}
	
}	
	
/*
if (isset($_POST['find_name'])) {	
	$request ="select * from `bu_adresse`";
	$request.=" where (firmanr=".$_SESSION['firmanr'].")";
	$request.=" and (`name` like '%".$_POST['name']."%'";
	$request.=" or concat (`vorname`,' ',`nachname`) like '%".$_POST['vorname']." ".$_POST['nachname']."%')";

	$arbeiter->query($request);
	$arbeiter->next();
	$arbeiter->transfer();
}

*/
showHeader("Projekt Arbeiter Informationen erfassen/ändern");


$layout = '<tr><th>$label</th><td>';
$layout.= '$command';
$layout.= '</td></tr>';
$out->setFormat($arbeiter->format);
$out->setLayout($layout);
$fremdfirma="";
/* Es gibt noch nicht die Zuordnung zur fremdfirma

if (!empty($arbeiter->row['fremdfirma'])) {
	$kunden=new Kunden($db);
	$kunden->loadByRecnum($arbeiter->row['fremdfirma']);
	$fremdfirma=$kunden->row['name'];
}
*/

	
echo "<center>";
echo $out->msg($msg,$err);
echo $out->formStart;
echo "<table>";
echo '<tr><th>Projekt</th><td><i>'.$projekt->row['name']."</i></td></tr>";
echo '<tr><th>Aufgabe</th><td><i>'.$aufgabe->row['name']."</i></td></tr>";
echo '<tr><th>Arbeiter</th><td><i>'.$ma->row['name'].$fremdfirma."</i></td></tr>";
echo '</table>';

echo '<br>';

echo "<table>";
echo $out->printField("recnum" );
echo $out->printField("projekt_recnum");
echo $out->printField("mitarbeiter_recnum");
echo $out->printField("start"	);
echo $out->printField("ende");
echo $out->printField("anfahrt");
echo $out->printField("abfahrt");
// $lo=str_replace('$command','$command '.$out->getSubmit("find_name","Suche").' '.$out->getSubmit("liste","Liste","projekt_liste.php"),$layout);
// $out->setLayout($lo);

// echo "X";
$text="";
if (!empty($_POST['unterkunft_recnum'])) {	
// echo "Heir";
	$unterkunft->loadByRecnum($_POST['unterkunft_recnum']);
	if (!empty($unterkunft->row["name"])) {
		$text=$unterkunft->row["name"];
		// echo "$text gesetzt";
	} else 
	if (!empty($unterkunft->row['vorname'].$unterkunft->row['nachname'])) {
		$text=$unterkunft->row['vorname']." ".$unterkunft->row['nachname'];
	}
}
$button=$out->getSubmit("liste","Unterkunft","adresse_liste.php");
// echo "---".htmlspecialchars($button)."---<br>";
echo $out->printField("unterkunft_recnum",$text,$button);

// echo $out->printField("unterkunft_recnum");
echo $out->printField("unterkunft_preis");
echo $out->printField("unterkunft_start");
echo $out->printField("unterkunft_ende");
echo $out->printField("info");
echo $out->printField("km_pauschale");
echo $out->printField("km_weg");
echo $out->printField("km_fahrten");
echo $out->printField("tagessatz");
echo $out->printField("tagessatz_offday");
echo $out->printField("arbeitszeit");
echo $out->printField("ueberstunden_satz");





/*
// echo '<tr><th colspan="2">'.$ma->row['vorname']." ".$ma->row['nachname'].$fremdfirma."</th></tr>";
echo $out->printField("recnum" );
echo $out->printField("firmanr" );
$lo=str_replace('$command','$command '.$out->getSubmit("find_name","Suche").' '.$out->getSubmit("liste","Liste","adresse_liste.php"),$layout);
$out->setLayout($lo);
echo $out->printField("name"	);
$out->setLayout($layout);
echo $out->printField("anrede"	);
echo $out->printField("vorname"	);
echo $out->printField("nachname");
echo $out->printField("location");
echo $out->printField("istfirma");
echo $out->printField("strasse"	);
echo $out->printField("plz"	  	);
echo $out->printField("ort"		);
echo $out->printField("tel1"	);
echo $out->printField("tel2"	);
echo $out->printField("mail"	);
echo $out->printField("info"	);
*/
echo "</table>";

echo $out->getAutoButton();
echo $out->getHidden("arbeiter_recnum",$_POST['recnum']);
echo $out->getHidden("projekt_recnum");
echo $out->getHidden("aufgabe_recnum");
echo $out->getSubmit("find_recnum","weiter","projekt.php");
echo $out->formEnd;
showBottom();

?>


