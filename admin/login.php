<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start(); //session_start();
$_POST['firmanr']=5;
$_POST['username']="Luka Hellmund";
$_SESSION['firmanr']=$_POST['firmanr'];
$_SESSION['username']=$_POST['username'];
$_SESSION['admin']="Jörg Deymann";
header("location:../menu_einstellung.php");
?>



