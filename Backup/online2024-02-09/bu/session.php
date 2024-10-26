<?php
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['username'])) {
	header("location:login.php");
}

if (!isset($_SESSION['firmanr']) && basename($_SERVER['SCRIPT_NAME'])!="firma.php") {
	header("location:firma.php");
}

/* $_SESSION['firmanr']=14; // Test 
if (!isset($_SESSION['firmanr'])) {
	$_SESSION['firmanr']=0;
}
*/
	
?>