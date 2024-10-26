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
	public $from;
	public $from_name;
	public $to;
	public $subject;	
	public $reply_to;

	public $headers; // formatierte Header
	public $content; // formatierter Content
	
	private $boundary;
	private $message; // Content in der Mail
	
	private $attachment;
	private $inline;
	private $signature;
	
	
	/*
		Vorbereitungen		
	*/
	function __construct() {
		$this->from_name = "Die Deymann's";
		$this->from="joerg.deymann@die-deymanns.de";
		$this->to="joergdeymann@gmx.net";
		$this->reply_to=$this->from;
		// $this->to="joergdeymann@web.de";
		
		$this->subject="Eindeutige Hilfe aber Mehr Nr 5";
		$this->message="Zeile 1: geht\r\nZeile 2: weiter\r\n";

		/* Wichtige sachen */
		
		$this->signature="";
		$this->attachment=array();
		$this->inline=array();

		$this->setBoundary(); // Muss am start, wegen späteren Inline Attachments
	}

	private function setBoundary() {
		$this->boundary = "-----=" . md5(uniqid(microtime(), true));
	}
	
	private function setHeaders() {
		$mime_boundary = $this->boundary;

		
		$h[]='From: '. $this->getMail($this->from, $this->from_name);
		$h[]='Reply-To: '.$this->reply_to;

		$h[]='X-Mailer: PHP/' . phpversion();

		$h[] = "MIME-Version: 1.0";
		$h[] = "Content-Type: multipart/mixed; charset=utf-8; ";
		$h[] = " boundary=".$mime_boundary;


		$this->headers = implode("\r\n",$h);
		// $headers .= 'Content-Type: text/plain; charset='. get_option('blog_charset', 'UTF-8') . "\n"
	}
	
	private function setContent() {
		$mime_boundary = $this->boundary;
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
		$content[] = $this->getMessage();
		
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
			$cid="fix";

			$content[] = "--".$mime_boundary; 
			$content[] = "Content-Disposition: inline; ";
			$content[] = " filename=\"".$dat['name']."\";";
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
			$content[] = " filename=\"".$dat['name']."\";";
			$content[] = "Content-Length: .".$dat['size'];
			$content[] = "Content-Type: ".$dat['type']."; name=\"".$dat['name']."\"";
			$content[] = "Content-Transfer-Encoding: base64";
			$content[] = $data;
		}

		/*
			Boundary Ende : Mail auch zuende
		*/
		$content[] = "--".$mime_boundary."--"; 

		$this->content=implode("\r\n",$content);
		return;
		
		
		// $content[] = $this->getMessage();  // Inklusive Signature
		
	}
		
	
	private function getMail($mail,$name="") {
		$mailmix="";
		$mailname="";
		
		if (empty($name)) {
			$mailmix= $mail;
		} else {
			if (preg_match('/[^\x20-\x7f]/', $name)) {
				$mailname="=?UTF-8?B?". base64_encode($name) ."?=";
			} else {
				$mailname=$name;
			}
			$mailmix = $mailname . " <" . $mail . ">";
		}
		return $mailmix;			
	}
	
	private function getMessage() {
		$message  = '<html lang="de">';
		$message .= '<head>';
		$message .= '<meta charset="utf-8">';
		$message .= '<meta name="description" content="Diese Mail enthält das Anschreiben für eine Rechnung und als Anghang die Rechnung im PDF-Format. Ausserdem ist die Signatur der Firma zu sehen">';
		$message .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
		$message .= '</head>';
		$message .= "<body>";
		$message .= $this->message;
		$message .= $this->signature;
		$message.= '</body></html>';		
		return $message;
	}
	
	// =======================================================================
	// = PUBLIC
	// =======================================================================
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
		Signatur mit Bild, alles als inline vorbereiten
	*/
	public function setSignature($m) {
		$suche="/"."(<img.*?src=\")(.*?)(\".*?>)"."/is";
		preg_match_all($suche,$m,$matches);

		$this->inline = $matches[2]; // Extrahierte Image Dateien
		
		// Umbenennen der Dateien
// Temporär raus		$m=preg_replace($suche,"$1cid:$2".$this->boundary."$3",$m);
		$m=preg_replace($suche,"$1cid:fix"."$3",$m);
		$this->signature = $m."<br>";
	
		/*	
		echo "<hr>";
		echo "<pre>";
		var_dump($this->inline);
		echo "<pre>";
		echo "<hr>";
		echo htmlspecialchars($this->signature);
		exit;
		*/
	}

	public function setFrom($mail,$name="") {
		$this->from=$mail;
		$this->from_name=$name;
		if (empty($this->reply_to)) {		
			$this->reply_to=$this->from;
		}
	}
	
	public function setSubject($s) {
		$this->subject=$s;
	}
	public function setTo($t) {
		$this->to=$t;
	}
	public function setReplyTo($t) {
		$t=str_ireplace(";",",",$t);
		$this->reply_to=$t;
	}
	public function setMessage($m) {
		$this->message=preg_replace("/(<.*?>)/is","$1\r\n",$m);
		// $this->message=$m;
	}

	
	
	// =======================================================================
	//	Mail zu test anzeigen
	// =======================================================================
    public function testmail() {
		$this->setHeaders();
		$this->setContent();
		echo "<br>".$this->to;
		echo "<br>".$this->subject;
		echo "<br>".$this->headers;
		echo "<br>".$this->content;
		return;
	}
	// =======================================================================
	// = Send the Mail away
	// =======================================================================
	public function send() {
		$this->setHeaders();
		$this->setContent();

		return mail($this->to,$this->subject,$this->content,$this->headers);
	}
} 
?>
