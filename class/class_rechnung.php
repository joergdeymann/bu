<?php 
/* Reihenfolge:
	0. Init
		$c=new Rechnung();
	1. Firma angeben und laden
		$c->setFirma(Firmennummer); 
	2. renr uebergeben:  
		$c->setReNr($renr);
	3. Mahnstufe festlegen
		$c->setMahnstufe(0);
		$c->setLatestMahnstufe();

    4. Benunutte datensätrze
	   $c->row_

	5. Rechnung-Vorlage laden aus Datei
		$c->getVorlage();

	X-depricated. rechnungslayout angeben
		$c->setLayout($layoutnr);

	// Beispiel
	$rechnung=new Rechnung();
	$rechnung->setFirma($_POST['firmanr']);
	$rechnung->setReNr($_POST['renr']);
	$rechnung->setMahnstufe($_POST['mahnstufe']);
*/
include_once "class/class_qr.php";  // zur Erzeugung von Paypal Link und Code
include_once "class/class_io.php";

class Rechnung {
	public $db;

	// Schluessel
	private $renr="";        // Rechnungsnummer
	private $firma_nr=0;     // Recnumin der bu_firme Initialwert
	private $layout_nr=0;    // Layout Nummer
	private $mahnstufe=0;    // Mahnstiufe Nummer
	private $kdnr=0;         // Kuhndennummer fuer die Rechnung
	public  $typ=0;          // 0 = Rechnung, 1= Angebo
	
	// Datensaetze‚ alles später in klassen
	public $row_kunde=array();
	public $row_firma=array();
	public $row_re=array();
	public $row_layout=array();

	public $output_kunde=array(); 
	public $output_firma=array(); 
	public $output_layout=array(); 
	public $output_re=array();       // Alle Felder die für die Rechnung gebraucht werden
									  // beinhaltet $row_re
									  // später eigenes array
									  // gespeichert in :
									  // abgerufen in: fillContent
	
									  
									  
	private $row_posten = array();    // Array des Datensatzes
	private $result_posten = array(); // Ergebnis von Select


	// Andere 
	public $layout=array(); // HTML, CSS Dateien
	
	private $htmlcontent;   // HTML Inhalt vom Layout bis zur fertigstellung
	
	public $paypal;         // Paypal QR Code und Link
	
	/* 
		1. PUBLIC FUNCTIONS
	*/
	/*
		FIRMA:  nicht angegeben-> $_SESSION['firma']  Ueberlegungen spaeter machen
	*/
	public function __construct($renr="",$firma_nr="") {
		// echo "Konstruct<br>";
		// global $db;
		$this->init(); // $this->$db=$db;
		

		if ($firma_nr == "") { 
			return;
		}
		$this->firma_nr=$firma_nr;		
		$this->loadFirma();
		// echo "Firmennummer: ".$this->firma_nr."<br>";
		
		if ($renr == "") {
			return;
		}
		$this->renr=$renr;
		$this->loadRechnung();

	}

	private function init() {
		$db=&$this->db;
		include "dbconnect.php";
		$this->paypal=new QR();
/*		
		$dbname = "bu"; 
		$user="php";
		$pw="#php#8.0-..";
		$host="localhost";
		// $host="192.168.64.2";
		// $user="root";
		// $pw="";

		$this->db = new mysqli($host, $user, $pw, $dbname);
		if ($this->db->connect_errno) {
		    die("Verbindung fehlgeschlagen: " . $this->db->connect_error);
		}
		$this->db->set_charset("utf8mb4");
*/
	}
	

	/* 
		allgemeine Funktionen
	*/
	public function getDate($date) {
		return date("d.m.Y",strtotime($this->row_re[$date])); 
	}
	
	/*
		BU_FIRMA
	*/
	public function setFirma($firmanr=0) {
		$this->firma_nr=$firmanr;
		$this->loadFirma();
	}
	

	/* 
		BU_RECHNUNG: Rechnungsdaten Setzen und Laden
	*/
	public function setReNr($renr) {
		$this->renr=$renr;
		$this->loadRechnung();
		$this->loadKunde();
		$this->loadAdresse();

		
		if ($this->row_re) {
			return true;
		} else {
			return false;
		}
		
		
	}

	/* 
	 Versenden der Rechnung
	 0 = nicht Versendet
	 1 = per mail
     2 = per Post
	 */ 
	public function versenden($versandart) {
		$this->senden($versandart);
	}
	public function senden($versandart) {
		$typ=$this->typ;
		// Als Versendet markieren
		$request="update `bu_re` set `versandart`='".$versandart."',`versanddatum`=CURRENT_DATE where firmanr='".$this->firma_nr."' and `renr` = '".$this->renr."' and `typ` = '".$this->typ."'";
		// echo "class_rechnung.php#senden:".$request;exit;
		$result = $this->db->query($request);
		// exit;
	}
	
	public function getMailMessage() {
		return "";
		
	}
	/*
		BU_RE_LAYOUT: setzen laden  
		-> fuer die weiteerverarbeitung
		(-> benoetigt: $firmas_nr, §renr, $mahnstufe fürs laden) 
	   da Layoit aus Firma oder Rechnung kommt, ist hier keine Aenderung nötig
		Deacktoiviert
	*/
	/*
	public function setLayout($layout=0) {
		$this->layout=$layout;
	}
	*/
	
	/*
		BU_RE_LAYOUT: setzen der Mahnstufe
		-> fuer die weiteerverarbeitung	
		(-> benoetigt: $firmas_nr, §renr, $mahnstufe fürs laden) 
	*/
	public function setMahnstufe($mahnstufe=0) {
		$this->mahnstufe=$mahnstufe;
		$this->loadLayout();	
		
	}
	public function getMahnstufe() {
		return $this->mahnstufe;
	}
	public function getTyp() {
		return $this->typ;
	}


