<?php
include 'session.php';
include 'class/class_rechnung.php';

if (!empty($_POST['send_mail'])) {
	include 'class/class_pdf.php';
	include 'class/class_phpmailer.php';
}
include 'menu.php';


$msg=""; //Meldungen zum, Benutzer 


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


/*
	1. RECHNUNGSDATEN LADEN 
*/
$rechnung=new Rechnung();
$rechnung->typ=$_POST['typ'];
$rechnung->setFirma($_POST['firmanr']);
$rechnung->setReNr($_POST['renr']);
$rechnung->setMahnstufe($_POST['mahnstufe']);

/*
	2. Vorbereiten der Austauschdaten
*/

// if (ob_get_level() == 0) {
// }
// @ini_set('implicit_flush', 1);
// header( 'Content-Encoding: none; ' );//disable apache compressed

// echo ob_get_level();exit;



// error_reporting(0);

$html="";
showHeader($rechnung->row_layout['name'],1);
if (isset($_POST['send_mail'])) {
	
	
	// $html.= "<center>";
	// $html.= "<div id='noerr'>Bitte einen Moment Gedult<br>Die Mail mit der Rechung wird vorbereitet</div></center>";
	// echo $html;
	// $html="";
	$content=$rechnung->getContent(); // Vorlagen laden
	/*
	if (ob_get_level() > 0) {
		// echo ob_get_level();
		ob_end_flush();
		// echo "<br>".ob_get_level();
		// exit;
	}
	*/	
	// Eventuell aktives Output-Buffering beenden
	// while(ob_get_length()!==FALSE) { ob_end_clean(); }
	// Eventuell aktive Output-Kompression abstellen
	// @ini_set('zlib.output_compression',0);

	// ob_start();
	// set_time_limit(0);
	// $lenout = strlen($html);
	// header('Content-Type: text/html; charset=UTF-8'); 
	// header("Content-Length: ".$lenout); 
	// ob_start();
	// flush();
	// ob_flush();
	// sleep(10);
	// header_remove('Content-Type');
	// header_remove('Content-Length');
	// showHeader($rechnung->row_layout['name'],1);
	// echo "ende";exit;
	
	
	// showBottom();
	
	// ob_flush();
	//  flush();
	// ob_end_flush();
	

	// ob_end_flush();
	// sleep(10);
	// echo "ende";exit;
	
} else {
	$rechnung->getSetup(false); // Ohne Laden von Bildern
}


// echo "ende";exit;

/*
	Mailvorlage vorbereiten mit Inhalt aus Datenbank
*/

$mail_sig=    trim($rechnung->row_layout['mail_signature']);
$mail_content=trim($rechnung->row_layout['mail_text']);
$mail_subject=trim($rechnung->row_layout['mail_subject']);

if (!empty($_POST['mail_content'])) {
	$mail_content=trim(nl2br($_POST['mail_content'],false));
}
if (!empty($_POST['mail_subject'])) {
	$mail_subject=trim($_POST['mail_subject']);
}



$rechnung->fillContent($mail_sig);
$rechnung->fillContent($mail_content);   
$rechnung->fillContent($mail_subject);

/*
	CCs holen
*/
$request="SELECT `mail` from `bu_adresse` where `firmanr`='".$_SESSION['firmanr']."' and `mail` != '' and `zuordnung`='5'";
// echo $request;
$result=$db->query($request);
$cc=array();
while($row=$result->fetch_assoc()) {
	$cc[]=$row['mail'];
}

// echo $subject."<br>";
// echo $content."<br>"; 
// echo htmlspecialchars($content)."<br>"; 
// echo $sig;exit;
// echo "ENDE:<br>";
// exit;
function br2nl( $string, $separator = PHP_EOL )
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
		<td><input name="mail_subject" type="text" style="width:99%" value="'.$mail_subject.'"></td>
	</tr>
	<tr><th>Text</th>
	<td><textarea name="mail_content" rows=15 style="width:99%">'.br2nl($mail_content).'</textarea><br>'.$mail_sig.'</td>
	</tr>
	<tr><th>an:</th><td>'.$rechnung->row_kunde['mail_dienst'].'</td></tr>';
	
	
	if (count($cc)>0) {
		$html.='<tr><th>CC an:</th><td>';
		foreach($cc as $v) {
			$html.= "$v<br>";
		}
		$html.= '</td></tr>';
	}
	
	$html.='
	<tr><th>BCC (Kopie) für Dich ?</th><td><input type="radio" name="rb_bcc" value="1">Ja&nbsp;<input type="radio" name="rb_bcc" value="2" checked>Nein</td></tr>
	</table>
	<br><input type="submit" value="Mail versenden" name="send_mail">
	</form>
	<br>BITTE BEACHTEN: das Senden der Mail dauert einige Sekunden !!<br>
	</center>
	';


	// showHeader($rechnung->row_layout['name'],1);
	echo $html;
	showBottom();
	exit;
} else {
	if (!empty($_POST['HTTP_REFERER'])) $_SERVER['HTTP_REFERER']=$_POST['HTTP_REFERER'];
	// if (!empty($_POST['content'])) $content=$_POST['content'];
}


/*
	PDF erstellen
	
*/
// $content=$rechnung->getContent(); // Vorlagen laden
$filename=$rechnung->getServerFilename();
$pdf=new PDF();
$pdf->setContent($content);
$pdf->saveas($filename);





/*
	Mail versenden Daten müssen aus bu_re_layout kommen, die Angaben aus bu_sendmail müssen raus, kopiert werden 
*/
// $m = new sendmail();
$m = new PHPMailer();
// $rechnung->row_kunde['mail_dienst']="test-af52vzbgt@srv1.mail-tester.com";       // ############ ACHTUNG wieder raus
// echo "Versenden nach ".$rechnung->row_kunde['mail_dienst'];//exit; // ############ ACHTUNG wieder raus
$m->setTo($rechnung->row_kunde['mail_dienst']);  // Anders nennen in der eingabe: Rechnungsmail
$m->setFrom($rechnung->row_firma['rmail'],$rechnung->row_firma['firma']); 
$m->addAttachment($filename); //Anhang -> Rechnung

$m->setSignature($mail_sig);      // $Firma, Vorlage 0 Mahnstufe 0 -> Standart Signature für alle 
$m->setMessage($mail_content);    // Firma , Vorlage, mahnstufe  -> Formatierungen anpassen
$m->setSubject($mail_subject);    


$err=false;
$rechnung_text="Rechnung";
if ($_POST['typ'] == 1) {
	$rechnung_text="Angebot";
} else  
if ($_POST['mahnstufe'] >0) {
	$rechnung_text="Mahnung";
}  
if (isset($_POST['rb_bcc']) and $_POST['rb_bcc']==1) {
	// echo "rechnung_versenden.php:BCC hinzugefügt<br>";
	$m->addBCC();
}	

if (count($cc)>0) {
	foreach($cc as $v) {
		$m->addCC($v);
	}
}	
	
	
/*
if (ob_get_level() == 0) {
   ob_start();
}
showHeader($rechnung->row_layout['name'],1);
$html="<center>";
$html.= "<div id='noerr'>Bitte einen Moment Gedult<br>Die Mail mit der Rechung wird vorbereitet</div>";
echo $html;
flush();
ob_flush();
sleep(10);
echo "<br>..Fertig";
ob_end_flush();
exit;
*/
	// $msg=$rechnung_text." ".$rechnung->row_re['renr']."<br>erfolgreich<br>per Mail versendet !";

if (!$m->send()) {
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
		$html.= "<div id=\"err\">$msg</(div>";
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


echo $html;
showBottom();

?>