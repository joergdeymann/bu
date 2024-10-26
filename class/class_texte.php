<?php
/*
Texte
=============
firmanr 
datei   ($_SERVER['SCRIPT_NAME']) z.B. menu.php
text
		feldname1=Text\rfeldname2=Text2\r
		-> array("feldname1" => "Text","feldname2" => "Text2");
*/


		
class Texte {
	private $db="";
	public $row=array();     // Daten aus der Tabelle
	public $row_pre=array(); // Standart Daten aus der php
	public $layout='$text:$input';       // Ausgabelayout
	public $firmanr=0;
	
	public function __construct(&$db) {
		$this->db=$db;
		$this->init();
		if(session_status() !== PHP_SESSION_ACTIVE) return;
		$this->get("menu"); // Alle übersetzungen für Menu
		$this->get();       // Alle Übersetzungen für das PHP 
		$this->layout = '<tr><th>$text</th><td>$input</td></tr>';
	}
	public function add($array) {
		foreach($array as $k => $v) {
			$this->row_pre[$k]=$v;
		}
		// array_push($this->row_pre,$array);
	}
	
	private function get($datei="") {
		if (empty($_SESSION['firmanr']))  {
			return;
		}
		if (empty($datei)) {
			$datei=basename($_SERVER['SCRIPT_NAME']);
		}
		$request="SELECT * from bu_texte where firmanr = '".$_SESSION['firmanr']."' and datei='".$datei."'";
		$result=$this->db->query($request);
		if ($row=$result->fetch_assoc()) {
		// echo "<br>Text=*".$row['text']."*";	
		$liste = preg_split('/(\r\n|\r|\n)/', $row['text']);	
		// echo "<pre>";
		// print_r($liste);exit;		
		// $liste=explode("\r\n",$row['text']);
			foreach($liste as $c) {
				if (!empty($c)) {
					list($k,$v)=explode("=",$c,2);
					// echo "C=$c, K=$k, V=$v<br>";
					$this->row[$k]=$v;
				}
			}
		}
		return $this->row;
	}
	private function init() {
		// Menuwerte beispiele / Feste Voreinstellungen
		$this->row=array();
		$this->row['artikel']="Artikel";
		$this->row['artikelliste']="Artikelliste";
		$this->row['btn_suchen']="Suchen";
		$this->row['btn_ja']="Ja";
		$this->row['btn_nein']="Nein";
		
	}
	
	// Für die Administration
	// insert($row) z.b. insert(array("name" => "Dein Name","vn" => "Dein Vorname));
	// oder
	// insert ($datei,$row) z.B. insert("adresse.php",array("name" => "Dein Name","vn" => "Dein Vorname));
	public function insert($datei="",$row="") {
		if (empty($row)) { // Bein einen Wert ist $row gemeint
			$row=$datei;
			$datei="";
		}
		$firmanr=&$this->firmanr;
		if (empty($datei)) {
			$datei=basename($_SERVER['SCRIPT_NAME']);
		}
			
		if (empty($firmanr)) {
			if (empty($_SESSION['firmanr'])) {
				return;
			}
			$firmanr=$_SESSION['firmanr'];
		}
		
		// $text="artikel=Diestleistung\r\nartikelliste=Liste Dienstleistungen";
		$text="";
		foreach($row as $k => $v) {
			$text.="$k=$v\r\n";
		}
		$request="INSERT INTO bu_texte set firmanr = '".$firmanr."',datei='".$datei."',text='".$text."'";
		$result=$this->db->query($request);
	}

	// Pure übersetzung aus der Datei
	public function translate($key,$data="")  {
		$html="";
		if (!isset($this->row[$key]) or  (isset($this->row[$key]) and !empty($this->row[$key]))) {
			$text="";
			if (!empty($this->row[$key])) $text=$this->row[$key];
			else if (!empty($this->row_pre[$key])) $text=$this->row_pre[$key];
			else $text=$key; // Erst mal
			// Nur debug: else echo "KEY: $key nicht vorhanden<br>";
			$html= $text;
			if (!empty($data)) {
				$html=str_ireplace('$data',$data,$html);
			} else {
				$html=str_ireplace('$data ','',$html); // Keine 2 Leerzeichen , sieht nicht gut aus
			}
				
		} else {
			// $html="XX".$key;
		}
		return $html;
	}
	
	
	// Einbiden von <input> und Tabellen Tags
	public function output($input,$key,$data="") {
		$html="";
		$text=$this->translate($key,$data);
		if (!empty($text)) {
			$html= str_replace('$text',$text,$this->layout);
			$html= str_replace('$input',$input,$html);
		}
		return $html;		
	}
	
}
?>