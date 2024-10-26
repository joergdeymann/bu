<?php
include("dbconnect.php");

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
	<link type="text/css" rel="stylesheet" href="standart.css">
</head>
<body>
<center>
<h1>Buchhaltung</h1>
<h2>Rechnungen anzeigen</h2>


<table>
<?php
	echo '<tr><td colspan="8"><h3><center>Unbezahlte überfällige Rechnungen nach Fälligkeit</center></h3></td></tr>';
	echo '<tr><th>Fälligkeit</th><th>Rechnungsdatum</th><th>Rechnungs-Nr</th><th>Kundennummer</th><th>Kundenname</th><th>Netto</th><th>Brutto</th><th>Aktion</th></tr>';
	

	$r1="select concat(vorname,' ',nachname) from bu_kunden where bu_re.kdnr=bu_kunden.kdnr limit 1";
	// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
	$r2="select SUM(netto) from bu_re_posten where bu_re.renr=bu_re_posten.renr";

	$request="select *,($r1) as kdname,($r2) as netto from `bu_re` where `bezahlt` is NULL order by `faellig`";
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td id="red">'.date("d.m.Y",strtotime($row['faellig'])).'</td>';
		echo '<td id="red">'.date("d.m.Y",strtotime($row['datum'])).'</td>';
		echo '<td id="red">'.$row['renr'].'</td>';
		echo '<td id="red">'.$row['kdnr'].'</td>';
		echo '<td id="red">'.$row['kdname'].'</td>';
		echo '<td id="red">'.sprintf("%.2f",$row['netto']).'</td>';
		echo '<td id="red">'.sprintf("%.2f",$row['netto']*1.19).'</td>'; // Erst mal
		echo '<td id="red">'.'AKTION'.'</td>';
		echo '</tr>';
		
	}; 

	echo '<tr><td colspan="8"><h3><center>Unbezahlte Rechnungen nach Fälligkeit</center></h3></td></tr>';
	echo '<tr><th>Fälligkeit</th><th>Rechnungsdatum</th><th>Rechnungs-Nr</th><th>Kundennummer</th><th>Kundenname</th><th>Netto</th><th>Brutto</th><th>Aktion</th></tr>';
	$r1="select concat(vorname,' ',nachname) from bu_kunden where bu_re.kdnr=bu_kunden.kdnr limit 1";
	// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
	$r2="select SUM(netto) from bu_re_posten where bu_re.renr=bu_re_posten.renr";

	$request="select *,($r1) as kdname,($r2) as netto from `bu_re` where `bezahlt` < now() order by `faellig`";
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td id="yellow">'.date("d.m.Y",strtotime($row['faellig'])).'</td>';
		echo '<td id="yellow">'.date("d.m.Y",strtotime($row['datum'])).'</td>';
		echo '<td id="yellow">'.$row['renr'].'</td>';
		echo '<td id="yellow">'.$row['kdnr'].'</td>';
		echo '<td id="yellow">'.$row['kdname'].'</td>';
		echo '<td id="yellow">'.sprintf("%.2f",$row['netto']).'</td>';
		echo '<td id="yellow">'.sprintf("%.2f",$row['netto']*1.19).'</td>'; // Erst mal
		echo '<td id="yellow">'.'AKTION'.'</td>';
		echo '</tr>';
		
	}; 

	echo '<tr><td colspan="8"><h3><center>Bezahlte Rechnungen nach Fälligkeit</center></h3></td></tr>';
	echo '<tr><th>Fälligkeit</th><th>Rechnungsdatum</th><th>Rechnungs-Nr</th><th>Kundennummer</th><th>Kundenname</th><th>Netto</th><th>Brutto</th><th>Aktion</th></tr>';

	$r1="select concat(vorname,' ',nachname) from bu_kunden where bu_re.kdnr=bu_kunden.kdnr limit 1";
	// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
	$r2="select SUM(netto) from bu_re_posten where bu_re.renr=bu_re_posten.renr";

	$request="select *,($r1) as kdname,($r2) as netto from `bu_re` where `bezahlt` >= now() order by `faellig`";
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td id="green">'.date("d.m.Y",strtotime($row['faellig'])).'</td>';
		echo '<td id="green">'.date("d.m.Y",strtotime($row['datum'])).'</td>';
		echo '<td id="green">'.$row['renr'].'</td>';
		echo '<td id="green">'.$row['kdnr'].'</td>';
		echo '<td id="green">'.$row['kdname'].'</td>';
		echo '<td id="green">'.sprintf("%.2f",$row['netto']).'</td>';
		echo '<td id="green">'.sprintf("%.2f",$row['netto']*1.19).'</td>'; // Erst mal
		echo '<td id="green">'.'AKTION'.'</td>';
		echo '</tr>';
		
	}; 

	

 	// select * from `bu_re` where `kdnr`='$kdnr' and `bezahlt` is NULL order by datum  //bestimmter Kunde
?>
</table>
</center></body>
</html>
