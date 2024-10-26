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
	<script type="text/javascript">			
	window.onload = function ()	 {
		var ifr = document.createElement("iframe");
		ifr.src="?PDF";
		ifr.src = "rechnung_out.php?renr=20220023";
		ifr.id="PDF";
		ifr.style.width="200px";
		ifr.style.height="200px";
		//ifr.style.border="0px";
		// ifr.style.display="none";
		document.body.appendChild(ifr);
	}
	
	function setFrame(pdf) {
		// document.getElementById('FR').src="rechnung.php";	
		// document.getElementById('FR').contentWindow.location.assign("rechnung.php");	
		document.getElementById('FR').contentWindow.location.replace("rechnung.php");	
	}		

	function setPDF(pdf) {
		// document.getElementById('FR').src="rechnung.php";	
		// document.getElementById('FR').contentWindow.location.assign("rechnung.php");	
		document.getElementById('FR').contentWindow.location.replace(pdf);	
	}		

	var PDF = function (File) {
		var ifra;
		// newIframe();
/*		
		var PDFG=document.getElementById("PDF");
		PDFG.src = "rechnung.php?renr='20220023'";
		document.getElementById('PDF').contentWindow.location.assign("http://google.com");	
		document.getElementById('PDF').contentWindow.location.reload();
		// document.getElementById('PDF').contentWindow.location.reload();



		

		// reload_message_frame() ;
		window.frames['FR'].location.href.reload();
		*/

		// Verursacht fehler
		// ifra = document.getElementById("FR");
		//ifra.location.reload();

		// document.getElementById('zwei').src = 'drei.html';
		// setFrame(); // document.getElementById('FR').src="rechnung.php";	
		// document.getElementById('PDF').src="rechnung.php";	
		// document.getElementById('FR').contentWindow.location.assign("rechnung.php");	
		document.getElementById('FR').contentWindow.location.replace("rechnung.php");	
		document.getElementById('PDF').contentWindow.location.replace("rechnung.php");	
		// document.getElementById('FR').contentWindow.location.reload(); // läd den Ursprung
		// flush();
		// alert("Hallo");


		/*
		if (File) {
			PDFG.src = "?PDF="+File;
		} else {
			PDFG.contentWindow.print();
		}
		*/
	}

	</script>
	<iframe id="FR" src="rechnung_out.php?renr=20220023"></iframe>
	<iframe id="FR2" src="rechnung.php"></iframe>
	<!-- form action="pdf-button.php" method="GET"-->
	<script>PDF('test');</script>	
	<input type="submit" value="Download" onClick="PDF('Rechgnung')">
	<input type="button" value="Print" onClick="PDF();">
	<!-- /form-->
<?php	
}
		// ;

?>
		
	