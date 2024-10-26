<?php
class IO {
	// original geht nicht mit URLs, nicht in benutzung
	function file_exists($file_path) {
		if (!ini_get('allow_url_fopen')) {
			echo "allow_url_fopen is not enabled";
			exit;
		}

		$handle = fopen($file_path, 'r');
		$file_path = (!$handle) ? false : $file_path;
		fclose($handle);
		return $file_path;
	}
	
	// img/GLH/logo.jpg
	
	function file_get_contents($file) {
		//WARNUNG_E ist nicht mehr abgefangen in SESSION,php deswegen kann man den status jetzt abfragen
		
		/* Das Kostet Zeit besser die Warning zurücksetzten
		   15.03.2024
		if (filter_var($file, FILTER_VALIDATE_URL)) {
			// Ist URL
			$file_headers = @get_headers("https://www.die-deymanns.de/bu/rechnung_versenden.php");	
			if($file_headers[0] != 'HTTP/1.0 404 Not Found'){
				return "";
			}
		} else {
			// local file on Server
			if(!file_exists($file)) return "";
		}
		*/
		// error_reporting(E_ALL);
		// ini_set('display_errors', 1);         // PHP Fehler anzeigen
		// ini_set('display_startup_errors', 1); // Beim Starten von PHP

		$fd = fopen($file, "r");// or die("Unable to open file!");
		if (!$fd) {
			// echo "Datei nicht vorhanden:class_io:file_get_contents";exit;
			
			return "";
			// echo "Datei nicht vorhanden";exit;
		}
		// $data =fread($df,filesize($file));
		$data = "";
		while (!feof($fd)) {
			$data .= fread($fd, 1024);
		}
		fclose($fd);
		// echo $file.",".strlen($data)."<br>";

		return $data;
	}
	/*
		Allgemeine Funktionen
	*/
	public function getBase64Image($path) {
		if (empty($path)) return "";
		$data =$this->file_get_contents($path);
		if (empty($data)) {
			// echo "NICHT GEFUNGDEN".getcwd()."/".$path;exit;
			return "";
			// echo "Datei existiert nicht oder keine Internetverbiundung";exit;
		}
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}
/*
geht bei http:
	public function getBase64Image($img) {
		if (!$this->file_exists($img)) {
			// echo "NICHT GEFUNGDEN".getcwd()."/".$img;exit;
			return "";
			// echo "Datei existiert nicht oder keine Internetverbiundung";exit;
		}
		$path = $img;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		// echo "IO:$path<br>";		exit;
		// $data = file_get_contents($path);
		$data =$this->file_get_contents($path);
		//echo strlen($data);exit;		
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}
*/			 
	public function convertToBR($s) {
		$suchen = "/(\r\n|\n|\r|\t)/";
		$ersetzen = "<br>";
		return preg_replace($suchen,$ersetzen,$s);
	}
	
	public function toDate($date) {
		return date("d.m.Y",strtotime($row['datum'])); 
	}



	



	/*
		URL auseinanderteilen
		
		und so gehts:
		$url=$io->splitURL();
		// $url= splitURL();
		echo $url['protocol']."://".$url['dirname']."/".$url['filename'].".".$url['extension']."<br>";
		echo "CWD:".$url['protocol']."://".$url['dirname']."<br>";
	*/
	public function splitURL() {
			$protocol=((empty($_SERVER['HTTPS'])) ? 'http' : 'https');
			$path=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url=$protocol .'://'. $path;
			
			
			$pieces 	= parse_url($url);
			// $protocol   = $pieces['scheme']; // enthält "http"
			// $host  		= $pieces['host']; // enthält "www.example.com"
			// $path  		= $pieces['path']; // enthält "/dir/dir/file.php"
			// $query 		= $pieces['query']; // enthält "arg1=foo&arg2=bar"
			// $fragment 	= $pieces['fragment']; // ist leer, da getCurrentUrl() diesen Wert nicht zurückgibt

			$path=pathinfo($path); // Beispiel /www/htdocs/inc/lib_inc.php
									// dirname /www/htdocs/inc
									// basename  lib_inc.php
									// extension php 
									// filename lib_inc
									
			$pieces['protocol']=$protocol;
			foreach($path as $k => $v) {
				$pieces[$k]=$v;
			}
			$pieces['url']=$url;
			$pieces['host']=$_SERVER['HTTP_HOST'];
			
			
			return $pieces;
	}
}
// echo "io<BR>";
$io= new IO();
?>