	/*
    	Firmendaten/Rechnungsdaten -> Layout  
	*/
	public function getLogo($logo_sub="") {
		// echo "<br>$logo_sub<br>";
		$logo="";
		
		if (isset($this->row_layout['logo']) && $this->row_layout['logo']) {
			$logo=$this->row_layout['logo'];
		} else 
		if (isset($this->row_firma['logo']) && $this->row_firma['logo']) {
			$logo=$this->row_firma['logo'];
		}
		if (!empty($logo_sub)) {
			// echo "<br>class_rechnung.php->getLogo():".trim($this->row_firma['logo'])."<br>";
			// if (preg_match("/^https{0,1}:\/\//",trim($this->row_firma['logo']))) {
			$logo=preg_replace("/(^.+)(\..+$)/","$1_$logo_sub$2",$logo);
			// }
		} 
		// echo $logo;exit;

		return $logo;
	}
	
	public function getVorlage() {
		echo "GET VORLAGET() veraltet";
		exit;
		// Fusch:
		return $this->getHTML();
		// Fuschende
		return $this->layout['html']; // Dateiname der Vorlage
	}

	/*
		Dateiname für PDF herausfinden
		Später vielleichzt ein benutzerdefiniertes Muster ermöglichen
		ist das noch aktuell ?
	*/
	public function getFilenamePDF() {
		echo "Du bist in classe_rechnung.php#getFilenamePDF() gelandet. Bitte anpassen";
		exit;
		$file="firma/".$this->row_firma['recnum']."/pdf/RE".$this->renr;
		
		if ($this->mahnstufe > 0) {
			$file.=$this->mahnstufe;
		}
		$file.=".pdf";
		return $file;
	}
	
	//===========================================================================================================
	// Lokalen DATEINAME holenb: $filename für die PDF, zb in: rechnung_pdf.php
	//===========================================================================================================
	public function getFilename() {
		$rechnung=&$this;
		
		$filename=$rechnung->row_re['renr'];
		if ($rechnung->getMahnstufe() > 0) $filename.="-M".$rechnung->getMahnstufe();
		if ($rechnung->getTyp() == 1)      $filename=$rechnung->row_re['renr']."-AN";
		// echo $filename."<br>";
		// exit;
		
		return $filename;
	}
	
	public function getServerFilename() {
		$firmanr=$_SESSION['firmanr'];
		$dir="firma/".$firmanr."/pdf/";
		$filename=$dir.$this->getFilename().".pdf";
		
		// Dats Verzeichnis muss bei der Erstellung der Firma erstellt werden nicht hier testen
		if (!is_dir("firma/$firmanr/pdf")) {
			mkdir("firma/$firmanr/pdf",0777,true);
		}
		return $filename;
	} 
	
	
	/* 
		HTML Vorlage Laden und Pointer zurückgeben
	*/
	public function getHTML() {
		// echo "GET HTML():".htmlspecialchars($this->layout['html']);
		$this->htmlcontent=file_get_contents($this->layout['html']);
		// echo htmlspecialchars($this->htmlcontent)."YY";
		// exit;
		return $this->htmlcontent;
	}

	
	private function getMahngebuehr() {
		$request='SELECT sum(mahngebuehr) as mahngebuehr 
			from bu_re_layout 
			where bu_re_layout.mahnstufe <= '.$this->mahnstufe.'
			and bu_re_layout.firmanr="'.$this->firma_nr.'"'; // vorher $this->firmanr
		$result = $this->db->query($request);
		$row=$result->fetch_assoc();
		return($row['mahngebuehr']);
	}
	
	/*
		RECHNUNGs POSTEN
	*/	
	public function PostenSelectExtend() {
		$request ='SELECT * FROM `bu_re_posten` LEFT JOIN `bu_artikel` ON bu_re_posten.artikelnr = bu_artikel.artikelnr WHERE renr="'.$this->renr.'" and firmanr="'.$this->firma_nr.'" and bu_re_posten.typ="'.$this->typ.'" order by `pos`';
		// echo "POSTENSELECTExtend:".$request."<br>";exit;
		$this->result_posten = $this->db->query($request);
	}
	
	public function PostenSelect() {
		$request="select  from `bu_re_posten` where `firmanr` = '".$this->firma_nr."' and `renr` = '".$this->renr.'" and typ="'.$this->typ."' order by `pos`";
		$this->result_posten = $this->db->query($request);
	}
	public function PostenGet() {
		$this->row_posten = $this->result_posten->fetch_assoc();
// print_r($this->row_posten);exit;
/*
		foreach($this->row_posten as $k => $v) {        // #10.03.2024
 echo "$k = $v<br>";	
 			if (is_null($v)) $this->row_posten[$k]="";
		}
	exit;
	*/
		return($this->row_posten);
	}
	
	private function PostenGetRow(&$row) {
	
	}
	
