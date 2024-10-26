<?php
include "../class/class_projekt_kalender.php";



$ton1	="rgba(0,153,255,0.5)";   // #0099FF
$ton2  	="rgba(102,255,255,0.5)"; // #66FFFF ("#6666FF","#9999FF","#00FFFF")
$licht1 ="#FFFF00";               // #FFFF00  
$licht2 ="#FFFFCC";				  // #FFFFCC
$reise 	="#FF33FF";
$schul 	="#66FF66";
$sperr 	="#FF3366";

$event_color=array("",$licht1,$ton1,$sperr,$schul);
$kalender=new Projekt_kalender();
$kalender->backgroundcolor_reisetag=$reise;
// print_r($_POST);
// $event[3]['color'] ="#66FF66";

// $kalender->color['links'];

/* 
	Daten vorbereiten: alle Kunden laden
*/

$html_kunden ='<datalist id="kunden">';
$html_kunden.='<option value="ME Event">';
$html_kunden.='<option value="Acoustic Network">';
$html_kunden.='<option value="Die Deymann\'s">';
$html_kunden.='<option value="5">Nummer 5</option>'; // Das wäre dann korrekt , 5 istr dann recnum;
$html_kunden.='</datalist>';

/* 
	Daten vorbereiten: alle Equipment laden
*/
$html_equipment ='<datalist id="equipment">';
$html_equipment.='<option value="Mischpult Soundcraft">';
$html_equipment.='<option value="Mischpult Alan&Heath">';
$html_equipment.='<option value="Mischpult Scoby">';
$html_equipment.='</datalist>';

/* 
	Daten vorbereiten: alle vorhanden Hotels laden
*/
$html_unterkunft ='<datalist id="unterkunft">';
$html_unterkunft.='<option value="Maritim Hotel Hamburg">';
$html_unterkunft.='<option value="IBIS Hotel Duisburg">';
$html_unterkunft.='<option value="Die Linde Markkleeberg">';
$html_unterkunft.='</datalist>';


$event=array();
$event[0]['start'] ="2024-01-12";
$event[0]['ende']  ="2024-01-12";
$event[0]['color'] =$ton1;
$event[0]['info']  ="Index"; 

$event[4]['start'] ="2024-01-13";
$event[4]['ende']  ="2024-01-13";
$event[4]['color'] =$ton2;
$event[4]['info']  ="Index"; 
$event[4]['left']  ="An $"; 
$event[4]['right'] ="Ab $"; 
       
$event[1]['start'] ="2024-01-26";
$event[1]['ende']  ="2024-01-29";
$event[1]['color'] =$licht1;
$event[1]['info']  ="Nix"; 

$event[5]['start'] ="2024-01-26";
$event[5]['left']  ="An"; 

$event[6]['start'] ="2024-01-29";
$event[6]['color'] =$reise;
$event[6]['right'] ="Ab $"; 
       
$event[2]['start'] ="2024-02-03";
$event[2]['ende']  ="2024-02-13";
$event[2]['color'] ="#00FFFF";
$event[2]['info']  ="Nr 3"; 

$event[3]['start'] ="2024-01-23";
$event[3]['ende']  ="2024-01-25";
$event[3]['color'] =$licht2;
$event[3]['info']  ="Index"; 

$event[7]['start'] ="2024-01-30";
$event[7]['ende']  ="2024-01-31";
$event[7]['color'] =$sperr;
$event[7]['info']  ="Index"; 

$event[8]['start'] ="2024-01-20";
$event[8]['ende']  ="2024-01-21";
$event[8]['color'] =$schul;
$event[8]['info']  ="Index"; 

$event[9]['start'] ="2024-01-19";
$event[9]['color'] =$reise;

