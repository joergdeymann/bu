<?php
$message="";
$message.= "<h1 style='background-color:green'>Kopf</h1>Hello ä ö ü ' `world!<br>";
$message.= 'Hidermit erhalten Sie folgende Rechnung<br>';
$message.= "Rechnunr Nr RE20220523<br>";
$message.= "Datum: 29.05.2023<br>";
$message.= "Ort: LaGa Bad Gandersheim<br>";
$message.= "Projekt: Arbeiten Lichtoperator<br>";
$message.="Zu zahlender Betrag: <b>172,45 €</b> oder 160,00 € mit Skonto";

$message.= "Vielen Dank fü+r die Zusammenarbeit!<br>";

$sig = "<h1 style='background-color:green'>Signatur</h1><br>";
$sig.= '<table><tr><td><a href="https://www.die-deymanns.de/VA/leistungen.html"><img src="img/logo.png" alt="Logo"></a></td><td>Jörg Deymann</td></tr></table>';


$m = new sendmail();
$m->setSubject("Test 15 Message mit miltipart/mixed");
$m->setMessage($message);
$m->setSignature($sig);
$m->addAttachment("testimg.jpg");
// $m->setTextMessage($textmessage);

// ; = geht nicht
// , = geht 
// $m->setTo("joergdeymann@web.de,joergdeymann@gmx.net");
// $m->setTo("joergdeymann@gmx.net");
// $m->setTo("joergdeymann@web.de");
// $m->setTo("test-j3inm9@experte-test.com");
// $m->setTo("test-8b3aca@test.mailgenius.com");
// $m->setTo("test-j3inm9@experte-test.com");
$m->setTo("joerg080973@gmail.com");

$m->setFrom("joerg.deymann@die-deymanns.de","Die Deymann's");

if ($m->send()) {
	echo "Mail erfolgreich versendet";
} else {
	echo "Mail nicht erfolreich gessendet";
	echo "Fehler:<br>";
	print_r( error_get_last() );
}

class sendmail {
	private $to = "";
	private $subject = "";
	private $message = "";
	
	private $signature = "";
	private $inline = array();
	
	private $headers = "";
	private $sender_name = "";
	private $sender_mail = "";
	private $reply_mail = "";

	private $textmessage="Diese Mail ist eine Rechnung in HTML-Code. Der Textteil wurde nicht erstellt.";   //Falls HTML doch nicht akzeptiert wird
	private $textsignature=""; //Falls HTML doch nicht akzeptiert wird

	private $attachment = array();   // Anhänge als array zwischenspeichern;
	
	private $boundary = ""; // Mime Boundary Schlüssel
	
	private $content=""; // Inhalt mailbody mit allen formaten
	


	function __construct() {
		$this->setBoundary();
		$this->attachment = array();
		
	}

