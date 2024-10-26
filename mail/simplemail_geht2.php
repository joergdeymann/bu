<?php

$m = new sendmail();
$m->setSubject("Test2");
$m->sendNow();




class sendmail {
	private $to = "joergdeymann@web.de";
	private $subject = "My subject";
	private $txt = "Hello world!";
	private $headers = "";

	function __construct() {
		/*
		$headers[] = "MIME-Version: 1.0";
		$headers[] = "Content-type: text/html; charset=utf-8";
		*/
		
		$this->headers .= "MIME-Version: 1.0" . "\r\n";
		$this->headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
		$this->headers .= "From: joerg.deymann@die-deymanns.de" . "\r\n";

	}
	
	function setSubject($s) {
		$this->subject=$s;
	}

    function sendNow() {
		mail($this->to,$this->subject,$this->txt,$this->headers);
	}
	
}

?> 