$anchor="1";
$focus=1;
if (!isset($_POST['equipment_name']['1'])) {
	$_POST['equipment_name']['1']="X";
	$_POST['equipment_name']['1']="";
}
if (!empty($_POST['add_equipment'])) {
	$k=count($_POST['equipment_name'])+1;
	$_POST['equipment_name'][$k]="";
	$focus=4;
}
$pressed_btn=0;
if (!empty($_POST['btn_licht'])) {
	$_POST['event_typ']=1;
	$pressed_btn=1;

}
if (!empty($_POST['btn_ton'])) {
	$_POST['event_typ']=2;
	$pressed_btn=2;
}
if (!empty($_POST['btn_sperr'])) {
	$_POST['event_typ']=3;
	$pressed_btn=3;
}
if (!empty($_POST['btn_schul'])) {
	$_POST['event_typ']=4;
	$pressed_btn=4;
}
if ($pressed_btn>0) {
	$_POST['event_typ_detail']="";
	$focus=2;
	$anchor="2";
}
if (!empty($_POST['kalender_tag'])) {
	$anchor="2";
}

// Eingabe von Name der Veranstaltung und Ort der Veranstaltung
if (empty($_POST['phase'])) {
	$_POST['phase']=0;
}


// Eingabe des Zeitraums
if (isset($_POST['btn_zeitraum'])) {
	$_POST['phase']=1;
	if (!empty($_POST['kalender_select_start'])) {
		$_POST['kalender_select_start']="";
	}
	$focus=2;
}
if (isset($_POST['btn_anreise'])) {
	$_POST['phase']=2;
	$focus=2;
}
if (isset($_POST['btn_abreise'])) {
	$_POST['phase']=0;
	$focus=2;
}



/*
if (!empty($_POST['kalender_select_start']) and !empty($_POST['kalender_select_ende']) and $_POST['phase']==1) {
	$_POST['projekt_start']=$_POST['kalender_select_start'];
	$_POST['projekt_ende']=$_POST['kalender_select_ende'];
	$_POST['phase']=2;
} 
*/ 
if (!empty($_POST['undo'])) {
	// echo "phase=".$_POST['phase'];
	if ($_POST['phase']==1) {
		$_POST['reset']=1;
		$_POST['phase']=0;
	}
	if ($_POST['phase']==2) {
		$_POST['phase']=1;
		$_POST['kalender_tag']="";
		$_POST['anreise']="";
		$_POST['fahrtkosten_erstattung']="";
	}
	if ($_POST['phase']==0 and !empty($_POST['abreise'])) {
		$_POST['phase']=2;
		$_POST['kalender_tag']="";
		$_POST['abreise']="";
	}		
}	
if (!empty($_POST['reset'])) {
	$_POST['projekt_start']="";
	$_POST['projekt_ende']="";
	$_POST['abreise']="";
	$_POST['anreise']="";
	$_POST['phase']="";
	$_POST['kalender_select_start']="";
	$_POST['kalender_select_ende']="";
	$_POST['kalender_tag']="";
	$_POST['fahrtkosten_erstattung']="";
}	
	
// Autoswitch falls gewümscht
if (empty($_POST['kalender_select_start']) and empty($_POST['kalender_select_ende']) and $_POST['phase'] == 0 and !empty($_POST['kalender_tag'])) { // and $_POST['phase'] == 0
	$_POST['projekt_ende']="";
	$_POST['abreise']="";
	$_POST['anreise']="";
}
if (!empty($_POST['kalender_tag']) and $_POST['phase'] == 0 and !empty($_POST['kalender_select_start'])) {
	$_POST['projekt_start']=$_POST['kalender_select_start'];
	$_POST['projekt_ende']=$_POST['kalender_tag'];
	if ((new DateTime($_POST['kalender_select_start'])) > (new DateTime($_POST['kalender_tag']))) {
		$_POST['projekt_ende']=$_POST['kalender_select_start'];
		$_POST['projekt_start']=$_POST['kalender_tag'];
	} 

	$_POST['phase']=1;
} else 
if (!empty($_POST['kalender_tag']) and ($_POST['phase']==1)) {
	$_POST['anreise']=$_POST['kalender_tag'];
	$_POST['phase']=2;
} else 
if (!empty($_POST['kalender_tag']) and ($_POST['phase']==2)) {
	$_POST['abreise']=$_POST['kalender_tag'];
	$_POST['phase']=0;
	$focus=2;
	$_POST['kalender_tag']="";
	$_POST['kalender_select_start']="";
	$_POST['kalender_select_ende']="";
	if ((new DateTime($_POST['anreise'])) > (new DateTime($_POST['abreise']))) {
		$p=$_POST['abreise'];
		$_POST['abreise']=$_POST['anreise'];
		$_POST['anreise']=$p;
	}
}
	

