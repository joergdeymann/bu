<?php
include("dbconnect.php");
$msg="";

$fields=array(
 "renr",
 "datum",
 "leistung", 
 "kdnr",
 "faellig", 
 "firma"
);


$values=array();

$einheiten_value=array(
 "Stunde",
 "Tour",
 "Frachtstück"
);

$einheiten_mz=array(
 "Stunden",
 "Touren",
 "Frachtstücke"
);

$zuschlag_value=array(
 "",
 "Nachtzuschlag",
 "Sonntagszuschlag"
);


$km_value=array(
  "",
 "pro KM",
 "Gesamt"
);


$posten=array(
"anz" => 0,
"einheit" => 0
);


	
function mask($s) { // Apostrophe werden somit nicht zum Fehler
	return str_replace("'","\'",$s);
}

foreach($fields as $k) {
	if (isset($_POST[$k])) {
		$values[$k]=$_POST[$k];
	} else {
		$values[$k]="";
	}
}	

if (isset($_POST['save']) ) {
	$row=mask($_POST); // gepostete Felder
	$set="";
	$f=$fields;        // Felder inklusive recnum
	
	//kONTOROLLE Gibt es schon eine Recnungnummer ?
	// Rechnung automatisieren 
	// Jahr 4  Nr 4 Automatisch ausfüllen
	//  20220001   -> Erstellen bei Druck auf Weiter und dann anzeigen
	
	$request="select max(renr) as renr from `BU_re`";
	$result = $db->query($request);
	$r = $result->fetch_assoc();
	if (strlen($r['renr']) == 8) {
		$d=date("Y"); // 
		$c=sprintf("%04d",(substr($r['renr'],4,5)+1));
		$renr=$d.$c;	
	} else {
		$renr=date("Y")."0001";
	}
	$values['renr']=$renr;
		
	
	// INSERT
	$request="insert into `BU_re` (`".join("`,`",$f)."`) values ('".join("','",$values)."')";

	// echo $request;
	$result = $db->query($request) or die(mysql_fehler()); 
	if ($result) {
		$msg="Rechnung wurde neu angelegt. Jetzt nur noch die Posten hinzufügen<br>";
	}
}

if (isset($_POST['change']) ) {
	$row=mask($_POST); // gepostete Felder
	$f=$fields;       
	
	$r="";	
	foreach ($f as $v) {
		if ($r) {
			$r.=",";
		}
		$r.= "`".$v."`='".$row[$v]."'";
	}
		
	
	// UPDATE
	$request="update `BU_re` set $r where renr='".$row['renr']."'";

	// echo $request."<br>";



	// echo $request;
	$result = $db->query($request) or die(mysql_fehler()); 
	if ($result) {
		$msg="Rechnungsdaten wurde geändert.<br>";
	}
}



/*
	Posten löschen
   
*/
if (isset($_POST['del'])) {
	echo "Delete:".$_GET['pos']."von ". $_POST['renr']."<br>";
	$request="DELETE from `BU_re_posten` where `pos`='".$_GET['pos']."' and renr='".$_POST['renr']."'";
	$result = $db->query($request) or die(mysql_fehler()); 
	if ($result) {
		$msg="Posten ".$_GET['pos']." von Rechnung ".$_POST['renr']." erfolgreich gelöscht<br>";
	}
	
	
}




/*
   Noch gar nicht aktiv (IF)
   
*/
if (isset($_POST['find']) && isset($_POST['renr'])) {
	$renr=mask($_POST['renr']);
	
	$request="select * from `BU_re` where `renr` = '".$renr."'";
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	
	if ($row) {
		foreach($fields as $k) {
			// echo $k."=".$row[$k]."<br>";
			$values[$k]=$row[$k];
		}
	}	
}


// Anzahl der gespeicherten Posten herausfinden auch beim Speichern (Add Button)
if (isset($values['renr'])) { 
	//Posten
	$request="select max(`pos`) as posten from `BU_re_posten` where `renr` = '".$values['renr']."'";
	$result = $db->query($request) or die(mysql_fehler());
	$row = $result->fetch_assoc();
	
	if (empty($row['posten'])) {
		$posten['nr']=1;
	} else {
		$posten['nr']=$row['posten']+1;
		
	}
}

if (isset($_POST['add'])) {
	// update -> $request="insert into `BU_re_posten set `renr`='".$values['renr']."', `pos`='".$posten['nr']."', `anz`='".$_POST['anz']."', `einheit`='".$_POST['zuschlag']."', `km`='".$_POST['km']."', `netto`='".$_POST['netto']."'";
	$request="insert into `BU_re_posten` (`renr`,`pos`,`anz`,`einheit`,`zuschlag`,`km`,`netto`) VALUES ('".$values['renr']."','".$posten['nr']."','".$_POST['anz']."','".$_POST['einheit']."','".$_POST['zuschlag']."','".$_POST['km']."','".$_POST['netto']."')";
	$result = $db->query($request) or die(mysql_fehler());
	if ($result) {
		$msg="Neuer Posten angelegt";
		$posten['nr']++;
	} else {
		$msg="<b style=\"background-color:orange\">Posten konnte nicht angelegt werden</b>";
	}
	
	
	// $result = $db->query($request);
}
	
	



?>	
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
<link rel="stylesheet" href="standart.css">
</head>
<body><center>
<h1>Buchhaltung</h1>
<h2>Rechnung erstellen - Anlegen</h2>

