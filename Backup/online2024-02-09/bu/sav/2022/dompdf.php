<?php

  require_once('./vendor/dompdf/dompdf/dompdf_config.inc.php');
  $dompdf = new DOMPDF();
  
  $html = file_get_contents("./index.php");
  $dompdf->load_html($html);
  $dompdf->render();
  $dompdf->stream("file.pdf");

/*  
require 'vendor/autoload.php';

// reference the Dompdf namespace
use dompdf\dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml('hello world');

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();
*/
?>


