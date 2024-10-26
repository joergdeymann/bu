<?php
include("dbconnect.php");
include "menu.php";

showHeader("Rechnung anzeigen - Aktion");

function toDate($datum) {
	return date("d.m.Y",strtotime($datum));
}

// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
$r2="select SUM(netto) from bu_re_posten where bu_re.renr=bu_re_posten.renr";
$request="select bu_re.*, bu_kunden.nachname,bu_kunden.vorname, bu_kunden.plz,bu_kunden.ort, bu_kunden.strasse, bu_kunden.firma as firmenname ,($r2) as netto from `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr where bu_re.renr='".$_POST['renr']."'";
// echo $request;
$result = $db->query($request);
$row = $result->fetch_assoc(); 
if ($row['layout'] == 0) {
	$request="select nr from bu_re_layout where prio=1";
	$result = $db->query($request);
	$r = $result->fetch_assoc(); 
	$std_layout=$r['nr'];
	$row['layout']=$r['nr'];
}

// $request="select *,(select ueberschrift from bu_re_layout where bu_mahn.mahnstufe=bu_re_layout.mahnstufe and bu_re_layout='".$row['layout']."') as mahntext from bu_mahn where renr='".$_POST['renr']."' order by mahnstufe";

$request="select * from bu_mahn left join bu_re_layout on bu_re_layout.mahnstufe=bu_mahn.mahnstufe where bu_mahn.renr='".$_POST['renr']."' and bu_re_layout.nr='".$row['layout']."'  order by bu_mahn.mahnstufe";


	
echo $request;
$result = $db->query($request);
// $row_mahn = $result->fetch_assoc(); 

// var_dump($row);

?>
<iframe id="PDF" height="300px" width="280px" style="position:absolute;right:0;bottom:0;display:none;"></iframe>
<script>
	function setPDF(pdf) {
		var ifr=document.getElementById("PDF");
		// var pdf= "rechnung_out.php?renr=20220023";

		ifr.style.width="200px";
		ifr.style.height="280px";
		ifr.style.display="initial";//"none"; // initial;
		ifr.style.border="0px";
		ifr.contentWindow.location.replace(pdf);
		
		// display:none;position:absolut;left:0;bottom:0;"
	
		
		// document.getElementById('PDF').contentWindow.location.replace(pdf);	
		// alert("Hallo setPDF");
	}
	
	function printPDF() {
	 	document.getElementById("PDF").contentWindow.print();	
	}
	
</script>

<center>
<form action="kunde_aktoin.php" method="POST">

<div id="submenu_neu" style="height:10em;margin-right:5% !important;">
<div>
<h1>Rechnungsdaten</h1>
<b>Rechnungsnummer:</b><i><?php echo $row['renr'];  ?></i><br>
<b>Rechnungsdatum:</b><i><?php echo toDate($row['datum']); ?></i><br>
<b>Fälligkeit:</b><i><?php echo toDate($row['faellig']);    ?></i><br>
<b>ursprünglicher Betrag: Netto: </b><i><?php echo $row['netto']; ?></i><br>
<b>Betrag mit Mahnungen: Netto: </b><i><?php echo $row['netto']; ?></i><br>
</div>

<div>
<h1>Kundendaten</h1>
<b>Kundennummer:</b><i><?php echo $row['kdnr'];  ?></i><br>
<b>Firma:</b><i><?php echo $row['firmenname']; ?></i><br>
<b>Name:</b><i><?php echo $row['vorname'].' '.$row['nachname'];    ?></i><br>
<b>Adresse:</b><i><?php echo $row['strasse']; ?></i><br>
<b>Ort:</b><i><?php echo $row['plz'].' '.$row['ort'];    ?></i><br>
</div>
</div><!-- submenu -->

<div>
<table>
	<tr>
		<th>Art</th><th>Erstellung</th><th>Fällig</th><th>Mahngebuer</th>
	</tr>
<!--
	<tr>
		<td>Rechnung</td>
		<td><?php echo $row['datum']?></td>
		<td><?php echo $row['faellig']?></td>
		<td><?php /* echo $raw['mahngebuehr']*/ ?></td>
	</tr>
-->	
	<?php
	// Mahnunungen bisher mit Text
		$latest="";
		while($row_mahn = $result->fetch_assoc()) {
			$m="Mahnumng";
			$m=$row_mahn['ueberschrift'];
			$d=toDate($row_mahn['datum']);
			$f=toDate($row_mahn['faellig']);
			$g=$row_mahn['mahngebuehr'];
			
			$latest_date=$row_mahn['faellig'];
			$latest_mahnstufe=$row_mahn['mahnstufe'];
			
			echo "<tr>";
			echo "<td>$m</td>";
			echo "<td>$d</td>";
			echo "<td>$f</td>";
			echo '<td style="text-align:right">'.$g.'</td>';
			echo "</tr>";				
		}
		
	?>
</table><br>
<?php
if (date("Y-m-d") > $row['faellig']) {
	$request="select * from bu_re_layout where mahnstufe>$latest_mahnstufe and nr = ".$row['layout']." order by mahnstufe limit 1";
	$result = $db->query($request);
	if ($row_next = $result->fetch_assoc()) {
		$msg='<b>Nachste Mahnstufe:'.$row_next['ueberschrift'].' mit folgendem Zahlungsziel: <input type="date" value="'.date('Y-m-d',strtotime('+'.$row_next['zahlungsziel_dauer']. 'days')).'" name="input_mahndatum"></b>';
	} else {
		$msg='<b style="color:orange;">Keine weitere Mahnstufe möglich</b>';
	}
} else {
	$msg='<b style="color:green;">Keine Mahnung nötig.</b>';
	
}
echo $msg;
echo "<br>";

/*
// $file="rechnung_print.php?renr=".$row['renr']."&mahnstufe=".($latest_mahnstufe+1)."&firmanr=".$values['firma'];	
$file="rechnung_print.php?renr=".$row['renr']."&mahnstufe=0&firmanr=5";	
echo "<script>";
echo "setPDF('$file');";
echo "</script>";
?>
*/	
<input type="submit" value="Neue Mahnung per Mail" name="mahnungmail"    style="margin-right:1%;font-size: 2em;">
<input type="submit" value="Neue Mahnung Drucken"  name="mahnungdrucken" style="margin-right:1%;font-size: 2em;">
<input type="submit" value="PDF Ansicht"           name="mahnungpdf"     style="margin-right:1%;font-size: 2em;">
<input type="submit" value="Zurück"                name="zurueck"        style="margin-right:1%;font-size: 2em;">
<button type="button" name="drucken" onClick="printPDF()">Drucken<br>für<br>Versand</button>

<br>
<!-- input type="submit" name="details" value="Details"><br-->
</div>

</form>
</center>
<?php
showBottom();
?>
