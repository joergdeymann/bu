<?php
// include 'dbconnect.php';
include 'class/class_pdf.php';
include 'class/class_rechnung.php';
include 'class/class_io.php';

// var_dump($_GET);


$msg=""; //Meldungen zum, Benutzer 
$re = array();

$abs = array(
	'firma'     => "",
	'strasse'   => "",
	'plz'       => "",
	'ort'       => "",
	'vorname'   => "",
	'nachname'  => "",
	'inhaber'   => "",
	'bankname'  => "",
	'iban'      => "",
	'bic'       => "",
	'hrname'    => "",
	'hra'       => "",
	'ustid'     => "",
	'betriebsnr'=> ""
);	
$empf = array(
	'firma'     => "",
	'strasse'   => "",
	'plz'       => "",
	'ort'       => "",
	'vorname'   => "",
	'nachname'  => ""
);	
$layout = array(
	'logo' 	 => "",  // Dateiname HTTP-Link oder absolut/relativer name
	'anrede' => "",  // Anrede in der Rechnung
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


// $_GET['renr']="20220023";



//-----------------------------------------------------------------------------------

/* 
	Rechnungsnummer uebergeben
*/
if (isset($_GET['renr']) && $_GET['renr']) {
	$_POST['renr']=$_GET['renr'];
}
if (isset($_POST['renr']) && $_POST['renr']) {
} else {
	$msg="Keine Rechnung angegeben";
	echo $msg;
	exit;
}

/* 
	Mahnstufe uebergeben
*/
if (isset($_GET['mahnstufe']) && $_GET['mahnstufe']) {
	$_POST['mahnstufe']=$_GET['mahnstufe'];
} 
if (isset($_POST['mahnstufe']) && $_POST['mahnstufe']) {
} else {
	$_POST['mahnstufe']="0";
}
/* 
	Firmennummer(sich selbst als Firma) uebergeben
*/
if (isset($_GET['firmanr']) && $_GET['firmanr']) {
	$_POST['firmanr']=$_GET['firmanr'];
} 
if (isset($_POST['firmanr']) && $_POST['firmanr']) {
} else {
	$_POST['firmanr']="0";
}

$rechnung=new Rechnung();
$rechnung->setFirma($_POST['firmanr']);
$rechnung->setReNr($_POST['renr']);
$rechnung->setMahnstufe($_POST['mahnstufe']);

if ($rechnung->row_re['renr']) {
	$re['renr']     =$rechnung->row_re['renr'];
	$re['datum']    =$rechnung->getDate('datum');

	$re['kdnr']     =$rechnung->row_re['kdnr'];
	$re['faellig']  =$rechnung->getDate('faellig');
	
	$t=strtotime($rechnung->row_re['leistung']);
	$monate = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	$re['leistung']=$monate[date("n",$t)]." '".date("y",$t);
	
	$re['layout']=$rechnung->row_re['layout'];
	$re['firmanr']=$rechnung->row_re['firmanr'];
} else {
	$msg="Rechnung ".$_POST['renr']." nicht vorhanden!";
	echo $msg;
	exit;
}


/*
       Firmendaten
*/

foreach($abs as $k => $v) {
	$abs[$k]=$rechnung->row_firma[$k];
}

/*
	Layout einstellungen
*/
$layout['logo']=$io->getBase64Image($rechnung->getLogo()); 
$layout['anrede']=$rechnung->row_layout['retext']; // Anrede !! 
$layout['css']=$rechnung->layout['css'];

/*
	Empfängerdaten
*/
/*
var_dump($empf);
var_dump($rechnung->row_kunde);
*/
foreach($empf as $k => $v) {
	$empf[$k]=$rechnung->row_kunde[$k];
}


/*
	Felder die nicht in der Datei sind:
	nettosumme
	nettogesamt
	bruttosumme
	bruttogesamt
	mwstsatz    = mwstsatz[$mwst]
	mwstsumme   = mwstsumme[$mwst]
	
	
	Umprogramieren
	Header
	<posten>
	zb:
	<tr>
		<td>$re['pos']</td>
		<td>$re['anz'] $re['einheit']</td>
		<td>$re['re_text']</td>
		<td>$re['netto']</td>
		<td>$re['nettosumme']</td>
	
		<td>$re
	</posten>
*/


//Posten
$posten = "";
$re['summe']=0;
$re['ust']=0;

$rechnung->PostenSelectExtend();
while ($rechnung->PostenGet()) {
	
	$row=$rechnung->row_posten;
	$z=$row['zuschlag'];
	$z1=$zuschlag_value[$z];
	
	$z=$row['km'];
	$z2=$km_value[$z];
	
	$e=$row['einheit'];
	$einheit=$einheiten_mz[$e];
	$einheit=$row['einheit_mehrzahl'];

	if ($row['anz'] == 1) {
		$e=$row['einheit'];
		$einheit=$einheiten_value[$e];
		$einheit=$row['einheit_einzahl'];
		
		
	}
	
	$z1=$row['re_text'];
	
	$gesamt=$row['netto']*$row['anz'];
	
	$re['summe']=$re['summe']+$gesamt;

	if ($row['mwst']==0) {
		$row['mwst']=19;
	}
	$mwst=round($gesamt*$row['mwst']/100,2);
	$re['ust']=$re['ust']+$mwst;
	
	
	$posten.="<tr>";
	$posten.="<td>".$row['pos']."</td>";
	$posten.="<td>".$row['anz']." ".$einheit."</td>";
	$posten.="<td>".$z1." ".$z2."</td>";
	$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp;",$row['netto'])."</td>";
	$posten.="<td align=\"right\">".sprintf("%.2f € &nbsp; ",$gesamt)."</td>";
	$posten.="</tr>";
} 
$re['posten']=$posten;
$re['gesamtsumme']=$re['summe']+$re['ust'];

$re['summe' ]     =sprintf("%.2f € &nbsp;",$re['summe']);
$re['ust']        =sprintf("%.2f € &nbsp;",$re['ust'])	;
$re['gesamtsumme']=sprintf("%.2f €",$re['gesamtsumme']);


	
/* 
		Output generieren
*/
// echo "Output";
// echo htmlspecialchars(strlen($posten));
// exit;

// Fill HTML Content for Rechnung
// $content=file_get_contents('rechnung.html');
$content=file_get_contents($rechnung->getVorlage());
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

// echo $content;


$output=new PDF();
$output->setContent($content);
$output->print();
// $output->destruct();
	
?>
	
	
