<?php

/*
To Do:
	- Überprüfung der Eingaben auf Überschneidungen
	  a) bei AZvon bis : Erledigt
	  b) neue Pausen
	  
*/

/*
if (isset($_POST['AZ_von'])) {	
	$dt=new DateTime($_POST['AZ_von']);
	$_POST['AS_Date']=$dt->format("Y-m-d");
	$_POST['AS_Time']=$dt->format("H:i");
echo "AZVON:".$_POST['AZ_von'];
}
if (isset($_POST['AZ_bis'])) {	
	$dt=new DateTime($_POST['AZ_bis']);
	$_POST['AE_Date']=$dt->format("Y-m-d");
	$_POST['AE_Time']=$dt->format("H:i");
echo "AZBIS:".$_POST['AZ_bis'];
}
exit;
*/

include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_zeiten.php";
include "class/class_mitarbeiter.php";
showHeader("Arbeitszeiten ändern");

$m=new mitarbeiter($db);
$z=new zeiten($db);
$zeiten = array();
$msg="";
$marker=array();
$inarbeit=false;
/*
function Xdisplay_eingabe() {
	echo "Engabe Mitarbeiter und Zeiten Neu";
	exit;
}
*/
/*
	Nur Arbeitszeiten eingabe	
*/
function display_eingabe() {
	global $m;
	global $msg;
	
	$as=new DateTime();
	$ae=new DateTime();
	echo '<center><form action="zeiten_liste.php" method="POST">';
	echo '<input type="hidden" name="mitarbeiter_recnum" value="'.$_POST['mitarbeiter_recnum'].'">';
	echo '<h2>'.$msg.'</h2>';
	// echo '</h2>';
	echo '<table id="liste">';

	echo '<tr><th colspan=2>Arbeitzeit von '.$m->row['name'].' ('.$m->row['nr'].')';
	echo '</th></tr>';
	echo '<tr><th>Kommt </th><td><input type="date" name="AS_Date" value="'.$as->format("Y-m-d").'"><input type="time" name="AS_Time" value="'.$as->format("H:i").'"></td></tr>';
	echo '<tr><th>Geht  </th><td><input type="date" name="AE_Date" value="'.$ae->format("Y-m-d").'"><input type="time" name="AE_Time" value="'.$ae->format("H:i").'"></td></tr>';

	echo '<tr><td colspan=2 style="text-align:center;">';
	echo '<input type="submit" name="AZ_neu" value="Diese Zeit hinzufügen">';
	echo '</td></tr>';


	echo '</table></form></center>';
	
	showBottom();
	exit;
}

function getTime($date,$sec="00") {
	$dt=new DateTime($date);
	return new DateTime($dt->format("Y-m-d H:i").":$sec");
}

/*
function XcheckTimesFile() {
	$ps        = array();
	$ps_recnum = array();
	$pe        = array();
	$pe_recnum = array();
	*
		1. Vorladen und Modifizieren der Zeiten aus der Datei
	*
	$z->loadByDate($m->row['nr'],$_POST['AZ_von'],$_POST['AZ_bis']);
	$c=0;
	while($row=$z->next()) {
		if ($row['type'] == "AS") {
			$as=getTime($row['time'],"00");
		}
		if ($row['type'] == "AE") {
			$ae=getTime($row['time'],"59");
		}
		if ($row['type'] == "PS") {		
			$ps[$c]=getTime($row['time'],"10");
			$ps_recnum[$c]=$row['recnum'];
		}
		if ($row['type'] == "PE") {		
			$pe[$c]=getTime($row['time'],"20");
			$pe_recnum[$c]=$row['recnum'];
			$c++;
		}
	}
}
*/
	
