<?php
	include "session.php";
	include "dbconnect.php";
	
// 	if (!isset($_GET['kdnr']) || !isset($_GET['kdnr'])) {
// 		exit;
// 	}
	if (!isset($_GET['renr']) || $_GET['renr']=="") {
		exit;
	}
	
	$versandart=2; // Per Brief

	$set_faellig="";  // Fällig nur bei Mahnungen updaten Mahnstufe 0 = Rechnung
	if (!isset($_GET['mahnstufe']) || $_GET['mahnstufe']=="") {
		$_GET['mahnstufe']=0;
	} else {
		if (isset($_GET['faellig']) && $_GET['faellig']) {			
			$request="insert into `bu_mahn` (`firmanr`,`renr`,`mahnstufe`,`datum`,`faellig`) values ('".$_SESSION['firmanr']."','".$_GET['renr']."','".$_GET['mahnstufe']."',CURRENT_DATE,'".$_GET['faellig']."')";
			$result = $db->query($request); // Vertrauen da blindflug (Verstecktes Fenster)
			
			// Fälligkeitsdatum bleibt wie es ist $set_faellig=", `faellig`='".$_GET['faellig']."'";
		}
	}
	
	// Basisrechnungseintrag einrichten
	$request="update `bu_re` set `mahnstufe`='".$_GET['mahnstufe']."', `versandart`='".$versandart."',`versanddatum`=CURRENT_DATE where firmanr='".$_SESSION['firmanr']."' and `renr` = '".$_GET['renr']."'";
	// echo $request;
	
	$result = $db->query($request);

?>