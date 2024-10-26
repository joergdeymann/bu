<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_zeit.php";
include "class/class_mitarbeiter.php";
showHeader("Mitarbeiter anzeigen");


if (empty($_POST['order'])) {
	$_POST['order']="name";
}

if (isset($_POST['del_urlaub'])) {
	
	include "class/class_urlaub.php";
	$u=new Urlaub($db);
	$u->loadByRecnum($_POST['recnum']);
	$m=new mitarbeiter($db);
	$m->loadByRecnum($_POST['mitarbeiter_recnum']);
	$days=$m->getUrlaub($u->row['von'],$u->row['bis']);
	// echo "Tage:".$days."<br>";
	
	if ($u->row['art'] == 1) {
		$urlaubtext="Arbeitsunfähig";
		$_POST['button_krank']=1;
	} else {
		$urlaubtext="Urlaub";
		$_POST['button_urlaub']=1;
	}
	$u->del($_POST['recnum']);

	// Nur Urlaub abziehen wenn 
	// - Genehmigt und 
	// - bezahlter Urlaub
	// - kein extra Urlaub 
	
	if (($u->row['art'] == 0) && ($u->row['status'] == 1) && ($u->row['extra'] == 0)) {
		$request='
			update `bu_mitarbeiter` set resturlaub=resturlaub+'.$days.' 
			where recnum='.$_POST['mitarbeiter_recnum'];
		$result=$db->query($request);
	}
	// echo $result."<br>";
	// $m=new mitrarbeiter();
	// $m->loadByRecnum($_POST['mitarbeiter_recnum']);
	$dt_von=new DateTime($u->row['von']);
	$dt_bis=new DateTime($u->row['bis']);
	$von=$dt_von->format("d.m.Y");
	$bis=$dt_bis->format("d.m.Y");
	$name=$m->row['name'];
	
	$msg="$urlaubtext $von bis $bis von $name entfernt";
}

if(!isset($_POST['datumvon'])) {
	$dt=new DateTime();
	$_POST['datumvon']=$dt->format("Y-01-01");
}
if(!isset($_POST['datumbis'])) {
	$dt=new DateTime();
	$_POST['datumbis']=$dt->format("Y-12-31");
}

