<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_zeiten.php";
showHeader("Mitarbeiter Arbeitszeit ändern");




function suche($key,$suchstring) {	
	// echo $key.":".$suchstring."<br>";
	$suche=explode(" ",$suchstring);
	$where="";
	foreach($suche as $s) {
		if (!empty($where)) {
			$where.=" or ";
		} else 
		$s=trim($s);
		$where.="$key like '%$s%'";
	}
	if (empty($where)) {
		$s=trim($suchstring);
		$where="`$key` like '%$s%'";
	}
	return $where;
}		

?>

<center>
<form action="mitarbeiter_arbeitszeiten.php" method="POST">

<table id="liste">

<?php
	showZeiten();
		
?>
</table>
</center>
<?php
showBottom();
?>
<?php
/*
	Berechnete Zeitsummen anzeigen
*/
function azSumme($az_gesamt,$pz_gesamt) {
	$az_gesamt_h=(int)($az_gesamt/3600);
	$az_gesamt_m=(int)(($az_gesamt%3600)/60);
	$pz_gesamt_h=(int)($pz_gesamt/3600);
	$pz_gesamt_m=(int)(($pz_gesamt%3600)/60);
	$line="";
	$line.='<tr><td colspan="6" style="border-bottom:2px solid red"></td></tr>';
	$line.='<tr>';
	$line.='<td colspan=2 style="text-align:right">'.sprintf("%d:%02d",$az_gesamt_h,$az_gesamt_m).'</td>';
	$line.='<td>&nbsp;</td>';
	$line.='<td colspan=2 style="text-align:right">'.sprintf("%d:%02d",$pz_gesamt_h,$pz_gesamt_m).'</td>';
	$line.='<td>&nbsp;</td></tr>';
	return $line;
}	


function showZeiten() {
	global $db;
	$t = new zeiten($db);
	
	$m = new mitarbeiter($db);
	$m->loadByRecnum($_POST['nr']);

	$request = "
	SELECT * 
	FROM `bu_zeiten`
	WHERE `firmanr`  = '".$_SESSION['firmanr']."'
	AND   `kundennr` = '".$_POST['nr']."'
	AND   zeit BETWEEN '".$_POST['von']."' AND '".$_POST['bis']."'
	ORDER BY time";

	$text=array();
	$text['AS'] = "Kommt";
	$text['AE'] = "Geht";
	$text['PS'] = "Pause start";
	$text['PE'] = "Pause Ende";
	$text['KS'] = "Krankschreibung Anfang";
	$text['KE'] = "Krankschreibung Ende";
	$text['US'] = "Urlaubsanfang";
	$text['UE'] = "Urlaubsende";
	
	
	$result = $db->query($request);
	while($row = $result->fetch_assoc()) {
		echo "<tr>";
		$t=$row['type'];
		echo "<th>".$text[$t]."</th>";
		echo '<td><input type="date" name="time[]" value="'.$row['time'].'"></td>';
		echo "</tr>";
	}
	
	$line="";
	
	$firmanr=$_SESSION['firmanr'];
	$request="
	SELECT bu_mitarbeiter.name, bu_mitarbeiter.recnum, bu_zeit.*  
	FROM `bu_zeit` 
	LEFT JOIN bu_mitarbeiter ON bu_mitarbeiter.recnum = bu_zeit.usernr 
	WHERE bu_mitarbeiter.firmanr = $firmanr 
	AND bu_mitarbeiter.nr = $mitarbeiternr 
	ORDER BY bu_mitarbeiter.name,bu_zeit.time";

	$result = $db->query($request);
	$name="";
	
	
	while($row = $result->fetch_assoc()) {
		if ($row['name'] != $name) {
			if ($name != "") {
				$line.=azSumme($az_gesamt,$pz_gesamt);
				$line.='</table><br><table id="liste">';
			}
			

			$name=$row['name'];
			$line.='<tr><td colspan=6><b style="font-size:2em;">'.$name.'</td></tr>';
			$az_gesamt=0;
			$pz_gesamt=0;
			$az_monat=0;
			$ap_monat=0;
			$az_s=0;
			$az_e=0;
			$pz_s=array();
			$pz_e=array();
			

			$line.='<tr><th width="1">Kommt - Geht</th><th width="1">Zeit</th><th style="width:auto;">&nbsp;</th><th width="1">Pause</th><th width="1">Zeit</th><th width="1">Action</th></tr>';

		}
		
		if ($row['type'] == "PS") {	
			$pz_s[]=$row['time'];
		}
		if ($row['type'] == "PE") {	
			$pz_e[]=$row['time'];
		}
		if ($row['type'] == "AS") {	
			$az_s=$row['time'];
			
			$pz_s=array();
			$pz_e=array();
		}

		if ($row['type'] == "AE") {	
			$az_e=$row['time'];
			
			$as=new DateTime($az_s);
			$ae=new DateTime($az_e);
			$dt=$ae->diff($as);
			$worktime=sprintf("%02d:%02d",$dt->h,$dt->i);
			$az_gesamt+=($dt->h*60*60+$dt->i*60+$dt->s);

			// Hier datensumme hinschreiben
			if ($dark) {
				$dark=false;
				$line.='<tr id="dark">';
			} else {
				$dark=true;
				$line.='<tr>';
			}
			$line.='<td style="width:16em">'.$as->format("d.m.Y H:i").' - '.$ae->format("d.m.Y H:i").'</td>';
			$line.='<td>'.$worktime.'</td><td stype="width: auto;">';	

//			echo "$line</tr></tabke>";
// 			exit;

			/*
				Pause Anfang Tagessumme
			*/
/*
			$line.='<td>';
			foreach($pz_s as $k => $v) {
				$dt=new DateTime($v);
				$line.=$dt->format("H:i")."<br>";
				
			}
			$line.='</td>';
*/
			/*
				Pause Ende Tagessumme
			*/
/*
			$line.='<td>';
			foreach($pz_e as $k => $v) {
				$dt=new DateTime($v);
				$line.=$dt->format("H:i")."<br>";
			}
			$line.='</td>';
*/			
			
			$line.='<td>';
			foreach($pz_e as $k => $v) {
				$ps=new DateTime($pz_s[$k]);
				$pe=new DateTime($pz_e[$k]);
				$line.=$ps->format("H:i")." - ".$pe->format("H:i");
				$line.="<br>";
			}
			$line.='</td>';

			/*
				Pause Tagessumme
			*/
			$line.='<td>';
			foreach($pz_e as $k => $v) {
				$ps=new DateTime($pz_s[$k]);
				$pe=new DateTime($pz_e[$k]);
				$dt=$pe->diff($ps);
				$line.=sprintf("%02d:%02d",$dt->h,$dt->i);
				$line.="<br>";

				$pz_gesamt+=($dt->h*60*60+$dt->i*60+$dt->s);
			}
			$line.='</td>';
			
			$line.='<td><input type="submit" value="bearbeiten"></td>';
			$line.='</tr>';

			$az_tag=0;
			$ap_tag=0;
			$az_s=0;
			$az_e=0;
			$pz_s=array();
			$pz_e=array();
		}
	}

	if ($name != "") {
		$line.=azSumme($az_gesamt,$pz_gesamt);
	}
	/* 
		output
	*/
	// echo '<table>';
	echo $line;
	// echo '</table>';
}
?>