function PZ_Check() {	
	if (empty($_POST['P_Date'])) { // Tag hat keine Pausen
		return true;
	}		
	global $m;
	global $z;
	global $msg;
	global $marker;
	$c=0;

	$post_as = "";
	$post_ae = "";
	
	$post_ps        = array();
	// $post_ps_recnum = array();
	$post_pe        = array();
	// $post_pe_recnum = array();
	
	$dt=new DateTime();
	
	/*
		2. Vorladen und Modifizieren der Zeiten aus der Eingabe
	*/
	
	$p_date=$_POST['P_Date'];  // Bei Änderung der Daten führt es zum fehler
	$p_time=$_POST['P_Time'];  // dto
	$p_typ =$_POST['P_Typ'];   // dto
	
	$c=0;
	
	$post_as = new DateTime($_POST['AS_Date']." ".$_POST['AS_Time'].":00");

	if (empty($_POST['AE_Date'])) {
		$post_ae = new DateTime($dt->format("Y-m-d H:i:59"));
	} else {
		$post_ae = new DateTime($_POST['AE_Date']." ".$_POST['AE_Time'].":59");
	}
	
	foreach ($_POST['P_Date'] as $recnum => $time) {		
		if ($p_typ[$recnum] == "PS") {		
			$post_ps[$c] = new DateTime($p_date[$recnum]." ".$p_time[$recnum].":10");
			$post_pe[$c] = new DateTime($dt->format("Y-m-d H:i:10")); // Schon mal vorladen falls nicht definiert
		}
	
		if ($p_typ[$recnum] == "PE") {		
			$post_pe[$c] = new DateTime($p_date[$recnum]." ".$p_time[$recnum].":20");
			$c++;
		}
	}

	/* 
		3. Auswertung Pausen kontrolle
	*/
	
	$msg1="";
	$msg2="";
	$msg3="";
	$msg4="";
	$msg5="";
	$msg6="";
	
	foreach ($post_ps as $c => $v) {		
		$recnum=1; // XXXXXXXXXXX Marker / Recnum nicht benutzt
		
		/*
			Vergleich mit Arbeitsanfang
		*/
		if ($post_ps[$c] < $post_as) {
			$msg1 = "Pause fängt vor dem Arbeitsanfang an.<br>";
			$marker[$recnum]=true;
		}
		if ($post_ps[$c] > $post_ae) {
			
			$msg2 = "Pause fängt nach der Arbeit an.<br>";
			$marker[$recnum]=true;
		}
		if ($post_pe[$c] < $post_as) {
			$msg3 = "Pause endet vor dem Arbeitsanfang.<br>";
			$marker[$recnum]=true;
		}
		if ($post_pe[$c] > $post_ae) {
			$msg4 = "Pause hört nach der Arbeit auf.<br>";
			$marker[$recnum]=true;
		}
		if ($post_ps[$c] > $post_pe[$c]) {
			$msg6 = "Pausenanfang liegt vor Pausenende";
			$marker[$recnum]=true;
		}
			
		
		
		
		foreach ($post_ps as $k => $post_v) {
			if ($k == $c) {
				continue;
			}
			$recnum=1; // XXXXXXX; Marker / Recnum nicht benutzt

			if (($post_ps[$k] >= $post_ps[$c]) and ($post_ps[$k] <= $post_pe[$c])) {
				$msg5 = "Pausen überschneiden sich<br>";
				$marker[$recnum]=true;
			}
			if (($post_pe[$k] >= $post_ps[$c]) and ($post_pe[$k] <= $post_pe[$c])) {
				$msg5 = "Pausen überschneiden sich<br>";
				$marker[$recnum]=true;
			}
		}
	}
	
	$errmsg=$msg1.$msg2.$msg3.$msg4.$msg5.$msg6;
	if (empty($errmsg)) {
		$ok=true;
	} else {
		$ok=false;
		$msg.='<div style="border:red solid 5px;width:60%;">'.$errmsg.'</div>';
	}
	return $ok;
}