	public function PostenGetAll(&$re=null) {
		$re=&$this->output_re;
		
		foreach($this->row_re as $k => $v) {
			if (isset($v)) {
				if (empty($re[$k])) $re[$k]=$v;
			}
		}
		// $this->output_re = &$re; 

		/*
			Postenbereich extrahieren
		*/

		$suche_start="<!-- POSTEN START -->";
		$suche_ende ="<!-- POSTEN ENDE -->";

		$suche="/".$suche_start."(.*?)".$suche_ende."/is";

		
		$result=preg_match($suche,$this->htmlcontent,$matches);
		if (!$result) return $this->htmlcontent; // Kein Posten in der Liste
		
		// $suche=$matches[0];

		$summe_netto=0;
		$summe_brutto=0;
		
		$content="";
		$this->PostenSelectExtend();
		
			
		/* 
			Einzelene Posten für Rechnung
		*/
		$mwst_max=0;
		while ($row = $this->PostenGet()) {
			// print_r($row);exit;
			$row['einheit']=$row['einheit_mehrzahl'];
			if ($row['anz'] == 1) {
				$row['einheit']=$row['einheit_einzahl'];
			}
			if ($row['mwst'] > $mwst_max) {
				$mwst_max=$row['mwst'];
			}
			
			/*
				Hier Berechnungen rein
				$re['summe'] stil
			*/
			
			$row['gesamt_netto']   	   = sprintf("%.2f",(float)$row['netto']        * (int)$row['anz']);
			$row['gesamt_mwst_betrag'] = sprintf("%.2f",(float)$row['gesamt_netto'] * (int)$row['mwst'] / 100);
			$row['gesamt_brutto']      = $row['gesamt_netto'] + $row['gesamt_mwst_betrag'];
			
			$row['text_gesamt_netto']   	 =  $row['gesamt_netto']." €";   	   
			$row['text_gesamt_mwst_betrag']  =	$row['gesamt_mwst_betrag']." €"; 
			$row['text_gesamt_brutto']       =	$row['gesamt_brutto']." €";      
			
			$mwst_satz  = sprintf("%.2f",$row['mwst']);
			$summe_netto += $row['gesamt_netto'];
			$summe_brutto += $row['gesamt_brutto'];

			// echo "EMPTY";
			if (empty($summe_mwst[$mwst_satz])) {
				$summe_mwst[$mwst_satz]=0;
			
			}
			$summe_mwst[$mwst_satz] += (float)$row['gesamt_mwst_betrag'];


			
			/* 
				Formatierungen für Zahlen innerhalb eines Postens
			*/
			$row['netto']=sprintf ("%.2f",$row['netto']);  // ." €";
			$row['text_netto']=sprintf ("%.2f",$row['netto'])." €";
			if ($row['gesamt_netto'] == $row['netto']) {
				$row['text_netto']="";
			}
			
			// $row['re_beschreibung']=nl2br();
			$content_posten=$matches[1];  // Fehler Undefined Array Key 1 
			foreach($row as $k => $v) {
				if (is_null($v)) $v=""; // #10.03.2024
				$xkey="\$re['$k']";
				$content_posten=str_ireplace($xkey,nl2br($v),$content_posten);	
			}
			// echo "<hr>".htmlspecialchars($row['beschreibung'])."<hr>";
			// echo htmlspecialchars($content_posten)."<br>";
			// exit;

			$content.=$content_posten;

		}
		// $this->htmlcontent = preg_replace($suche,$content,$this->htmlcontent);
		/*
			Mahnbereich wie ein Posten behandeln
		*/
		
		if ($this->mahnstufe > 0) {
			$vorlage=$matches[1];
			$row=array();
			$row['mwst']=sprintf("%.2f",$mwst_max); // $this->row_firma['mwst'];
			// $row['mwst']=$mwst_max; // $this->row_firma['mwst'];
			$row['re_text']="Mahngebühr";
			$row['re_beschreibung']="";
			$row['beschreibung']=""; // 17.06.2024
			$row['pos']="";
			$row['anz']=1;
			$row['einheit']="";
			$row['netto']=$this->getMahngebuehr();
			
			$row['gesamt_netto']   	   = sprintf("%.2f",(float)$row['netto']        * (int)$row['anz']);
			$row['gesamt_mwst_betrag'] = sprintf("%.2f",(float)$row['gesamt_netto'] * (int)$row['mwst'] / 100);
			$row['gesamt_brutto']      = $row['gesamt_netto'] + $row['gesamt_mwst_betrag'];
			
			$row['text_gesamt_netto']   	 =  $row['gesamt_netto']." €";   	   
			$row['text_gesamt_mwst_betrag']  =	$row['gesamt_mwst_betrag']." €"; 
			$row['text_gesamt_brutto']       =	$row['gesamt_brutto']." €";      
			
			$mwst_satz  = $row['mwst'];
			$summe_netto += $row['gesamt_netto'];
			$summe_brutto += $row['gesamt_brutto'];

			// echo "EMPTY";
			if (empty($summe_mwst[$mwst_satz])) {
				$summe_mwst[$mwst_satz]=0;
			
			}
			$summe_mwst[$mwst_satz] += (float)$row['gesamt_mwst_betrag'];

			//print_r($summe_mwst);exit;
			$row['netto']=sprintf ("%.2f",$row['netto']);  // ." €";
			$row['text_netto']=""; // =sprintf ("%.2f",$row['netto'])." €";
			$row['anz']="";
			
			
			$content_posten=$matches[1];  // Fehler Undefined Array Key 1 
			foreach($row as $k => $v) {
				$xkey="\$re['$k']";
				$content_posten=str_ireplace($xkey,nl2br($v),$content_posten);	
			}

			$content.=$content_posten;
			
			// exit;
			
		}
		$this->htmlcontent = preg_replace($suche,$content,$this->htmlcontent);
		


		
		
		
		
		/*
			Summen
		*/
	 	$re['summe_netto']  = sprintf("%.2f",$summe_netto);
		$re['summe_brutto'] = sprintf("%.2f",$summe_brutto);
		// $re['summe_mwst'] ='<div style="background-color:green;display:inline-block;margin:0;padding:0;margin-top:2px;;text-align:right;vertical-align:inherit;">';
		// $re['text_mwst'] = '<div style="background-color:green;display:inline-block;margin:0;padding:0;margin-top:2px;;text-align:right;vertical-align:inherit;">';
		$re['summe_mwst'] ='<div style="display:inline-block;margin:0;margin-left:0.5em;padding:0;text-align:right;">';
		$re['text_mwst'] = '<div style="display:inline-block;margin:0;margin-left:0.5em;padding:0;text-align:right;">';
		$re['text_summe_mwst'] = '<div style="display:inline-block;margin:0;margin-left:0.5em;padding:0;text-align:right;">';
		
		// Sonst keine Posten vorhanden
		if (isset($summe_mwst)) {
			foreach($summe_mwst as $k => $v ) {
				$re['summe_mwst'].= sprintf("%.2f",$v)." €<br>";
				$re['text_mwst'] .= sprintf("%.2f",$k)." % USt.<br>";

				$re['text_summe_mwst'].= sprintf("%.2f",$v)." €<br>";
			}
		}
		
		$re['summe_mwst'].="</div>";
		$re['text_mwst'].="</div>";
		$re['text_summe_mwst'].= "</div>";

		$re['text_summe_netto']=$re['summe_netto']." €";
		$re['text_summe_brutto']=$re['summe_brutto']." €";
		$mwst_all=(double)$re['summe_brutto']-$re['summe_netto'];
		$re['text_summe_mwst_gesamt']=sprintf("%.2f",$mwst_all)." €";
		
		// if ($this->row_firma['skonto_prozent']>0) {
		if (!isset($re['skonto_prozent'])) {
			// Zeilen auskommentiert 26.05.2024
			// Kommt hier rein, wenn Angebot erstellt wird und Skonto nicht definiert ist
			// echo 'Bitte Skonto anpassen, sprich die Funktion <br>$rechnung->replaceContent<br> aufrufen';
			// exit;
			$re['skonto_prozent']=0; // Passiert eigentlich nur bei Aufrufen die nicht vo Standart kommen.

		}
		if ($re['skonto_prozent']>0) {
			// $re['skonto_betrag']=$re['summe_brutto']*(100.00-$this->row_firma['skonto_prozent'])/100;
			$re['skonto_betrag']=$re['summe_brutto']*(100.00-$re['skonto_prozent'])/100;
			$re['skonto_text']=sprintf("%.2f",$re['skonto_betrag'])." €";
			/*
			echo  $re['summe_brutto'];
			echo "<br>";
			echo $this->row_firma['skonto_prozent'];
			echo "<br>";
			echo (100.00-$this->row_firma['skonto_prozent']);
			echo "<br>";
			echo $re['summe_brutto']*(100.00-$this->row_firma['skonto_prozent'])/100;
			echo "<br>";
			exit;
			*/
		} else {
			$re['skonto_betrag']=$re['summe_brutto'];
			$re['skonto_text']=sprintf("%.2f",$re['skonto_betrag'])." €";
			
		}
		
		/*
		echo htmlspecialchars($re['text_mwst']);
		echo "<br>";
		echo htmlspecialchars($re['text_summe_mwst']);
		echo "<br>";
		exit;
		*/
		$re['paypal_link']=$this->row_firma['paypal_link'];
		// echo $re['paypal_link'];exit;
		
		// $re['paypal_link']='deymanns'; // ################## wweg
		if (!empty($re['paypal_link'])) {
			$re['paypal_link_standart']=$this->paypal->getPaypalLink($re['paypal_link'],$re['summe_brutto']);		
			$re['paypal_qr_standart']=$this->paypal->getBase64();
		} else {
			$re['paypal_link_standart']="";
			$re['paypal_qr_standart']="";
		}

		if (!empty($re['bank_link'])) {
			$re['bank_link_standart']=""; // $this->paypal->getPaypalLink($re['paypal_link'],$re['summe_brutto']);		
			$re['bank_qr_standart']="";   // $this->paypal->getBase64();
		} else {
			$re['bank_link_standart']="";
			$re['bank_qr_standart']="";
		}

		// blödsinn weil oben steht: $re=&$this->output_re;
		// $this->output_re = $re;
		
		// Das ist eigentlich richtig ##### bei der Verarbeitungschaun
		// foreach($re as $v => $k) {
		// 	$this->re[$k]=$v;
		// }
		// echo $re['text_summe_netto'];
		// echo "<br>";
		// echo $this->output_re['text_summe_netto'];
		// echo "<br>";
		// exit;
		return $this->htmlcontent;
	}
		


