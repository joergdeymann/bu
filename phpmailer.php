<?php
$debug = true; // or
$debug = false;
// require_once "../vendor/autoload.php";


// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\SMTP;

require_once "../vendor/phpmailer/phpmailer/src/PHPMailer.php";
require_once "../vendor/phpmailer/phpmailer/src/Exception.php";
require_once "../vendor/phpmailer/phpmailer/src/SMTP.php";

try {
        // neue instanz der klasse erstellen
        $mail = new PHPMailer\PHPMailer\PHPMailer($debug);

        if ($debug) {
                // gibt einen ausführlichen log au
                $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
        }
        // authentifiziere dich über den smtp-login
        $mail->isSMTP();
        $mail->SMTPAuth = true;

        // login
        $mail->Host       = "mx2ecf.netcup.net";
        $mail->Port       = "587";
        $mail->Username   = "joerg.deymann@die-deymanns.de";
        $mail->Password   = "Radio#123";
		
		
		
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // PORT 587
        // $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;       // PORT 465
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

        $mail->addAttachment("img/logo.png", "logo.png");
		/*
			oder :
				foreach($AttmFiles as $key => $value)
				{ $mail->addAttachment($value, $key); }			
		*/

		$from='joerg.deymann@die-deymanns.de';
		$fromName='Jörg Deymann';
	
		$from='Lplucalo@gmail.com';
		$fromName='Jörg Deymann';
		
		// $mail->setFrom($from, $fromName);
		$mail->setFrom($from);
		$to="test-446a9d@test.mailgenius.com";
		$toName="Jörg D.";
		
		$mail->addAddress($to);
		// $mail->addAddress($to, $toName);



        $mail->isHTML(true);
		$subject="Test Mail mit ä ö und ü";
		$html="Reihe 1<br>Reihe 2<br>Reihe 3-ä ü ";
		$text=strip_tags($html); // preg_replace("/\<.*?\>/","")
        $mail->Subject = $subject;  // utf8_encode
        $mail->Body    = $html;
        $mail->AltBody = $text;

        $mail->send();

} catch (PHPMailer\PHPMailer\Exception $e) {
    echo "Message could not be sent. Mailer Error: ".$mail->ErrorInfo; 
}
?>