function AS_CheckOnly() {
	global $db;
	global $msg;
	$dts = $_POST['AS_Date']." ".$_POST['AS_Time'].":00";
	
	$request="
	SELECT type FROM `bu_zeit`  
	WHERE  `usernr` = '".$_POST['mitarbeiter_recnum']."'
	AND    `time` < '".$dts."'
	AND    `recnum` != '".$_POST['AS_recnum']."'
	AND    (`type` = 'AS' or `type` = 'AE')
	ORDER BY `time` DESC, `recnum` DESC
	LIMIT 1
	";

	$result = $db->query($request);
	if ($row = $result->fetch_assoc()) {
		if ($row['type'] != "AE") {
			$msg="Neue Arbeitszeit überschneidet sich mit anderen Zeiten";
			return false;
		}
	}

	$request="
	SELECT type FROM `bu_zeit`  
	WHERE  `usernr` = '".$_POST['mitarbeiter_recnum']."'
	AND    `time` > '".$dts."'
	AND    `recnum` != '".$_POST['AS_recnum']."'
	AND    (`type` = 'AS' or `type` = 'AE')
	ORDER BY `time`,`recnum`
	LIMIT 1
	";
	$result = $db->query($request);
	if ($row = $result->fetch_assoc()) {
		if ($row['type'] != "AS") {
			$msg="Neue Arbeitszeit überschneidet sich mit anderen Zeiten";
			return false;
		}
	}

	return true;
}


function AZ_Check() {
    if 	(empty($_POST['AE_recnum'])) {
		return AS_CheckOnly();
	}

	global $z;
	global $msg;

	
	$dts = $_POST['AS_Date']." ".$_POST['AS_Time'].":00";
	$dte = $_POST['AE_Date']." ".$_POST['AE_Time'].":59";

	$rs = $_POST['AS_recnum'];
	$re = $_POST['AE_recnum'];
	$ok=true;
    // echo $dts."=AS<br>";
    // echo $dte."=AE<br>";
    // echo $rs."=Recnum AS<br>";
    // echo $re."=Recnum AE<br>";
	
	$z->loadByDate($_POST['mitarbeiter_recnum'],$dts,$dte);
	while ($row=$z->next()) {
		$r=$row['recnum'];
		$type=$row['type'];
		// echo $r."=Recnum<br>";
		if (($type == "AS") && ($r != $rs)) {
			$ok=false;
			$msg.="Arbeitszeit kollidiert mit anderen Arbeitszeiten<br>";
			break;
		}
		if (($type == "AE") && ($r != $re)) {
			$ok=false;
			$msg.="Arbeitszeit kollidiert mit anderen Arbeitszeiten<br>";
			break;
		}
	}
	// echo $ok;
	return $ok;
	
	// AS_recnum
	
}

	

/*
	Update der Arbeitszeiten
*/
function AZ_Update() {
	global $z;
	$row=array();
	$row['recnum']=$_POST['AS_recnum'];
	$row['time']  =$_POST['AS_Date']." ".$_POST['AS_Time'].":00";
	$z->update($row);

	
	if (empty($_POST['AE_Date']) or  empty($_POST['AE_Time'])) {
		return;
	}
	
	if (empty($_POST['AE_recnum'])) {
		$row['time']  =$_POST['AE_Date']." ".$_POST['AE_Time'].":59";
		$z->addTime("AE",$row['time']);	
		return;
	}

	$row['recnum']=$_POST['AE_recnum'];
	$row['time']  =$_POST['AE_Date']." ".$_POST['AE_Time'].":59";
	$z->update($row);
	
}

/*
	Update der Pausenzeiten
*/
function PZ_Update() {
	if (empty($_POST['P_Date'])) { // Tag hat keine Pausen
		return;
	}		

	global $z;
	
	$pt=$_POST['P_Time'];  //bei Änderung der Zeiten^wird hier Fehler erzeugt
	$pd=$_POST['P_Date'];  // dto
	$ptyp=$_POST['P_Typ']; // dto
	
	foreach($_POST['P_Date'] as $k => $v) {
		$ds=array();

		if ($ptyp[$k] == "PS")  {
			$s=":10";
		} else {
			$s=":20";
		}
		
		$ds['recnum']=$k;
		$ds['time']  =$pd[$k]." ".$pt[$k].$s;
		
		$z->update($ds);
	}
}


