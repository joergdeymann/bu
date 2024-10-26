<?php
// include 'dbconnect.php';
include 'session.php';
include 'class/class_pdf.php';
include 'class/class_rechnung.php';
include 'class/class_io.php';
include 'class/class_mail.php';
include 'class/class_db_sendmail.php';

/*
$m='Gabel<img src=""><br></body>Und das ende';
$m=preg_replace("/(<.*?>)/is","$1\r\n",$m);
echo "<pre>";
echo htmlspecialchars($m);
echo "</pre>";

exit;
*/


/*
UPDATE `bu_sendmail` SET `content`="Sehr geehrte Damen und Herren!<br> Hiermit erhalte Sie die Rechnung RE$re['renr'].<br> <table> <!-- Posten Start --> <tr> <td>$pos['anz'] $pos['einheit']</td> <td>$pos['text']<br>$pos['beschreibung']</td> </tr> <!-- Posten Ende --> </tabe>" WHERE recnum=1
*/

/*
include "dbconnect.php";
$request="select * from bu_sendmail where recnum=1";
$result = $db->query($request);
$r = $result->fetch_assoc();
echo htmlspecialchars($r['content']);

// update bu_sendmail set content="Sehr geehrte Damen und Herren!<br> Hiermit erhalte Sie die Rechnung RE$re['renr'].<br> <table> <!-- Posten Anfang --> <tr> <td>$pos['anz'] $pos['typ']</td> <td>$pos['text']<br>$pos['beschreibung']</td> </tr> <!-- Posten Ende --> </tabe>" where recnum=1;
exit;
*/


$msg=""; //Meldungen zum, Benutzer 
$re = array();

$abs = array(
	'firma'     => "",
	'strasse'   => "",
	'plz'       => "",
	'ort'       => "",
	'vorname'   => "",
	'nachname'  => "",
	'inhaber'   => "",
	'bankname'  => "",
	'iban'      => "",
	'bic'       => "",
	'hrname'    => "",
	'hra'       => "",
	'ustid'     => "",
	'betriebsnr'=> ""
);	
$empf = array(
	'firma'     => "",
	'strasse'   => "",
	'plz'       => "",
	'ort'       => "",
	'vorname'   => "",
	'nachname'  => ""
);	
$layout = array(
	'logo' 	 => "",  // Dateiname HTTP-Link oder absolut/relativer name
	'anrede' => "",  // Anrede in der Rechnung
	'css'    => ""	 // Ort und Name des CSS-Files aus der Layout HTML
);

/*
	Einheiten für eine Postenzeile
*/
$einheiten_value=array(
 "Stunde",
 "Tour",
 "Frachtstück"
);

$einheiten_mz=array(
 "Stunden",
 "Touren",
 "Frachtstücke"
);

$zuschlag_value=array(
 "",
 "Nachtzuschlag",
 "Sonntagszuschlag"
);


$km_value=array(
  "",
 "pro KM",
 "Gesamt"
);


// $_GET['renr']="20220023";



//-----------------------------------------------------------------------------------

/* 
	Rechnungsnummer uebergeben
*/
if (isset($_GET['renr']) && $_GET['renr']) {
	$_POST['renr']=$_GET['renr'];
}
if (isset($_POST['renr']) && $_POST['renr']) {
} else {
	$msg="Keine Rechnung angegeben";
	echo $msg;
	exit;
}

/* 
	Mahnstufe uebergeben
*/
if (isset($_GET['mahnstufe']) && $_GET['mahnstufe']) {
	$_POST['mahnstufe']=$_GET['mahnstufe'];
} 
if (isset($_POST['mahnstufe']) && $_POST['mahnstufe']) {
} else {
	$_POST['mahnstufe']="0";
}
/* 
	Firmennummer(sich selbst als Firma) uebergeben
*/
if (isset($_GET['firmanr']) && $_GET['firmanr']) {
	$_POST['firmanr']=$_GET['firmanr'];
} 
if (isset($_POST['firmanr']) && $_POST['firmanr']) {
} else {
	if ($_SESSION['firmanr']) {
		
		$_POST['firmanr']=$_SESSION['firmanr'];
	} else {
		$_POST['firmanr']="0";
	}
}

