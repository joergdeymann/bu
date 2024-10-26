<?php

$m = new sendmail();
$m->setSubject("Test 12 Message mit miltipart/mixed");
// $m->setTo("132ba3@ingest.email2go.io");

$message = "<h1 style='background-color:green'>Kopf</h1>Hello ä ö ü ' `world!<br>";
$message.= '<img id="XX" class="xy" src="cid:logo.png@email.de" width="50%">';
$message.= 'einbindug des Logos vor diesem text';

$sig = "<h1 style='background-color:green'>Kopf</h1>Hello ä ö ü ' `world!<br>";
$sig.= '<img src="logo.png">';
$sig.= 'einbindug des Logos vor diesem text';

// $m->setMessage($message);
$m->setSignature($sig);

exit;





if ($m->send()) {
	echo "Mail erfolgreich versendet";
} else {
	echo "Mail nicht erfolreich gessendet";
	echo "Fehler:<br>";
	print_r( error_get_last() );
}

/*
Bild einfügen
------=_NextPart_000_0034_01D946B9.E1E07CE0
Content-Type: image/png;
	name="image001.png"
Content-Transfer-Encoding: base64
Content-ID: <image001.png@01D946B9.64EB8130>
dann der Base64 kram
*/

/*
--------------040202010204080305090405
Content-Type: image/png; name="test.png"
Content-Transfer-Encoding: base64
Content-ID: <part1.02080004.04000407@sample.com>
Content-Disposition: inline; filename="test.png"
*/

/*
TD width=3D769 colSpan=3D3 height=3D110><IMG alt=3D"" hspace=3D0=20
      src=3D"cid:000901c5bea3$cda0eeb0$2501a8c0@CADTH" =
border=3D0></TD></TR>	

Beispiel für die Einbindung: <img src="cid:bild1.jpg">
Beispiel für die Einbindung: <img src="cid:000901c5bea3$cda0eeb0$2501a8c0@CADTH">

------=_NextPart_000_000E_01C5BEB4.946C1870
Content-Type: image/jpeg;
   name="header.jpg"
Content-Transfer-Encoding: base64
Content-ID: <000901c5bea3$cda0eeb0$2501a8c0@CADTH>

/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAAQwAA/+4AJkFkb2JlAGTAAAAAAQMA
FQQDBgoNAAAVAAAAIvoAADfBAABPTP/bAIQABQMDAwMDBQMDBQcEBAQHCAYFBQYICQcHCAcHCQsJ
CgoKCgkLCwwMDAwMCw4ODg4ODhQUFBQUFhYWFhYWFhYWFgEFBQUJCAkRCwsRFA8ODxQWFhYWFhYW...

*/

/*
Content-Type: image/png; name=logo.png
Content-Transfer-Encoding: base64
Content-ID: <logo.png>
Content-Disposition: inline; filename=logo.png 
*/



// mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers)


class sendmail {
	private $to = "joergdeymann@web.de";
	private $subject = "My subject";
	private $message = "<h1 style='background-color:green'>Kopf</h1>Hello ä ö ü ' `world!<br>";
	
	private $signature = "";
	private $cid = "";
	
	private $headers = "";
	private $sender_name = "Die Deymann's";
	private $sender_mail = "joerg.deymann@die-deymanns.de";
	private $reply_mail = "joerg.deymann@die-deymanns.de";
	
	private $content=""; // Inhalt mailbody mit allen formaten

	private $mime = "";  	// Mime Schlüssel nötig ?? erst mal lassen
	private $boundary = ""; // Mime Boundary Schlüssel
	


	function __construct() {
		$this->setBoundary();
	}

