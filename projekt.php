<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_output.php";
include "class/class_projekt.php";
include "class/class_adresse.php";
include "class/class_kunden.php";
include "class/class_projekt_einteilung.php";
include "class/class_project_equipment.php";

$msg="";
$err="";
$html="";
$out=new Output($db);
$projekt=new Projekt($db);
$projekt->setTransfer(true);
$adresse=new Adresse($db);
$unterkunft=new Adresse($db);
$kunde=new Kunden($db);
$einteilung=new Projekt_einteilung($db);
$project_eq=new ProjectEquipment($db);

$silent=false;

if (isset($_SESSION['projekt'])) {
	echo "wieder hier";	
	

	if (count($_POST)>0) {
		
		$POST=$_POST;
		
		$_POST=$_SESSION['projekt'];
		unset($_SESSION['projekt']);
		
		foreach($POST as $k => $v) {
			$_POST[$k]=$v;
		}

	} else {
		$_POST=$_SESSION['projekt'];
		echo "und hier";
	}
	unset($_SESSION['projekt']);
	
	if (!empty($_POST['projekt_recnum'])) {
		$_POST['recnum']=$_POST['projekt_recnum'];
	}
	if (empty($_POST['find_recnum'])) {
		// Zum Speichern vorbereiten zur sicherheit
		if (!empty($_POST['recnum'])) {
			$_POST['update']=true;
		} else {
			$_POST['insert']=true;
		}
		$silent=true;
	}
}

if ($projekt->isUpdate()) {	
	if ($projekt->save()) {
		$msg="Projekt erfolgreich verändert";
		$err=false;
	} else  {
		$msg="Projekt konnte nicht geändert werden";
		$err=true;
	}
} else 
if ($projekt->isInsert()) {	
	$_POST['firma_recnum']=$_SESSION['firmanr'];
	$_POST['erstell_datum']=(new DateTime())->format("Y-md H:i:s");
	if ($projekt->save()) {
		$msg="Projekt erfolgreich hinzugefügt";
		$err=false;
	} else  {
		$msg="Projekt konnte nicht hinzugefügt werden";
		$err=true;
	}
}
if ($silent) $msg="";

// von Extern
if (isset($_POST['find_recnum'])) {
	// var_dump($_POST);	
	$_POST['recnum']=$_POST['projekt_recnum'];
	$projekt->loadByRecnum($_POST['projekt_recnum']);
	$projekt->transfer();
	
}

if (isset($_POST['find_name'])) {	
	$request ="select * from `bu_projekt`";
	$request.=" where (firma_recnum=".$_SESSION['firmanr'].")";
	$request.=" and (`name` like '%".$_POST['name']."%'";
	$request.=" or `nr` like '%".$_POST['nr']."%')";

	$projekt->query($request);
	$projekt->next();
	$projekt->transfer();
}

echo showHeader("Projekt anlegen/ändern");


$layout = '<tr><th>$label</th><td>';
$layout.= '$command';
$layout.= '</td></tr>';
$out->setFormat($projekt->format);
$out->setLayout($layout);

echo "<center>";
echo $out->msg($msg,$err);
echo $out->formStart;
echo "<table>";
echo $out->printField("recnum" );
echo $out->getHidden("projekt_recnum",$_POST['recnum']); // für den Transport in andere Listen
echo $out->printField("firma_recnum" );
$lo=str_replace('$command','$command '.$out->getSubmit("find_name","Suche").' '.$out->getSubmit("liste","Liste","projekt_liste.php"),$layout);
$out->setLayout($lo);
echo $out->printField("nr"	);
echo $out->printField("name"	);

$out->setLayout($layout);
echo $out->printField("start"	);
echo $out->printField("ende"	);
echo $out->printField("aufbau"  );
echo $out->printField("abbau"   );

$text  =$adresse->getFieldByRecnum("location","name");
$button=$out->getSubmit("find_location","Auswahl","adresse_liste.php");
echo $out->printField("location",$text,$button);


$text  =$kunde->getFieldByRecnum("kunde_recnum","firma");
$button=$out->getSubmit("find_kunde","Auswahl","kunde_liste.php");
echo $out->printField("kunde_recnum",$text,$button);

// echo htmlspecialchars($text);
// echo "<br>";
// echo htmlspecialchars($button);
// echo "<br>";