if (!isset($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 
$lim=$_POST['zeilen'];

if (!isset($_POST['order'])) {
	$_POST['order']="name,von";
} 


/*
if (!isset($_POST['ansehen'])  && !isset($_POST['details'])) {
	$_POST['inarbeit']="0";
	$_POST['faellig']="1";
	$_POST['ueberfaellig']="1";
	$_POST['bezahlt']="1";
}
*/

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

<?php 
if (!empty($msg) ) {
	// echo '<b style="font-size:2em;">'.$msg.'</b>';
	echo '<h1>'.$msg.'</h1>';
}
?>

<form action="mitarbeiter_liste.php" method="POST">
<div id="submenu_neu" style="height:8em">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="nr"           <?php if ($_POST['order']=="nr")           echo "checked";?>>Nr<br>
</div>

<div>
<h1>Filter</h1>
Name,Mitarbeiternummer <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
<input type="date" name="datumvon" value="<?php echo $_POST['datumvon']; ?>"> - <input type="date" name="datumbis" value="<?php echo $_POST['datumbis']; ?>"><br> 
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen"          value="Liste"><br>
<input type="submit" name="button_zeiten"    value="Zeiten"><br>
<input type="submit" name="button_urlaub"    value="Urlaub">
<input type="submit" name="button_kalender"  value="Kalender" formmethod="post" formaction="mitarbeiter_kalender.php"><br>
<input type="submit" name="button_krank"     value="Krankheit"><br>
<!-- input type="submit" name="details" value="Details"><br-->
</div>
</div>

</form>
<table id="liste">

<?php
	if (isset($_POST['button_zeiten'])) {
		showZeiten();
	} else 
	if (isset($_POST['button_urlaub'])) {
		showUrlaub();
	} else 
	if (isset($_POST['button_krank'])) {
		showUrlaub(false);
	} else {
		showMitarbeiter();	
	}
		
?>
</table>
</center>
<?php
showBottom();
?>
<?php
function restzeit(&$row,&$pz_s,&$pz_e,&$dark) {
	$az_e=$row['time'];	
	$ae_r=$row['recnum'];
	// echo "AS:$az_s.<br>";

	// Hier datensumme hinschreiben
	if ($dark) {
		$dark=false;
		$line.='<tr id="dark">';
	} else {
		$dark=true;
		$line.='<tr>';
	}
	$line.='<td style="width:16em">'.$as->format("d.m.Y H:i").' - jetzt</td>';
	$line.='<td>offen</td><td style="width: auto;">&nbsp;</td>';	


	$line.='<td>';
	foreach($pz_e as $k => $v) {
		// echo "$k:$v<br>";
		if (isset($pz_s[$k])) {
			$ps=new DateTime($pz_s[$k]);
			$pe=new DateTime($pz_e[$k]);
			$line.=$ps->format("H:i")." - ".$pe->format("H:i");
		}
		$line.="<br>";
	}

	$line.='</td>';

	/*
		Pause Tagessumme
	*/
	$line.='<td>';
	foreach($pz_e as $k => $v) {
		if (isset($pz_s[$k])) {
			$ps=new DateTime($pz_s[$k]);
			if (isset($pz_e[$k])) {
			
				$pe=new DateTime($pz_e[$k]);
				$dt=$pe->diff($ps);
				$line.=sprintf("%02d:%02d",$dt->h,$dt->i);
				$pz_gesamt+=($dt->h*60*60+$dt->i*60+$dt->s);
			}
		}
		$line.="<br>";

	}

	
	$line.='</td>';
	
	$line.='<td>';
	$line.='<form method="POST" action="zeiten_liste.php">';
/*
	$line.='<input type="hidden" name="AZ_von" value="'.$az_s.'">';
	$line.='<input type="hidden" name="AZ_bis" value="'.$az_e.'">';
	$line.='<input type="hidden" name="AS_recnum" value="'.$as_r.'">';
	$line.='<input type="hidden" name="AE_recnum" value="'.$ae_r.'">';
	$line.='<input type="hidden" name="mitarbeiter_recnum" value="'.$mitarbeiter_recnum.'">';
*/
	$line.='<input type="submit" value="bearbeiten">';
	$line.='</form>';
	$line.='</td>';
	$line.='</tr>';
}

/*
	Berechnete Zeitsummen anzeigen
*/
function azSumme($az_gesamt,$pz_gesamt) {
	$az_gesamt_h=(int)($az_gesamt/60);
	$az_gesamt_m=(int)($az_gesamt%60);
	$pz_gesamt_h=(int)($pz_gesamt/60);
	$pz_gesamt_m=(int)($pz_gesamt%60);
	$line="";
	$line.='<tr><td colspan="6" style="border-bottom:2px solid red"></td></tr>';
	$line.='<tr>';
	$line.='<td colspan=2 style="text-align:right">'.sprintf("%d:%02d",$az_gesamt_h,$az_gesamt_m).'</td>';
	$line.='<td>&nbsp;</td>';
	$line.='<td colspan=2 style="text-align:right">'.sprintf("%d:%02d",$pz_gesamt_h,$pz_gesamt_m).'</td>';
	$line.='<td>&nbsp;</td></tr>';
	return $line;
}	

function showMitarbeiter() {
	global $db;
	
	if (isset($_POST['details']) && $_POST['details']) {
		echo '<tr><th>Name</th><th>Nr</th><th>Eintritt</th><th>Urlaub</th><th>Action</th></tr>';
	} else { 
		echo '<tr><th>Name</th><th>Nr</th><th>Eintritt</th><th>Urlaub</th><th>Action</th></tr>';
	}


	$order="`firmanr`,`name`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="bu_mitarbeiter.firmanr";
		} else 
		if  ($_POST['order'] == "nr") {
			$order="bu_mitarbeiter.firmanr";
		}
	}


	$where1="";
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("nr",$_POST['suche']);
		if ($where1 == "") {
			$where1="where ($w)";
		} else {
			$where1.="and ($w)";			
		}
	}
	
		
	
	$firmanr=$_SESSION['firmanr'];
	if ($where1) {
		$where1.= " and firmanr=$firmanr";
	} else {
		$where1 = " where firmanr=$firmanr";
	}
	
	$lim=$_POST['zeilen'];
	$request="select * from bu_mitarbeiter $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {		
		$action="";
		$action ='<form style="display:inline;margin:0;padding:0;"><input type = "hidden" name="recnum" value="'.$row['recnum'].'">';
		$action.='<input type = "submit" value="bearbeiten" name="find_recnum" formmethod="POST" formaction="mitarbeiter.php"></form>';
	
		$dt=new DateTime($row['entree']);
		
		echo '<tr>';
		echo '<td id="red">'.$row['name'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$row['nr'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$dt->format("d.m.Y").'</td>';
		echo '<td id="red" style="text-align:center;">'.$row['jahresurlaub'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$action.'</td>';
	
		echo '</tr>';
	}; 
}	

/* 
	innerhalb <table> 
*/
function showZeiten() {
	global $db;
	// $t = new zeiten($db);
	$zeit = new Zeit($db);
	
	
	$as_r=0;
	$ae_r=0;
	
	$dark=false;	
	$line="";

	$order="bu_mitarbeiter.name,bu_zeit.time,bu_zeit.recnum";
	// $order="`firma`,`name`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="bu_mitarbeiter.name,bu_zeit.time,bu_zeit.recnum";
		} else 
		if  ($_POST['order'] == "nr") {
			$order="bu_mitarbeiter.nr,bu_zeit.time,bu_zeit.recnum";
		}
	}

	$firmanr=$_SESSION['firmanr'];
	$where1="WHERE (bu_mitarbeiter.firmanr = $firmanr) "; 

	if ($where1) {
		$where1.= " and firmanr=$firmanr";
	} else {
		$where1 = " where firmanr=$firmanr";
	}

	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("nr",$_POST['suche']);
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}
	
		
		
	if (!empty($_POST['datumvon'])) { 
		$dt=new DateTime($_POST['datumvon']);		
		$w="bu_zeit.time >= '".$dt->format("Y-m-d 00:00:00")."'";
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}

	if (!empty($_POST['datumbis'])) { 	
		$dt=new DateTime($_POST['datumbis']."+2days");

		$w="bu_zeit.time <= '".$dt->format("Y-m-d 23:59:59")."'";
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}


	/*
		Komlette Liste Laden Ist das noch korrekt ? recnum und usernr ? sollte das nicht bu_mitarbeiter.nr sein ?
	*/
	$request="
	SELECT bu_mitarbeiter.name, bu_mitarbeiter.recnum as mitarbeiter_recnum, bu_zeit.*  
	FROM `bu_zeit` 
	LEFT JOIN bu_mitarbeiter ON bu_mitarbeiter.recnum = bu_zeit.usernr 
	$where1 
	ORDER BY $order";
	// echo $request;


	$result = $db->query($request);
	$name="";

	$zeit->loadRequest($request);
	$row=$zeit->firstAS();
	$line="";
	while ($row) {
		if ($row['name'] != $name) {
			if (!empty($name)) {
				if (!isset($zeit->dt_AE) and isset($zeit->dt_AS)) {
					// echo "Noch in Arbeit";
					$line.=$zeit->output();
				}
				
				$line.=azSumme($zeit->sum_minutes,$zeit->sum_pause);
				// $line.='<tr><td style="background-color:gray;width:120%;" colspan="6">&nbsp;</td></tr>';
				$line.='<tr><td colspan="6">&nbsp;</td></tr>';
			}
			$name=$row['name'];
			$line.='<tr>';
			$line.='<td colspan="3"><b style="font-size:2em;">'.$name.'</b></td>';
			$line.='<td colspan="3" style="text-align:right;vertical-align:middle;">';
			$line.='<form method="POST" action="zeiten_liste.php">';
			$line.='<input type="hidden" name="mitarbeiter_recnum" value ="'.$row['mitarbeiter_recnum'].'">';
			$line.='<input type="submit" name="AZ_add" value="Arbeitszeit hinzufügen">';
			$line.='</form>';
			$line.='</td>';
			$line.='</tr>';
			
			$line.='<tr><th width="1">Kommt - Geht</th><th width="1">Zeit</th><th style="width:auto;">&nbsp;</th><th width="1">Pause</th><th width="1">Zeit</th><th width="1">Action</th></tr>';
			
			  		 	                // Gesamt der Selektion:
			$zeit->sum_minutes=0;       // Arbeits-Stunden 60 Minuten * 24 h *30 Tage = Maximal 43.200: int 32 Bit >= 2.147.483.647
			$zeit->sum_pause=0;         // Arbeits-Pausen  
			$zeit->sum_overhours=0;     // Über Stunden
			$zeit->sum_underhours=0;    // unter Stunden
		}	
		$line.=$zeit->calc();

		$row=$zeit->next();
	}
	// echo $zeit->dt_AS->format("Y-m-d");
	// echo "<br>";

	if (!isset($zeit->dt_AE) and isset($zeit->dt_AS)) {
		// echo "Noch in Arbeit";
		$line.=$zeit->output();
	}

	// Summe je Mitarbeiter
	if ($name != "") {
		//restzeit($row,$pz_s,$pz_e,$dark);
		$line.=azSumme($zeit->sum_minutes,$zeit->sum_pause);
	}

	/* 
			output
	*/
	echo $line;

}
/*
	Urlaubszeiten anzeigen
*/