switch ($_POST['phase']) {
	case 0:
		$_POST['kalender_usage']=0;
		$kalender->usage=$_POST['kalender_usage'];
		$headline="Wann ?";
		$button="btn_zeitraum";
		break;
	case 1:
		$_POST['kalender_usage']=1;
		$kalender->usage=$_POST['kalender_usage'];
		$headline="Anreise";
		$button="btn_anreise";
		// if (!empty($_POST['kalender_tag'])) {
		// 	$_POST['anreise']=$_POST['kalender_tag'];
		// }
		break;
	case 2:
		$_POST['kalender_usage']=1;
		$kalender->usage=$_POST['kalender_usage'];
		$headline="Abreise";
		$button="btn_abreise";
		// if (!empty($_POST['kalender_tag'])) {
		//	$_POST['abreise']=$_POST['kalender_tag'];
		// }
		break;
	default:
		$_POST['kalender_usage']=0;
		$kalender->usage=$_POST['kalender_usage'];
		$headline="Wann ?";
		$button="btn_zeitraum";
		break;
	
		
} 

// if (!empty($_POST['kalender_select_start']) and !empty($_POST['kalender_select_ende'])) {

if (!empty($_POST['projekt_start']) and !empty($_POST['projekt_ende'])) {
	$ev=array();
	$ev['start']=(new DateTime($_POST['projekt_start']))->format("Y-m-d");	   
	$ev['ende'] =(new DateTime($_POST['projekt_ende']))->format("Y-m-d");	   
	$ev['color']="orange";
	if (!empty($_POST['event_typ'])) {
		$ev['color']=array("orange",$licht1,$ton1,$sperr,$schul)[$_POST['event_typ']];
	}
	
	$event[]=$ev;
}
if (!empty($_POST['anreise'])) {
	$ev=array();
	$ev['start']=(new DateTime($_POST['anreise']))->format("Y-m-d");	   
	$ev['left']  ="An"; 
	$ev['color']=$reise;
	$event[]=$ev;
}

if (!empty($_POST['abreise'])) {
	$ev=array();
	$ev['start']=(new DateTime($_POST['abreise']))->format("Y-m-d");	   
	$ev['right']  ="Ab"; 
	$ev['color']=$reise;
	$event[]=$ev;
}
	   
	   
// echo "<pre>";
// print_r($event);	   
// echo "</pre>";
foreach($event as $v)  {
	$kalender->addEvent($v);
}


if (empty($_POST['event_name']) ) 	$_POST['event_name']="";
if (empty($_POST['event_ort']) ) 	$_POST['event_ort']="";
if (empty($_POST['anreise']) ) 	    $_POST['anreise']="";
if (empty($_POST['abreise']) ) 	    $_POST['abreise']="";
if (empty($_POST['projekt_start']) ) 	    $_POST['projekt_start']="";
if (empty($_POST['projekt_ende']) ) 	    $_POST['projekt_ende']="";
if (empty($_POST['event_typ']) ) 	    $_POST['event_typ']="";
if (empty($_POST['event_typ_detail']) ) 	    $_POST['event_typ_detail']="";
if (empty($_POST['kunde_name']) ) 	    $_POST['kunde_name']="";
if (empty($_POST['equipment_name']) ) 	    $_POST['equipment_name']="";
if (empty($_POST['event_typ_detail']) ) 	    $_POST['event_typ_detail']="";
if (!empty($_POST['btn_event_typ_detail'])) {
	$_POST['event_typ_detail']=$_POST['btn_event_typ_detail'];
	$focus=2;
}	
if (empty($_POST['fahrtkosten_erstattung']) ) 	    $_POST['fahrtkosten_erstattung']="";
if (empty($_POST['fahrtkosten']) ) 	    			$_POST['fahrtkosten']="";
if (empty($_POST['strecke']) ) 	    				$_POST['strecke']="";
if (empty($_POST['fahrten']) ) 	    				$_POST['fahrten']="";
if (!empty($_POST['fahrtkosten_ja'])) {
	$_POST['fahrtkosten_erstattung']=1;
	$focus=3;
}
if (!empty($_POST['fahrtkosten_nein'])) {
	$_POST['fahrtkosten_erstattung']=2;
	$focus=4;
}

