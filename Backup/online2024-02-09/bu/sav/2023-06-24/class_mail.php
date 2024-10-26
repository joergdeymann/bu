<?php
/*
	Beispiele

	$m = new sendmail();

	$m->setSubject("Message mit Anhang und Signatur und HTML code");
	$m->setMessage($message);
	$m->setSignature($sig);
	$m->addAttachment("/img/testimg.jpg");
	$m->setTo("name@provider.com");
	$m->setFrom("vorname.nachname@adresse.de","Vorname Nachname");
	$m->setFrom("vorname.nachname@adresse.de");

	if ($m->send()) {
		echo "Mail erfolgreich versendet";
	} else {
		echo "Mail nicht erfolreich gessendet";
		echo "Fehler:<br>";
		print_r( error_get_last() );
	}

	
*/


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

	/* 
		Mail zu test anzeigen
	*/
    public function testmail() {
		echo $this->initHeaders();
		echo "<br>".$this->to;
		echo "<br>".$this->subject;
		echo "<br>".$this->headers;
		echo "<br>".$this->content;
		return;
	}
	
}

?> 
