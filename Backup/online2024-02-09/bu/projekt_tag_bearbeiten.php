<?php
include "session.php";
include "dbconnect.php";
include "class/class_kunden.php";
include "class/class_projekttag.php";
include "class/class_mitarbeiter.php";
include "menu.php";

$pt   =new ProjektTag($db);
$kunde=new Kunden($db);
$m    =new mitarbeiter($db);
$msg="";

if (isset($_POST['find_mitarbeiter'])) {

} else
if (isset($_POST['find_kdnr'])) {
	// nichts mahchen, da die Post daten wieder übergeben wurden
} else 

if (isset($_POST['change'])) {

	/*
	echo "change<br>";
	echo "<pre>";
	var_dump ($_POST);
	echo "</pre>";
	*/
	
	$row=array();
	$row['datum']        =$_POST['datum'];
	$row['kdnr']         =$_POST['kdnr'];
	$row['mitarbeiternr']=$_POST['mitarbeiternr'];
	$row['km']           =$_POST['km'];
	$row['arbeitstyp']   =$_POST['arbeitstyp'];   
	$row['info']         =$_POST['info'];      
	$row['recnum']       =$_POST['recnum'];	
	
	$pt->save($row);
	$msg="Projekt Tag geändert";
	
	
} else {
	if (isset($_POST['recnum'])) {
		$pt->loadByRecnum($_POST['recnum']);		
		if (isset($pt->row['kdnr'])) {
			$_POST['datum']         =$pt->row['datum'];
			$_POST['kdnr']          =$pt->row['kdnr'];
			$_POST['mitarbeiternr'] =$pt->row['mitarbeiternr'];
			$_POST['km']            =$pt->row['km'];
			$_POST['arbeitstyp']    =$pt->row['arbeitstyp'];
			$_POST['info']          =$pt->row['info'];
			$_POST['recnum']        =$pt->row['recnum'];
			
			
			
		} else {
			$kunde_name="";
			$mitarbeiter_name="";
		}
			
	}
}


if (isset($_POST['mitarbeiternr'])) {
	if ($m->loadByNr($_POST['mitarbeiternr'])) {
		$mitarbeiter_name=$m->row['name'];
	} else {
		$mitarbeiter_name="";
	}
} else {
	$_POST['mitarbeiternr']="";
	$mitarbeiter_name="";
}	

if (isset($_POST['kdnr'])) {
	if($kunde->loadByKDNR($_POST['kdnr'])) {
		$kunde_name=$kunde->row['firma'];
	} else {
		$kunde_name="";
	}
} else {
	$_POST['kdnr']="";
	$kunde_name="";
}

/*
if (!isset($_POST['datum'])) {
	$_POST['datum']="";
}
*/

showHeader("Projekttag bearbeiten");
echo '<center><b>'.$msg.'</b><table>';
echo '<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
echo '<input type="hidden" name="recnum" value="'.$_POST['recnum'].'">';
echo '<tr><th>Datum</th><td><input type="date" name="datum" value="'.$_POST['datum'].'"></td></tr>';
echo '<input type="hidden" name = "kdnr" value="'.$_POST['kdnr'].'">';
// echo '<tr><th>Kunde</th><td><span style="display:inline-block;min-width:10em;">'.$kunde_name.' </span><input formaction="kunde_auswahl.php" method="POST" name="change_kunde" type="submit" value = "auswahl"></td></tr>';

echo '<tr><th>Kunde</th><td>';
if (!empty($kunde_name)) {
	echo '<span style="display:inline-block;min-width:10em;">'.$kunde_name.' </span>';
}
echo '&nbsp;<input formaction="kunde_auswahl.php" method="POST" name="change_kunde" type="submit" value = "Auswahl">';
echo '</td></tr>';

echo '<input type="hidden" name = "mitarbeiternr" value="'.$_POST['mitarbeiternr'].'">';
echo '<tr><th>Mitarbeiter</th><td><span style="display:inline-block;min-width:10em;">'.$mitarbeiter_name.' </span>&nbsp;<input formaction="mitarbeiter_auswahl.php" method="POST" name="change_mitarbeiter" type="submit" value = "zuteilen"></td></tr>';
echo '<tr><th>Gefahrene KM</th><td><input type="number" name="km" value="'.$_POST['km'].'"></td></tr>';

echo '<input type="hidden" name = "arbeitstyp" value="'.$_POST['arbeitstyp'].'">';
echo '<tr><th>Arbeitstyp</th><td>
<select name="arbeitstyp" style="width: 20em;">';
foreach($pt->arbeitstyp as $k => $v) {
	if ($k == $_POST['arbeitstyp']) {
		echo '<option value="'.$k.'" selected>'.$v.'</option>';
	} else {
		echo '<option value="'.$k.'">'.$v.'</option>';
	}		
}
	
echo '</select>
</td></tr>';

echo '<tr><th>Info</th><td><textarea rows=10 cols=80 name="info">'.$_POST['info'].'</textarea></td></tr>';
echo '<tr><th>&nbsp;</th><td><input type="submit" name ="change" value="ändern"></td></tr>';

echo '</form>';
echo '</table>';
echo '</center>';



?>

