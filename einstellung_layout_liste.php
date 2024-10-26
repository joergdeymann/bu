<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Einstellungen - Rechungslayouts");
$recnum=0;
if (isset($_POST['add'])) {
	// 1. Kopieren der Daten in der datenbank
	// 2. Kopieren der Dateien in einem neuen Verzeichnis
	$new_layoutnr=0;

	
	$request="select max(nr) as maxlayout from bu_re_layout where firmanr=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row=$result->fetch_assoc();
	if (isset($row['maxlayout'])) $new_layoutnr=$row['maxlayout']+1;
	
	
	$request="
	CREATE TEMPORARY TABLE tmp select * from bu_re_layout where firmanr=0 and nr=".$_POST['layoutnr'].";
	UPDATE tmp SET recnum = 0, nr=".$new_layoutnr.",firmanr=".$_SESSION['firmanr'].";
	INSERT INTO bu_re_layout SELECT * FROM tmp;";
	$result = $db->multi_query($request);
	while ($db->next_result()) {};

	// echo $request;exit;

	// 2.
	$layoutnr=$_POST['layoutnr'];
	$firmanr=$_SESSION['firmanr'];
	
	// copy('foo/test.php', 'bar/test.php');
	// $src="vorlage/0/$layoutnr/*";
	// $dest="vorlage/$firmanr/$new_layoutnr/";



	// shell_exec("cp -r $src $dest");
	// if (!file_exists("vorlage/$firmanr")) {
	// 	mkdir("vorlage/$firmanr","0777");
	// }
	// if (!file_exists("vorlage/$firmanr/$new_layoutnr")) {
	// 	mkdir("vorlage/$firmanr/$new_layoutnr","0777");
	// }

	$src="firma/0/vorlage/$layoutnr/*";
	$dest="firma/$firmanr/vorlage/$new_layoutnr/";
	
	if (!is_dir("firma/$firmanr/pdf")) {
		mkdir("firma/$firmanr/pdf",0777,true);
	}
	
	if (!is_dir("firma/$firmanr/vorlage/$new_layoutnr")) {
		mkdir("firma/$firmanr/vorlage/$new_layoutnr",0777,true);
	}

/*	
	if (!file_exists("firma/$firmanr")) {
		mkdir("firma/$firmanr",0777,true);
	}
	if (!file_exists("firma/$firmanr/vorlage")) {
		mkdir("firma/$firmanr/vorlage","0777");
		mkdir("firma/$firmanr/pdf","0777");
	}
	if (!file_exists("firma/$firmanr/vorlage/$new_layoutnr")) {
		mkdir("firma/$firmanr/vorlage/$new_layoutnr","0777");
	}
*/
	// copy($src,$dest);
	// shell_exec("cp -r $src $dest");
	// echo getcwd()."<br>";
	// echo __DIR__."<br>";
	foreach (glob($src) as $source) {
		// echo "Source:".$source."<br>";
		// echo "Target:".$dest.basename($source)."<br>";
		// if (!file_exists($source)) {
		//	echo "Datei nicht geunden";
		//} else {
		//	echo "Datei gefunden";
		// }
		// echo "<br>";

		// copy($source, "X99");
		copy($source, $dest.basename($source));
		

	}
}	





/*
	Originalmasse;
	height: 277mm;
	max-width: 210mm;
*/
//unten
function vorlage($row) {
	// echo '<div id="vorlage">';
	// echo '<div id="vorlage" style="height:277mm;width:210mm;transform: scale(0.1);display:inline-block;border:black solid 1px;float:right;">';
	// echo '<div id="vorlage" style="height:56mm;width:42mm;display:table;border:black solid 1px;float:left;margin:2px;">';
	// echo '';
	// $row['firmanr']=0; // Testweise
	echo '<button type="button" id="vorlage" onClick="setPDF(\'rechnung_layout_vorlage.php?firmanr='.$row['firmanr'].'&layout='.$row['nr'].'&mahnstufe='.$row['mahnstufe'].'\')">';
	// echo '<button type="button" id="vorlage" onClick="setPDF(\'index.php\')">';
	
	echo '<div id="vorlage">';
	echo '<p style="width:160px;margin:0;padding:0;overflow:hidden;white-space: nowrap;">'.$row['name'].'</p>';
	// echo $row['name'];
	echo '<div>';
	echo "Hier die Vorlage";
	// echo "<br>Firmnr: ".$row['firmanr']."<br>";
	// echo "layout: ".$row['nr']."<br>";
	echo '</div>'; // reale Vorlage
	echo '</div>'; // id=vorlage
	echo '</button>';
}

