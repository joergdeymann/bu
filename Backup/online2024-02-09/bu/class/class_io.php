<?php
class IO {

	/*
		Allgemeine Funktionen
	*/
	public function getBase64Image($img) {
		$path = $img;
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}
			 
	public function convertToBR($s) {
		$suchen = "/(\r\n|\n|\r|\t)/";
		$ersetzen = "<br>";
		return preg_replace($suchen,$ersetzen,$s);
	}
	
	public function toDate($date) {
		return date("d.m.Y",strtotime($row['datum'])); 
	}
	
}
$io= new IO();
?>
