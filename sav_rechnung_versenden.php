<?php
// include 'dbconnect.php';
include 'session.php';
include 'class/class_pdf.php';
include 'class/class_rechnung.php';
include 'class/class_io.php';
include 'class/class_mail.php';
include 'class/class_db_sendmail.php';
include 'menu.php';


$msg=""; //Meldungen zum, Benutzer 
$re = array();

$abs = array(
	'firma'     => "",
	'strasse'   => "",
	'plz'       => "",
	'ort'       => "",
	'vorname'   => "",
	'nachname'  => "",
	'web'       => "",
	'inhaber'   => "",
	'bankname'  => "",
	'iban'      => "",
	'bic'       => "",
	'paypal_link' => "",
	'hrname'    => "",
	'hra'       => "",
	'ustid'     => "",
	'betriebsnr'=> "",
	'imail'     => "",
	'rmail'     => "",
	'amail'     => "",
	'itel'      => "",
	'atel'      => "",
	'rtel'      => ""
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
	'logo' 	 		=> "",  // Dateiname HTTP-Link oder absolut/relativer name
	'anrede' 		=> "",  // Anrede in der Rechnung alias von retext
	'retext'	 	=> "",	 // Rechnungstext in der Redchnug 
	'ueberschrift' 	=> "",	 // "Rechnung" oder "M A H N U N G";	
	'css'    		=> ""	 // Ort und Name des CSS-Files aus der Layout HTML
	
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
	Angebot Ja / Nein --> Typ=1 Angebot 
*/
if (isset($_GET['typ']) && $_GET['typ']) {
	$_POST['typ']=$_GET['typ'];
} else {
	if (empty($_POST['typ'])) {
		$_POST['typ']=0;
	}
}

/*
echo "Typ";
echo $_POST['typ'];
exit;
*/ 


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
$rechnung->typ=$_POST['typ'];
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
	$re['leistung']="";
	$re['leistung_text']="";
	if (!empty($rechnung->row_re['leistung'])) {		
		$dt_von=new DateTime($rechnung->row_re['leistung']);
	}
	if (!empty($rechnung->row_re['leistungbis'])) {		
		$dt_bis=new DateTime($rechnung->row_re['leistungbis']);
	}
	
	
	switch($rechnung->row_firma['re_input_leistung']) {
		case 0: 
				$re['leistung']="";
				$re['leistung_text']="";
				break;
		case 1: 
				$re['leistung']=$monate[date("n",$t)]." '".date("y",$t);
				$re['leistung_text']="vom". $monate[date("n",$t)]." '".date("y",$t);
				break;
		case 2: 
				$re['leistung']=date("W",$t)." ".date("Y",$t);
				$re['leistung_text']="der Woche ".date("W",$t)." ".date("Y",$t);
				break;
		case 3: 
				$re['leistung']=$dt_von->format("d.m.Y")." bis ".$dt_bis->format("d.m.Y");
				$re['leistung_text']="von ".$dt_von->format("d.m.Y")." bis ".$dt_bis->format("d.m.Y");
				break;
		case 4: 
				$re['leistung']=$dt_von->format("d.m.Y");
				$re['leistung_text']="vom ".$dt_von->format("d.m.Y");
				break;
	}
	
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
// echo $rechnung->row_firma['web']."<br>";

foreach($abs as $k => $v) {
	$abs[$k]=$rechnung->row_firma[$k];
}
// echo $abs['web']."<br>";

/*
	Layout einstellungen
*/
foreach($rechnung->row_layout as $k => $v) {
	$layout[$k]=$v;
}

$layout['logo']=$io->getBase64Image($rechnung->getLogo()); 
$layout['logo_trans']=$io->getBase64Image($rechnung->getLogo("trans")); 
// echo $layout['logo'];exit;
$layout['anrede']=$rechnung->row_layout['retext'];
$layout['retext']=$rechnung->row_layout['retext'];
$layout['ueberschrift']=$rechnung->row_layout['ueberschrift'];

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
		$xkey='\$layout[\''.$k.'\']';
		// echo 'X$xkey-$v-<br>';
		$content=str_replace($xkey,$v,$content);
}