if (empty($_POST['unterkunft_noetig']) ) 	    $_POST['unterkunft_noetig']="";
if (!empty($_POST['unterkunft_ja'])) {
	$_POST['unterkunft_noetig']=1;
	$focus=7;
}
if (!empty($_POST['unterkunft_nein'])) {
	$_POST['unterkunft_noetig']=2;
	$_POST['unterkunft_gestellt']="";
	$focus=6;
}
if (empty($_POST['unterkunft_gestellt']) ) 	    $_POST['unterkunft_gestellt']="";
if (!empty($_POST['unterkunft_gestellt_ja'])) {
	$_POST['unterkunft_gestellt']=1;
	// if ($focus == "") 
	$focus=5;
	// $focus=6;
}
if (!empty($_POST['unterkunft_gestellt_nein'])) {
	$_POST['unterkunft_gestellt']=2;
	$focus=6;
}
if (empty($_POST['unterkunft_name']) ) 	    $_POST['unterkunft_name']="";
if (empty($_POST['info']) ) 	    $_POST['info']="";

$autofocus="";
$html = "";
$html.= '
	<!doctype html>
	<html lang="de">

	<head>
	<meta charset="utf-8">
	<!-- meta name="viewport" content="width=320px, initial-scale=1, user-scalable=yes"-->
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">


<meta name="pragma" content="no-cache" />
<meta name="robots" content="noarchive" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="cache-control" content="no-cache" />

	
	</head>
	<body>
	';
// $html.= '<form method="POST" action="projekt_kalender.php" style="width:320px;border:1px solid red;">';
$html.= '<span id="P1"></span>';
$html.= '<form  method="POST" action="projekt_kalender.php">';
$html.= '<h1>Projekt anlegen</h1>';	
$html.= '<input type="hidden" name="kalender_usage" value="'.$_POST['kalender_usage'].'">';
$html.= '<input type="hidden" name="phase" value="'.$_POST['phase'].'">';
$html.= '<input type="hidden" name="anreise" value="'.$_POST['anreise'].'">';
$html.= '<input type="hidden" name="abreise" value="'.$_POST['abreise'].'">';
$html.= '<input type="hidden" name="projekt_start" value="'.$_POST['projekt_start'].'">';
$html.= '<input type="hidden" name="projekt_ende"  value="'.$_POST['projekt_ende'].'">';
$html.= '<input type="hidden" name="event_typ"  value="'.$_POST['event_typ'].'">';
$html.= '<input type="hidden" name="event_typ_detail"  value="'.$_POST['event_typ_detail'].'">';
$html.= '<input type="hidden" name="fahrtkosten_erstattung"  value="'.$_POST['fahrtkosten_erstattung'].'">';
$html.= '<input type="hidden" name="unterkunft_noetig"  value="'.$_POST['unterkunft_noetig'].'">';
$html.= '<input type="hidden" name="unterkunft_gestellt"  value="'.$_POST['unterkunft_gestellt'].'">';
$autofocus="";
if ($focus==1)  {
	$autofocus="autofocus";
}
$html.='<input '.$autofocus.' type="text" name="event_name" value="'.$_POST['event_name'].'" placeholder="Name der Veranstaltung" style="margin-left:2px;width:95%;border: black solid 2px;"><br style="margin-bottom:1em;">';
$html.='<input type="text"  name="event_ort" value="'.$_POST['event_ort'].'" placeholder="Ort der Veranstaltung" style="margin-left:2px;width:95%;border: black solid 2px;"><br style="margin-bottom:1em">';
$html.= '<span id="P2"></span>';
$html.='<center>';
$html.= '<div id="kalender" style="display:inline-block;">';

