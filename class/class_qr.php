<?php    
/*
 * PHP QR Code encoder
 *
 * Exemplatory usage
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
class QR {
	public $level='H'; // L M Q H 
	public $size='4'; // 1-10 
	private $paypal="";   // Paypal Link
	
	public function __construct () {
		include "qr/qrlib.php";    
	}
	
	public function getPaypalLink($name,$betrag,$waehrung="eur") {
		$name=trim($name);
		if (preg_match("/^https{0,1}:.*/",$name)) {
			if (substr($name,-1,1) != "/") {
				$name.="/";
			}
			$this->paypal="$name$betrag$waehrung";
		} else {
			$this->paypal="https://www.paypal.me/$name/$betrag$waehrung";
		}
		// $this->paypal="https://www.paypal.me/$name/$betrag";
		return $this->paypal;
	}
	
	// Livecode:
	// Paypal: https://paypal.me/Lplucalo@gmail.com/450eur
	// Banking App mit composer: composer require smhg/sepa-qr-data 
	//                           https://github.com/smhg/sepa-qr-data-php#migration-from-smhgsepa-qr
	
	public function get($data="") {
		// echo "A:".dirname(__FILE__)."<br>";
		// echo "B:".dirname(__FILE__."/..")."<br>";
		// echo "C:".pathinfo('..')."<br>";
		if (empty($data)) {
			if (empty($this->paypal)) return "";
			$data=$this->paypal;
		}			
		
			
		//set it to writable location, a place for temp generated PNG files
		$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
		$PNG_TEMP_DIR = 'tmp/';
		//html PNG location prefix
		$PNG_WEB_DIR = 'tmp/';
		//ofcourse we need rights to create temp dir
		if (!file_exists($PNG_TEMP_DIR))
			mkdir($PNG_TEMP_DIR);
		
		
		//processing form input: Qualität
		//remember to sanitize user input in real-life solution !!!
		$errorCorrectionLevel = $this->level; // L M Q H 
		$matrixPointSize      = $this->size;  // 1-10
		$filename = $PNG_TEMP_DIR.'qr'.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
// echo $filename."<br>";
// echo $PNG_WEB_DIR.basename($filename);

		QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
			
		// return Base64	
		// return $this->base64encode($filename);
		
		// display generated file
		// return '<img src="'.$PNG_WEB_DIR.basename($filename).'" alt="QR-Code">';  
		
		return $PNG_WEB_DIR.basename($filename);
	}
	
	public function getBase64($data="") {
		$path=$this->get($data);
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
// echo $path."<br>";
// echo $type."<br>";
// echo $base64."<br>";
// echo '<img src="'.$base64.'" alt="QR-Code">';  
// echt;
		return $base64;
	}

	public function getHTML($data="") {
		$filename=$this->get($data);
		return '<img src="'.$filename.'" alt="QR-Code">';  
	}
}
    