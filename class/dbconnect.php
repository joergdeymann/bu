<?php
$dbname = "bu"; 
$user="php";
$pw="#php#8.0-..";
$host="localhost";
if ($_SERVER['SERVER_NAME'] == 'www.die-deymanns.de') {
	$dbname = "k149450_ddbuero"; 
	$user="k149450_ddbuero";
	$pw="diE1.dEy9@jd#73";
	$host="10.35.46.206:3306";
}
// echo "Servername:".$_SERVER['SERVER_NAME'];
if ($_SERVER['SERVER_NAME'] == 'dd-office.de') {
		$user="k149450_ddbuero";
		$dbname = "k149450_ddbuero"; 
		$pw="diE1.dEy9@jd#73";
		$host="10.35.233.28:3306";
}


// $host="192.168.64.2";
// $user="root";
// $pw="";

$db = new mysqli($host, $user, $pw, $dbname);
if ($db->connect_errno) {
    die("Verbindung fehlgeschlagen: " . $db->connect_error);
}
$db->set_charset("utf8mb4");

/*
function mysql_fehler() {
     $logfehler = date("Y-m-d/H:i:s | ");
     $logfehler .= ('File: '.$_SERVER['PHP_SELF'].' | ');
     $logfehler .= ('MySQL Err.: '.$db->error().' | ');
     $logfehler .= ('IP: '.$_SERVER['REMOTE_ADDR']);  
     $logfehler .= chr(13).chr(10);
     error_log ($logfehler, 3, "mysql_error_log.txt");
     echo '<font color="#ff0000"><b><u><h3>Datenbank Fehler:</h3></u><br>';
     echo $logfehler;
     echo '<br>Das Script wurde vorzeitig beendet und der Webmaster wurde informiert!</b></font><br><br><input type="button" value="Zur&uuml;ck" onClick="history.back()">';    
} 
*/

?>