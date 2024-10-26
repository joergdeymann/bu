<?php
	$to="joergdeymann@web.de";
	$from="From:Name <noreply@die-deymanns.de>\r\n";
	$subject="Test Head";
	$content="Wie gehts so";
	mail($to,$subject,$content,$from);
	
	// mail($to,$this->subject,$this->content,$this->headers);
?>