<?php
function pdf() {
	/* 
		function printPDF()
		Aufruf: Button 
		die Namen kontrollieren: 
			NAME: renr kdnr
			ID: PRINTED PDF
	*/

	
	if (empty($_POST['renr'])) {
			return;
	}

	echo "<script>";



	echo '
	function setPDF(pdf) {
		var ifr=document.getElementById("PDF");

		ifr.style.display="initial"; //"none"; // initial;
		ifr.style.border="0px";
		ifr.contentWindow.location.replace(pdf);
	}
	
	function printPDF() {
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
	';
	
	
	
	$file="rechnung_print.php?renr=".$_POST['renr']."&mahnstufe=0&firmanr=".$_SESSION['firmanr'];	
	echo "setPDF('$file');";
	echo "</script>";
}
?>