//$html.= '<center style="font-size:1.5em;"><b>'.$headline.'</b></center>';
$html.= '<b style="font-size:1.5em;">'.$headline.'</b><br>';

$html.= $kalender->show();
$html.='<center class="nav">';
$html.='<input type="submit" value="RESET" name="reset" formaction="projekt_kalender.php#P1" class="kalender-nav-button">';
$html.='<input type="submit" value="WEITER" name="'.$button.'" formaction="projekt_kalender.php#P1"0 class="kalender-nav-button">';
$html.='<input type="submit" value="OOPS" name="undo" formaction="projekt_kalender.php#P1" class="kalender-nav-button">';
$html.='</center>';
$html.='</div>';
$html.= '<br>';
if (!empty($_POST['event_typ_detail'])) {
	$html.='<b style="font-size:1.5em;">'.$_POST['event_typ_detail'].'</b><br style="margin-bottom:5px;">';
}
$c=$event_color[$pressed_btn];
$action='formaction="projekt_kalender.php#P2" formmethod="POST"';
$height='height:'.$kalender->cellheight.';';
$style='background-color:'.$c.';'.$height;
// echo $pressed_btn;

switch($pressed_btn) {
	case 0:
		$style='vertical-align:middle; border:1px solid black;border-radius:25px;font-weight:bold;margin:1px;padding:0; min-width:120px;cursor:pointer;'.$height;
		$html.= '<button '.$action.' id="kalendertag2" class="margin-1" type="submit" name="btn_licht"   value="'.$licht1.'"  style="'.$style.';background-color:'.$licht1.';">Licht</button>';
		$html.= '<button '.$action.' id="kalendertag2" class="margin-1" type="submit" name="btn_ton"     value="'.$ton1.'"    style="'.$style.';background-color:'.$ton1.  ';">Ton</button>';
		$html.= '<br>'; 
		$html.= '<button '.$action.' id="kalendertag2" class="margin-1" type="submit" name="btn_sperr"   value="'.$sperr.'"   style="'.$style.';background-color:'.$sperr. ';">Krank<br>Familie</button>';
		$html.= '<button '.$action.' id="kalendertag2" class="margin-1" type="submit" name="btn_schul"   value="'.$schul.'"   style="'.$style.';background-color:'.$schul. ';">Schulung<br>sonstiges</button>';
		break;
	case 1:
		$style.='background-color:'.$c.';'.$height;
		// echo $style;echo "<br>";		
		$value="Lichttechniker";
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="Lichtoperator";                                                                                                                                                                                                                                                 
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		break;                                                                                                                                                                                                                                                                  
	case 2:                                                                                                                                                                                                                                                                     
		$value="Tontechniker";                                                                                                                                                                                                                                                  
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="Tonoperator";                                                                                                                                                                                                                                                   
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="FOH Ton";                                                                                                                                                                                                                                                       
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="Monitor Techniker";                                                                                                                                                                                                                                             
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		break;
		
	case 3:
		$value="Gesperrt";                                                                                                                                                                                                                                                  
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="Krank";                                                                                                                                                                                                                                                   
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="Familie";                                                                                                                                                                                                                                                       
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		break;
	case 4:
		$value="Schulung";                                                                                                                                                                                                                                                  
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="sonstiges";                                                                                                                                                                                                                                                   
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		$value="Familie";                                                                                                                                                                                                                                                       
		$html.= '<button '.$action.' id="kalendertag2" type="submit" name="btn_event_typ_detail" value="'.$value.'" style="'.$style.'">'.$value.'</button>';
		break;
		
		
}
// echo "<br>".$focus."<br>";
$html.='</center>';
$html.= '<br style="margin-bottom:5px;">';
$autofocus="";
if ($focus==2)  { 
	$autofocus="autofocus";
}	
$html.='<table style="width:100%;"><tr><td width="100%">';
// $html.='<input '.$autofocus.' type="text" name="kunde_name" value="'.$_POST['kunde_name'].'" list="kunden" placeholder="Name des Kunden" style="margin-left:2px;margin-right:2px;padding-top:0.5em;padding-bottom:0.5em;width:calc(100% - 10px);border: black solid 2px;">';
$html.='<input '.$autofocus.' type="text" name="kunde_name" value="'.$_POST['kunde_name'].'" list="kunden" placeholder="Name des Kunden" style="width:calc(100% - 20px);border: black solid 2px;">';
$html.='</td><td style="width:1px;margin-left:10px;";>';
$html.='<input style="width:80px;" type="submit" value="Auswahl" method="POST" action="projekt_kunde.php">';
$html.='</td></tr></table>';
$html.=$html_kunden;


