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
include "class/class_qr.php";  // zur Erzeugung von Paypal Link und Code

class Rechnung {
	public $db;

	// Schluessel
	private $renr="";        // Rechnungsnummer
	private $firma_nr=0;     // Recnumin der bu_firme Initialwert
	private $layout_nr=0;    // Layout Nummer
	private $mahnstufe=0;    // Mahnstiufe Nummer
	private $kdnr=0;         // Kuhndennummer fuer die Rechnung
	public  $typ=0;          // 0 = Rechnung, 1= Angebot
	
	// Datensaetze‚ alles später in klassen
	public $row_kunde=array();
	public $row_firma=array();
	public $row_re=array();
	public $row_layout=array();

	private $output_re=array();       // Alle Felder die für die Rechnung gebraucht werden
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
		
		$logo="";
		
		if (isset($this->row_layout['logo']) && $this->row_layout['logo']) {
			$logo=$this->row_layout['logo'];
		} else 
		if (isset($this->row_firma['logo']) && $this->row_firma['logo']) {
			$logo=$this->row_firma['logo'];
		}
		if (!empty($logo_sub)) {
			if (!preg_match("/^https{0,1}:\/\//",trim($this->row_firma['logo']))) {
				$logo=preg_replace("/\..*?$/","_$logo_sub$0",$logo);
			}
		} 
		//else echo $logo;exit;

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
		
	*/
	public function getFilenamePDF() {
		$file="firma/".$this->row_firma['recnum']."/pdf/RE".$this->renr;
		
		if ($this->mahnstufe > 0) {
			$file.=$this->mahnstufe;
		}
		$file.=".pdf";
		return $file;
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
		return($this->row_posten = $this->result_posten->fetch_assoc());
	}
	
	private function PostenGetRow(&$row) {
	
	}
	
	public function PostenGetAll(&$re) {
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
		
		foreach($summe_mwst as $k => $v ) {
			$re['summe_mwst'].= sprintf("%.2f",$v)." €<br>";
			$re['text_mwst'] .= sprintf("%.2f",$k)." %<br>";

			$re['text_summe_mwst'].= sprintf("%.2f",$v)." €<br>";
		}
	
		$re['summe_mwst'].="</div>";
		$re['text_mwst'].="</div>";
		$re['text_summe_mwst'].= "</div>";

		$re['text_summe_netto']=$re['summe_netto']." €";
		$re['text_summe_brutto']=$re['summe_brutto']." €";
		
		// if ($this->row_firma['skonto_prozent']>0) {
		if (!isset($re['skonto_prozent'])) {
			echo 'Bitte Skonto anpassen, sprich die Funktion <br>$rechnung->replaceContent<br> aufrufen';
			exit;
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

		$this->output_re = $re;
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

	/*	
		Input:
			$content = Inhalt der geändert werden soll
			$pre     = $pre['$'] zb: "abs" -> $abs['$']
			$relace  = each: 'feldname' => 'inhalt'
		
		Output:
			Veränderter $content 
	*/		
		
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
		// Layout ? auch ?
		
		/*
			Absenderdaten
		*/
		
		if (empty($this->row_firma['iname'])) {
			if (!empty($this->row_firma['vorname']) and !empty($this->row_firma['nachname'])) {
				$this->row_firma['iname']=$this->row_firma['vorname']." ".$this->row_firma['nachname'];
			} else 
			if (!empty($this->row_firma['vorname'])) {
				$this->row_firma['iname']=$this->row_firma['vorname'];
			} else
			if (!empty($this->row_firma['nachname'])) {
				$this->row_firma['iname']=$this->row_firma['nachname'];
			} else {
				$this->row_firma['iname']="";
			}
		}

		if (empty($this->row_firma['rname'])) {
			if (!empty($this->row_firma['iname'])) {
				$this->row_firma['rname']=$this->row_firma['iname'];
			} else 
			if (!empty($this->row_firma['aname'])) {
				$this->row_firma['rname']=$this->row_firma['aname'];
			} else {
				$this->row_firma['rname']="";
			}
		}
		// Ansprechpartner versuchen zu ermitteln
		if (empty($this->row_firma['aname'])) {
			if (!empty($this->row_firma['rname'])) {
				$this->row_firma['aname']=$this->row_firma['rname'];
			} else 
			if (!empty($this->row_firma['iname'])) {
				$this->row_firma['aname']=$this->row_firma['iname'];
			} else {
				$this->row_firma['aname']="";
			}
		}

/*
		if (empty($this->row_firma['rname'])) {
			if (!empty($this->row_firma['vorname']) and !empty($this->row_firma['nachname'])) {
				$this->row_firma['rname']=$this->row_firma['vorname']." ".$this->row_firma['nachname'];
			} else 
			if (!empty($this->row_firma['vorname'])) {
				$this->row_firma['rname']=$this->row_firma['vorname'];
			} else
			if (!empty($this->row_firma['nachname'])) {
				$this->row_firma['rname']=$this->row_firma['nachname'];
			} else {
				$this->row_firma['rname']="";
			}
		}
*/		
		// echo 		$this->row_firma['rname'];exit;

		$this->replaceContent($content,"abs",$this->row_firma);

		/*
			Kundendaten
		*/
		$this->replaceContent($content,"empf",$this->row_kunde);

		/*
			Rechnungsdaten und Zusatzangaben, Berechnungen
		*/
		// $re=$this->row_re;
		$re=$this->output_re;
		

		/*
			Vorher
			if (isset($empf['vorname']) && isset($empf['nachname']) && $empf['vorname'] && $empf['nachname'] ) {
				$re['name']=$empf['vorname']." ".$empf['nachname'];
			} else {
				$re['name']="";
			}
		*/

		$re['name'] = "";
		if (empty($this->empf['vorname']) || empty($this->empf['nachname'])) {
			if (!empty($this->empf['vorname'])) {
				$re['name'] = $re['vornmae'];
			}
			if (!empty($this->empf['vorname'])) {
				$re['name'] =$re['vornmae'];
			}
			$re['name']=""; // Damit das so ist wie vorher			
		} else {
			$re['name']=$empf['vorname']." ".$empf['nachname'];
		}
				
				
		
		$this->replaceContent($content,"re",$this->row_re);		
	}
	
}


//$c = new Rechnung();
?>