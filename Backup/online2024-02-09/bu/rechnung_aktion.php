<?php
include "session.php";
include "dbconnect.php";
include "menu.php";

showHeader("Rechnung anzeigen - Aktion");
function toDate($datum) {
	return date("d.m.Y",strtotime($datum));
}

// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
// $r2="select SUM(`netto`) from bu_re_posten where bu_re.renr=bu_re_posten.renr and bu_re.firmanr='".$_SESSION['firmanr']."'";
// $request="select bu_re.*, bu_kunden.nachname,bu_kunden.vorname, bu_kunden.plz,bu_kunden.ort, bu_kunden.strasse, bu_kunden.firma as firmenname ,($r2) as netto from `bu_re` left // join bu_kunden on bu_re.kdnr=bu_kunden.kdnr where bu_re.renr='".$_POST['renr']."' and bu_re.firmanr='".$_SESSION['firmanr']."'";
// echo $request;

$fn=$_SESSION['firmanr'];
$rn=$_POST['renr'];
$request= "
select *,bu_kunden.firma as firmenname,SUM(netto) as netto,SUM(netto*(1+mwst/100)) as brutto
from bu_re 
left join bu_re_posten on bu_re.renr=bu_re_posten.renr and bu_re.firmanr='$fn' 
left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr  and bu_re.firmanr='$fn' 
where bu_re.renr='$rn' and bu_re.firmanr='$fn';
";

//echo $request."<br>";

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

$request="select * from bu_mahn 
		  left join bu_re_layout on bu_re_layout.mahnstufe=bu_mahn.mahnstufe and bu_mahn.firmanr=bu_re_layout.firmanr 
		  where bu_mahn.renr='".$_POST['renr']."' and bu_mahn.firmanr='".$_SESSION['firmanr']."' and bu_re_layout.nr='".$row['layout']."'  order by bu_mahn.mahnstufe";


	
// echo $request."<br>";
$result = $db->query($request);
// $row_mahn = $result->fetch_assoc(); 

// var_dump($row);


// Mahnunungen bisher mit Text
$latest="";
$zeilen="";
$mahngebuehr=0;
while($row_mahn = $result->fetch_assoc()) {
	$m="Mahnumng";
	$m=$row_mahn['name'];
	$d=toDate($row_mahn['datum']);
	$f=toDate($row_mahn['faellig']);
	$g=$row_mahn['mahngebuehr'];
	$mahngebuehr+=$g;
	
	$latest_date=$row_mahn['faellig'];
	$latest_mahnstufe=$row_mahn['mahnstufe'];

	$zeilen.= "<tr>";
	$zeilen.= "<td>$m</td>";
	$zeilen.= '<td style="text-align:center">'.$d.'</td>';
	$zeilen.= '<td style="text-align:center">'.$f.'</td>';
	$zeilen.= '<td style="text-align:right">'.$g.' € </td>';
	$zeilen.= "</tr>";				
}
?>

<iframe id="PDF" style="position:absolute;right:0;bottom:0;display:none;width:210px;height:300px;scale: 1.0;"></iframe>
<!-- iframe id="PDF" style="position:absolute;right:0;bottom:0;display:none;width:200px;height:280px;"></iframe -->

<!-- iframe id="PRINTED"  style="display:none;" ></iframe-->
<!--
<iframe id="PRINTED"  style="display:none;" ></iframe>
-->

<center>
<form action="kunde_aktion.php" method="POST">

<div id="submenu_neu" style="height:10em;margin-right:5% !important;">
<div>
<h1>Rechnungsdaten</h1>
<b>Rechnungsnummer:</b><i id="renr"><?php echo $row['renr'];  ?></i><br>
<b>Rechnungsdatum:</b><i><?php echo toDate($row['datum']); ?></i><br>
<b>Fälligkeit:</b><i><?php echo toDate($row['faellig']);    ?></i><br>
<b>ursprünglicher Betrag: Netto: </b><i><?php echo sprintf("%.2f €",$row['netto']); ?></i><b> ,Brutto:</b><i><?php echo sprintf("%.2f €",$row['brutto']); ?></i><br>
<b>Betrag mit Mahnungen: Netto: </b><i><?php echo sprintf("%.2f €",($row['netto']+$mahngebuehr)); ?></i><b> ,Brutto:</b><i><?php echo sprintf("%.2f €",($row['brutto']+$mahngebuehr*1.19)); ?></i><br>
</div>

