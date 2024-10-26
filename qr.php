<?php
	include "class/class_qr.php";
	$qr=new QR();
	echo $qr->getHTML("Hallo");
	$b64=$qr->getBase64("Moin");
	echo '<img src="'.$b64.'" alt="QR">';
	
	echo "<br>";
	echo htmlspecialchars($qr->getPaypalLink("deymanns","20.02"));
	echo "<br>";
	$b64=$qr->getBase64();
	echo '<img src="'.$b64.'" alt="QR">';
	
	
	
?>