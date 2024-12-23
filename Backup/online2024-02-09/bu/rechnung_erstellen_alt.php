Alt ?

<?php
$layout = array(
	'logo' 	 => "https://www.all-transport24.de/wp-content/uploads/2019/10/logo.png",
	'anrede' => "XSehr geehrte Damen und Herren,\r\n\r\nVielen Dank in Ihre Vertrauen in die All Transport 24 e. K.\r\nWir stellen Ihnen hiermit folgende Leistung in Rechnung:"
);


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
$_POST['renr']="20220023";

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
		



	
	
	

/*
#rechts {
background-color: #FFFFCC;
height: 100%;
width: 200px;
right: 10px;
position: absolute;
padding: 10px;
top: 0px;
margin-top: 0px;
margin-right: 20px;
margin-bottom: 0px;
}
*/
?>


<!--  css<br> body margin-left 10cm margin right:10cm -->
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">


<!doctype html>
<html lang="de">
<head>

<style type="text/css">
html {
	margin:0;
	padding:0;

	width:21cm;
	height:29cm;
	
}

body {
	position: relative;
	width: 210mm;
	height: 297mm;
	max-width: 210mm;
  
	margin-left: 1cm;
	margin-right:1cm; /* 50px; */
	margin-top: 1cm;
	margin-bottom: 1cm;
	
	font-family:Calibri,Arial,Tahoma;
	font-size:12pt;

	border: white 1px solid; /* sonst wird alles zusammengeschoben */  
}
	
div#rechts {
	position: absolute;
	right: 0px; /* #10px; */
	top: 0px;

	padding: 0px; /* 10px;*/
	margin-top: 0px;
	margin-right: 0px; /* #50px; */
	margin-bottom: 0px;
	
	/* border: green 1px solid; */
}

div#rechts img {
	height:100px;
}

div#rechts p {
	margin-top: 20px;
	color:#0080e0;
}
	
div#absender {
	margin-top:150px;
	width:350px;
	height:50px;
	font-size:20px;
	color:black;
	font-weight:1000;
}
div#absender div {
	font-size:12px;
	border-bottom:#0080d0 solid 1px;
	color:#0080e0;
	text-align:center;
}
	
div#daten {
	position: absolute;
	right: 0px; /* 10px */
	width: 300px;
	
	font-size:12px;

	border:#0080d0 solid 2px;
	
	padding: 5px;
	margin-top: 40px;
	margin-right: 0px; /* 50px; */
	margin-bottom: 0px;
}
div#betreff {
	margin-bottom: 20px;
	font-size:24px;
	font-weight: 1000;
}
#foot {
	font-size: 12px;
	position: absolute;
	bottom: 0;
}


div#spacer{
	margin-top: 150px;
	margin-bottom: 0px;
}

table#pos,table#pos tr * {
	border-left:#0070c0 solid 1px;
	border-right:#0070c0 solid 1px;
	
}

table#pos * th {
	border:#0070c0 solid 1px;
}


table#pos * * {
	padding-left:5px;
	padding-right:5px;
}

th {
	background-color:#8db4e2 ;
}

td {
	padding:1px;
	#text-align: initial;
	vertical-align:top;
}

button {
	border-size: 0px;
	border-style: solid;
	border-color: white;
	
	background-color: white;
	color:inherit;
	font: inherit;
	font-family: inherit;
	text-align: inherit;
	align-items:inherit;
	alignment:inherit;
	
	
	margin:0;
	padding:0;	
	width:100%;
}

button:hover {
	border-size:0px;
	border-style:  solid;
	border-color: red;
	background-color: #CCCCCC;
}
	
* {
    -webkit-print-color-adjust: exact !important;   /* Chrome, Safari */
    color-adjust: exact !important;                 /*Firefox*/
}
/* @page { 
	size: A4;
	margin: 0cm; 
}*/
@media print {
	body {
		# display: block;
	}
	
	button:hover {
		display: none !important;
	}
}

/*
#Blaufarben
#89cff0. Babyblau

#0070c0 linien der Tabelle Positionen
#8db4e2 Helles Blau bei überschriften
*/

	
</style>
</head>
<body>
<!-- 
<h1>Buchhaltung</h1>
<h2>Rechnung erstellen - Anlegen</h2>
-->

<!--
table, tr,th,td {
	border:#0070c0 solid 1px;
}

-->



</head>
<body>
<div id="rechts">

<button>
<img src="<?php echo $layout['logo'] ?>">
</button>
<br>
<button>
	<p> <?php 
		echo $abs['firma']."<br>".$abs['strasse']."<br><br>".$abs['plz']." ".$abs['ort'];
	?></p> 
</button>

</div>
 </body>