	private function XsetMime() {
		$this->mime = md5(uniqid(microtime(), true));
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
		
		
		$headers[] = 'From: "'.'=?utf-8?B?'.base64_encode($this->sender_name).'?='.'" <'.$this->sender_mail.'>';
		$headers[] = "Reply-To: ".$this->reply_mail;

		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: multipart/mixed; charset=utf-8; ";
		$headers[] = " boundary=".$mime_boundary;

		$content[]  = "This is a multi-part message in MIME format.\r\n";

		// Boundary für Roh Text
		// $body = "--" . $separator . $eol;
		// $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
		// $body .= "Content-Transfer-Encoding: 8bit" . $eol;
		// $body .= $message . $eol;

		// Boundary für HTML
		$content[] = "--".$mime_boundary; 
		$content[] = "Content-type: text/html; charset=utf-8";
		$content[] = "Content-Transfer-Encoding: 8bit\r\n";
		$content[] = $this->message;
		
		// Boundary für Inline Image für die Signatur
		$datei = "logo.png";
		$dat["name"] = basename($datei);
		$dat["size"] = filesize($datei);
		$dat["data"] = file_get_contents($datei);
		$dat["type"] = mime_content_type($datei);

		$content[] = "--".$mime_boundary; 
		$content[] = "Content-Disposition: inline; filename=\"".$dat['name']."\";";
		$content[] = "Content-Length: .".$dat['size'];
		$content[] = "Content-Type: ".$dat['type']."; name=\"".$dat['name']."\"";
		$content[] = "Content-Transfer-Encoding: base64";
		$content[] = "Content-ID: <logo.png@email.de>";
		$content[] = chunk_split(base64_encode($dat['data']));
		
		//Boundary für Anhang Bild / PDF
	
			$datei = "testimg.jpg";
			$name = basename($datei);
			$size = filesize($datei);
			$data = file_get_contents($datei);
			$type = mime_content_type($datei);

			// echo $name."<br>";
			// echo $size."<br>";
			// echo $type."<br>";
			// echo chunk_split(base64_encode($data))."<br>";
			
			$dat = array();
			
			
			$dat['data'] = $data;
			$dat['name'] = $name;
			$dat['size'] = $size;
			$dat['type'] = $type;

			$data = chunk_split(base64_encode($dat['data']));
			$content[] = "--".$mime_boundary;
			$content[] = "Content-Disposition: attachment;";
			$content[] = "\tfilename=\"".$dat['name']."\";";
			$content[] = "Content-Length: .".$dat['size'];
			$content[] = "Content-Type: ".$dat['type']."; name=\"".$dat['name']."\"";
			$content[] = "Content-Transfer-Encoding: base64";
			$content[] = $data;
		
		
		// Boundary Ende
		$content[] = "--".$mime_boundary."--"; 
		

		$this->content = implode("\r\n",$content);
		$this->headers = implode("\r\n",$headers);
	}

	
	public function setSubject($s) {
		$this->subject=$s;
	}
	public function setTo($s) {
		$this->to=$s;
	}
	public function setMessage($m) {
		$this->message=$m;
	}
	public function setSignature($m) {
		$suche="/"."(<img.*?src=\")(.*?)(\".*?>)"."/is";
		preg_match($suche,$m,$matches);
		
echo "<pre>";
var_dump($matches);
foreach($matches as $v) { 
	echo htmlspecialchars($v)."<br>";
}
echo "</pre>";
		$m=preg_replace($suche,"$1cid:$2".$this->boundary."$3",$m);
		$this->signature = $m;
		
echo "<pre>";
	echo htmlspecialchars($m)."<br><br>";
	echo "Key=cid:".$matches[2].$this->boundary;
		$c = $matches[2];
		// $this->cid[$c]="cid:".$matches[2].$this->boundary;
		$this->cid="cid:".$matches[2].$this->boundary;
echo "</pre>";
		
		
		// $this->message=$m;
	}

    public function send() {
		$this->initHeaders();
		
		return mail($this->to,'=?utf-8?B?'.base64_encode($this->subject).'?=',$this->content,$this->headers);
	}
	
}

?> 