echo $out->printField("stellung");
echo $out->printField("info"	);


echo "</table>";

echo $out->getAutoButton();
echo $out->formEnd;

if (empty($_POST['recnum'])) {
	exit;
}
/*
Hier ändern:
Select * from aufgaben
*/

$select =",bu_projekt_aufgabe.recnum as aufgabe_recnum";
$select.=",bu_projekt_aufgabe.name as aufgabe_name";
$select.=",bu_projekt_aufgabe.text as aufgabe_text";
$select.=",bu_projekt_arbeiter.recnum as projekt_arbeiter_recnum";
$select.=",bu_mitarbeiter.name as mitarbeiter_name";
$select.=",bu_mitarbeiter.recnum as mitarbeiter_recnum";
$select.=",bu_projekt_arbeiter.*";
$select.=",bu_adresse.name      as unterkunft_name";
$select.=",bu_adresse.vorname   as unterkunft_vorname";
$select.=",bu_adresse.nachname  as unterkunft_nachname";
$select.=",bu_adresse.plz       as unterkunft_plz";
$select.=",bu_adresse.ort       as unterkunft_ort";
$select.=",bu_adresse.strasse   as unterkunft_strasse";
$select.=",bu_projekt.recnum    as project_recnum";

/*
$request="SELECT *$select from bu_projekt_aufgabe ";

$request.=" WHERE    bu_projekt_einteilung.firma_recnum = '".$_SESSION['firmanr']."'";
$request.=" AND      bu_projekt_einteilung.projekt_recnum = '".$_POST['projekt_recnum']."'";

$request.=" ORDER BY bu_projekt_aufgabe.name,bu_mitarbeiter.name";

*/





/*

$request="SELECT *$select from bu_projekt_einteilung ";
$request.=" RIGHT JOIN bu_projekt_aufgabe";
//$request.=" ON bu_projekt_aufgabe.projekt_recnum = bu_projekt_einteilung.aufgabe";
$request.=" ON bu_projekt_aufgabe.recnum = bu_projekt_einteilung.aufgabe";
// $request.=" OR bu_projekt_aufgabe.recnum IS NULL";
// $request.=" AND bu_projekt_aufgabe.projekt_recnum = '".$_POST['projekt_recnum']."'";
$request.=" LEFT JOIN bu_projekt_arbeiter";
$request.=" ON  bu_projekt_arbeiter.recnum=bu_projekt_einteilung.arbeiter";
$request.=" AND bu_projekt_aufgabe.recnum= bu_projekt_einteilung.aufgabe";
// $request.=" AND bu_projekt_arbeiter.projekt_recnum = '".$_POST['projekt_recnum']."'";
$request.=" LEFT JOIN bu_mitarbeiter";
$request.=" ON bu_mitarbeiter.recnum = bu_projekt_arbeiter.mitarbeiter_recnum";

$request.=" WHERE    bu_projekt_einteilung.firma_recnum = '".$_SESSION['firmanr']."'";
$request.=" AND      bu_projekt_einteilung.projekt_recnum = '".$_POST['projekt_recnum']."'";

// $request.=" GROUP by bu_projekt_einteilung.aufgabe,bu_projekt_einteilung.arbeiter";

$request.=" ORDER BY bu_projekt_aufgabe.name,bu_mitarbeiter.name";

*/


// Neuer Request ist besser:

$request ="SELECT *$select FROM bu_projekt";

$request.=" RIGHT JOIN bu_projekt_aufgabe"; 
$request.=" ON bu_projekt_aufgabe.projekt_recnum = bu_projekt.recnum";

$request.=" LEFT JOIN bu_project_division pd";
$request.=" ON  pd.projectId = bu_projekt.recnum";
$request.=" AND CAST(pd.jobDescription AS UNSIGNED) = bu_projekt_aufgabe.recnum";

$request.=" LEFT JOIN bu_projekt_arbeiter";
$request.=" ON bu_projekt_arbeiter.recnum= pd.projectWorkerId";
$request.=" AND bu_projekt_aufgabe.recnum= CAST(pd.jobDescription AS UNSIGNED)";

$request.=" LEFT JOIN bu_adresse";
$request.=" ON bu_projekt_arbeiter.unterkunft_recnum = bu_adresse.recnum";
$request.=" AND bu_projekt_arbeiter.recnum >0";