	/* 
		2. PRIVATE FUNCTIONS
	*/
	/* --------------------------------------------------------------------------- */

	// Firmendaten laden der eigenen Firma
	// 0 = Standartfirma aussuchen
	/*
		FIRMA
	*/
	private function loadFirma() {
		if ($this->firma_nr == 0) {
			$request="select * from bu_firma where standart=1 limit 1"; ;		
		} else {
			$request="select * from bu_firma where recnum=".$this->firma_nr." limit 1"; 
		}
		$result = $this->db->query($request);
		$this->row_firma = $result->fetch_assoc();
		
		$this->layout_nr=$this->row_firma['rechnungs_layout']; //Standart Layout für Rechnungen , kann mnan hier auch weglassen
		$this->firma_nr=$this->row_firma['recnum'];         // Ausgewählte Firma
		// echo $this->row_firma['web'];exit;
	}
	
	private function loadAdresse() {
		// $m=$this->mahnstufe;
		$zuordnung=0;                           // Rechnung
		// if ($m == -1) $zuordnung=1;             // Angebot oder anderes
		if ($this->getTyp() == 1) $zuordnung=1; // Angebot
		
		
		$request="SELECT * from `bu_adresse` where `firmanr`='".$this->firma_nr."' and `zuordnung`='".$zuordnung."' and `kunde_recnum`='".$this->row_kunde['recnum']."'";
		$result=$this->db->query($request);
		// echo "$request<br>";// exit;
		if ($result->num_rows>0) {
			// echo "FOUND";exit // Bestimmte Felder für Adresse dürfen nicht mehr auftauchen
			// echo "gefunden";exit;
			$row=$result->fetch_assoc();
			$this->row_kunde['firma']=$row['name'];
			$this->row_kunde['firma_zusatz']=$row['name_zusatz'];
			
			$k="plz";			$this->row_kunde[$k]	 =$row[$k];
			$k="ort";			$this->row_kunde[$k]	 =$row[$k];
			$k="strasse";		$this->row_kunde[$k]	 =$row[$k];
			$k="strasse_zusatz";$this->row_kunde[$k]	 =$row[$k];
			$k="mail";			$this->row_kunde['rmail']=$row[$k];
			$k="ort";			$this->row_kunde[$k]	 =$row[$k];
			$k="tel1";			$this->row_kunde['rtel'] =$row[$k];
			$k="anrede";		$this->row_kunde[$k]	 =$row[$k];
			$k="vorname";		$this->row_kunde[$k]	 =$row[$k];
			$k="nachname";		$this->row_kunde[$k]	 =$row[$k];
			
			$rname ="";
			if (!empty($row['anrede']))   $rname.=$row['anrede']." ";
			if (!empty($row['vorname']))  $rname.=$row['vorname']." ";
			if (!empty($row['nachname'])) $rname.=$row['nachname'];
			$this->row_kunde['rname']=trim($rname); // Falls Anrede vorne fehlt oder Nachname am ende fehlt
			
			
			
			/*
			if (!empty($row['name_zusatz'])) $this->row_kunde['firma'].='<br>'.$row['name_zusatz'];
			$k="plz";		if (!empty($row[$k])) $this->row_kunde[$k]=$row[$k];
			$k="ort";		if (!empty($row[$k])) $this->row_kunde[$k]=$row[$k];
			$k="strasse";	if (!empty($row[$k])) $this->row_kunde[$k]=$row[$k];
			$k="mail";		if (!empty($row[$k])) $this->row_kunde['rmail']=$row[$k];
			$k="ort";		if (!empty($row[$k])) $this->row_kunde[$k]=$row[$k];
			$k="tel1";		if (!empty($row[$k])) $this->row_kunde['rtel']=$row[$k];
			// $k="tel2";		if (!empty($row[$k])) $this->row_kunde[$k]=$row[$k];
			$rname ="";
			if (!empty($row['anrede']))   $rname.=$row['anrede']." ";
			if (!empty($row['vorname']))  $rname.=$row['vorname']." ";
			if (!empty($row['nachname'])) $rname.=$row['nachname'];
			// $this->row_kunde['rname']=trim($rname); // Falls Anrede vorne fehlt oder Nachname am ende fehlt
			// echo "class_rechnung->loadAdresse:".$this->row_kunde['strasse'];exit;
			*/
		}
	}