<div id="absender">
<div><button>
<?php 
echo $abs['firma'].",".$abs['strasse'].",".$abs['plz']." ".$abs['ort']; 
?></button></div>
<button>
<?php 

	$name="";
	if (isset($empf['vorname']) && isset($empf['nachname']) && $empf['vorname'] && $empf['nachname'] ) {
		$name=$empf['vorname']." ".$empf['nachname']."<br>";
	}
	echo $empf['firma']."<br>".$name.$empf['strasse']."<br>".$empf['plz']." ".$empf['ort']."<br>";
?></button></div>
</div>
<div id="daten">
<button>
<table width="100%">
<tr><td align="left">Rechnung-Nr:</td><td align="right"><?php echo $re['renr']; ?></td></tr>
<tr><td align="left">Rechnungsdatum:</td><td align="right"><?php echo $re['datum']; ?></td></tr>
<tr><td align="left">Leistungsmonat:</td><td align="right"><?php echo $re['leistung']; ?></td></tr>
<tr><td align="left">Ihre Kundennummer:</td><td align="right"><?php echo $re['kdnr']; ?></td></tr>
<tr><td align="left">Fälligkeitsdatum:</td><td align="right"><?php echo $re['faellig']; ?></td></tr>
</table>
</button>
</div>
<div id="spacer"></div>

<button>
<div id="betreff">Rechnungsnummer: <?php echo $re['renr']; ?></div> 
<?php
	echo $layout['anrede'];
?>
</button>
<table id="pos" cellspacing=0 width="100%">
<?php
	
	echo "<tr><th>Pos</th><th>Menge,Einheit</th><th>Thema</th><th>Einzelpreis</th><th>Gesamtpreis</th></tr>";
	//Posten
	$summe=0;
	$ust=0;
	$request="select * from `bu_re_posten` where `renr` = '".$re['renr']."' order by `pos`";
	$result = $db->query($request);
	while($row = $result->fetch_assoc()) {
	
		// $z1=$row['zuschlag'];
		// $z2=$row['zuschlag2'];
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
		$summe=$summe+$gesamt;

		if ($row['mwst']==0) {
			$row['mwst']=19;
		}
		$mwst=round($gesamt*$row['mwst']/100,2);
		$ust=$ust+$mwst;
		
		
		echo "<tr>";
		echo "<td>".$row['pos']."</td>";
		echo "<td>".$row['anz']." ".$einheit."</td>";
		echo "<td>".$z1." ".$z2."</td>";
		echo "<td align=\"right\">".sprintf("%.2f €",$row['netto'])."</td>";
		echo "<td align=\"right\">".sprintf("%.2f €",$gesamt)."</td>";
		echo "</tr>";
		
	} 
	$gesamtsumme=$summe+$ust;
	
// 			$b=$row['netto']*(1+$row['mwst']/100);
// 			$brutto=round($b,2);

?>
<tr><td colspan=5 style="border-top:#0070c0 dashed 2px">
	<table style="border:initial;padding:0;" width="100%" cellspacing=0 cellpadding=0>
		<tr style="border:initial;padding:0;"><td style="border:initial;padding:0;" align="left">Zwischensumme</td><td style="border:initial;padding:0;" align="right"><?php echo sprintf("%.2f €",$summe); ?></td></tr>
		<tr style="border:initial;padding:0;"><td style="border:initial;padding:0;" align="left">Umsatztzsteuer 19%</td><td style="border:initial;padding:0;" align="right"><?php echo sprintf("%.2f €",$ust); ?></td></tr>
	</table>	
</tr>
<tr><th colspan="5">
	<table style="border:initial;padding:0;" width="100%" cellspacing=0 cellpadding=0><tr style="border:initial;padding:0;"><td style="border:initial;padding:0;" align="left">Rechnungsbetrag</td><td style="border:initial;padding:0;" align="right"><?php echo sprintf("%.2f €",$gesamtsumme); ?></td></tr></table>
</th>

</tr>

</table>


<br>
<br>
<br>
<table id="foot" cellspacing=20 width="100%"> <tr>
<td width="33%"><?php
echo $abs['firma']."<br>".$abs['strasse']."<br>".$abs['plz']." ".$abs['ort'];
echo "<br><br>Inhaber: ";
echo $abs['inhaber'];
?>
</td>

<td width="33%">
<?php
echo "Bankverbindung:<br>".$abs['bankname']."<br><nobr>IBAN:".$abs['iban']."</nobr><br>BIC:".$abs['bic']."<br>";
?>
</td>

<td>
<?php
echo "Handelsregistzer:<br>".$abs['hrname']."<br>HRA:".$abs['hra']."<br>Ust-ID-Nr:".$abs['ustid']."<br>Betriebs-Nr:".$abs['betriebsnr'];
?>
</td>
</tr></table>


</body>
</html>
