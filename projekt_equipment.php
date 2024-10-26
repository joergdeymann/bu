<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_output.php";
// include "class/class_projekt_equipment.php";
include "class/class_equipment.php";
include "class/class_project_equipment.php";
include "class/class_mitarbeiter.php";
include "class/class_projekt.php";


// print_r($_POST);
// Werte wiederholen
popPOST('adresse');

/*
Liste von Equipment von Susi Veit im Zeitraum der Veranstaltung

Name Benötigt von/bis Verfügbar Ja/Nein

<h1>Eingeplant für dieses Projekt</h1>
Susi Veit erreichen: +49 1233455

Bezeichnung        	von         bis        Status        Action
Grand Ma   		 	1.01.2024   10.01.2024 eingeplant   [Bearbeiten] [Angefragt]
Midas Pro1  		1.01.2024   10.01.2024 anfrage      [Bearbeiten] [Bestätigt]
Allan&heath Avanis  1.01.2024   10.01.2024 bestätigt    [Bearbeiten]

<h1>Was soll eingeplant werden ?<h1>
Hier sind die Geräte von Susi Veit:

Eingeplante Zeit: Datum von: [01.01,2024] 
				        bis: [10.01.2024]

Bezeichnung Einplanen:
Grand Ma    [ ]			
Midas       [X]
[Ausgewählte einplanen]


<h1>Neues Gerät einplanen<h1>
Bezeichnung: [              ]
Beschreibung: [             ]
Preis:       [              ]

- Zeit aus obige eingeplante Zeit nehemn
- Status = Eingeplant
*/










$msg="";
$err="";
$html="";
$out=new Output($db);
// $adresse=new Adresse($db);
$project_eq=new ProjectEquipment($db);
$mi=new Mitarbeiter($db);
$mi->loadByRecnum($_POST['mitarbeiter_recnum']);
$pro=new Projekt($db);
$pro->loadByRecnum($_POST['projekt_recnum']);

// print_r($_POST);
//print_r($pro->row);
// exit;

/*
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
*/ 
$title="Geräte für Projekt";
showHeader($title);
$html='<center><table>';
$html.='<tr><th colspan="5">Eingeplant für das Projekt '.$_POST['projekt_name'].' </th></tr>';
$html.='<tr><td colspan="5">Besitzer: '.$mi->row['name'].'<br>Telefon: '.$mi->row['tel'].'<br>E-Mail: '.$mi->row['mail'].'</td></tr>';
$html.='<tr><th>Bezeichnung</th><th>von</th><th>bis</th><th>Status</th><th>Aktion</th></tr>';

$request='
SELECT * FROM bu_project_equipment

left join bu_equipment
on bu_equipment.recnum = bu_project_equipment.equipment_recnum

where bu_equipment.mitarbeiter_recnum = '.$_POST['mitarbeiter_recnum'].'
and   bu_project_equipment.project_recnum = '.$_POST['projekt_recnum'].';

';

$result=$project_eq->query($request);
$prj_row=array();
while ($prj_row=$result->fetch_array()) {
    $status_text=array(
        '<span style="color:red;font-weight:700;">Nicht verfügbar</span>',
        '<span style="color:yellow;font-weight:700;">Eingeplant</span>',
        '<span style="color:yellow;font-weight:700;">Anfrage gestellt</span>',
        '<span style="color:green;font-weight:700;">Bestätigt</span>'
        
    );
    $status=$status_text[$prj_row['status']];
    $html.='<tr>';
    $html.='<td>'.$prj_row['name'].'</td>';
    $html.='<td>'.(new Datetime($prj_row['von']))->format("d.m.Y H:m").'</td>';
    $html.='<td>'.(new Datetime($prj_row['bis']))->format("d.m.Y H:m").'</td>';
    $html.='<td>'.$status.'</td>';
    $html.='</tr>';

}
$html.='</table>';
$html.='<br>';
$request='
SELECT * FROM bu_equipment where mitarbeiter_recnum="'.$_POST['mitarbeiter_recnum'].'" order by name';


$result=$project_eq->query($request);
$row=array();
$html.='<table id="liste">';
$html.='<tr><th colspan="5">Geräte von '.$mi->row['name'].' </th></tr>';

$_POST['von']=(new Datetime($pro->row['start']))->format("Y-m-d");
$_POST['bis']=(new Datetime($pro->row['ende']))->format("Y-m-d");

$html.='<tr>';
$html.='<td colspan="3">
        <label style="display: inline-block; width:50px;">von:</label><input name="von" type="date" value="'.$_POST['von'].'"><br> 
        <label style="display: inline-block; width:50px;">bis:</label><input name="bis" type="date" value="'.$_POST['bis'].'">
        </td>';
$html.='</tr>';

$html.='<tr><th>Bezeichnung</th><th>Beschreibung</th><th>Action</th></tr>';

while ($row=$result->fetch_array()) {
    $action='
    <form>
    <input type="hidden" name="equipment_recnum" value="'.$row['recnum'].'">
    <input type="submit" value="hinzufügen" action="projekt_equipment.php" method="POST">
    </form>';

    $html.='<tr>';
    $html.='<td>'.$row['name'].'</td>'; 
    $html.='<td>'.nl2br($row['beschreibung']).'</td>';
    $html.='<td>'.$action.'</td>'; 
    $html.='</tr>';
}
$html.='</table>';


$html.='</center>';

echo $html;


/*
$layout = '<tr><th>v$label</th><td>';
$layout.= '$command';
$layout.= '</td></tr>';

// $out->setFormat($adresse->format);
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
*/

// echo $out->getAutoButton();
// echo $out->formEnd;
showBottom();

?>