$as="";
$ae="";
$az_tag="";
$az=false;


/*
	Aufruf von aussen
*/
if (!isset($_POST['mitarbeiter_recnum'])) {
	display_eingabe();
	exit;
} else {
    // echo "MR:".$_POST['mitarbeiter_recnum']."<br>";
	$m->loadByRecnum($_POST['mitarbeiter_recnum']);
	$z->setUser($_POST['mitarbeiter_recnum']);
}

/*
if (isset($_POST['AZ_von'])) {	
	$dt=new DateTime($_POST['AZ_von']);
	$_POST['AS_Date']=$dt->format("Y-m-d");
	$_POST['AS_Time']=$dt->format("H:i");
echo "AZVON:".$_POST['AZ_von'];
}
if (isset($_POST['AZ_bis'])) {	
	$dt=new DateTime($_POST['AZ_bis']);
	$_POST['AE_Date']=$dt->format("Y-m-d");
	$_POST['AE_Time']=$dt->format("H:i");
echo "AZBIS:".$_POST['AZ_bis'];
}
*/

/*
	Nur Von- Zeit übergeben //##
*/
if (!empty($_POST['AS_Date']) and empty($_POST['AE_Date'])) {
	$inarbeit=true;
}
	
if (!empty($_POST['AZ_von']) and empty($_POST['AZ_bis'])) {
	$dtnow=new DateTime();
	$dt=new DateTime($_POST['AZ_von']);
	$_POST['AS_Date']=$dt->format("Y-m-d");
	$_POST['AS_Time']=$dt->format("H:i");

	$_POST['AE_Date']=""; // $dtnow->format("Y-m-d"); //##
	$_POST['AE_Time']=""; // $dtnow->format("H:i");   //##
	$_POST['AZ_bis']=$dtnow->format("Y-m-d H:i:59");
	$inarbeit=true;
} else 

/*
	Von oder Bis übergeben
*/
if (!isset($_POST['AZ_von']) or !isset($_POST['AZ_bis'])) {
	$ok=true;
	if (isset($_POST['AS_Time']) && isset($_POST['AS_Date'])) {
		$_POST['AZ_von']=$_POST['AS_Date']." ".$_POST['AS_Time'].":00";
	} else {
		$ok=false;
	}
	if (isset($_POST['AE_Time']) && isset($_POST['AE_Date'])) {
		$_POST['AZ_bis']=$_POST['AE_Date']." ".$_POST['AE_Time'].":59";
	} else {
		$ok=false;
	}
	
	if ($ok == false) {		
		display_eingabe();
		exit;
	}
}
/*
echo "<pre>";
var_dump($_POST);
echo "</pre>";
*/

if (isset($_POST['AZ_neu'])) {
	$ok=false;
	$msg="";

	$dts = $_POST['AS_Date']." ".$_POST['AS_Time'].":00";
	$dte = $_POST['AE_Date']." ".$_POST['AE_Time'].":59";
	if ($dts > $dte) {
		$msg="Arbeitszeitende liegt vor dem Arbeitsanfang";
	} else {
			
		$result=$z->loadByDate($_POST['mitarbeiter_recnum'],$dts,$dte);

		if ($result->num_rows == 0) {
			$request="select * from `bu_zeit` where `time` < '".$dts."' and usernr='".$_POST['mitarbeiter_recnum']."' order by time DESC limit 1";
			$result=$db->query($request);
			$row=$result->fetch_assoc();	
			$ok_AS=false;
			if ($result->num_rows > 0) {
				if ($row['type'] == "AE" or $row['type'] == "KE"  or $row['type'] != "UE") {
					$ok_AS=true;
				}
			} else {
				$ok_AS=true;
			}
			
			$request="select * from `bu_zeit` where `time` > '".$dte."' and usernr='".$_POST['mitarbeiter_recnum']."' order by time ASC limit 1";
			$result=$db->query($request);
			$row=$result->fetch_assoc();	
			$ok_AE=false;
			if ($result->num_rows > 0) {
				if ($row['type'] == "AS" or $row['type'] == "KS"  or $row['type'] != "US") {
					$ok_AE=true;
				}
			} else {
				$ok_AE=true;
			}

			if ($ok_AS == false) {
				$msg.="Arbeitszeitanfang überschneidet sich mit anderen Zeiten";
			}
			if ($ok_AE == false) {
				$msg.="Arbeitszeitende überschneidet sich mit anderen Zeiten";
			}
			
			$ok=$ok_AS & $ok_AE;

		} else {
			$msg="Arbeitszeitüberschneidungen";
		}
	}
	
	if ($ok) {
		$msg="Neue Zeiten erfasst!";
		// echo "$msg";
		// exit;
		// echo "DTS:".$dts."<br>";		
		// echo "DTE:".$dte."<br>";
		$z->addTime("AS",$dts);
		$_POST['AS_recnum'] = $z->row['recnum'];
		$z->addTime("AE",$dte);
		$_POST['AE_recnum'] = $z->row['recnum'];
	}
}


