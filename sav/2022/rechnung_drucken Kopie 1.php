<?php
// $_POST['renr']="20220023";
$layout = array(
	'logo' 	 => "https://www.all-transport24.de/wp-content/uploads/2019/10/logo.png",
	'anrede' => "XSehr geehrte Damen und Herren,\r\n\r\nVielen Dank in Ihre Vertrauen in die All Transport 24 e. K.\r\nWir stellen Ihnen hiermit folgende Leistung in Rechnung:"
);
$path = 'img/all-transport24.png';
$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
$layout['logo']=$base64;


$abs = array(
	'firma'     => "All Transport 24",
	'strasse'   => "Bonifaciusstraße 160",
	'plz'       => "D-45309",
	'ort'       => "Essen",
	'vorname'   => "",
	'nachname'  => "",
	'inhaber'   => "Frau Miriam Stamm",
	'bankname'  => "Deutsche Bank Essen",
	'iban'      => "DE32 3607 0024 0104 9824 00",
	'bic'       => "DEUTDEDBESS",
	'hrname'    => "Amtsgericht Essen",
	'hra'       => "10585",
	'ustid'     => "DE308854876",
	'betriebsnr'=> "94140767"
);	

$empf = array(
	'firma'     => "Xbikesale Solutions GmbH",
	'strasse'   => "XEugen-Sänger-Ring 7b",
	'plz'       => "X85649",
	'ort'       => "XBrunntal",
	'vorname'   => "Xa",
	'nachname'  => "Xb"
);	

$re=array(
	'renr'  => "XRE345678",
	'datum' => "X20.02.2022",
	'kdnr'  => "X10003",
	'faellig' => "X15.03.2022",
	'leistung' => "XFebruar '22" // datum("monatsnamen, Jahr",$re;
);
	

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

	
	
function getBR($s) {
	$suchen = "/(\r\n|\n|\r|\t)/";
	$ersetzen = "<br>";
	return preg_replace($suchen,$ersetzen,$s);
}

include("dbconnect.php");

if (isset($_GET['renr']) && $_GET['renr']) {
	$_POST['renr']=$_GET['renr'];
}
if (isset($_POST['renr']) && $_POST['renr']) {
}


//Rechnungsdaten
$request="select * from `bu_re` where `renr` = '".$_POST['renr']."' limit 1";
$result = $db->query($request);
$row = $result->fetch_assoc();
if ($row) {
	$re['renr']=$row['renr'];
	$re['datum']=date("d.m.Y",strtotime($row['datum']));
	$re['kdnr']=$row['kdnr'];
	$re['faellig']=date("d.m.Y",strtotime($row['faellig']));
	
	$t=strtotime($row['leistung']);
	$monate = array('','Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
	$re['leistung']=$monate[date("n",$t)]." '".date("y",$t);
	
	$re['layout']=$row['layout'];
	$re['firma']=$row['firma'];
}

//Firmendaten 0 = Standart Firma		
if ($row['firma'] == 0) {
	$where="where prio=1";
} else {
	$where="where recnum=".$row['firma'];
}
	
$request="select * from `bu_firma` $where  limit 1";
$result = $db->query($request);
$row = $result->fetch_assoc();
if ($row) {
	foreach($abs as $k => $v) {
		$abs[$k]=$row[$k];
	}
	$layout['logo']=$row['logo'];
}

//Layout = Standart Layout		
if ($re['layout'] == 0) {
	$where="where prio=1";
} else {
	$where="where nr=".$re['layout'];
}
	
$request="select * from `bu_re_layout` $where limit 1";
$result = $db->query($request);
$row = $result->fetch_assoc();
if ($row) {
	$layout['anrede']=getBR($row['retext']);
	if ($row['logo']) {
		$layout['logo']=$row['logo'];
	}						
}

//Kunde		
$request="select * from `bu_kunden` where kdnr='".$re['kdnr']."' limit 1";
$result = $db->query($request);
$row = $result->fetch_assoc();
if ($row) {
	foreach($empf as $k => $v) {
		$empf[$k]=$row[$k];
	}
}

// Posten		
$posten = "";
//Posten
$re['summe']=0;
$re['ust']=0;
$request="select * from `bu_re_posten` where `renr` = '".$re['renr']."' order by `pos`";
$result = $db->query($request);
while($row = $result->fetch_assoc()) {
	$z=$row['zuschlag'];
	$z1=$zuschlag_value[$z];
	
	$z=$row['km'];
	$z2=$km_value[$z];
	
	$e=$row['einheit'];
	$einheit=$einheiten_mz[$e];

	if ($row['anz'] == 1) {
		$e=$row['einheit'];
		$einheit=$einheiten_value[$e];
	}
	
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
	$posten.="<td align=\"right\">".sprintf("%.2f €",$row['netto'])."</td>";
	$posten.="<td align=\"right\">".sprintf("%.2f €",$gesamt)."</td>";
	$posten.="</tr>";
	
} 
$re['posten']=$posten;
$re['gesamtsumme']=$re['summe']+$re['ust'];

$re['summe' ]     =sprintf("%.2f €",$re['summe']);
$re['ust']        =sprintf("%.2f €",$re['ust'])	;
$re['gesamtsumme']=sprintf("%.2f €",$re['gesamtsumme']);


$path = 'img/all-transport24.png';
$path = 'https://www.all-transport24.de/wp-content/uploads/2019/10/logo.png';
$path = $layout['logo'];

$type = pathinfo($path, PATHINFO_EXTENSION);
$data = file_get_contents($path);
$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
$layout['logo']=$base64;

// Fill HTML Content for Rechnung
$content=file_get_contents('rechnung.html');
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



	// echo $content;

    require_once '../vendor/autoload.php';  
    use Dompdf\Dompdf; 
    use Dompdf\Options; 
    $dompdf = new Dompdf();

	//  $html = file_get_contents("../FasilTamiz/text.html"); 
 echo getcwd()."<br>";   
	$dompdf->set_option('chroot', getcwd()); //assuming HTML file is in the root folder Damit die CSS Datei gefunden wird! 
	// Instantiate and use the dompdf class
    /*
	For XAMPP on MAC, you could also set it as in dompdf.php
	DOMPDF_TEMP_DIR = '/Applications/XAMPP/xamppfiles/temp'
	*/
	
	
	/* permission denied 
	$options = $dompdf->getOptions();
	$options->setTempDir('/Applications/XAMPP/xamppfiles/temp');
	$dompdf->setOptions($options);
	*/	
	
	/* permission denied 
	$tmp='/Applications/XAMPP/xamppfiles/temp'; 
	$dompdf->set_option('tempDir',$tmp);
	*/

	/* permission denied */
	$tmp=getcwd()."/tmp"; 
	$dompdf->set_option('tempDir',$tmp);
	
	
	/*
	$dompdf->set_option('enable_remote', TRUE);
	$dompdf->set_option('enable_css_float', TRUE);
	$dompdf->set_option('enable_html5_parser', FALSE);
	*/
	
    echo "2<br>";   
	$dompdf->loadHtml($content); 
	
     
    //$dompdf->setPaper('A4', 'landscape'); 
    echo "3<br>";   
    $dompdf->setPaper('A4', 'portrait'); 
    echo "4<br>";   
    $dompdf->render(); 
	// $dompdf->stream();
	// Output the generated PDF (1 = download and 0 = preview)
    echo "5<br>";   
	$dompdf->stream("codex",array("Attachment"=>0));

	

?>
	
	