$rechnung=new Rechnung();
$rechnung->setFirma($_POST['firmanr']);
$rechnung->setReNr($_POST['renr']);
$rechnung->setMahnstufe($_POST['mahnstufe']);

if ($rechnung->row_re['renr']) {
	$re['renr']     =$rechnung->row_re['renr'];
	$re['datum']    =$rechnung->getDate('datum');

	$re['kdnr']     =$rechnung->row_re['kdnr'];
	$re['faellig']  =$rechnung->getDate('faellig');
	
	$t=strtotime($rechnung->row_re['leistung']);
	$monate = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	$re['leistung']=$monate[date("n",$t)]." '".date("y",$t);
	
	$re['layout']=$rechnung->row_re['layout'];
	$re['firmanr']=$rechnung->row_re['firmanr'];
} else {
	$msg="Rechnung ".$_POST['renr']." nicht vorhanden!";
	echo $msg;
	exit;
}


/*
       Firmendaten
*/

foreach($abs as $k => $v) {
	$abs[$k]=$rechnung->row_firma[$k];
}

/*
	Layout einstellungen
*/
$layout['logo']=$io->getBase64Image($rechnung->getLogo()); 
$layout['anrede']=$rechnung->row_layout['retext'];
$layout['css']=$rechnung->layout['css'];

/*
	Empfängerdaten
*/
foreach($empf as $k => $v) {
	$empf[$k]=$rechnung->row_kunde[$k];
}


/*
	Posten
*/

	
/* 
		Outpoiut generieren
*/


$content=$rechnung->getHTML();
$content=$rechnung->PostenGetAll($re);

