<?php
include "session.php";
include "dbconnect.php";
include "class/class_rechnung_extra.php";

$err="";
$target="";
$tabelle="";
function get() {
	global $err;
	global $target;
	global $tabelle;
	global $db;
	try {

		$tabelle="bu_re";
		$request="SELECT * from bu_re WHERE renr='".$_POST['renr']."' and typ=0 and firmanr='".$_SESSION['firmanr']."'";
		$result=$db->query($request);
		$row=$result->fetch_assoc();
		if ($result->num_rows == 0) {
			$err="Fehler: Rechung nicht gefunden!"; // Kann eigentlich nicht passieren
			$target="rechnung.php";	
			return;
		}
		$row['typ']=0;
		$row['mahnstufe']=0;
		$row['versandart']=0;
		unset($row['versanddatum']); // NULL
		unset($row['faellig']);
		unset($row['bezahlt']);
		unset($row['recnum']);
		$row['datum'] = (new DateTime())->format("Y-m-d");
		
		$rex=new Rechnung_extra($db);
		$renr=$rex->getNextRenr(); // get next Invoice Number
		
		$row['renr']=$renr;
		
		$keys="";
		$values="";
		foreach($row as $k => $v) {
			// echo "$k = $v<br>";			
			if (!empty($values)) {
				$values.=",";
				$keys.=",";
			}
			$es="";
			if (!empty($v)) $es=$db->real_escape_string($v);
			$values.= "'".$es."'";
			$keys  .= "`".$k."`";
		}

		$tabelle="bu_re";
		$request="INSERT INTO bu_re ($keys) VALUES ($values)";
		$result=$db->query($request);
		
		// $renr=$db->insert_id;
		
	} catch (Exception $e) {
		echo '<div style="display:inlne-box;margin:10px;padding:5px; border:red solid 2px;background-color: #EEEEEE;color:black;">';
		echo "Tabelle: ".$tabelle."<br>";
		echo "Script:". $_SERVER["SCRIPT_NAME"]."<br>";
		echo "Fehler:". $db->errno.":".$db->error."<br>";
		echo "Request:<br>";
		echo $request."<br>";
		echo "</div>";
	}


	/*
		POSTEN
	*/

	$request="SELECT * from bu_re_posten WHERE renr='".$_POST['renr']."' and typ=0 and firmanr='".$_SESSION['firmanr']."'";
	$result=$db->query($request);
	$row=array();
	while($row=$result->fetch_assoc()) {
		$values="";
		$keys="";
		$row['typ']=0;
		unset($row['recnum']);
		$row['renr']=$renr;
		foreach($row as $k => $v) {
			if (!empty($values)) {
				$values.=",";
				$keys.=",";
			}
			$values.= "'".$db->real_escape_string($v)."'";
			$keys  .= "`".$k."`";
		}
		$request="INSERT INTO bu_re_posten ($keys) VALUES ($values)";
		$result2=$db->query($request);
		//echo $request."<br>";		
		
	}
	
	$target="rechnung.php";
	$err="";
	$_POST['renr']=$renr;


}

get();
$_POST['err']=$err;
// echo $err."<br>".$target."<br>";



/*
if ($target == "rechnung.php") {
	include "rechnung.php";
} else if ($target == "angebot.php") {
	include "angebot.php";
}	
*/

// 31.03.2024 include $target; Läd include.php doppelt, abgefangen durcht include_once
header("location:".$target); // Wenn es Probleme gibt dann include target nehmen nehmen

?>