$request.=" LEFT JOIN bu_mitarbeiter ON bu_mitarbeiter.recnum = bu_projekt_arbeiter.mitarbeiter_recnum"; 
$request.=" WHERE bu_projekt.firma_recnum = '".$_SESSION['firmanr']."' ";
$request.=" AND bu_projekt.recnum = '".$_POST['projekt_recnum']."' ";

$request.=" ORDER BY bu_projekt_aufgabe.name,bu_mitarbeiter.name";



// echo $request;
$einteilung->query($request);
$aufgabe_recnum=0;
echo "<center><br>";
while ($einteilung->next()) {
// echo "<hr>";
// print_r($einteilung->row);
// echo "<hr>";
	if ($einteilung->row['aufgabe_recnum'] != $aufgabe_recnum) {
		if ($aufgabe_recnum > 0) {
			echo "</table>";
	
			
			echo '<table>';
			echo '<tr><td>';
			// echo '<form method="POST" action="mitarbeiter_liste.php">';
			echo '<form>';
			echo '<input name="projekt_recnum" type="hidden" value="'.$_POST['projekt_recnum'].'">';
			echo '<input name="aufgabe_recnum" type="hidden" value="'.$aufgabe_recnum.'">';
			echo '<input type="submit" value="weitere Arbeiter zuordnen" formaction="mitarbeiter_liste.php" formmethod="POST">'; // über mitarbeiter_liste nach projekt_arbeiter
			echo '</form>';
			echo '</td></tr>';
			echo '</table><br>';
			
		}
		$aufgabe_recnum=$einteilung->row['aufgabe_recnum'];
		
		$action="";
		$action.='<form style="display:inline-block;">';
		$action.='<input name="projekt_recnum" type="hidden" value="'.$_POST['projekt_recnum'].'">';
		$action.='<input name="aufgabe_recnum" type="hidden" value="'.$aufgabe_recnum.'">';
		$action.='&nbsp;&nbsp;<input name="change" type="submit" value="bearbeiten" formaction="projekt_aufgabe.php" formmethod="POST">'; // über mitarbeiter_liste nach projekt_arbeiter
		$action.='</form>';

		
		echo "<table>";
		echo '<tr><th colspan=3>'.$einteilung->row['aufgabe_name'].$action.'</th></tr>';
		echo '<tr><td colspan=3>';
		echo nl2br($einteilung->row['aufgabe_text'],false)."<br><br>";
		echo '</td></tr>';	
	}
	
	if ($einteilung->row['projekt_arbeiter_recnum'] > 0) {
		
		$arbeiter_text ='<table>';
		$arbeiter_text.='<tr><th>Einsatz</th><td>'.$out->DateTime($einteilung->row['start']).' bis '.$out->DateTime($einteilung->row['ende']).'</td></tr>';
		$arbeiter_text.='<tr><th>Anfahrt</th><td>'.$out->DateTime($einteilung->row['anfahrt']).'</td></tr>';
		$arbeiter_text.='<tr><th>Abfahrt</th><td>'.$out->DateTime($einteilung->row['abfahrt']).'</td></tr>';

		$unterkunft="";
		if (!empty($einteilung->row['unterkunft_recnum'])) {
			$unterkunft.=$einteilung->row['unterkunft_name']."<br>";
			$unterkunft.=$einteilung->row['unterkunft_vorname']." ".$einteilung->row['unterkunft_nachname']."<br>";
			$unterkunft.=$einteilung->row['unterkunft_strasse']."<br>";
			$unterkunft.=$einteilung->row['unterkunft_plz']." ".$einteilung->row['unterkunft_ort']."<br>";
			$unterkunft.="<br>";
		}
		$arbeiter_text.='<tr><th>Unterkunft</th><td>';
		$arbeiter_text.=$unterkunft;// '$unterkunft.name<br>und Adresse<br>';
		$arbeiter_text.='Nettopreis der Unterkunft: <i>'.$einteilung->row['unterkunft_preis'].' €</i><br>';
		$arbeiter_text.='<span style="width:5em;display:inline-block;">Check-in: </span>'.$out->DateTime($einteilung->row['unterkunft_start']).'<br>';
		$arbeiter_text.='<span style="width:5em;display:inline-block;">Check-out: </span>'.$out->DateTime($einteilung->row['unterkunft_ende']).'<br>';		
		$arbeiter_text.='</td></tr>';
		
		$arbeiter_text.='<tr><th>Preise</th><td>';
		$arbeiter_text.='<span style="width:8em;display:inline-block;">Tagessatz:        </span><span style="width:4em;display:inline-block;text-align:right;">'.sprintf("%5.2f",$einteilung->row['tagessatz']).' €</span><br>';
		$arbeiter_text.='<span style="width:8em;display:inline-block;">Tagessatz Offday: </span><span style="width:4em;display:inline-block;text-align:right;">'.sprintf("%5.2f",$einteilung->row['tagessatz_offday']).' €</span><br>';
		$arbeiter_text.='<br>';                                                                                    
		$arbeiter_text.='<span style="width:8em;display:inline-block;">KM-Pauschale:     </span><span style="width:4em;display:inline-block;text-align:right;">'.sprintf("%5.2f",$einteilung->row['km_pauschale']).' €</span><br>';
		$arbeiter_text.='<span style="width:8em;display:inline-block;">Weg zum Einsatz:  </span><span style="width:4em;display:inline-block;text-align:right;">'.sprintf("%5.0f",$einteilung->row['km_weg']).' km</span><br>';
		$arbeiter_text.='<span style="width:8em;display:inline-block;">Anzahl Fahrten:   </span><span style="width:4em;display:inline-block;text-align:right;">'.sprintf("%5.0f",$einteilung->row['km_fahrten']).'</span><br>';
		$arbeiter_text.='</td></tr>';

		$arbeiter_text.='<tr><th>Abeitszeiten</th><td>';
		$arbeiter_text.='<span style="width:12em;display:inline-block;">Standart Tages-Arbeitszeit:</span><span style="width:6em;display:inline-block;text-align:right;">'.sprintf("%3.0f",$einteilung->row['arbeitszeit']).' Stunden</span><br>';
		$arbeiter_text.='<span style="width:12em;display:inline-block;">Überstundensatz:</span><span style="width:6em;display:inline-block;text-align:right;">'.sprintf("%5.2f",$einteilung->row['ueberstunden_satz']).' €</span><br>';
		$arbeiter_text.='</td></tr>';
	
		$arbeiter_text.='<tr><th>Information</th><td>'.nl2br($einteilung->row['info']).'</td></tr>';
		$arbeiter_text.='<tr><th>Equipment von<br>'.$einteilung->row['mitarbeiter_name'].'</th><td>';
/*		
		'<span style="width:8em;display:inline-block;">Midas Pro 1:   </span><span style="width:4em;display:inline-block;text-align:right;">'.sprintf("%5.2f €",250).'</span><br>'.
		'<span style="width:8em;display:inline-block;">Stagebox:   </span><span style="width:4em;display:inline-block;text-align:right;">'.sprintf("%5.2f €",0).'</span><br>'.
*/	
		
		$request='
		SELECT * FROM bu_project_equipment
		
		left join bu_equipment
		on bu_equipment.recnum = bu_project_equipment.equipment_recnum
		
		where bu_equipment.mitarbeiter_recnum = '.$einteilung->row['mitarbeiter_recnum'].'
		and   bu_project_equipment.project_recnum = '.$einteilung->row['project_recnum'].';
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
			$arbeiter_text.= '<span style="width:8em;display:inline-block;">'.$prj_row['name'].':   </span><br>
			<div style="margin-left: 20px"> 
				<div>Preis:'.sprintf("%5.2f €",$prj_row['netto']).'</div>
				<div>Zeitraum:'.(new Datetime($prj_row['von']))->format("d.m.Y H:m").' - '. (new Datetime($prj_row['bis']))->format("d.m.Y H:m").'</div>'.'
				<div>Status:'.$status.'</div>
			</div>
			';
		}
		$arbeiter_text.='<br>';
		$arbeiter_text.='<form>';
		$arbeiter_text.='<input type="hidden" name="mitarbeiter_recnum" value="'.$einteilung->row['mitarbeiter_recnum'].'">';
		$arbeiter_text.='<input type="hidden" name="projekt_recnum" value="'.$einteilung->row['project_recnum'].'">';
		$arbeiter_text.='<input type="hidden" name="projekt_name"   value="'.$projekt->row['name'].'">';

		$arbeiter_text.='<input type="submit" value="ändern / hinzufüge" formaction="projekt_equipment.php" formmethod="POST">' ;

		$arbeiter_text.='<input name="add_eq" type="submit" value=" + " formaction="projekt_equipment.php" formmethod="POST">'; // hinzufügen weiteres Equipment bu_projekt_equipment
		
		'</td></tr>';
		$arbeiter_text.='</form>';

		
		$arbeiter_text.='</table>';
		$action="";
		$action.='<form style="display:inline-block;">';
		$action.='<input name="projekt_recnum" type="hidden" value="'.$_POST['projekt_recnum'].'">';
		$action.='<input name="aufgabe_recnum" type="hidden" value="'.$aufgabe_recnum.'">';
		$action.='<input name="arbeiter_recnum" type="hidden" value="'.$einteilung->row['projekt_arbeiter_recnum'].'">';
		$action.='&nbsp;&nbsp;<input name="change" type="submit" value="bearbeiten" formaction="projekt_arbeiter.php" formmethod="POST">'; // über mitarbeiter_liste nach projekt_arbeiter
		$action.='</form>';
		
	
		echo '<tr><td style="width:25px;text-align:center;vertical-align:middle;">&bullet;</td>';
		echo '<th style="text-align:left;padding-left:10px;background-color:transparent;border-bottom:2px black solid;border-top:2px black solid;">'.$einteilung->row['mitarbeiter_name'];
		echo $action;
		echo '</th>';
		echo '<td style="width:25px;">&nbsp;</td></tr>';	
		echo '<tr><td style="width:25px;text-align:center;">&nbsp;</td><td>'.$arbeiter_text.'</td><td style="width:25px;">&nbsp;</td></tr>';	
		// echo "</table><br>";
	}

