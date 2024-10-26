<?php
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['usernr'])) {
	header("location:zeiterfassung.php");
}	
?>