	/*
		KUNDEN
	*/
	private function loadKunde() {
		$request="select * from bu_kunden where (auftraggeber=0 or auftraggeber='".$this->firma_nr."') and kdnr='".$this->kdnr."' limit 1";
		$result = $this->db->query($request);
		$this->row_kunde = $result->fetch_assoc();
	}
	
	/* 
		LAYOUT
	*/
	// LAYOUT / Layout mittels aktueller Mahnstufe laden
	
	private function loadLayout() {
		//echo "class_rechnung:loadLayout():Firma-nr:".$this->firma_nr."<br>";
		// echo "Layout-nr:".$this->layout_nr."<br>";
		// echo "Mahnstufe:".$this->mahnstufe."<br>";
		
		// Angebot mahnstufe -1
		if ($this->typ == 1) {
			$this->mahnstufe=-1;
		}
		
		// 1. Versuch: gibt es eine Rechnung speziell für diesen Kunden ?
		$request="SELECT * from bu_re_layout where firmanr=".$this->firma_nr." and nr='".$this->layout_nr."' and mahnstufe='".$this->mahnstufe."' and kdnr='".$this->row_kunde['kdnr']."'";
		$result = $this->db->query($request);
// echo "numrows:".$result->num_rows."<br>";
//echo "loadLayout:".$request;exit;
		if ($result->num_rows == 0) {
			// 2. Versuch: Gibt es eine Rechnung für die Firma, sollte es geben, ist derr Standart
			$request="SELECT * from bu_re_layout where firmanr=".$this->firma_nr." and nr='".$this->layout_nr."' and mahnstufe='".$this->mahnstufe."' and kdnr='0'";
			// echo "<br>class_rechnung:loadLayout:$request<br>";
			$result = $this->db->query($request);
			if ($result->num_rows == 0) {
				$request="SELECT * from bu_re_layout where firmanr=0 and nr='".$this->layout_nr."' and mahnstufe='".$this->mahnstufe."'";
				$result = $this->db->query($request);
				if ($result->num_rows == 0) {
					echo "class_rechnung:loadLayout()-ALTERNATIVES LAYYOUT nicht gefunden<br>$request<br>"; // -> jetzt ein Layout erstellen, eigentlich zu Beginn der 1. Installation erstellen,
					exit;
				}
			}
		}	
		$this->row_layout = $result->fetch_assoc();
		$this->setLayoutFile();		
	}

	// LAYOUT / Mahnstufe
	private function loadNextLayout() {
		echo "class_rechnung: loadNextLayout(): nicht fertig";
		exit;
		
		$request="SELECT * FROM bu_re_layout WHERE firmanr=".$this->firma_nr." and nr='".$this->layout_nr."' and mahnstufe > (SELECT max(mahnstufe) from bu_mahn where renr='".$this->renr."') order by mahnstufe limit 1";			
		$result = $this->db->query($request);
		$this->row_layout = $result->fetch_assoc();
		$this->setLayoutFile();		
	}
	
	// LAYOUT / Mahnstufe
	private function loadLastLayout() {
		echo "class_rechnung: loadNLastLayout(): nicht fertig";
		exit;
		$request="SELECT * from bu_re_layout where firmanr=".$this->firma_nr." and nr='".$this->layout_nr."' and mahnstufe = (SELECT max(mahnstufe) from bu_mahn where renr='".$this->renr."') order by mahnstufe limit 1";			
		$result = $this->db->query($request);
		$this->row_layout = $result->fetch_assoc();
		$this->setLayoutFile();		
	}
	