// oben
function vorlage2($row) {
	echo '<div id="vorlage2btn">';
	echo '<button type="button" id="vorlage" onClick="setPDF(\'rechnung_layout_vorlage.php?firmanr='.$row['firmanr'].'&layout='.$row['nr'].'&mahnstufe='.$row['mahnstufe'].'\')">';
	
	echo '<div id="vorlage">';
	echo '<p style="width:160px;margin:0;padding:0;overflow:hidden;white-space: nowrap;">'.$row['name'].'</p>';
	// echo $row['name'];
	echo '<div>';
	echo "Hier die Vorlage";
	echo '</div>'; // reale Vorlage
	echo '</div>'; // id=vorlage
	echo '</button>';

	echo '<br>';
	echo '<form action="einstellung_layout.php" method="POST" style="margin:0;padding:0;">';
	echo '<input type="hidden" name="nr" value="'.$row['nr'].'">';
	echo '<input type="hidden" name="mahnstufe" value="'.$row['mahnstufe'].'">';
	echo '<input type="hidden" name="layout_suchen" value="1">';
	
	echo '<button type="submit" style="margin-left:0px;min-height:0px;">';
	echo 'Bearbeiten';
	echo '</button>';
	echo '</form>';
	
	echo '</div>';
}
?>

<div id="left"><center>
<table style="width:100%;">
<tr><th style="font-size:28px;">verwendete Layouts</th></tr>
<!-- form action="einstellung_layout_liste.php" method="POST" style="width:100%" -->

<?php 
$nr="";
$request="select * from bu_re_layout where firmanr=".$_SESSION['firmanr']." order by nr,mahnstufe";
$result = $db->query($request);
while ($row = $result->fetch_assoc()) {
	if ($row['nr'] != $nr) {
		if ($nr!="") {
			// echo '</div>';
			echo '</th></tr>';
		}

		$nr=$row['nr'];
		echo '<tr><th align="center">Layout Nr '.$row['nr'].'</th></tr>';
		echo '<tr><th align="center" style="align-content:center;text-align:center;">';
		// echo '<div style="display:inline-block;align-content:center !important;text-align:center !important;border:green 2px solid;">'; // Buttons in der Mitte stellen
		
	}
	vorlage2($row);	
}
// echo '<input type="hidden" name="layoutnr" value="'.$row['nr'].'">';
// echo "</div>";
echo "</th></tr>";
/* zum Testen Ende */

?>
</table>

<br>


<table>
<tr><th style="font-size:28px;">mögliche Layout Vorlagen</th></tr>
<!-- form action="einstellung_layout_liste.php" method="POST" style="width:100%" -->

<?php 
function button_add($layoutnr) {
	echo '<form action="einstellung_layout_liste.php" method="POST">';
	echo '<input type="hidden" name="layoutnr" value="'.$layoutnr.'">';
	echo '<button name="add"  style="margin-left:0px;min-height:0px;"><br>Hinzufügen<br>&nbsp;</button>';
	echo '</form>';
	echo "</th></tr>";
}


$nr="";
$request="select * from bu_re_layout where firmanr=0 order by nr,mahnstufe";
$result = $db->query($request);
while ($row = $result->fetch_assoc()) {
	if ($row['nr'] != $nr) {
		if ($nr!="") {
			button_add($nr);
		}

		$nr=$row['nr'];
		echo '<tr><th align="center">Vorlage Nr '.$row['nr'].'</th></tr>';
		echo '<tr><th align="center" style="align-content:center">';
	}
	vorlage($row);	
}
// echo '<input type="hidden" name="layoutnr" value="'.$row['nr'].'">';
button_add($nr);
/* zum Testen Ende */

