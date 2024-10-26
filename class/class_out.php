<?php
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
		$this->init();
		
	}
	
	/* 
		meine Prioritaeten setzen
	*/
	private function init() {
		$dompdf = &$this->dompdf; // -> $dompdf = new Dompdf();

		
		$dompdf->set_option('chroot', getcwd()); //Damit die CSS Datei gefunden wird! 

		$tmp=getcwd(); // ."/tmp"; // Fiunkltioniert nicht mit "/tmp" eventuell "tmp/" ?
		$dompdf->set_option('tempDir',$tmp);
		$dompdf->set_option('enable_remote', TRUE);
		$dompdf->set_option('enable_css_float', TRUE); //erlaubt: zb: Float:left

		$dompdf->loadHtml($this->content); 
	    $dompdf->setPaper('A4', 'portrait'); 
	    $dompdf->render(); 
		// $dompdf->stream();

		// BasePath
		// setBasePath(): Sets the base path to include external stylesheets and images.
		// $basePath (string) – The base path to be used when loading the external resources URLs.
		// $dompdf->setBasePath("path-to-file");

		// Garbage collection
		unset ($dompdf);
	}
	
	
	
	/*
		PDF Funktionen
	*/
	public function setContent($content) {
		$this->content=$content;
	}



	public function print() {	
		
		// PDF Im Browser zum Drucken anzeigen
		// 0 = Preview
		// 1 = Download
		$this->dompdf->stream("invoice.pdf",array("Attachment"=>0));
	}

	public function mail() {
	}
	
	public function download() {
		$this->dompdf->stream("invoice.pdf",array("Attachment"=>1));
		
		// PDF als Datei speichern
		// -> Später das Verzeichnus mit uebergeben Firma/Rechnung / R .... _mahnstufe .pdf
		// $filename="pdf/R".$re['renr'].".pdf";
	    // file_put_contents($filename, $output);	
		
		unset ($dompdf);
	}

	public function saveas($filename) {
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
	private function output() {
		$dompdf = &$this->dompdf; // -> $dompdf = new Dompdf();

		
		//  $html = file_get_contents("../FasilTamiz/text.html"); 
		// $cssdir=getcwd()."/".$layout['cssdir'];
		// CSS Dir aus dem Content rausfiltern und das Verzeichnis festlegen
		// was genau macht css FloaT
		$dompdf->set_option('chroot', getcwd()); //Damit die CSS Datei gefunden wird! 
		// $tmp=getcwd().'/tmp';
		// BasePath
		// setBasePath(): Sets the base path to include external stylesheets and images.
		// $basePath (string) – The base path to be used when loading the external resources URLs.
		// $dompdf->setBasePath("path-to-file");

		$tmp=getcwd(); // ."/tmp"; 
		$dompdf->set_option('tempDir',$tmp);
		$dompdf->set_option('enable_remote', TRUE);
		$dompdf->set_option('enable_css_float', TRUE); //erlaubt: zb: Float:left

		$dompdf->loadHtml($this->content); 
	    $dompdf->setPaper('A4', 'portrait'); 
	    $dompdf->render(); 
		// $dompdf->stream();
	
		// PDF Im Browser zum Drucken anzeigen
		// 0 = Preview
		// 1 = Download
		$dompdf->stream("invoice.pdf",array("Attachment"=>0));
	
		// PDF als Datei speichern
		// -> Später das Verzeichnus mit uebergeben Firma/Rechnung / R .... _mahnstufe .pdf
		// $filename="pdf/R".$re['renr'].".pdf";
	    // file_put_contents($filename, $output);	
		
		// Garbage collection
		unset ($dompdf);
	}
}
	*/
?>