function showUrlaub($urlaub=true) {
	global $db;
	// global $urlaub;
	$m=new mitarbeiter($db);
	
	// 	echo '<tr><th>Name</th><th>Nr</th><th>Urlaub von</th><th>Urlaub bis</th><th>Tage</th><th>Action</th></tr>';
	
	
	$order="bu_mitarbeiter.firmanr,`name`,`status`,`von`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="bu_mitarbeiter.firmanr,`name`,`status`,`von`";
		} else 
		if  ($_POST['order'] == "nr") {
			$order="bu_mitarbeiter.firmanr,`nr`,`status`,`von`";
		}
	}



	
	// Urlaub / Krank
	$on=" AND (`art` = 0 or `art` = 2)";
	if ($urlaub==false) {
		$on=" AND `art` = 1";
	}
	

	if (isset($_POST['datumvon']) && $_POST['datumvon']) {
		// echo "VON<br>";
		$dt=new DateTime($_POST['datumvon']);
		
		$w = 'bu_urlaub.von >= "'.$dt->format("Y-m-d 00:00:00").'"';
		// $w.= ' or bu_urlaub.von IS NULL';
		$on.=" AND ($w)";			
	}

	if (isset($_POST['datumbis']) && $_POST['datumbis']) {
		// echo "Bis<br>";
		$dt=new DateTime($_POST['datumbis']);
		
		$w = 'bu_urlaub.von <= "'.$dt->format("Y-m-d 23:59:59").'"';
		// $w.= ' or bu_urlaub.von IS NULL';
		$on.=" AND ($w)";			
	}
	
	$firmanr=$_SESSION['firmanr'];
	$where1 = "bu_mitarbeiter.firmanr=$firmanr";
		
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("nr",$_POST['suche']);
		$where1.=" and ($w)";			
	}
	


		