	//LAYOUT -> aus Firma oder Rechnung, beides muss geladen sein
	private function updateLayoutNr() {
		$this->layout_nr=$this->row_firma['rechnungs_layout']; //Standart Layout aus der Firma vorab einstellen
		if ($this->row_re) {
			if ($this->row_re['layout'] > 0) { 
				$this->layout_nr=$this->row_re['layout'];
			}
		}
		
	}
	//LAYOUT Logo bestimmen 
	// ist das nicht $this->layout['logo'] ??
	/* 
		Veraltet und nicht verwendet
		
 	private function setLogo() {
		$this->layout=$this->row_firma['logo']; //Standart Layout aus der Firma vorab einstellen
		if ($this->row_re_layout) {
			if ($this->row_re_layout['logo'] != "") { 
				$this->layout=$this->row_re_layout['logo'];
			}
		}
		
	}
	*/
	
	
	//LAYOUT: HTML und CSS
	//Das Texten der Dateien dauert zu lange, hier muss ich was kürzen
	//Die Datenbank abfragen und damit eine eindeutuge Vorlage nehmen
	
	private function setLayoutFile() {
		/*
			Aufbau alt:
			vorlage/Firmennummer/Layoutnr/rechnung<mahnstufe>.html
			
			Aufbau Neu:
			"firma"/Firmennummer/"vorlage"/Layoutnr/rechnung<mahnstufe>.html

			firma/0/vorlage/0/rechnung0.html      Absolute vorlage für alles was nicht Konfiguriert wurde -> Test OK
			firma/1/vorlage/0/rechnung0.html      1. Vorlage der 1. Firma
			firma/2/vorlage/0/rechnung0.html      1. Vorlage der 2. Firma
			firma/2/vorlage/1/rechnung0.html      2. Vorlage der 2. Firma
			
			firma/standart ist erstmal ein Entwurf, der nicht beutzt wird
						
		*/
		// cssfile: zugriff auf Fonts
		// ../../../font.ttf
		//## $dir0="vorlage/0/0"; // Wenn nichts anderes vorhanden
		//## $dir1="vorlage/".$this->row_firma['recnum']."/0";  // Firmenindividuell
		//## $dir2="vorlage/".$this->row_firma['recnum']."/".$this->row_layout['nr'];  // Firmenindividuell Layoiut individuell

		$dir0="firma/0/vorlage/0"; // Wenn nichts anderes vorhanden
		$dir1="firma/".$this->row_firma['recnum']."/vorlage/0";  // Firmenindividuell
		$dir2="firma/".$this->row_firma['recnum']."/vorlage/".$this->row_layout['nr'];  // Firmenindividuell Layoiut individuell
		
		$dir="";	
		$file0="/rechnung0.html";
		$file="/rechnung".$this->mahnstufe.".html";
		$css_file="/rechnung.css";
		if ($this->typ == 1) {
			$file="/angebot.html";
			$file0="/angebot.html";		
		} 
	
		if (!empty($this->row_layout['vorlage'])) {
			$dir="";
			$file=$this->row_layout['vorlage'];
			$css_file="";		
		} else
		if (file_exists($dir2.$file)) {  
			$dir=$dir2;
		} else
		if (file_exists($dir1.$file)) {  
			$dir=$dir1;
		} else
		if (file_exists($dir0.$file)) {  
			$dir=$dir0;
		} else
		if (file_exists($dir0.$file0)) {  
			$dir=$dir0;
			$file=$file0;
		}
			
		
	
		$this->layout['css']=$dir.$css_file;
		$this->layout['html']=$dir.$file; 	
// echo 		$this->layout['html'];
// exit;
	}

	
	/*
		RECHNUNG
	*/
	// RECHNUNG laden
	private function loadRechnung() {
		$request="SELECT * from bu_re where renr='".$this->renr."' and firmanr='".$this->firma_nr.'" and typ="'.$this->typ."' limit 1";
		// echo $request."<br>";
		$result = $this->db->query($request);
		$this->row_re = $result->fetch_assoc();
		// var_dump($this->row_re);		
		$this->kdnr=$this->row_re['kdnr'];
		$this->updateLayoutNr();
	}

	
	
	
	
