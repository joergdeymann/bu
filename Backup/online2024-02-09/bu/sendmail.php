<?php
// Mailtest

$to="joergdeymann@web.de";
$subject="Testmail von Joerg 2000 ";
$message='<html><body><h1 style="background-color:green;">Test</h1>Das ist der Text mit ö ß und Ä.</body></html>';
$message="Das ist der Text der Angezeigt werden soll";
$sender="Joerg Deymann";
$sender_email="joerg.deymann@die-deymanns.de";
$reply_email="joerg.deymann@die-deymanns.de";
$dateien="testimg.jpg";

$m = new sendmail();

$ergebnis = $m->mail_att($to, $subject, $message, $sender, $sender_email, $reply_email, $dateien);
if ($ergebnis) {
	echo "Mail versendet";
} else {
	echo "Mail konnte nicht versendet werden";
}




/*
	BEISPIELE
	=========
		//Aufruf der Funktion, Versand von 1 Datei
		mail_att("Empfaenger@domain.de", "Betreff", "Euer Nachrichtentext", "Absendername", "absender@domain.de", "antwortadresse@domain.de", "datei.zip");
		 
		//Versand mehrerer Dateien, die sich im Unterordner befinden:
		$dateien = array("pfad/zu/datei1.zip", "pfad/zu/datei2.png");
		mail_att("Empfaenger@domain.de", "Betreff", "Euer Nachrichtentext", "Absendername", "absender@domain.de", "antwortadresse@domain.de", $dateien);
		 
		//Dateien vor dem Versenden umbennen
		$dateien = array("pfad/zu/alterName.zip" => "neuerName.zip");
		mail_att("Empfaenger@domain.de", "Betreff", "Euer Nachrichtentext", "Absendername", "absender@domain.de", "antwortadresse@domain.de", $dateien);

		// hochgeladene Datei
		$dateien = array($_FILES['datei_feld']['tmp_name'] => $_FILES['datei_feld']['name']);

*/


class sendmail {
	public $to;					// Ziel, wohin die Mail gehen soll
	public $subject;			// Überschrift: Mail Subject
	public $message;			// Nachricht die gesendet weden soll (HTML möglich ?) 
	public $sender;         	// Name des Versenders (Ausgehende Firma)
	public $sender_email;   	// Mailadresse der Firma, von der die Mail ausgeht
	public $reply_email;    	// Mailadresse auf der geantwortet werden soll: normal = $sender_email
	public $dateien = array() ; // alles was angehägt werden sollFormat : 
								// als array : array("Dateiname", "Dateiname") oder 
								// als text :  "Dateiname"
	
	// if(filter_var($newEmail, FILTER_VALIDATE_EMAIL) !== false) {
	
	/*
	subject    Die Formatierung dieser Zeichenkette muss nach » RFC 2047 erfolgen.
	*/
  
	// mail return: true = Erfolgreich versand, sonst false
	
	
	private function addAttachment($altername,$neuername="") {
		if ($neuername=="") {
			$this->dateien[]=$altername;
		} else {
			$this->dateien[$altername] = $neuername;
		}
	}

	public function mail_att($to, $subject, $message, $sender, $sender_email, $reply_email, $dateien) {   
		if(!is_array($dateien)) {
			$dateien = array($dateien);
		}   

		$attachments = array();
		foreach($dateien AS $key => $val) {
			if(is_int($key)) {
				$datei = $val;
				$name = basename($datei);
			} else {
				$datei = $key;
				$name = basename($val);
			}
		 
			$size = filesize($datei);
			$data = file_get_contents($datei);
			$type = mime_content_type($datei);

			$attachments[] = array("name"=>$name, "size"=>$size, "type"=>$type, "data"=>$data);
		}

		$mime_boundary = "-----=" . md5(uniqid(microtime(), true));
		// $encoding = mb_detect_encoding($message, "utf-8, iso-8859-1, cp-1252");
		$encoding = mb_detect_encoding($message, "utf-8, iso-8859-1");

		$header  = 'From: "'.addslashes($sender).'" <'.$sender_email.">\r\n";
		$header .= "Reply-To: ".$reply_email."\r\n";
		$header .= "Subject: ".$subject."\r\n";

		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; charset=\"$encoding\"\r\n";
		$header .= " boundary=\"".$mime_boundary."\"\r\n";


		$content  = "This is a multi-part message in MIME format.\r\n\r\n";
		$content .= "--".$mime_boundary."\r\n";
		$content .= "Content-Type: text/html; charset=\"$encoding\"\r\n";
		$content .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$content .= $message."\r\n";

		//$anhang ist ein Mehrdimensionals Array
		//$anhang enthält mehrere Dateien
		foreach($attachments AS $dat) {
			$data = chunk_split(base64_encode($dat['data']));
			$content.= "--".$mime_boundary."\r\n";
			$content.= "Content-Disposition: attachment;\r\n";
			$content.= "\tfilename=\"".$dat['name']."\";\r\n";
			$content.= "Content-Length: .".$dat['size'].";\r\n";
			$content.= "Content-Type: ".$dat['type']."; name=\"".$dat['name']."\"\r\n";
			$content.= "Content-Transfer-Encoding: base64\r\n\r\n";
			$content.= $data."\r\n";
		}
		$content .= "--".$mime_boundary."--"; 
/*
echo "<pre>";
echo htmlentities($header);
echo "<br>";
echo "<br>";
echo htmlentities($content);
echo "</pre>";
*/	
		return mail($to, $subject, $content, $header);
	}

}
	
 
 
?>
