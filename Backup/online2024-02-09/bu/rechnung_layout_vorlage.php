<?php
include 'dbconnect.php';
include 'class/class_pdf.php';
// include 'class/class_rechnung.php';
// include 'class/class_io.php';

// var_dump($_GET);

// echo "Layout:".$_GET['layout']."<br>";
// echo "Mahnstufe:".$_GET['mahnstufe']."<br>";
// exit; 


function loadLayout() {
	global $db;
	global $layout;
	global $mahnstufe;

	$request="SELECT * from bu_re_layout where firmanr=".$_POST['firmanr']." and nr='".$_POST['layout']."' and mahnstufe='".$_POST['mahnstufe']."'";
	$result = $db->query($request);
	return $result->fetch_assoc();		
}

$msg=""; //Meldungen zum, Benutzer 
$re = array (
	'renr'      => "200201234",
	'datum'     => "01.05.2002",
	'kdnr'	    => "KDNR10203040",
	'faellig'   => "15.05.2002",
	'leistung'  => "Juni 2022",	
	'firmanr'   => "0",
);


$abs = array(
	'firma'     => "Muster Firma",
	'strasse'   => "Musterstrasse 255",
	'plz'       => "00471",
	'ort'       => "Musterort",
	'vorname'   => "Mu",
	'nachname'  => "Ster",
	'inhaber'   => "Niemand",
	'bankname'  => "Sparkasse Emsland",
	'iban'      => "26675000101234567890",
	'bic'       => "NOLADE21EMS",
	'hrname'    => "Osnabrück",
	'hra'       => "111111",
	'ustid'     => "12345678",
	'betriebsnr'=> "1010101010"
);	
$empf = array(
	'firma'     => "Empfägerfirma",
	'strasse'   => "Empfängerstrasse 199",
	'plz'       => "00019",
	'ort'       => "Empfängerort",
	'vorname'   => "Empfän",
	'nachname'  => "Ger"
);	
$layout = array(
	'logo' 	 => "",  // Dateiname HTTP-Link oder absolut/relativer name
	'anrede' => "Vielen Dank für die Nutzung unserer Dienstleistung. Wir stellen folgendes ind Rechnung:",  // Anrede in der Rechnung
	'css'    => ""	 // Ort und Name des CSS-Files aus der Layout HTML
);

/*
	Einheiten für eine Postenzeile
*/
$einheiten_value=array(
 "Stunde",
 "Tour",
 "Frachtstück"
);

$einheiten_mz=array(
 "Stunden",
 "Touren",
 "Frachtstücke"
);

$zuschlag_value=array(
 "",
 "Nachtzuschlag",
 "Sonntagszuschlag"
);


$km_value=array(
  "",
 "pro KM",
 "Gesamt"
);


// $_GET['renr']="20020123";



//-----------------------------------------------------------------------------------

/* 
	Firmanr uebergeben
*/
if (isset($_GET['firmanr']) && $_GET['firmanr']) {
	$_POST['firmanr']=$_GET['firmanr'];
} 
if (!isset($_POST['firmanr'])) {
	$_POST['firmanr']=0;
}
// echo "Firmanr=".$_POST['firmanr'];
/* 
	Mahnstufe uebergeben
*/
if (isset($_GET['mahnstufe']) && $_GET['mahnstufe']) {
	$_POST['mahnstufe']=$_GET['mahnstufe'];
} 
if (!isset($_POST['mahnstufe'])) {
	$_POST['mahnstufe']="0";
}
/* 
	Layout uebergeben
*/

if (isset($_GET['layout']) && $_GET['layout']) {
	$_POST['layout']=$_GET['layout'];
} 
if (!isset($_POST['layout'])) {
	$_POST['layout']="0";
}






//Posten
$posten = "";
$posten.="<tr>";
$posten.="<td>1</td>";
$posten.="<td>7 Stunden</td>";
$posten.="<td>&nbsp;</td>";
$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp;",14.50)."</td>";
$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp; ",101.50)."</td>";
$posten.="</tr>";
$posten.="<tr>";
$posten.="<td>2</td>";
$posten.="<td>7 Stunden</td>";
$posten.="<td>Überstunden 25%</td>";
$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp;",3.62)."</td>";
$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp; ",25.37)."</td>";
$posten.="</tr>";
$posten.="<tr>";
$posten.="<td>3</td>";
$posten.="<td>4 Stunden</td>";
$posten.="<td>Nachtzulage 25%</td>";
$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp;",3.62)."</td>";
$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp; ",14.50)."</td>";
$posten.="</tr>";


$re['posten']=$posten;
	
$re['summe' ]     =sprintf("%.2f € &nbsp;",(101.5+25.37+14.5));
$re['ust']        =sprintf("%.2f € &nbsp;",(101.5+25.37+14.5)*19/100)	;
$re['gesamtsumme']=sprintf("%.2f €",(101.5+25.37+14.5)*1.19);



	
/* 
		Output generieren
*/
// echo "Output";
// echo htmlspecialchars(strlen($posten));
// exit;


$layout=loadLayout();

$dir="vorlage/".$_POST['firmanr']."/".$_POST['layout'];  // Firmenindividuell
$file="/rechnung".$_POST['mahnstufe'].".html";
$css_file="/rechnung.css";

$layout['css']=$dir.$css_file;
$layout['html']=$dir.$file; 	
// echo $layout['css']."<br>";
// echo $layout['html']."<br>";
// exit;
// $layout['html']="/".basename( getcwd())."/".$layout['html'];

// chdir($_SERVER["DOCUMENT_ROOT"]);
// $content=file_get_contents("FazalTamiz/vorlage/0/1/rechnung0.html");

// $content=file_get_contents("vorlage/0/1/rechnung0.html");

// echo $content;
// 	exit;
// echo $layout."<br>";
// echo basename( getcwd())."<br>";
// echo __dir__."<br>";
// exit;
	

// Fill HTML Content for Rechnung
// $content=file_get_contents('rechnung.html');
$content=file_get_contents($layout['html']);
//  echo 	 $rechnung->getVorlage();
// var_dump($layout); 
//  exit;
foreach($layout as $k => $v) {
		$xkey='$layout[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}

foreach($abs as $k => $v) {
		$xkey='$abs[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}
foreach($empf as $k => $v) {
		$xkey='$empf[\''.$k.'\']';
		// echo "$xkey.<br>";
		$content=str_replace($xkey,$v,$content);
}
if (isset($empf['vorname']) && isset($empf['nachname']) && $empf['vorname'] && $empf['nachname'] ) {
	$re['name']=$empf['vorname']." ".$empf['nachname'];
} else {
	$re['name']="";
}

foreach($re as $k => $v) {
		$xkey="\$re['$k']";
		$content=str_replace($xkey,$v,$content);
}


// $rechnung->senden(2); //Versandart 2 = DRUCKEN

// echo '<div style="position:relativ;border: red solid 1px;scale:25%">'.$content.'</div>';

//echo '<div style="width:210mm; height:297mm;">';
// echo $content;
// echo '</div>';
//exit;
// echo '<div style="aspect-ratio:210 / 297;">';

$output=new PDF();
$output->setContent($content);
$output->print();
// 	echo '</div>';
// $output->destruct();
	
?>
	
	
