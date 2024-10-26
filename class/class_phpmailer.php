<?php
// #$mail->AddCC("cc-recipient-email", "cc-recipient-name");
class PHPMailer {
	private $mail; // phpmailer
	
	// private $message;
	private $signature;
	private $content;
	
	public function  __construct($debug=false) {
		$mail=&$this->mail;

		require_once "../vendor/phpmailer/phpmailer/src/PHPMailer.php";
		require_once "../vendor/phpmailer/phpmailer/src/Exception.php";
		require_once "../vendor/phpmailer/phpmailer/src/SMTP.php";

		// require_once "../vendor/autoload.php";
		$mail=new PHPMailer\PHPMailer\PHPMailer($debug);


        if ($debug) {
			// gibt einen ausführlichen log au
			$mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
        }

        // authentifiziere dich über den smtp-login
        $mail->isSMTP();
        $mail->SMTPAuth = true;

		$this->setServer();

		$mail->CharSet    = 'utf-8';
		$mail->Debugoutput = 'html';
        $mail->SMTPOptions = array(
            'ssl' => array(
                  'verify_peer' => false,
                  'verify_peer_name' => false,
                  'allow_self_signed' => true
            ),
            'tls' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->isHTML(true);

		
	}
	
	public function setServer($data="") {
		$mail=&$this->mail;
		if (empty($data)) {
			// login
			$mail->Host       = "mx2ecf.netcup.net";
			$mail->Port       = "587";
			// $mail->Username   = "joerg.deymann@die-deymanns.de";
			// $mail->Password   = "Radio#123";
			$mail->Username   = "mail@die-deymanns.de";
			$mail->Password   = "firma@2021&";
		
		} else {
			$mail->Host       = $data['host'];
			$mail->Port       = $data['port'];
			$mail->Username   = $data['username']; 
			$mail->Password   = $data['password'];
		}			
        if ($mail->Port == 587) $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // PORT 587
        if ($mail->Port == 465) $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;    // PORT 465
			
	}
	

	public function addEmbeddedImage($file,$cid, $name="") {
		if (empty($cid)) $cid=basename($file);
		if (empty($name)) $name=basename($file);
		
		$this->mail->addEmbeddedImage($file, $cid, $name); 
	}
	/* 
		addAttachment("img/logo.png", "logo.png")
		oder
		addAttachment(array("logo.png" => "img/logo.png", "logo2.png" => "img/logo2.png")
	*/		
	public function addAttachment($file, $name="") {
		$mail=&$this->mail;
		if (!is_array($file)) {
			if (empty($name)) {
		        $mail->addAttachment($file,basename($file));
			} else {
		        $mail->addAttachment($file,$name);
			}
		} else {
			foreach($file as $key => $value) { 
				$mail->addAttachment($value, $key); 
			}			
		}	
	}
	public function clearTo() {
		$mail=&$this->mail;
		$mail->clearAllRecipients();
	}
	
	public function setTo($email, $name='') {
		$this->addTo($email, $name);
	}
	
	public function addBCC($email='', $name='') {
		if (empty($email)) {
			$email=$this->mail->From;
			$name=$this->mail->FromName;
		}
		$this->mail->addBCC($email, $name);
	}

	public function addCC($email='', $name='') {
		if (empty($email)) {
			$email=$this->mail->From;
			$name=$this->mail->FromName;
		}
		$this->mail->addCC($email, $name);
	}
	
	public function addTo($email, $name="") {
		$mail=&$this->mail;
		if (!is_array($email)) {
			if (empty($name)) {
		        $mail->addAddress($email);
			} else {
		        $mail->addAddress($email,$name);
			}
		} else {
			foreach($email as $key => $value) { 
				$mail->addAddress($value, $key); 
			}			
		}	
	}
	public function setFrom($email, $name="") {
		$mail=&$this->mail;
		if (empty($name)) {
			$mail->setFrom($email);
		} else {
			$mail->setFrom($email,$name);
		}
	}

	public function setSubject($subject) {
		$this->mail->Subject=$subject;
	}

	public function setUnsubscribe($mail,$http="") {
		$u="<mailto:".$mail."?subject=Unsubscribe>";
		if (!empty($http)) $u.=", ".$http;
		$this->mail->AddCustomHeader("List-Unsubscribe: $u");
		$this->mail->AddCustomHeader("List-Unsubscribe-Post: List-Unsubscribe=One-Click");
	}
	
	
	public function ConfirmReadingTo($to="") {                                                 
		if (empty($to)) $to=$this->mail->From;
		$this->mail->ConfirmReadingTo = $to;
	}

	private function replaceEmbeddedImages($content) {		
		$suche="/"."(<img.*?src=\")(.*?)(\".*?>)"."/is";
		preg_match_all($suche,$content,$matches);

		// $this->inline = $matches[2]; // Extrahierte Image Dateien
		// print_r($this->inline);exit;
		foreach($matches[2] as $v) {
			$this->addEmbeddedImage($v,$v,basename($v)); // Link cid Name
		}
		// Umbenennen der Dateien
		$content=preg_replace($suche,"$1cid:$2$3",$content);
		return $content;
		// echo htmlspecialchars(	$content);exit;	
	}
	public function setSignature($sig) {
		$this->signature=$this->replaceEmbeddedImages($sig)."<br>";

		/*
		$suche="/"."(<img.*?src=\")(.*?)(\".*?>)"."/is";
		preg_match_all($suche,$sig,$matches);

		$this->inline = $matches[2]; // Extrahierte Image Dateien
		// print_r($this->inline);exit;
		foreach($matches[2] as $v) {
			$this->AddEmbeddedImage($v,$v,basename($v)); // Link cid Name
		}
		// Umbenennen der Dateien
		$sig=preg_replace($suche,"$1cid:$2$3",$sig);
		$this->signature = $sig."<br>";
		echo htmlspecialchars(	$sig);exit;	
		
		// $this->signature=$sig;
		*/
	}


	
	public function setMessage($html) {		
        $this->content = $this->replaceEmbeddedImages($html);
	}	

	private function setTextMessage($content) {
		$text=strip_tags(str_replace("<br>","\r\n",$content)); // preg_replace("/\<.*?\>/","")
		
/*		
		$text.='\r\n
				\r\n
				HTML Aktivieren\r\n
				===============\r\n
				Die Einstellung von Text zu HTML ist zwar riskanter, doch haben viele Anbieter auch noch HTML Texte, \r\n
				da sie schöner zu lesen sind und optisch besser gestaltet werden können. Sie können dies folgendermaßen aktivieren.\r\n
				\r\n
				\r\n
				Outlook 2016\r\n
				============\r\n
				Die Anleitung wurde mit Outlook 2016 ausgeführt, ggf. sind bei anderen Versionen die Menüeinträge etwas anders angeordnet.\r\n
				1. Wählen Sie im Menü Datei den Eintrag Optionen aus\r\n
				2. Aktivieren Sie den Eintrag E-Mail und wählen (2) beim Format fürs Verfassen „Text und HTML“ aus. \r\n
				   Klicken Sie auf „Trust Center“ im Menü.\r\n
				3. Klicken Sie auf den Button „Einstellungen für das Trust Center“\r\n
				4. Wählen Sie den Eintrag „E-Mail-Sicherheit“, \r\n
				   deaktivieren Sie bei „Als Nur-Text lesen“ beide Checkboxen für Standard- und signierte Nachrichten.\r\n 
				   Bestätigen Sie diese Änderungen mit OK.\r\n
				5. Bestätigen Sie bei den Outlook-Optionen ebenso die Änderungen mit OK. Dann wird alles übernommen.\r\n
				6. Fertig\r\n
				\r\n
				Thunderbird\r\n
				===========\r\n
				1. Im Menu den Punkt Ansicht auswählen\r\n
				2. Nachtrichtentext auswählen \r\n
				3. dann auf Original HTML clicken\r\n';
		*/
		return $text;
	}

	public function send() {
		$content=&$this->content;
		$signature=&$this->signature;
		// $text=&$this->text;
		
		
		// echo htmlspecialchars($content.$signature);exit;
		$mail=&$this->mail;

		
		// $text=strip_tags(str_replace("<br>","\r\n",$content.$signature)); // preg_replace("/\<.*?\>/","")
        $mail->Body    = $content.$signature;
        $mail->AltBody = $this->setTextMessage($content.$signature); // $text
		try  {
			$mail->send();
		} catch (PHPMailer\PHPMailer\Exception $e) {
			echo "Message could not be sent. Mailer Error: ".$mail->ErrorInfo; 
		}
		

	}
}


?>
