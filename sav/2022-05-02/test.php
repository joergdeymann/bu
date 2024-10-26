<?php
$content='
<html>

<head>
<link type="text/css" rel="stylesheet" href="test.css">
</head>

<body>
<h1>Halllo</h1>
Moin<br>Moin2<br>

</body></html>;
';




	// echo $content;

    require_once '../vendor/autoload.php';  
    use Dompdf\Dompdf; 
    use Dompdf\Options; 
    $dompdf = new Dompdf();

	$dompdf->set_option('chroot', getcwd()); //assuming HTML file is in the root folder Damit die CSS Datei gefunden wird! 
	$tmp=getcwd()."/tmp"; 
	$dompdf->set_option('tempDir',$tmp);
	
	$dompdf->loadHtml($content); 
    $dompdf->setPaper('A4', 'portrait'); 
    $dompdf->render(); 
	// $dompdf->stream("codex",array("Attachment"=>0));
	$dompdf->stream();
	

?>
	
	
