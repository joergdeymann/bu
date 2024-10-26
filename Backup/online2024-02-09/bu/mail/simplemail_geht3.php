<?php

$m = new sendmail();
$m->setSubject("Test10 Message mit miltipart/mixed");
if ($m->sendNow()) {
	echo "Mail erfolgreich versendet";
} else {
	echo "Mail nicht erfolreich gessendet";
}




// mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers)


class sendmail {
	private $to = "joergdeymann@web.de";
	private $subject = "My subject";
	private $message = "<h1 style='background-color:green'>Kopf</h1>Hello ä ö ü ' `world!";
	private $headers = "";
	private $sender_name = "Die Deymann's";
	private $sender_mail = "joerg.deymann@die-deymanns.de";
	private $reply_mail = "joerg.deymann@die-deymanns.de";
	
	private $content=""; // Inhalt mailbody mit allen formaten
	function __construct() {

	}
	
	private function initHeaders() {
		$headers = array();
		$content = array();
		// $encoding = mb_detect_encoding($this->message, "utf-8, iso-8859-1");

		$mime_boundary = "-----=" . md5(uniqid(microtime(), true));
		
		
		$headers[] = 'From: "'.'=?utf-8?B?'.base64_encode($this->sender_name).'?='.'" <'.$this->sender_mail.'>';
		$headers[] = "Reply-To: ".$this->reply_mail;

		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: multipart/mixed; charset=utf-8; ";
		$headers[] = " boundary=".$mime_boundary;

		$content[]  = "This is a multi-part message in MIME format.\r\n";

		$content[] = "--".$mime_boundary; 
		$content[] = "Content-type: text/html; charset=utf-8";
		$content[] = "Content-Transfer-Encoding: 8bit\r\n";
		$content[] = $this->message;
		
		
		$content[] = "--".$mime_boundary."--"; 
		

		$this->content = implode("\r\n",$content);
		$this->headers = implode("\r\n",$headers);
	}

	
	function setSubject($s) {
		$this->subject=$s;
	}

    function sendNow() {
		$this->initHeaders();
		
		return mail($this->to,'=?utf-8?B?'.base64_encode($this->subject).'?=',$this->content,$this->headers);
	}
	
}

?> 