/*
SELECT bu_mitarbeiter.name, bu_mitarbeiter.nr, bu_mitarbeiter.recnum as mitarbeiter_recnum, bu_urlaub.* 
FROM `bu_mitarbeiter` 
left JOIN `bu_urlaub`
ON (bu_mitarbeiter.nr = bu_urlaub.mitarbeiternr ) 
AND (bu_mitarbeiter.firmanr = bu_urlaub.firmanr) 

AND (bu_urlaub.art = 1)
and (bu_urlaub.von >= "2023-01-01 00:00:00") 
and (bu_urlaub.von <= "2023-12-31 23:59:59") 
where bu_mitarbeiter.firmanr=14 
ORDER BY bu_mitarbeiter.name,`status`,`von` ;

*/
	
	
	$lim=$_POST['zeilen'];

	$request="
		SELECT bu_mitarbeiter.name, bu_mitarbeiter.nr, bu_mitarbeiter.recnum as mitarbeiter_recnum, bu_urlaub.* 
		FROM `bu_mitarbeiter` 
		left JOIN `bu_urlaub` 
		ON (bu_mitarbeiter.nr = bu_urlaub.mitarbeiternr ) 
		AND (bu_mitarbeiter.firmanr = bu_urlaub.firmanr)
		$on
		WHERE $where1 
		ORDER BY $order
		LIMIT $lim;
	";
	
	// echo $request;
	