	//------------------------------------------------------------------------------------------------------
	//	Rechnungs Daten vorbereiten: RE
	//------------------------------------------------------------------------------------------------------	
	private function setupRE() {
		$re=&$this->output_re;
		// $re=array();
		$rechnung=&$this;
		
		foreach($rechnung->row_re as $k => $v) {
			$re[$k]=$rechnung->row_re[$k];
		}

		if (empty($rechnung->row_re['renr'])) {
			$msg="Rechnung ".$rechnung->row_re['renr']." nicht vorhanden!";
			echo $msg;
			exit;
		}

		$re['datum']    =$rechnung->getDate('datum');
		$re['faellig']  =$rechnung->getDate('faellig');
		
		$t=strtotime($rechnung->row_re['leistung']);
		$monate = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
		$re['leistung']="";
		$re['leistung_text']="";
		$re['leistung_text2L']="";
		$re['leistung_von']="";
		$re['leistung_bis']="";

		if (!empty($rechnung->row_re['leistung'])) {		
			$dt_von=new DateTime($rechnung->row_re['leistung']);
		} 

		if (!empty($rechnung->row_re['leistungbis'])) {		
			$dt_bis=new DateTime($rechnung->row_re['leistungbis']);
		}
		
		
		// Fehlende Werte versuchen anzupassen 27.05.2024
		if (!empty($dt_von) and empty($dt_bis) ) {
			$dt_bis=$dt_von;
		}
		if (empty($dt_von) and !empty($dt_bis) ) {
			$dt_von=$dt_bis;
		}
		if (empty($dt_von) and empty($dt_bis) ) {
			$dt_bis=new DateTime();
			$dt_von=new DateTime();
		}
		// Ende Fehlende Werte 27.05.2024
		
		switch($rechnung->row_firma['re_input_leistung']) {
			case 0: 
					break;
			case 1: 
					$re['leistung']=$monate[date("n",$t)]." '".date("y",$t);
					$re['leistung_text']="vom". $monate[date("n",$t)]." '".date("y",$t);
					$re['leistung_text2L']=$re['leistung_text'];
					break;
			case 2: 
					$re['leistung']=date("W",$t)." ".date("Y",$t);
					$re['leistung_text']="der Woche ".date("W",$t)." ".date("Y",$t);
					$re['leistung_text2L']=$re['leistung_text'];
					break;
			case 3: 
					$re['leistung']=$dt_von->format("d.m.Y")." bis ".$dt_bis->format("d.m.Y");
					$re['leistung_von']=$dt_von->format("d.m.Y");
					$re['leistung_bis']=$dt_bis->format("d.m.Y");
					$re['leistung_text']="von ".$dt_von->format("d.m.Y")." bis ".$dt_bis->format("d.m.Y");
					$re['leistung_text2L']="von ".$dt_von->format("d.m.Y")."<br>bis ".$dt_bis->format("d.m.Y");
					
					if (empty($rechnung->row_re['leistungbis']) or ($rechnung->row_re['leistung'] == $rechnung->row_re['leistungbis'])) {
						$re['leistung']=$dt_von->format("d.m.Y");
						$re['leistung_text']="vom ".$dt_von->format("d.m.Y");
						$re['leistung_text2L']=$dt_von->format("d.m.Y");
					}
					
					
					break;
			case 4: 
					$re['leistung']=$dt_von->format("d.m.Y");
					$re['leistung_text']="vom ".$dt_von->format("d.m.Y");
					$re['leistung_text2L']=$re['leistung_text'];
					break;
		}
			
		
		return $re;
	}
	
	//------------------------------------------------------------------------------------------------------
	//	Firmendaten vorbereiten: ABS
	//------------------------------------------------------------------------------------------------------
	private function setupFirma() {
		$abs=&$this->output_firma;
		// $abs=array();
		$rechnung=&$this;
		foreach($rechnung->row_firma as $k => $v) {
			$abs[$k]=$v;
		}



		if (empty($abs['iname'])) {
			if (!empty($abs['vorname']) and !empty($abs['nachname'])) {
				$abs['iname']=trim($abs['vorname']." ".$abs['nachname']);
			} else 
			if (!empty($abs['vorname'])) {
				$abs['iname']=$abs['vorname'];
			} else
			if (!empty($abs['nachname'])) {
				$abs['iname']=$abs['nachname'];
			} else {
				$abs['iname']="";
			}
		}

		
		if (empty($abs['rname'])) {
			if (!empty($abs['iname'])) {
				$abs['rname']=$abs['iname'];
			} else 
			if (!empty($abs['aname'])) {
				$abs['rname']=$abs['aname'];
			} else {
				$abs['rname']="";
			}
		}
		
		
		// Ansprechpartner versuchen zu ermitteln
		if (empty($abs['aname'])) {
			if (!empty($abs['rname'])) {
				$abs['aname']=$abs['rname'];
			} else 
			if (!empty($abs['iname'])) {
				$abs['aname']=$abs['iname'];
			} else {
				$abs['aname']="";
			}
		}

		$abs['inhaber']=$abs['iname'];

		// #### Mails kann man noch optimieren
		if (empty($abs['imail'])) $abs['imail']="";
		if (empty($abs['itel'])) $abs['itel']="";
		if (empty($abs['rmail'])) $abs['rmail']=$abs['imail'];
		if (empty($abs['rtel'])) $abs['rtel']=$abs['itel'];
		if (empty($abs['rmail'])) $abs['rmail']=$abs['amail'];
		if (empty($abs['rtel'])) $abs['rtel']=$abs['atel'];



		return $abs;
	}
	
