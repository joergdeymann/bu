<?php
include "session.php";
include "dbconnect.php";

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
		$row_to=$result->fetch_assoc();
		if ($result->num_rows > 0) {
			$err="Es wurde bereits eine Rechnung angefangen / erstellt mir diesem Angebot!<br>Hier ist die Rechnung<br>";
			$target="rechnung.php";
			return;			
		}


		$tabelle="bu_re";
		$request="SELECT * from bu_re WHERE renr='".$_POST['renr']."' and typ=1 and firmanr='".$_SESSION['firmanr']."'";
		$result=$db->query($request);
		$row=$result->fetch_assoc();
		if ($result->num_rows == 0) {
			$err="Fehler Angebot nicht gefunden!";
			$target="angebot.php";	
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
		echo $request."<br>";
		$result=$db->query($request);
		
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

	$tabelle="bu_re";
	$request="SELECT * from bu_re_posten WHERE renr='".$_POST['renr']."' and typ=0 and firmanr='".$_SESSION['firmanr']."'";
	$result=$db->query($request);
	if ($result->fetch_assoc()) {
		$err="Es gibt bereits Posten dieses Angebots für die Rechnung. Die Rechnung wurde aber gelöschet. Die Herrenlosen Posten wurden jetzt auch gelöscht.";
		$request="DELETE from bu_re_posten WHERE renr='".$_POST['renr']."' and typ=0 and firmanr='".$_SESSION['firmanr']."'";
		$result=$db->query($request);		
		return;
	}
			
	$request="SELECT * from bu_re_posten WHERE renr='".$_POST['renr']."' and typ=1 and firmanr='".$_SESSION['firmanr']."'";
	$result=$db->query($request);
	$row=array();
	while($row=$result->fetch_assoc()) {
		$values="";
		$keys="";
		$row['typ']=0;
		unset($row['recnum']);
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


}

get();
$_POST['err']=$err;
// echo $err."<br>".$target."<br>";

include $target;


/*
if ($target == "rechnung.php") {
	include "rechnung.php";
} else if ($target == "angebot.php") {
	include "angebot.php";
}	
*/

// header("location:".$target);

?>