<?php
=============================================================================================================================
1. Speichern: rechnung_pdf.php
=============================================================================================================================
echo '<button type="submit" name="saveas" formaction="rechnung_pdf.php" formmethod="POST" formtarget="_self"><br>Speichern<br>&nbsp;</button>';
Gibt es überhaupt formtarget ?
Das kann ich 1:1 übernehmen




=============================================================================================================================
2. Mail: rechnung_versenden.php
	Kann So übernommen werden: Soll es aber überprüfen nicht das irgendwoeine Mail hingeht wo nicht soll
=============================================================================================================================
echo '<button type="submit" name="mailto" formaction="rechnung_versenden.php" formmethod="POST" formtarget="_blank">per<br>Mail<br>versenden</button>';


=============================================================================================================================
3. Drucken: rechnung.php
	Alternative = Rechnung speichern, dann kann sie gedruckt werden also nur wenn Javascript erlaubtr ist machen
=============================================================================================================================
-----------------------------------------------------------------------------------------------------------------------------
1. Button
-----------------------------------------------------------------------------------------------------------------------------
	this.blur()
		- xxx
		
	printPDF(this.form) 
		- Druck mit Javascript anstoßen
		- this.form = ohne Parameter übergeben
		
-----------------------------------------------------------------------------------------------------------------------------		
echo '<button type="button" name="drucken" onClick="this.blur();printPDF(this.form)" formaction="rechnung.php" formmethod="POST">Drucken<br>für<br>Versand</button>';

-----------------------------------------------------------------------------------------------------------------------------
2. Javascript Teil: printPDF(); 
   Dieser Teil war direkt nach den Buttons
-----------------------------------------------------------------------------------------------------------------------------

<script>
	function setPDF(pdf) {
		var ifr=document.getElementById("PDF");

		ifr.style.display="initial"; //"none"; // initial;
		ifr.style.border="0px";
		ifr.contentWindow.location.replace(pdf);
	}
	
	/* 
		Aufruf: Button 
		die Namen kontrollieren: 
			NAME: renr kdnr
			ID: PRINTED PDF
	*/
	function printPDF(form) {
	 	document.getElementById("PDF").contentWindow.print();
		
		var renr=document.getElementsByName("renr")[0].value;
		var kdnr=document.getElementsByName("kdnr")[0].value;
		var file="rechnung_printed.php?renr="+renr+"&kdnr="+kdnr;		
		var printed=document.getElementById("PRINTED");		
		printed.contentWindow.location.replace(file);
	}
	
	
	function closePDF() {
	 	document.getElementById("PDF").contentWindow.close();	
	}	
</script>

-----------------------------------------------------------------------------------------------------------------------------
2. Javascript Teil: PDF Drucken vorbereiten; 
   Dieser Teil war ganz am ende des PHP-Scripts
-----------------------------------------------------------------------------------------------------------------------------
if ($values['renr']) {
	$file="rechnung_print.php?renr=".$values['renr']."&mahnstufe=0&firmanr=".$values['firmanr'];	
	echo "<script>";
	echo "setPDF('$file');";
	echo "</script>";
}
-----------------------------------------------------------------------------------------------------------------------------
3. Javascript Teil: iframe mit Position und größe
	Dieer Teil ist nach dem Header(), also ganz am Anfang
-----------------------------------------------------------------------------------------------------------------------------
<iframe id="PDF" style="position:absolute;right:-100px;bottom:-300;display:none;width:420px;height:600px;transform:scale(0.5);origin:bottom left;"></iframe>
<!-- Dieser Frame ist für das Speichern über PHP -->
<iframe id="PRINTED"  style="display:none;" ></iframe>


-----------------------------------------------------------------------------------------------------------------------------
4. Drucken: abfertigung nach dem druckbutton;
-----------------------------------------------------------------------------------------------------------------------------
if (isset($_POST['drucken'])) {
	$request="update `bu_re` set versandart=2,versanddatum=CURRENT_DATE() where firmanr='".$_SESSION['firmanr']."' and renr='".$_POST['renr']."'";
	// echo $request."<br>";
	// echo $request;
	// echo $request;
	$result = $db->query($request) or die(mysql_fehler()); 
	if ($result) {
		$msg="Rechnung zu Versenden markiert:".date("d.m.Y")."<br>";
	}
}