<?php
// 	include "menu.php";
?>
<form action="rechnungNeu.php" method="POST";>
<input type="hidden" name="recnum" value="<?php echo $recnum ?>">
<table>
<tr><th>Rechnungsnummer</th><td>          <input type="hidden" name="renr"            size="15" value="<?php 
if (!empty($values['renr']) ) {
	echo $values['renr'];     
}
?>"><?php

if (empty($values['renr']) ) {
	echo "<b id=\"rand\">Neue Rechnung</b>";
} else {
	echo "<b id=\"rand\">".$values['renr']."</b>";     
}?>
<input type="submit" name="find" value="Suchen" formaction="rechnungSuchen.php"></td></tr>
<tr><th>Rechnungsdatum</th><td>           <input type="date" name="datum"                     value="<?php echo $values['datum']    ?>"></td></tr>
<tr><th>Leistungsmonat</th><td>           <input type="date" name="leistung"                  value="<?php echo $values['leistung'] ?>"></td></tr>
<tr><th>Kundennummer</th><td>             <input type="text" name="kdnr"            size="15" value="<?php echo $values['kdnr']     ?>"></td></tr>
<tr><th>Fälligkeitsdatum</th><td>  		  <input type="date" name="faellig"                   value="<?php echo $values['faellig']  ?>"></td></tr>
<tr><th>Rechnungsformular/Firma</th><td>  <input type="text" name="firma"           size="5"  value="<?php echo $values['firma']    ?>"></td></tr>
<tr><td colspan=2 style="text-align:right;"><?php 
if (empty($values['renr']) ) {
	echo '<input type = "submit" name="save" value = "Weiter" style="font-size:1.5em;">';
} else {
	echo '<input type = "submit" name="change" value = "Ändern" style="font-size:1.5em;">';
}
?></td></tr>

<!-- if recnum= 0 then "anlegen" if recnum >0 then "ändern" "neu anlegen" -->


</table>
<h3><?php echo $msg ?></h3>


<br>
<?php
if (empty($values['renr']) ) {
} else {
	// <!-- Posten -->
	echo "<table>";
	if (isset($values['renr'])) { 
	
		echo "<tr><th>Pos</th><th>Menge,Einheit</th><th>Thema</th><th>Einzelpreis</th><th>Aktion</th></tr>";
		//Posten
		$request="select * from `BU_re_posten` where `renr` = '".$values['renr']."' order by `pos`";
		$result = $db->query($request);
		while($row = $result->fetch_assoc()) {
		
			// $z1=$row['zuschlag'];
			// $z2=$row['zuschlag2'];
			$z=$row['zuschlag'];
			$z1=$zuschlag_value[$z];
			
			$z=$row['km'];
			$z2=$km_value[$z];
			
			$e=$row['einheit'];
			$einheit=$einheiten_mz[$e];

			if ($row['anz'] == 1) {
				$e=$row['einheit'];
				$einheit=$einheiten_value[$e];
			}
			
			
			echo "<tr>";
			echo "<td>".$row['pos']."</td>";
			echo "<td>".$row['anz']." ".$einheit."</td>";
			echo "<td>".$z1." ".$z2."</td>";
			echo "<td>".$row['netto']."</td>";
			echo "<td><input type=\"submit\" value=\"Löschen\" name =\"del\" formaction=\"rechnungNeu.php?pos=".$row['pos']."\" formmethod=\"post\"></td>";
			echo "</tr>";
			
		} 
	}
	echo '</table><br><br>';



	echo '<table>';
	echo '<tr><th>Pos</th><td>'.$posten['nr'].'</td><tr>';
	echo '<tr><th>Anzahl</th><td><input type="number" name="anz" style="width: 5em"></td><tr>';

	echo '<tr><th>Einheit</th><td><select name="einheit">';

	foreach($einheiten_value as $k => $v) {
	  echo '<option value="'.$k.'">'.$v.'</option>';
	}
	echo '  </select></td><tr>';

	echo '<tr><th>Zuschlag</th><td><select name="zuschlag">';
	
	foreach($zuschlag_value as $k => $v) {
	  echo '<option value="'.$k.'">'.$v.'</option>';
	}
	echo '</select>';

	echo '<select name="km">';

	foreach($km_value as $k => $v) {
	  echo '<option value="'.$k.'">'.$v.'</option>';
	}
	
	
	echo '</select>';



	echo '</td><tr>';
	
	echo '<tr><th>Einzelpreis</th><td><input type="number" step="any"  name="netto" style="width: 9em"></td><tr>';

	echo '<tr><td colspan=2 style="text-align:right;">';
	echo '<input type = "submit" name="add" value = "Hinzufügen" style="font-size:1.5em;">';
	echo '</td></tr>';
	
	echo '</table>';

	echo '<center><center>';
	

}


	


?>

<br><input type="submit" name="zurueck" value="Menü" formaction="index.php"> 
<input style="margin-left:20px;" type="submit" name="show" value="Anzeigen" formaction="rechnungPDF.php" formmethod="POST" formtarget="_blank">
<input style="margin-left:20px;" type="submit" name="show" value="Drucken" formaction="rechnungDrucken.php" formmethod="POST" formtarget="_blank">
<input style="margin-left:20px;" type="submit" name="show" value="Versenden" formaction="rechnungVersenden.php" formmethod="POST" formtarget="_blank">

</form>
</center></body>
