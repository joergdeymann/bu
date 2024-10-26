<?php
$to = "joergdeymann@web.de";
$subject = "My subject";
$txt = "Hello world!";
$headers = "From: joerg.deymann@die-deymanns.de" . "\r\n";

mail($to,$subject,$txt,$headers);


?> 
