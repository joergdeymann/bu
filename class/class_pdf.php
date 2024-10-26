<?php
/*
	$output=new PDF();
	$output->setContent($content);
	$output->print();
*/
require_once '../vendor/autoload.php';  
use Dompdf\Dompdf; 
use Dompdf\Options; 

class PDF {
	/* 
		Klassenvariablen
	*/
	private $content="";
    private $dompdf;
	
	public function __construct() {
		$this->dompdf = new Dompdf();		
	}
	public function destruct() {
		unset($this->dompdf);
	}
	/* 
		meine Prioritaeten setzen
	*/
	private function init() {
		// echo "class_pdf():cwd=".getcwd();exit;
		// CWD = Rootverzeichnis ohne slash
		// zb: C:\PROG\XAMPP\htdocs\FazalTamiz\bu
		$dompdf = &$this->dompdf; // -> $dompdf = new Dompdf();

		
		// $dompdf->set_option('chroot', getcwd()); C:\PROG\XAMPP\htdocs\FazalTamiz\/Damit die CSS Datei gefunden wird! 

		$tmp=getcwd()."/tmp"; // ."/tmp"; // Fiunkltioniert nicht mit "/tmp" eventuell "tmp/" 
		$fontdir=getcwd().'/tmp';  // So wird dern FONT zwiachengespeichert, funktioniert nur im CWD
		// Unbedingt; !!! Rechte des verzeichnis auf JEDER LESEN SCHREIBEN SETZTEN
		// echo $fontdir;
		$font_temp_dir=$tmp;
		
		$dompdf->set_option('fontDir', $fontdir); 
		$dompdf->set_option('fontCache', $fontdir);

		$dompdf->set_option('tempDir',$tmp);
		// $dompdf->set_option('temp_dir',$tmp);
		$dompdf->set_option('enable_remote', TRUE);

		$dompdf->set_option('enable_css_float', TRUE); //erlaubt: zb: Float:left

		$dompdf->set_option('defaultMediaType', 'all');
		$dompdf->set_option('isFontSubsettingEnabled', true); // Erlaubt Schriften
		// $dompdf->set_option('defaultMediaType', 'all'); // Was amcht das ?

		
		$options = $dompdf->getOptions();
		$options->setFontCache($font_temp_dir);
		$options->set('isRemoteEnabled', true);
		// Stanadart, wenn alles geht wierder rein 
		$options->set('pdfBackend', 'CPDF');
		$options->setChroot([
			'resources/views/',
			$font_temp_dir,           
			getcwd()             
		]);
		
		/* CHROOT getCWD() damit die CSS gefunden wird */
// $this->content="<html><body>Hallo</body></html>";
// echo "class_pdf->init<br>";echo htmlspecialchars($this->content);exit;
		$dompdf->loadHtml($this->content); 
	    $dompdf->setPaper('A4', 'portrait'); 
	    $dompdf->render(); 
		// $dompdf->stream();

		// BasePath
		// setBasePath(): Sets the base path to include external stylesheets and images.
		// $basePath (string) – The base path to be used when loading the external resources URLs.
		// $dompdf->setBasePath("path-to-file");

		// $dompdf->stream("invoice.pdf",array("Attachment"=>0));
		// $dompdf->set_option('fontDir', '/path/to/font/storage/directory');
		// $dompdf->set_option('fontCache', '/path/to/font/cache/directory');
		
		// Garbage collection
		unset ($dompdf);
	}
	
	
	
	/*
		PDF Funktionen
	*/
	public function setContent($content) {
		$this->content=$content;
	}



	/* 
		ALLES in einem
		Drucken mit übergabe des HTML codes
	*/
	public function print($content="") {
		if (!empty($content)) {
			$this->setContent($content);
		}		
		// PDF Im Browser zum Drucken anzeigen
		// 0 = Preview
		// 1 = Download
		$this->init();
		$this->dompdf->stream("invoice.pdf",array("Attachment"=>0));
		// ob_clean();
		// flush();		
	}

	public function mail() {
		$this->init();
	}
	
	public function download($file="invoice") {
		$this->init();		


		/*
		$file_to_save= "pdf/".$file.".pdf";
		$file_to_save = "file.pdf";	
		// $file_to_save = '/home/stsnew/public_html/pdf/file.pdf';
		//save the pdf file on the server		
		file_put_contents($file_to_save, $this->dompdf->output()); 
		//print the pdf file to the screen for saving
		header('Content-type: application/pdf');
		header('Content-Disposition: inline; filename="file.pdf"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($file_to_save));
		header('Accept-Ranges: bytes');
		readfile($file_to_save);

		// header("location:about.html");
		// header("refresh: 15; url=index.php"); 
		
		*/


		$this->dompdf->stream($file.".pdf",array("Attachment" => true));






		// echo "Hallo";
		// echo '<meta http-equiv="refresh" content="2;URL=javascript:window.close()">';
		
		// PDF als Datei speichern
		// -> Später das Verzeichnus mit uebergeben Firma/Rechnung / R .... _mahnstufe .pdf
		// $filename="pdf/R".$re['renr'].".pdf";
	    // file_put_contents($filename, $output);	
	}

	public function saveas($filename) {
		$this->init();
		// PDF als Datei speichern
		// -> Später das Verzeichnus mit uebergeben Firma/Rechnung / R .... _mahnstufe .pdf
		// $filename="pdf/R".$re['renr'].".pdf";
	    // file_put_contents($filename, $output);	
	    $output = $this->dompdf->output();
		// $filename="pdf/R".$re['renr'].".pdf";
	    file_put_contents($filename, $output);	
		
	}
	public function view() {
	}
	
	/*
		Am Start des <BODY> aufrufen
		später:
		echo '<button type="button" name="drucken" onClick="printPDF()">Drucken<br>für<br>Versand</button>';
	
	*/
	public function iframeInit() {
		echo '
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
				}

				
				function printPDF() {
				 	document.getElementById("PDF").contentWindow.print();	
				}

			</script>
		';
	}

	/*
		Vor dem Print-Button ausführen
	*/
	public function iframePrintPrepare($firma,$renr,$mahnstufe) {
		$file="rechnung_print.php?renr=".$renr."&mahnstufe=".$mahnstufe."&firmanr=".$firma;	

		echo "<script>";
		echo "setPDF('$file');";
		echo "</script>";
	}
	
}

/*
$dompdf_options = array("default_media_type" => 'print', "default_paper_size" => 'a4', "enable_unicode" => DOMPDF_UNICODE_ENABLED, "enable_php" => DOMPDF_ENABLE_PHP, "enable_remote" => true, "enable_css_float" => true, "enable_javascript" => true, "enable_html5_parser" => DOMPDF_ENABLE_HTML5PARSER, "enable_font_subsetting" => DOMPDF_ENABLE_FONTSUBSETTING);
 $dompdf = new DOMPDF();
 $dompdf->set_options($dompdf_options);
 */

?>