// $html.='<br style="margin-bottom:1em;">';
// $html.='</div>';

$html.= '<span id="P3"></span>';
if (!empty($_POST['anreise']) or !empty($_POST['abreise'])) {
	$html.='<fieldset style="margin-bottom:5px;"><legend>Fahrtkosten</legend>';
}
$html.='<table>';
$color1="";
$color2="";
if ($_POST['fahrtkosten_erstattung']==1) {
	$color1="background-color:lime;cursor:unset;";
	// $color1='id="ja"';
}

if ($_POST['fahrtkosten_erstattung']==2) {
	$color2="background-color:red;cursor:unset;";
}

$action='formaction="projekt_kalender.php#P3" formmethod="POST"';	
if (!empty($_POST['anreise']) or !empty($_POST['abreise'])) {
	$html.='<tr><td>Fahrtkostenerstattung: </td><td>
	<input '.$action.' name="fahrtkosten_ja"   value="Ja"   type="submit" style="'.$color1.'">
	<input '.$action.' name="fahrtkosten_nein" value="Nein" type="submit" style="width:5em;'.$color2.'"></td></tr>';
}
if ($_POST['fahrtkosten_erstattung'] != 1) {
	$html.='<input name="fahrtkosten" value="'.$_POST['fahrtkosten'].'" type="hidden">';
	$html.='<input name="strecke"     value="'.$_POST['strecke']    .'" type="hidden">';
	$html.='<input name="fahrten"     value="'.$_POST['fahrten']    .'" type="hidden">';
	if ($focus == 3) $focus=4; 
} else { // !empty($_POST['fahrtkosten_ja'] and 
	$autofocus="";
	if ($focus == 3) { 
		$autofocus="autofocus";
	}
	$html.='<tr><td>Fahrtkosten/KM: </td><td><input '.$autofocus.' name="fahrtkosten" value="'.$_POST['fahrtkosten'].'" type="text"   placeholder="0.00" pattern="^\d*([.,]\d{0,2})?$" style="width:5em;text-align:right;">€</td></tr>';
	$html.='<tr><td>Strecke:        </td><td><input name="strecke"     value="'.$_POST['strecke']    .'" type="number" placeholder="0"  style="width:5em;text-align:right;">km</td></tr>';
	$html.='<tr><td>Fahrten:        </td><td><input name="fahrten"     value="'.$_POST['fahrten']    .'" type="number" placeholder="0"  style="width:5em;text-align:right;"></td></tr>';
}
$html.='</table>';
if (!empty($_POST['anreise']) or !empty($_POST['abreise'])) {
	$html.='</fieldset>';
}
$i=0;
foreach($_POST['equipment_name'] as $k =>$v) {
	$i++;
	if ($i == count($_POST['equipment_name'])) {
		$autofocus="";
		if ($focus==4) $autofocus="autofocus";
	}
	$html.='<table style="width:100%;"><tr><td width="100%">';
	$html.='<input '.$autofocus.' type="text" name="equipment_name['.$i.']" value="'.$_POST['equipment_name'][$i].'" list="equipment" placeholder="Was bringst du mit ?" style="width:calc(100% - 20px);border: black solid 2px;">';
	$html.='</td><td style="width:"1px;margin-left:10px;";>';
	$html.='<input style="width:80px;" type="submit" value="Auswahl" method="POST" action="./projekt_equipment.php">';
	
	$html.='</td></tr></table>';
	// $html.='<br style="margin-bottom:1em;">';

}
$html.='<div style="margin-right: 14px;text-align:right;"><input '.$action.' name="add_equipment" style="width:60px;" type="submit" value="+"></span></div>';

