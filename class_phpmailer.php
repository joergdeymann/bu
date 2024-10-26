<?php


$sig='MFG<br>Jörg Deymann<br>Die Deymann\'s<img src="img/qr.png"><img src="img/logo.png" width="50px">';
$content='<img src="img/pfeil-rechts.png"><br><img src="img/logo.png"><br>Hallo wir sind ein Team<br>';
$to="joergdeymann@web.de";
$toName="Jörg Deymann";
$from="joerg.deymann@die-deymanns.de";
$fromName="J. Deymann";
$filename="img/logo.png";
$subject="Testmail für Dich";

$m=new PHPMail();
$m->setTo($to,$toName);
$m->setFrom($from,$fromName);
$m->addAttachment($filename,"logo.png");
$m->setSignature($sig);
$m->setMessage($content);
$m->setSubject($subject);
$m->send();
echo "Mail vrersendet";

exit; 

// $mailer->AddEmbeddedImage('../images/namDiams.png', 'logoimg', 'namDimes.png'); 
//   $footer .= '<img src=\"cid:logoimg\" />';
// #$mail->AddCC("cc-recipient-email", "cc-recipient-name");
class PHPMail {
	private $mail; // phpmailer
	
	// private $message;
	private $signature;
	private $content;
	
	public function  __construct($debug=false) {
		$mail=&$this->mail;

		require_once "../vendor/autoload.php";
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
	
	public function setTo($email, $name="") {
		 $this->addTo($email, $name);
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
	
	private function replaceEmbeddedImages($content) {		
		$suche="/"."(<img.*?src=\")(.*?)(\".*?>)"."/is";
		preg_match_all($suche,$content,$matches);

		$this->inline = $matches[2]; // Extrahierte Image Dateien
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

	public function send() {
		$content=&$this->content;
		$signature=&$this->signature;
		$text=&$this->text;
		
		
		// echo htmlspecialchars($content.$signature);exit;
		$mail=&$this->mail;
		$text=strip_tags(str_replace("<br>","\r\n",$content.$signature)); // preg_replace("/\<.*?\>/","")
        $mail->Body    = $content.$signature;
        $mail->AltBody = $text;
		try  {
			$mail->send();
		} catch (PHPMailer\PHPMailer\Exception $e) {
			echo "Message could not be sent. Mailer Error: ".$mail->ErrorInfo; 
		}
		

	}
}


?>