/*
	Absender infos
*/
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
if (empty($abs['imail'])) $abs['imail']="";
if (empty($abs['itel'])) $abs['itel']="";
if (empty($abs['rmail'])) $abs['rmail']=$abs['imail'];
if (empty($abs['rtel'])) $abs['rtel']=$abs['itel'];
if (empty($abs['rmail'])) $abs['rmail']=$abs['amail'];
if (empty($abs['rtel'])) $abs['rtel']=$abs['atel'];

// echo $abs['web']."<br>";exit;

foreach($abs as $k => $v) {
		$xkey='\$abs[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
		
		
		
}

/*
	empfaenger infos
*/
foreach($empf as $k => $v) {
		$xkey='\$empf[\''.$k.'\']';
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

// echo $content;exit;

// $rechnung->senden(2); //Versandart 2 = DRUCKEN

// echo $content;

// $filename="R".$re['renr'];
// anlegen mit rechten:
$firmanr=$_SESSION['firmanr'];
$dir="firma/".$firmanr."/pdf/";
$filename=$dir.$re['renr'];


if ($_POST['typ'] == 1) {
	$filename=$dir."AN".$re['renr'].".pdf";
	// echo "AN";
} else {
	// $filename="R".$re['renr'];
	$filename=$dir.$re['renr'].".pdf";
}
if (!file_exists("firma/$firmanr")) {
	mkdir("firma/$firmanr","0777");
}
if (!file_exists("firma/$firmanr/pdf")) {
	mkdir("firma/$firmanr/pdf","0777");
}


$output=new PDF();
$output->setContent($content);
$output->saveas($filename);
// echo "PDF erstellt:".$filename;exit;
// exit;
// echo "PDF";

// $output->destruct();



/*
	Mailvorlage vorbereiten mit Inhalt aus Datenbank
	AB HIER: $content = Mail content nicht mehr für die PDF
*/
/*
$dbmail = new db_sendmail($rechnung->db);
$dbmail->setMahnstufe($rechnung->getMahnstufe());
$dbmail->setVorlage(0);  //Erst mal auf 0 
$dbmail->init();

$sig=    $dbmail->getSignature();// echo htmlspecialchars($sig);exit;
$content=$dbmail->getContent();
$subject=$dbmail->getSubject();
*/

$sig=    trim($rechnung->row_layout['mail_signature']);
$content=trim($rechnung->row_layout['mail_text']);
$subject=trim($rechnung->row_layout['mail_subject']);

if (!empty($_POST['content'])) {
	$content=trim(nl2br($_POST['content'],false));
}
if (!empty($_POST['subject'])) {
	$subject=trim($_POST['subject']);
}



$rechnung->fillContent($sig);
$rechnung->fillContent($content);   
$rechnung->fillContent($subject);
// echo $subject."<br>";
// echo $content."<br>"; 
// echo htmlspecialchars($content)."<br>"; 
// echo $sig;exit;
// echo "ENDE:<br>";
// exit;
function br2nl ( $string, $separator = PHP_EOL )
{
    $separator = in_array($separator, array("\n", "\r", "\r\n", "\n\r", chr(30), chr(155), PHP_EOL)) ? $separator : PHP_EOL;  // Checks if provided $separator is valid.
    return preg_replace('/\<br(\s*)?\/?\>/i', $separator, $string);
}
$rechnung->row_firma['mail_preview'] = 1;

if ($rechnung->row_firma['mail_preview']==1 and empty($_POST['send_mail']))  {
	// Rohtext $content=$rechnung->row_layout['mail_text'];
	$html='
	<center>
	<h2>Preview der Mail</h2>
	<form action="rechnung_versenden.php" method="POST">
	<input type="hidden" value="'.$_POST['renr'].'" name="renr">
	<input type="hidden" value="'.$_POST['typ'].'" name="typ">
	<input type="hidden" value="'.$_POST['mahnstufe'].'" name="mahnstufe">
	<input type="hidden" value="'.$_POST['firmanr'].'" name="firmanr">
	<input type="hidden" value="'.$_SERVER['HTTP_REFERER'].'" name="HTTP_REFERER">
	
	<table>
	<tr><th>Subject:</th>
		<td><input name="subject" type="text" style="width:99%" value="'.$subject.'"></td>
	</tr>
	<tr><th>Text</th>
	<td><textarea name="content" rows=15 style="width:99%">'.br2nl($content).'</textarea><br>'.$sig.'</td>
	</tr>
	<tr><th>an:</th><td>'.$rechnung->row_kunde['mail_dienst'].'</td></tr>
	</table>
	<br><input type="submit" value="Mail versenden" name="send_mail">
	</form>
	</center>
	';


	showHeader($rechnung->row_layout['name'],1);
	echo $html;
	showBottom();
	exit;
} else {
	if (!empty($_POST['HTTP_REFERER'])) $_SERVER['HTTP_REFERER']=$_POST['HTTP_REFERER'];
}
// echo $subject;
// echo $content;
// echo "exit verpasst";exit;

/*
	Mail versenden Daten müssen aus bu_re_layout kommen, die Angaben aus bu_sendmail müssen raus, kopiert werden 
*/
$m = new sendmail();
$m->setTo($rechnung->row_kunde['mail_dienst']);  // Anders nennen in der eingabe: Rechnungsmail

$m->setFrom($rechnung->row_firma['rmail'],$rechnung->row_firma['firma']); 

$m->addAttachment($filename); //Anhang -> Rechnung
// $filename="img/logo.png";$m->addAttachment($filename); //Anhang -> Rechnung



$m->setSignature($sig);      // $Firma, Vorlage 0 Mahnstufe 0 -> Standart Signature für alle 
$m->setMessage($content);    // Firma , Vorlage, mahnstufe  -> Formatierungen anpassen
$m->setSubject($subject);    

// $m->setSignature("SIG");
// $m->setMessage("Hallo");
// $m->setSubject("XX");

// echo $rechnung->row_kunde['mail_dienst']."<br>";
// echo $rechnung->row_firma['rmail']."<br>";



// echo $filename."<br><br><hr>";
// exit;

// echo $m->testmail();exit;
	
$err=false;
$rechnung_text="Rechnung";
if ($_POST['typ'] == 1) {
	$rechnung_text="Angebot";
} else  
if ($rechnung->row_re['mahnstufe'] >0) {
	$rechnung_text="Mahnung";
}  
	
	// $msg=$rechnung_text." ".$rechnung->row_re['renr']."<br>erfolgreich<br>per Mail versendet !";

if ($m->send()) {
	$msg=$rechnung_text." ".$rechnung->row_re['renr']."<br>erfolgreich<br>per Mail versendet !";
	$err=false;
} else {
	$msg=$rechnung_text." ".$rechnung->row_re['renr']." <br>konnte nicht<br> per Mail versendet werden!";
	$err=true;
	$msg.="Fehler:<br>";
	print_r( error_get_last() );
}


// $weiter_file=$_SERVER['HTTP_REFERER'];

$html="<center>";
if (!empty($msg)) {
	if ($err) {
		$html.= "<div id=\"err\">$msg</div>";
	} else {
		$html.= "<div id='noerr'>$msg</div>";
		$rechnung->versenden(1); // //Einttragen, dass die Mail versendet wurde
	}
}
$html.='
<table>
<tr><th>Kunde:</th><td>'.$rechnung->row_kunde['firma'].'</td></tr>
<tr><th>'.$rechnung_text.':</th><td>'.$rechnung->row_layout['name'].'<br>'.$rechnung->row_re['renr'].'</td></tr>
<tr><th>an:</th><td>'.$rechnung->row_kunde['mail_dienst'].'</td></tr>
</table>
<br><form action="'.$_SERVER['HTTP_REFERER'].'" method="POST">
	<input type="hidden" value="'.$rechnung->row_re['renr'].'" name="renr">
	<input type="submit" value="weiter">
</form>
</center>
';


showHeader($rechnung->row_layout['name'],1);
echo $html;
showBottom();

?>