// $html.='<span style="display:inline-block;width:98%;text-align:right;"><input '.$action.' name="add_equipment" style="width:60px;" type="submit" value="+"></span>';

$html.=$html_equipment;


$html.= '<span id="P4"></span>';
$action='formaction="projekt_kalender.php#P4" formmethod="POST"';	
$html.='<fieldset style="margin-bottom:5px;"><legend>Unterkunft</legend>';
$color1="";
$color2="";
if ($_POST['unterkunft_noetig']==1) {
	$color1="background-color:lime;cursor:unset;";
}
if ($_POST['unterkunft_noetig']==2) {
	$color2="background-color:red;cursor:unset;";
}
$html.='<table>';
$html.='<tr><td>Unterkunft nötig? </td><td><input '.$action.' name="unterkunft_ja" value="Ja" type="submit" style="width:5em;'.$color1.'"><input '.$action.' name="unterkunft_nein" value="Nein" type="submit" style="width:5em;'.$color2.'"></td></tr>';

$color1="";
$color2="";
if ($_POST['unterkunft_gestellt']==1) {
	$color1="background-color:lime;cursor:unset;";
}
if ($_POST['unterkunft_gestellt']==2) {
	$color2="background-color:red;cursor:unset;";
}
if ($_POST['unterkunft_noetig']==1) {
	$autofocus="";
	if ($focus == 7) {
		$autofocus="autofocus";
		// $html.='<input '.$autofocus.' type="text">';
	}
	
	$html.='<tr><td>vom Kunden gestellt ? </td><td><input '.$action.' '.$autofocus.' name="unterkunft_gestellt_ja" value="Ja" type="submit" style="width:5em;'.$color1.'"><input '.$action.' name="unterkunft_gestellt_nein" value="Nein" type="submit" style="width:5em;'.$color2.'"></td></tr>';
	// echo htmlspecialchars($html);exit;
}
$html.='</table>';

if ($_POST['unterkunft_gestellt'] == 1) {
	$autofocus="";
	if ($focus == 5) {
		$autofocus="autofocus";
	}



	$html.='<table style="width:100%;"><tr><td width="100%">';
	$html.='<input '.$autofocus.' type="text" name="unterkunft_name" value="'.$_POST['unterkunft_name'].'" list="unterkunft" placeholder="Name der Unterkunft" style="width:calc(100% - 20px);border: black solid 2px;">';
	$html.='</td><td style="width:"1px;margin-left:10px;";>';
	$html.='<input style="width:80px;" type="submit" value="Auswahl" method="POST" action="projekt_unterkunft.php">';
	$html.='</td></tr></table>';
	// $html.='<br style="margin-bottom:1em;">';

	$html.=$html_unterkunft;
	
}
$html.='</fieldset>';

$html.='<fieldset style="margin-bottom:5px;padding-bottom:2px;padding-right:2px;padding-left:2px;max-width:80%;"><legend style="margin-left:10px">Notizen</legend>';
$autofocus="";
if ($focus == 6) {
	$autofocus="autofocus";
}
$html.='<textarea '.$autofocus.' name="info" style="width:calc(100vw - 37px);max-width:calc(100vw - 37px) !important;height:5em;">'.$_POST['info'].'</textarea>';
$html.='</fieldset>';

$html.='<center>';
$html.='<input id="button_green" type="submit" name="insert" value="Fertig">';
$html.='<input id="button_red"   type="submit" name="cancel" value="Abbruch!" >';
$html.='</center><br><br>';
$html.= "</form>";
$html.= '<div style="height:400px;"></div>';
/* $html.= '<div style="height:200px;background-color:green;"></div>';
$html.= '<div style="height:200px;background-color:blue;"></div>';
$html.= '<div style="height:200px;background-color:green;"></div>';
$html.= '<div style="height:200px;background-color:blue;"></div>';*/
// $html.= '<br style="height:700px;">';
$html.= '</body></html>';

echo $html;

?>	