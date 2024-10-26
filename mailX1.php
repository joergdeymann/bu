<?php

$empfaenger  = "joergdeymann@web.de"; // Mailadresse Empfaenger
$betreff     = "PHP-Mail-Test mit Umlauten";
$mailtext    = "Inhalt einer Mail zum Test von PHP ";
$mailtext    .= "mit den deutschen Sonderzeichen öäüß";
$absender    = "Ich Hier <joergdeymann@web.de>";
$sender_name = "Ich Hier";
$sender_mail = "joerg.deymann@die-deymanns.de";

$headers   = array();
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-type: text/html; charset=utf-8";
// $headers[] = "From: {$absender}";
// $headers[] = 'From: "'.addslashes($sender_name).'" <'.$sender_email.">";
$headers[] = 'From: '.addslashes($sender_name).' <'.$sender_mail.">";
// $headers[] = "Bcc: Der Da <mitleser@example.com>"; // falls Bcc benötigt wird
$headers[] = "Reply-To: {$sender_mail}";
$headers[] = "Subject: {$betreff}";
$headers[] = "X-Mailer: PHP/".phpversion();


$ergebnis=mail($empfaenger, $betreff, $mailtext,implode("\r\n",$headers));
if ($ergebnis) {
	echo "E-Mail mit Umlauten wurde gesendet!";
}
?>