/*	
	echo '<table>';
	echo '<tr><td>';
	// echo '<form method="POST" action="mitarbeiter_liste.php">';
	echo '<form>';
	echo '<input name="projekt_recnum" type="hidden" value="'.$_POST['projekt_recnum'].'">';
	echo '<input name="aufgabe_recnum" type="hidden" value="'.$einteilung->row['aufgabe_recnum'].'">';
	echo '<input type="submit" value="Arbeiter zuordnen" formaction="mitarbeiter_liste.php" formmethod="POST">'; // über mitarbeiter_liste nach projekt_arbeiter
	echo '</form>';
	echo '</td></tr>';
	echo '</table><br>';
*/	
	// echo '<h2 style="margin:0;padding:5px;display:inline-block;width:80%;background-color: #333333;color:#FFFFFF">'.$einteilung->row['aufgabe_name'].'</h2>'
	// echo '<div style="display:inline-block;width:80%;background-color: siler;color:black">';
	// echo nl2br($einteilung->row['aufgabe_text'],false);
	// echo '</div>';	
}
	if ($aufgabe_recnum >0) {
		echo '<table>';
		echo '<tr><td>';
		// echo '<form method="POST" action="mitarbeiter_liste.php">';
		echo '<form>';
		echo '<input name="projekt_recnum" type="hidden" value="'.$_POST['projekt_recnum'].'">';
		echo '<input name="aufgabe_recnum" type="hidden" value="'.$aufgabe_recnum.'">';
		echo '<input type="submit" value="weitere Arbeiter zuordnen" formaction="mitarbeiter_liste.php" formmethod="POST">'; // über mitarbeiter_liste nach projekt_arbeiter
		echo '</form>';
		echo '</td></tr>';
		echo '</table><br>';
	}

echo '</table>';
echo '<form method="POST" action="projekt_aufgabe.php">';
echo '<hr>';
echo '<input type="hidden" name="projekt_recnum" value="'.$_POST['projekt_recnum'].'">';
echo '<input type="submit" value="Aufgabe erstellen">';
echo '</form>';
 
echo "</center>";
		
/*
Projekteinteilung, anpassen oder wie handhaben
zuerst Projekt Aufgabe anlegen, dann sind ja noch keine einteilungen da
überlegen wie man das am besten macht
Habe mal ein Wert eingetragen ohne arbeiter
*/


showBottom();

?>