		//------------------------------------------------------------------------------------------------------
		//	Layout einstellungen LAYOUT
		//------------------------------------------------------------------------------------------------------
	private function setupLayout($loadFile=true) {
		// $io=&$this->io;
		// sonst vielleicht 
		global $io; // So geht es : wird für getBase64Image benötigt
		
		
		// echo "class_rechnung_setupLayout_vor";
		// exit;
		// include "class/class_io.php";
		// echo "class_rechnung_sertupLayout_nach";
		$layout=&$this->output_layout;
		// $layout=array();
		$rechnung=&$this;
		
		foreach($rechnung->row_layout as $k => $v) {
			$layout[$k]=$v;
		}
		
		if ($loadFile) {
			$layout['logo']=$io->getBase64Image($rechnung->getLogo());   // Die Umwandlung kostet Zeit
			$layout['logo_trans']=$io->getBase64Image($rechnung->getLogo("trans")); // Diese sollte dann nur auf anfrage gestartet werden
		}
		$layout['anrede']=$rechnung->row_layout['retext']; // #### Rechnungstext und anrede sollten unterschiedlich sein
		$layout['css']=$rechnung->layout['css'];

		return $layout;
	
	}

//------------------------------------------------------------------------------------------------------
//	Empfängerdaten/Kunde einstellungen
//------------------------------------------------------------------------------------------------------
	private function setupKunde() {
		$empf=&$this->output_kunde;
		// $empf=array();
		$rechnung=&$this;

		foreach($rechnung->row_kunde as $k => $v) {
			$empf[$k]=$v;
		}
		// Angepasste Firmenadresse
		if (empty($empf['adresse'])) {
			$empf['adresse']="";
			if (!empty($empf['rname'])) {
				// echo "X".$empf['rname']."X";exit;
				$empf['adresse'].=$empf['rname']."<br>";
				$empf['name']=$empf['rname'];
			}
			$empf['adresse'].=$empf['firma']."<br>";
			if (!empty($empf['firma_zusatz'])) {
				$empf['adresse'].=$empf['firma_zusatz']."<br>";
			}
			$empf['adresse'].=$empf['strasse']."<br>";
			// echo "Setup Kunde (".$empf['strasse_zusatz'].")";exit;
			if (!empty($empf['strasse_zusatz'])) {
				$empf['adresse'].=$empf['strasse_zusatz']."<br>";
			}
			$empf['adresse'].="<br>";
			
			$empf['adresse'].=$empf['plz'].' '.$empf['ort']."<br>";
// echo htmlspecialchars($empf['adresse']);exit;
			// unset ($empf['adresse']);
			// echo "classe_rechnung->setupKunde:".$empf['adresse'];exit;
		}

		if (empty($empf['name'])) {
			if (isset($empf['vorname']) && isset($empf['nachname']) && $empf['vorname'] && $empf['nachname'] ) {
				$empf['name']=$empf['vorname']." ".$empf['nachname'];
			} else {
				$empf['name']="";
			}
		}
		return $empf;
	}
	
	
	//------------------------------------------------------------------------------------------------------
	// Skonto Kunde(empf) / Firma(abs) einstellungen
	//------------------------------------------------------------------------------------------------------
	private function setupMix() {
		$empf=&$this->output_kunde;
		$re  =&$this->output_re;
		$abs =&$this->output_firma;
		
		if ($empf['skonto_prozent'] == -1) {
			$re['skonto_prozent']=$abs['skonto_prozent'];
			$re['skonto_tage']=$abs['skonto_tage'];	
		} else {
			$re['skonto_prozent']=$empf['skonto_prozent'];
			$re['skonto_tage']=$empf['skonto_tage'];
		}
		if ($re['skonto_prozent']>0) {
			$dt=new DateTime($re['datum']."+".$re['skonto_tage']." days");
			$re['skonto_datum']=$dt->format("d.m.Y");
		} else {
			$dt=new DateTime($re['datum']);
			$re['skonto_datum']=$dt->format("d.m.Y");
		}
		$re['name']=$empf['name'];
		
	}
	
	/*	
		Input:
			$content = Inhalt der geändert werden soll
			$pre     = $pre['$'] zb: "abs" -> $abs['$']
			$replace = each: 'feldname' => 'inhalt'
		
		Output:
			Veränderter $content 
	*/		
		
	// Wo wird es verwendet?	// lieber fillContent nehmen !!
	
	public function replaceContent(&$content,$pre,&$replace) {
		foreach($replace as $k => $v) {
			if (!isset($v)) {
				$v="";
			}
			$s = "/\\\$".$pre."\['".$k."'\]"."/is";
			$content=preg_replace($s,$v,$content);
		}
		// echo $content."<br>";

	}
	
	public function fillContent(&$content) {
		$this->setupFiller($content);
		return;
	}

	private function setupFiller(&$content) {
		$rechnung=&$this;
		
		/*
			Layout Felder
		*/
		foreach($rechnung->output_layout as $k => $v) {
			// echo "$k => $v<br>";exit;
			if (isset($v)) {
				$xkey='$layout[\''.$k.'\']';
				// echo "$xkey.<br>";
				$content=str_replace($xkey,$v,$content);
			}
		}

		/*
			Absender infos
		*/
		foreach($rechnung->output_firma as $k => $v) {
			if (isset($v)) {
				$xkey='$abs[\''.$k.'\']';
				// echo "$xkey.<br>";
				$content=str_replace($xkey,$v,$content);
			}
		}

		/*
			empfaenger infos
		*/
		foreach($rechnung->output_kunde as $k => $v) {
			if (isset($v)) {
				$xkey='$empf[\''.$k.'\']';
				// echo "$xkey.<br>";
				$content=str_replace($xkey,$v,$content);
			}
		}

		/*
			Rechnungsinfoirmationen
		*/
		foreach($rechnung->output_re as $k => $v) {
			if (isset($v)) {
				$xkey="\$re['$k']";
				$content=str_replace($xkey,$v,$content);
			}
		}
	}
	
	
	public function getSetup($loadFile=true) {
		$this->setupRE();           	//echo microtime()." Content RE<br>";
		$this->setupFirma();        	//echo microtime()." Content Firma<br>";
		$this->setupLayout($loadFile); 	//echo microtime()." Content lAYOUT<br>";
		$this->setupKunde();        	//echo microtime()." Content kUNDE<br>";
		$this->setupMix();          	//echo microtime()." Content MIX<br>";

		$this->getHTML();             // HTML Vorlage laden
		$this->PostenGetAll();          // Posten samt getauschter Inhalt hinzufügen
	}
	
	public function getContent() {
		// echo microtime()." Content Anfang<br>";

		$this->setupRE();           //echo microtime()." Content RE<br>";
		$this->setupFirma();        //echo microtime()." Content Firma<br>";
		$this->setupLayout();       //echo microtime()." Content lAYOUT<br>";
		$this->setupKunde();        //echo microtime()." Content kUNDE<br>";
		$this->setupMix();          //echo microtime()." Content MIX<br>";
		// echo microtime()." Content Setup<br>";
		
		// Aus Vorlage die Werte tauschen
		$content=$this->getHTML();             // HTML Vorlage laden
		$content=$this->PostenGetAll();        // Posten samt getauschter Inhalt hinzufügen
		
		$this->setupFiller($content);          // Restlichen Werte tauschen abs / empf / layout / re
		return $content;

	}
		
	private function set(&$v="") {
		$r="";
		if (!empty($v)) $r=$v;
		return $r;
	}
}


//$c = new Rechnung();
?>