/*
	Bestimmte Arbeitszeit inklusive Pausen löschen
*/
if (isset($_POST['AZ_del'])) {
	$z->delByDate($_POST['AZ_von'],$_POST['AZ_bis']);
	$msg="Arbeitszeit inklusive Pausen entfernt";

	echo '<center><form action="mitarbeiter_liste.php" method="POST">';
	echo '<h2>'.$msg.'</h2>';
	echo '<input name="button_zeiten" type="submit" value="OK">';
	echo '</form></center>';
	showBottom();	
}
/*
	Arbeitszeiten ändern
*/
if (isset($_POST['AZ_update'])) {
	if (PZ_Check() && AZ_Check()) { // Überprüfung auf Überschneidungen
	// if (AZ_Check()) {
		AZ_Update();
		PZ_Update(); // Sekunden veränderen z.B. AZ 59 Sek und Pause auch, kommt sonst zu Schwierikeiten
		$msg="Arbeitszeiten geändert";
	}
}


/*
	Pause entfernen
*/
if (isset($_POST['PS_recnum']) && isset($_POST['del_pause'])) {
	$recnums=$_POST['PS_recnum'];
	foreach($_POST['del_pause'] as $k => $v) {
		$r=$recnums[$k];
		
		if (isset($v)) {
			// echo "$k:$v - $k:$r<br>";
			$z->del($k);
			$z->del($r);
		}
	}
	$msg="Pause entfernt";
}

/*
	Änderung der Pausendaten
*/
if (isset($_POST['P_update'])) {
	// Überprüfung auf Überschneidungen
	// #################### Hier nicht korrekt
	// #################### Es müssen beide Zeiten überprüft werden, und die Variablen vorgeladen werden
	
	if (PZ_Check() && AZ_Check()) {
		AZ_Update(); // Sekunden anpassen
		PZ_Update();
		$msg="Pausenzeiten wurden angepasst";
	}
}	

/*
if (isset($_POST['addpause'])) {
	$z->setUser($_POST['mitarbeiter_recnum']);
	$dts=new DateTime($_POST['PS_add_Date']." ".$_POST['PS_add_Time'].":10");
	$dte=new DateTime($_POST['PE_add_Date']." ".$_POST['PE_add_Time'].":20");

	$z->addTime("PS",$dts);
	$z->addTime("PE",$dte);
	$msg="Pause wurde hinzugefügt";
	//eigentlich noch überprüfen ob es Überschneidungen mit anderen Pausen und der Arbeitszeit gibt.
}
*/

if (isset($_POST['addpause'])) {	
	$z->setUser($_POST['mitarbeiter_recnum']);
	/*
		Pausenzeiten		
	*/
	$dts=new DateTime($_POST['PS_add_Date']." ".$_POST['PS_add_Time'].":10");
	$dte=new DateTime($_POST['PE_add_Date']." ".$_POST['PE_add_Time'].":20");

	$_POST['P_Typ'][]="PS";
	$_POST['P_Date'][]=$_POST['PS_add_Date'];
	$_POST['P_Time'][]=$_POST['PS_add_Time'];

	$_POST['P_Typ'][]="PE";
	$_POST['P_Date'][]=$_POST['PE_add_Date'];
	$_POST['P_Time'][]=$_POST['PE_add_Time'];	
	
	if (PZ_Check()) {
		$z->addTime("PS",$dts);
		$z->addTime("PE",$dte);
		$msg.="Pause wurde hinzugefügt";
	}
}

