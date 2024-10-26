<?php
    require_once '../vendor/autoload.php';  
    use Dompdf\Dompdf; 
    $dompdf = new Dompdf();

if (isset($_GET['PDF'])) {
	if (empty($_GET['PDF'])) {
		$file='';
		$Attachment=false;
	} else {
		$file=$_GET['PDF'];
		$Attachment=true;
	}
	// echo "Hallo";
	// require_once '../vendor/autoload.php';  
    // use Dompdf\Dompdf; 
    // $dompdf = new Dompdf();

	$content="<html><body>Hallo<br><h1>Ach Ja</h1></body></html>";

	$dompdf->set_option('chroot', getcwd()); //assuming HTML file is in the root folder Damit die CSS Datei gefunden wird! 
	$dompdf->loadHtml($content); 
    $dompdf->setPaper('A4', 'portrait'); 
    $dompdf->render(); 
	$file="test.pdf";
	$dompdf->stream($file,array("Attachment"=>$Attachment));
} else { ?>
	<script>		

	window.onload = function() {
		var ifr = document.createElement("iframe");
		ifr.src="?PDF";
		ifr.id="PDF";
		ifr.style.width="200px";
		ifr.style.height="200px";
		//ifr.style.border="0px";
		// ifr.style.display="none";
		document.body.appendChild(ifr);
	}

	var PDF = function (File) {
		var PDFG=document.getElementById("PDF");
		if (File) {
			PDFG.src = "?PDF="+File;
		} else {
			PDFG.contentWindow.print();
		}
	}

	</script>
	<!-- form action="pdf-button.php" method="GET"-->
	<input type="submit" value="Download" onClick="PDF('test')">
	<input type="submit" value="Print" onClick="PDF();">
	<!-- /form-->
<?php	
}
		// ;

?>
		
	