<div>
<h1>Kundendaten</h1>
<b>Kundennummer:</b><i id="kdnr"><?php echo $row['kdnr'];  ?></i><br>
<b>Firma:</b><i><?php echo $row['firmenname']; ?></i><br>
<b>Name:</b><i><?php echo $row['vorname'].' '.$row['nachname'];    ?></i><br>
<b>Adresse:</b><i><?php echo $row['strasse']; ?></i><br>
<b>Ort:</b><i><?php echo $row['plz'].' '.$row['ort'];    ?></i><br>
</div>
</div><!-- submenu -->

<div>
<table>
	<tr>
		<th>Art</th><th>Erstellung</th><th>Fällig</th><th>Mahngebühr</th>
	</tr>

	<?php
		echo $zeilen;
	?>	
</table><br>
<?php
$mahnvorlage=true;
if (date("Y-m-d") > $row['faellig']) {
	$request="select * from bu_re_layout where mahnstufe>$latest_mahnstufe and nr = ".$row['layout']." and firmanr='".$_SESSION['firmanr']."' order by mahnstufe limit 1";
	$result = $db->query($request);
	if ($row_next = $result->fetch_assoc()) {
		$msg='<b>Nächste Mahnstufe:<i>'.$row_next['name'].'</i><br>Zahlungsziel: <input type="date" value="'.date('Y-m-d',strtotime('+'.$row_next['zahlungsziel_dauer']. 'days')).'" name="input_mahndatum" id="mahndatum"></b>';
	} else {
		$msg='<b style="color:orange;">Keine weitere Mahnvorlagen.</b>';
		$mahnvorlage=false;
	}
} else {
	$msg='<b style="color:green;">Keine Mahnung nötig.</b>';
	
}
echo $msg;
echo "<br><br>";

/*
// $file="rechnung_print.php?renr=".$row['renr']."&mahnstufe=".($latest_mahnstufe+1)."&firmanr=".$values['firma'];	
$file="rechnung_print.php?renr=".$row['renr']."&mahnstufe=0&firmanr=5";	
echo "<script>";
echo "setPDF('$file');";
echo "</script>";
*/	

?>
<input type="hidden" value="<?php echo $row_next['mahnstufe'] ?>" name="mahnstufe" id="mahnstufe">
<input type="hidden" value="<?php echo $row['renr'] ?>" name="renr" >
<!--
<input type="submit" value="Neue Mahnung per Mail" name="mahnungmail"    style="margin-right:1%;font-size: 2em;">
<input type="submit" value="Neue Mahnung Drucken"  name="mahnungdrucken" style="margin-right:1%;font-size: 2em;">
<input type="submit" value="PDF Ansicht"           name="mahnungpdf"     style="margin-right:1%;font-size: 2em;">
-->
<?php if ($mahnvorlage) { ?>
<button type="submit" name="saveas" formaction="rechnung_pdf.php" formmethod="POST" formtarget="_self"><?php echo $row_next['name']; ?><br><br>Speichern</button>
<button type="button" name="drucken" onClick="printPDF()"><?php echo $row_next['name']; ?><br>für Versand<br>Drucken<br></button>
<?php } ?>
<br>
<!-- input type="submit" name="details" value="Details"><br-->
</div>

</form>
</center>

<script>
	function setPDF(pdf) {
		var ifr=document.getElementById("PDF");

		// ifr.style.width="210px";
		// ifr.style.height="280px";
		ifr.style.display="initial"; //"none"; // initial;
		// ifr.style.overflow="hidden";
		ifr.style.border="0px";
		ifr.contentWindow.location.replace(pdf);
	}
	function printPDF() {
	 	// document.getElementById("PDF").contentWindow.print();
		var renr=document.getElementById("renr").firstChild.nodeValue; // .value;
		var kdnr=document.getElementById("kdnr").firstChild.nodeValue;
		
		var mahndatum=document.getElementById("mahndatum").value; // firstChild.nodeValue;
		var mahnstufe=document.getElementById("mahnstufe").value; // firstChild.nodeValue;
		var file="rechnung_printed.php?renr="+renr+"&mahnstufe="+mahnstufe+"&faellig="+mahndatum;
		
		alert(file);
		var printed=document.getElementById("PRINTED");		
		printed.contentWindow.location.replace(file);
		document.location.reload(true);		
		// form.submit();	
	}	
</script>



<?php
showBottom();
if ($mahnvorlage) {
	$file="rechnung_print.php?renr=".$row['renr']."&mahnstufe=".$row_next['mahnstufe']."&firmanr=".$_SESSION['firmanr'];
	// echo $file;exit;
	echo "<script>";
	echo "setPDF('$file');";
	echo "</script>";
}
?>