	private function setBoundary() {
		$this->boundary = "-----=" . md5(uniqid(microtime(), true));
		return $this->boundary;
	}
	
	
	private function initHeaders() {
		$headers = array();
		$content = array();
		// $encoding = mb_detect_encoding($this->message, "utf-8, iso-8859-1");

		$mime_boundary = $this->boundary;
		
		/*
			Header Daten
		*/
		$headers[] = 'From: "'.'=?utf-8?B?'.base64_encode($this->sender_name).'?='.'" <'.$this->sender_mail.'>';
		if (!empty($this->reply_mail)) {
			$headers[] = "Reply-To: ".$this->reply_mail;
		}

		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-Type: multipart/mixed; charset=utf-8; ";
		$headers[] = " boundary=".$mime_boundary;

		/*
			Esatztext
		*/
		if (!empty($this->textmessage)) {
			$content[] = $this->textmessage;
			if (!empty($this->textsignature)) {
				$content[] = $this->textsignature;
			}
		} else {
			$content[]  = "This is a multi-part message in MIME format.\r\n"; // Sinnlos oder ? Nein: Irgendwas sollte hier stehen
		}

		
		/*
			Falls nur Text gesendet wird ist das die möglichkeit anstatt HTML
		/*
		$content[] = "--".$mime_boundary; 
		$content[] = "Content-Type: text/plain; charset=utf-8";
		$content[] = "Content-Transfer-Encoding: 8bit\r\n";
		$content[] = $this->textmessage;
		if (!empty($this->textsignature)) {
			$content[] = $this->textsignature;
		}
		*/			

		/*
			Boundary für HTML		
		*/
		$content[] = "--".$mime_boundary; 
		$content[] = "Content-Type: text/html; charset=utf-8";
		$content[] = "Content-Transfer-Encoding: 8bit\r\n";
		$content[] = $this->getMessage();  // Inklusive Signature
		
		
		/*
			Boundary für Inline Image für die Signatur
		*/
		foreach ($this->inline as $datei) {
			// $datei = "img/logo.png";
			$dat["name"] = basename($datei);
			$dat["size"] = filesize($datei);
			$dat["data"] = file_get_contents($datei);
			$dat["type"] = mime_content_type($datei);
			$cid=$datei.$mime_boundary;

			$content[] = "--".$mime_boundary; 
			$content[] = "Content-Disposition: inline; filename=\"".$dat['name']."\";";
			$content[] = "Content-Length: .".$dat['size'];
			$content[] = "Content-Type: ".$dat['type']."; name=\"".$dat['name']."\"";
			$content[] = "Content-Transfer-Encoding: base64";
			$content[] = "Content-ID: <".$cid.">";		
			$content[] = chunk_split(base64_encode($dat['data']));
		}
		
		/* 
			Boundary für Anhang Bild / PDF
		*/
		foreach($this->attachment as $dat) {
			$data = chunk_split(base64_encode($dat['data']));
			$content[] = "--".$mime_boundary;
			$content[] = "Content-Disposition: attachment;";
			$content[] = "\tfilename=\"".$dat['name']."\";";
			$content[] = "Content-Length: .".$dat['size'];
			$content[] = "Content-Type: ".$dat['type']."; name=\"".$dat['name']."\"";
			$content[] = "Content-Transfer-Encoding: base64";
			$content[] = $data;
		}
		
		/*
			Boundary Ende : Mail auch zuende
		*/
		$content[] = "--".$mime_boundary."--"; 
		

		$this->content = implode("\r\n",$content);
		$this->headers = implode("\r\n",$headers);
	}

	public function setFrom($mail,$name="") {
		$this->sender_mail=$mail;
		$this->sender_name=$name;
	}
	
	public function setSubject($s) {
		$this->subject=$s;
	}
	public function setTo($t) {
		$this->to=$t;
	}
	public function setReplyTo($t) {
		$t=str_ireplace(";",",",$t);
		$this->reply_mail=$t;
	}
	public function setMessage($m) {
		$this->message=$m."<br>";
	}
	public function setSignature($m) {
		$suche="/"."(<img.*?src=\")(.*?)(\".*?>)"."/is";
		preg_match_all($suche,$m,$matches);

		$this->inline = $matches[2]; // Extrahierte Image Dateien
		
		// Umbenennen der Dateien
		$m=preg_replace($suche,"$1cid:$2".$this->boundary."$3",$m);
		$this->signature = $m."<br>";
	}
	private function getMessage() {
		$message  = '<html lang="de">';
		$message .= '<head>';
		$message .= '<meta charset="utf-8">';
		$message .= '<meta name="description" content="Diese Mail enthält das Anschreiben für eine Rechnung und als Anghang die Rechnung im PDF-Format. Ausserdem ist die Signatur der Firma zu sehen">';
		$message .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		$message .= '</head>';
		$message.= "<body>";
		$message .= $this->message;
		$message .= $this->signature;
		$message.= '</body></html>';		
		return $message;
	}
		

	public function setTextMessage($textmessage) {
		$this->textmessage=$textmessage;
	}

	public function setTextSignature($text) {
		$this->textsignature=$text;
	}

	/* 
		Mail Attachment JPG / GIF / PDF /DOC / txt
		Beispiele:
		addAttachment("bild.jpg");
		addAttachment(array("bild1.jpg","mypdf.pdf"));
	*/
	public function addAttachment($dateien)	{	
		if(!is_array($dateien)) {
			$dateien = array($dateien);
		}   

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

			$this->attachment[] = array("name"=>$name, "size"=>$size, "type"=>$type, "data"=>$data);
		}
	}

	public function removeAttachments() {
		$this->attachment=array();
	}

	/* 
		Mail lossenden
	*/
    public function send() {
		$this->initHeaders();
		
		return mail($this->to,'=?utf-8?B?'.base64_encode($this->subject).'?=',$this->content,$this->headers);
	}
	
}

?> 