$z->loadByDate($m->row['nr'],$_POST['AZ_von'],$_POST['AZ_bis']);
$as=new DateTime($_POST['AZ_von']);
$ae=new DateTime($_POST['AZ_bis']);
// echo "HIER<br>";
// echo "##".$_POST['AZ_von']."##".$_POST['AZ_bis']."<br>";	
// echo "##".$as->format("Y-m-d H:i:s")."##".$ae->format("Y-m-d H:i:s")."<br>";	
if (!empty($as)) { 
	$az=true;
	$az_tag=$as->format("d.m.Y");
}
if (!empty($ae)) { 
	$az=true;
	if (empty($az_tag)) {
		$az_tag=$ae->format("d.m.Y");
	}		
}
$pausen="";
$typ="";
while($z->next()) {
	
	$r=$z->row['recnum'];
	if($z->row['type'] == "PS") {
		$typ="PS";
		
		$ps=new DateTime($z->row['time']);
		$ps_recnum=$r;
		$pausen.='<tr><th>von  </th><td>';
		$pausen.='<input type="date"   name="P_Date['.$r.']"  value="'.$ps->format("Y-m-d").'">';
		$pausen.='<input type="time"   name="P_Time['.$r.']"  value="'.$ps->format("H:i").'">';
		$pausen.='<input type="hidden" name="P_Typ['.$r.']"   value="'.$z->row['type'].'">';
		// $pausen.='<input type="hidden" name="P_recnum[]" value="'.$z->row['recnum'].'">';
		$pausen.='</td></tr>';

		
	} else 
	if($z->row['type'] == "PE") {
		$typ="PE";
		$pe=new DateTime($z->row['time']);
		$pausen.='<tr><th>bis  </th><td>';
		$pausen.='<input type="date"   name="P_Date['.$r.']"  value="'.$pe->format("Y-m-d").'">';
		$pausen.='<input type="time"   name="P_Time['.$r.']"  value="'.$pe->format("H:i").'">';
		$pausen.='<input type="hidden" name="P_Typ['.$r.']"   value="'.$z->row['type'].'">';
		// $pausen.='<input type="hidden" name="P_recnum[]" value="'.$z->row['recnum'].'">';
		$pausen.='</td></tr>';			
		$pausen.='<tr><td colspan=2 style="text-align:center;">';
		$pausen.='<input type="hidden"   name="PS_recnum['.$r.']"  value="'.$ps_recnum.'">';
		$pausen.='<input type="submit"   name="del_pause['.$r.']"  value="entfernen">';
		$pausen.='</td></tr>';
		$ps_recnum=0;
	}
}

if($typ == "PS") {
	$r=$ps_recnum;
	$pausen.='<tr><td colspan=2 style="text-align:center;">';
	$pausen.='<input type="hidden"   name="PS_recnum['.$r.']"  value="'.$ps_recnum.'">';
	$pausen.='<input type="submit"   name="del_pause['.$r.']"  value="entfernen">';
	$pausen.='</td></tr>';
}









echo '<center><form action="zeiten_liste.php" method="POST">';
echo '<input type="hidden" name="mitarbeiter_recnum" value="'.$_POST['mitarbeiter_recnum'].'">';
echo '<input type="hidden" name="AZ_von" value="'.$_POST['AZ_von'].'">';
echo '<input type="hidden" name="AZ_bis" value="'.$_POST['AZ_bis'].'">';

echo '<h2>'.$msg.'</h2>';
// echo '<h2>Arbeitzeit von '.$m->row['name'].' ('.$m->row['nr'].')';