?>
</table>


<br>&nbsp;
</center></div>


<!-- 
               Rechte Seite - Layout anzeigen
transform:scale(0.5);
-->			 
<style>
iframe#PDF {
	margin:0;
	width:98%;
	min-width:0;
	aspect-ratio:21 / 29;
	
}

iframe#PDF_2 {
	position:fixed;
	margin-left:4%;
	max-width:inherit;
	width:30%;
	aspect-ratio:21 / 28;
}

iframe#PDF_vor2024 {
	border:1px red solid;
	position:fixed;
	bottom: 10px;
	right:10%;
	width:210mm;
	height: 320mm;
	/* scale: 0.45; */
	transform-origin: bottom right; 
	transform: scale(0.4);
}

iframe#PDF_original {
	border:1px red solid;
	position:fixed;
	right:calc(55% - 200mm);
	top: -320px;
	width:210mm;
	height: 320mm;
	/* scale: 0.45; */
	/* transform-origin: top right;*/ 
	transform: scale(0.45);
}

iframe#PDF3 {
	border:1px red solid;
	position:fixed;
	right:calc(55% - 180mm);
	top: -320px;
	width:210mm;
	height: 320mm;
	scale: 0.45;
}

iframe#PDF2 {
	border:1px red solid;
	position:fixed;
	right:9%;
	top:140px;
	width:37%;
	max-height:75%;
	zoom: 10%;	
	aspect-ratio: 21/28 !important;
	/* scale: 0.5; */
}

</style>  
  
<div id="right" style="text-align:center;"> <!-- overflow="hidden" -->
<!-- iframe id="PDF" style="max-width:calc(100% - 2px);max-height:calc(100% - 4px);height:calc(100% - 2px);aspect-ratio: 210 / 280;"></iframe -->
<!--
<iframe id="PDF" style="border:1px red solid;position:fixed;right:9%;top:calc(140px);width:37%;max-height:75%;aspect-ratio: 210 / 280 !important;"></iframe>
<iframe id="PDF" ></iframe>
-->

<!-- link rel="stylesheet" href="iframe_display_big.css"-->
<!-- div style="position:relative;width:100%;height:100%;background-color:green;"><div style="position:absolute;width:inherit;height:inherit;;background-color:yellow;">
<div style="position:fixed;max-width:inherit;width:inherit;height:inherit;background-color:red;">
</div></div></div-->
<!-- iframe id="PDF" ></iframe-->
<!-- div style="position:sticky">XXX</div-->
<!--	width:48.0%;
	height: calc(100% - 60px);
-->
<!-- GEHT: div style="position:fixed;max-width:inherit;width:38%;height:calc(80% - 60px);background-color:cyan;"></div-->
<div style="margin-left:4%;position:fixed;max-width:inherit;width:30%;aspect-ratio:21 / 30;">
<iframe id="PDF" ></iframe>
</div></div>

<script>
	function setPDF(pdf) {
		// alert("Hallo");
		var ifr=document.getElementById("PDF");
		// var pdf= "rechnung_out.php?renr=20220023";

		// ifr.style.width="200px";
		// ifr.style.height="280px";
		// ifr.style.display="initial"; //"none"; // initial;
		// ifr.style.overflow="hidden";
		// ifr.style.border="0px";
		// alert(pdf);
		// pdf=pdf+"#toolbar=0"; // Kopfzeile entfernen-> geht aber nicht, da die grösse nicht angepasst wird, später kann es aktiviert werden
		ifr.contentWindow.location.replace(pdf);
	}
	function printPDF(form) {
		//& alert("WTF");
	 	document.getElementById("PDF").contentWindow.print();
		var renr=document.getElementsByName("renr")[0].value;
		var kdnr=document.getElementsByName("kdnr")[0].value;
		var file="rechnung_printed.php?renr="+renr+"&kdnr="+kdnr;		
		var printed=document.getElementById("PRINTED");		
		printed.contentWindow.location.replace(file);
	}	
// setPDF("X");

</script>

<?php
showBottom();
?>