/*
	$request="
		SELECT bu_mitarbeiter.name, bu_mitarbeiter.nr, bu_mitarbeiter.recnum as mitarbeiter_recnum, bu_urlaub.* 
		FROM `bu_urlaub` 
		right JOIN bu_mitarbeiter ON (bu_mitarbeiter.nr = bu_urlaub.mitarbeiternr) 
								AND (bu_mitarbeiter.firmanr = bu_urlaub.firmanr)
		$where1 
		ORDER BY $order
	";
*/

	
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	$name="";
	$status="";
	$actionfile="mitarbeiter_urlaub.php";

	$bezahlt_text=array();
	$bezahlt_text[0]="Ja";
	$bezahlt_text[2]="Nein";
	if ($urlaub == false) {
		$bezahlt_text=array();
		$bezahlt_text[1]="Ja";
		$bezahlt_text[0]="Nein";
		$actionfile="mitarbeiter_krank.php";
	}
	
	// $au=array("Nein","Ja");
	
	while($row = $result->fetch_assoc()) {		
		$action="";
		$action ='<form style="display:inline;margin:0;padding:0;">';
		$action.='<input type = "hidden" name = "recnum"             value="'.$row['recnum'].'">';
		$action.='<input type = "hidden" name = "mitarbeiter_recnum" value="'.$row['mitarbeiter_recnum'].'">';
		// $action.='<input type = "hidden" name = "datum_von"          value="'.$row['von'].'">';
		// $action.='<input type = "hidden" name = "datum_bis"          value="'.$row['bis'].'">';
		$action.='<input type = "submit" value="bearbeiten" name="find_recnum" formmethod="POST" formaction="'.$actionfile.'">';
		$action.='<input type = "submit" value="entfernen"  name="del_urlaub"  formmethod="POST" formaction="mitarbeiter_liste.php">';
		$action.='</form>';
		
		if ($row['name'] != $name) {
			$status="";
			$m->loadByRecnum($row['mitarbeiter_recnum']);

			if ($name != "") {
				echo '<tr><td colspan="5">&nbsp;</td></tr>';
			}
			$name=$row['name'];
			echo '<tr><td colspan="5">';

			echo '<div style="float:left;font-size:2em;margin-right:1em;">'.$name.' ('.$row['nr'].')'.'</div>';
			echo '<div style="float:right;height:100%;text-align:right;"><!-- div style="margin-top:0.5em;"></div-->';
			if ($urlaub) {
				// Urlaub
				if ($m->row['resturlaub'] < 0 ) {
					$color="red";
				} else {
					$color="black";
				}
				echo 'Resturlaub:<b style="color:'.$color.';font-weight:1000;">'.$m->row['resturlaub'].' </b>Tage<br>';
				$actionfile="mitarbeiter_urlaub.php";
				$submitvalue="Urlaub hinzufügen";
				
			} else { 
				// Krank
				echo "<br>";
				$actionfile="mitarbeiter_krank.php";
				$submitvalue="Fehlzeit hinzufügen";
			}

				
			echo '<form action="'.$actionfile.'" method="POST">';
		    echo '<input type = "hidden" name = "mitarbeiter_recnum" value="'.$row['mitarbeiter_recnum'].'">';
			echo '<input type = "submit" value="'.$submitvalue.'">';
			echo '</form>';

			echo '</div>';
			
			echo '<td>';
			echo '</tr>';
			
			// echo '<td style="text-align:right;vertical-align:middle;"><input type="submit" value="Urlaub hinzufügen">';

			// 01.08.2023 echo '<tr><th>Urlaub von</th><th>Urlaub bis</th><th>Tage</th><th>Action</th></tr>';
			
		}
		if ($status != $row['status']) {
			if ($urlaub) {
				$status=$row['status'];
				$status_text=array("beantragt", "genehmigt","abgelehnt");				
				echo '<tr><th colspan="5">Urlaub '.$status_text[$status].'</td></tr>';				
				echo '<tr><th>Urlaub von</th><th>Urlaub bis</th><th>Tage</th><th>Bezahlt</th><th>Action</th></tr>';
			} else {
				$status=$row['status'];
				$status_text=array("ohne AU", "mit AU");				
				echo '<tr><th colspan="5">Krank '.$status_text[$status].'</td></tr>';				
				echo '<tr><th>Krank von</th><th>Krank bis</th><th>Tage</th><th>AU</th><th>Action</th></tr>';
			}
		}
		
		
		if (!isset($row['von'])) {
			continue;
		}

		$dt_von=new DateTime($row['von']);
		$dt_bis=new DateTime($row['bis']);
		$diff=$dt_von->diff($dt_bis);
		
		$bez=$row['art'];

		if ($urlaub == false) {
			$bez=$row['status'];
		}
		
		
/*
echo "<pre>";
var_dump($diff);
echo $diff->days;
*/
		echo '<tr>';
		echo '<td id="red" style="text-align:center;">'.$dt_von->format("d.m.Y").'</td>';
		echo '<td id="red" style="text-align:center;">'.$dt_bis->format("d.m.Y").'</td>';
// 		echo '<td id="red" style="text-align:right;">'.($diff->days+1).'</td>';
 		echo '<td id="red" style="text-align:center;">'.$m->getUrlaub($dt_von,$dt_bis).'</td>';
		echo '<td id="red" style="text-align:center;">'.$bezahlt_text[$bez].'</td>';

		echo '<td id="red" style="text-align:center;width:1px;">'.$action.'</td>';
	
		echo '</tr>';
	}; 
}	

?>