if ($az) {
	// echo ' am '.$az_tag;
	// echo '</h2>';
	echo '<table id="liste">';

	echo '<tr><th colspan=2>Arbeitzeit von '.$m->row['name'].' ('.$m->row['nr'].')';
	echo ' am '.$az_tag;
	echo '</th></tr>';
	
	// "Y.m.dTH:i"
	// echo "XX##".$as->format("c")."##".$ae->format("Y-m-d H:i:s")."<br>";	


	if (empty($_POST['AS_Date'])) {
		$_POST['AS_Date']=$as->format("Y-m-d");
		$_POST['AS_Time']=$as->format("H:i");
	}
	if (empty($_POST['AE_Date'])) {
		$_POST['AE_Date']=$ae->format("Y-m-d");
		$_POST['AE_Time']=$ae->format("H:i");
	}
	echo '<tr><th>Kommt </th><td><input type="date" name="AS_Date" value="'.$_POST['AS_Date'].'"><input type="time" name="AS_Time" value="'.$_POST['AS_Time'].'"></td></tr>';
	if ($inarbeit) {
		echo '<tr><th>Geht  </th><td><input type="date" name="AE_Date"><input type="time" name="AE_Time"></td></tr>';
	} else {		
		echo '<tr><th>Geht  </th><td><input type="date" name="AE_Date" value="'.$_POST['AE_Date'].'"><input type="time" name="AE_Time" value="'.$_POST['AE_Time'].'"></td></tr>';
	}
	echo '<tr><td colspan=2 style="text-align:center;">';
	echo '<input type="hidden" name="AS_recnum" value="'.$_POST['AS_recnum'].'">';
	if (isset($_POST['AE_recnum'])) {
		echo '<input type="hidden" name="AE_recnum" value="'.$_POST['AE_recnum'].'">';
	}
	echo '<input type="submit" name="AZ_del" value="entfernen">';
	echo '<span style="margin-left:20px;"><input type="submit" name="AZ_neu" value="Diese Zeit hinzufügen">';
	echo '<span style="margin-left:20px;"><input type="submit" name="AZ_update" value="anpassen">';
	echo '</td></tr>';
	echo '</table>';
	echo '<br><table id="liste">';
	echo '<tr><th colspan=2>Pausen</th></tr>';
	echo $pausen;
	echo '</table>';
	// echo '<h2>Veränderte Daten</h2>';
	echo '<table id="liste" style="margin-top:10px;">';
	echo '<tr><td colspen=2 style="text-align:center;padding-top:10px;padding-bottom:10px;"><input type="submit" name="P_update" value="Pausen anpassen"></td></tr>';
	echo '</table>';

	$now=new DateTime();
	if (!empty($pe)) {
		$now=$pe;
	} else
	if (!empty($ps)) {
		$now=$ps;
	}
	
	// echo '<h2>Pause einfügen</h2>';
	echo '<br><table id="liste">';
	
	echo '<tr><th colspan=2>Pause einfügen</th></tr>';
	echo '<tr><th>von  </th><td><input type="date" name="PS_add_Date" value="'.$now->format("Y-m-d").'"><input type="time" name="PS_add_Time" value="'.$now->format("H:i").'"></td></tr>';
	echo '<tr><th>bis  </th><td><input type="date" name="PE_add_Date" value="'.$now->format("Y-m-d").'"><input type="time" name="PE_add_Time" value="'.$now->format("H:i").'"></td></tr>';
	echo '<tr><td colspan=2 style="text-align:center;padding-top:10px;padding-bottom:10px;"><input type="submit" name="addpause" value="hinzufügen"></td></tr>';
	echo '</table>';

} else {
	// echo '</h2>';
	echo '<table id="liste">';
	echo '</tr><th colspan=2>';
	echo 'Arbeitzeit von '.$m->row['name'].' ('.$m->row['nr'].')';
	echo '</th></tr>';
	echo '<tr><td>keine Arbeitszeit</td></tr>';
	echo '</table>';
}
echo '</form></center>';

showBottom();
?>