/*
	Layout Felder
*/
foreach($layout as $k => $v) {
		$xkey='$layout[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}

/*
	Absender infos
*/
foreach($abs as $k => $v) {
		$xkey='$abs[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}

/*
	empfaenger infos
*/
foreach($empf as $k => $v) {
		$xkey='$empf[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}
if (isset($empf['vorname']) && isset($empf['nachname']) && $empf['vorname'] && $empf['nachname'] ) {
	$re['name']=$empf['vorname']." ".$empf['nachname'];
} else {
	$re['name']="";
}

/*
	Rechnungsinfoirmationen
*/
foreach($re as $k => $v) {
	$xkey="\$re['$k']";
	$content=str_replace($xkey,$v,$content);
}


/*
	PDF erstellen
*/
// $rechnung->senden(2); //Versandart 2 = DRUCKEN
// echo $content;
$filename="R".$re['renr'];
$filename=$rechnung->getFilenamePDF();
// echo "<html><head><title>Hallo</title></head><body>XXX</body></html>";
// echo $content;

$output=new PDF();
$output->setContent($content);
$output->saveas($filename);
// echo "PDF erstellt:".$filename;
// exit;
// echo "PDF";

// $output->destruct();



/*
	Mailvorlage vorbereiten mit Inhalt aus Datenbank
*/
$dbmail = new db_sendmail($rechnung->db);
$dbmail->setMahnstufe($rechnung->getMahnstufe());
$dbmail->setVorlage(0);  //Erst mal auf 0 
$dbmail->init();

$sig=    $dbmail->getSignature();// echo htmlspecialchars($sig);exit;
$content=$dbmail->getContent();
$subject=$dbmail->getSubject();



$s1="<!-- Posten Start -->";
$s2="<!-- Posten Ende -->";
$search="/(".addslashes($s1).")(.*?)(".addslashes($s2).")/is";
if (preg_match($search,$content,$matches)) {	
	/*
	echo "<pre>";
	echo htmlspecialchars($matches[1])."<br>";
	echo htmlspecialchars($matches[2])."<br>";
	echo htmlspecialchars($matches[3])."<br>";
	echo "</pre>";
	exit;
	*/
	
	$pos_vorlage=$matches[2];
	$pos_insert="";
	$rechnung->PostenSelectExtend();
	
	while($r = $rechnung->PostenGet()) {
        if ($r['anz'] == 1) {
			$r['einheit']=$r['einheit_einzahl'];
		} else {
			$r['einheit']=$r['einheit_mehrzahl'];
		}


		
		$pos=$pos_vorlage;


		// $c = "[a-zA-Z0-9_-]*?";
		// $s = "/\[".$c.":(".$c."):".$c."\]/is";
		// $pos = preg_relpace("\\[.?*:(.?*):.?*\]\is","\$pos['$1']",$pos);

		// [XX:YY:ZZ] -> $pos['YY'];
		$sub="[^\]:]*?";
		$s="/\[".$sub.":(".$sub."):".$sub."\]/is";	
		$e="\$pos['\$1']";		

// echo "<pre>";
// echo htmlspecialchars($pos)."<br>";
// echo "</pre>";

		
		$r['beschreibung']=preg_replace($s,$e,$r['beschreibung']);
		$r['re_text']     =preg_replace($s,$e,$r['re_text']);

		$rechnung->replaceContent($r['beschreibung'],"pos",$r);
		$rechnung->fillContent($r['beschreibung']);

		$rechnung->replaceContent($r['re_text'],"pos",$r);
		$rechnung->fillContent($r['re_text']);


// echo "<hr><pre>";
// echo var_dump($r)."<br>";
// echo "</pre><hr>";
	
	
		$rechnung->replaceContent($pos,"pos",$r);
		$rechnung->fillContent($pos);

		$pos_insert.=$pos;
		
	}
	
	$pos_insert=str_ireplace("<br>","\r\n<br>",nl2br($pos_insert,0));
	/*
	echo "<pre>";
	echo "INSERT:<br>";	
	echo htmlspecialchars($pos_insert)."<br>"; 
	echo "INSERT-ENDE:<br>";
	echo "</pre>";
	*/	
	// ohne Bemerkung  
	// $content=preg_replace($search,"$1".$pos_insert."$3",$content);
	$content=preg_replace($search,$pos_insert,$content);
	/*
	echo "<hr>";
	echo "<pre>";
	echo htmlspecialchars($content);
	echo "</pre>";
	echo "<hr>";
	exit;
	*/
}


$rechnung->fillContent($sig);
$rechnung->fillContent($content);   
$rechnung->fillContent($subject);

// echo "ENDE:<br>";
// echo htmlspecialchars($content)."<br>"; 
// exit;
/*
	Mail versenden
*/
$m = new sendmail();
$m->setTo($rechnung->row_kunde['mail_dienst']); 
$m->setFrom($rechnung->row_firma['rmail'],$rechnung->row_firma['firma']); 
$m->addAttachment($filename); //Anhang -> Rechnung



$m->setSignature($sig);      // $Firma, Vorlage 0 Mahnstufe 0 -> Standart Signature für alle 
$m->setMessage($content);    // Firma , Vorlage, mahnstufe  -> Formatierungen anpassen
$m->setSubject($subject);    

// $m->setSignature("SIG");
// $m->setMessage("Hallo");
// $m->setSubject("XX");

echo $rechnung->row_kunde['mail_dienst']."<br>";
echo $rechnung->row_firma['rmail']."<br>";
echo $filename."<br><br><hr>";

// echo $m->testmail();exit;
	

if ($m->send()) {
	echo "Mail erfolgreich versendet";
} else {
	echo "Mail nicht erfolreich gessendet";
	echo "Fehler:<br>";
	print_r( error_get_last() );
}

?>