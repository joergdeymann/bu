<?php
session_start();
include "../../class/dbconnect.php";
include "../../class/class_mitarbeiter.php";
include "../../class/class_cookie.php";

$worker	=new mitarbeiter($db);
$c		=new cookielogin();


$input = file_get_contents("php://input");
$data = json_decode($input, true);
if (json_last_error() === JSON_ERROR_NONE) {
    $_SESSION["filename"]=$data["query"] ?? "../index.html";
}

// $c->clear(); // Cookies und Sesseion löschen

// if (isset($_COOKIE["username"])) {
// 	echo "Cookie ist da<br>";
// }

$msg="";
$display="login";
if (isset($_SESSION['usernr']) && !empty($_SESSION['usernr'])) {
	$worker->loadByRecnum($_SESSION['usernr']);
	$_POST['user']=$worker->row['name'];
	$display="ok";	
} else 
if ($c->login_cookie()) {
	$_POST['user']=$_COOKIE['username'];
	$worker->load($_POST['user']);
	$display="ok";
	session_start();
	$_SESSION['usernr']=$worker->row['recnum'];
} else {
	$err=false;
	
	if (isset($_POST['pw1']) && isset($_POST['pw2'])) {
		if ($_POST['pw1'] != $_POST['pw2']) {
			$msg="Die Passwörter stimmen nicht überein<br>";
			$err=true;
			$display="change";
		} else {
			$worker->load($_POST['user']);
			$worker->setPassword($_POST['pw1']);
			$display="OK";
		}
				
	}
	
	if (empty($_POST['user']) && isset($_POST['user'])) {
		$msg.="Benutzer nicht eingegeben<br>";
		$err=true;	
	}
	if (empty($_POST['pw']) && isset($_POST['pw'])) {
		$msg.="Benutzer nicht eingegeben<br>";
		$err=true;	
	}
	
	if (($err==false) && isset($_POST['user']))  {
		// changepw();
		$worker->load($_POST['user']);
		if ($worker->load($_POST['user'])) {
			if ($c->check($_POST['pw'],$worker->row['pw'])) {  // PW = Nutzer Standart
				if ($c->check($_POST['user'],$worker->row['pw'])) {  // PW = Nutzer erster login
					$display="change";
				} else 	{
					$display="OK";
				    $c->setCookie();  // nur setzten wenn login complett
					$_SESSION['usernr']=$worker->row['recnum'];
				}
					
			} else {
				$msg="Passwort falsch";
				$err=true;
			}
		} else {
			$msg.="Benutzer ".$_POST['user']." nicht vorhanden!<br>";
			$err=true;
		}
		
	}
}


$contentType = $_SERVER['HTTP_ACCEPT'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    header('Content-Type: application/json');

    if ($display != "ok") {
        $json = array(
            "html" => "1"
        );
    } else {
        $json = array(
            "userId" => $worker->row['recnum'],
            "userName" => $worker->row['name'],
            "companyId" => $worker->row['firmanr']
        );
        
        
    }

    // JSON-Ausgabe
    echo json_encode($json);        
    exit;
}


if ($display=="login") {
	login($msg);
	exit;
}	
if ($display=="change") {
	changepw($msg);
	exit;
}	


function headers() {
    echo '<html lang="de"><head>
<meta charset="utf-8">
<meta name="description" 
	  content="Projekte erstellen und verwalten, auch über Handy">
<meta name="keywords" content="Handy, Projekt, Projektverwaltung, Login">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Projektverwaltung</title>
<link rel="stylesheet" href="../../zeit/zeiterfassung.css">
</head>
';
}
function login($msg="") {
	headers();
	echo '<body><center>';
	echo '<h1>Zeiterfassung</h1>';
	echo $msg;
	echo '<form method="POST" action="./login.php">';
	echo '<table>';
	echo '<tr><th>Name:</th><td><input type="text" size="50" name="user"></td></tr>';
	echo '<tr><th>Passwort:</th><td><input type="password" size="25" name="pw"></td></tr>';
	echo '<tr><th colspan=2><button name="OK">OK</th></tr>';
	echo '</table>';
	echo '</form>';
	echo '</center></body></html>';
}

function changepw($msg="") {
	headers();
	echo '<body><center>';
	echo '<h1>Projekterfassung</h1>';
	echo 'Sie loggen sich das erste mal ein.<br>Bitte ändern sie Ihr Passwort<br>';		
	echo $msg;
	echo '<br>';
	echo '<form method="POST" action="./login.php">';
	echo '<input type="hidden" name="user" value="'.$_POST['user'].'">';
	echo '<table border =1>';
	echo '<tr><th>Passwort:</th><td><input type="password" size="30" name="pw1"></td></tr>';
	echo '<tr><th>Passwort wiederholen:</th><td><input type="password" size="30" name="pw2"></td></tr>';
	echo '<tr><th colspan=2><button name="OK">OK</th></tr>';
	echo '</table>';
	echo '</form>';
	echo '</center></body></html>';
}

header("location:".$_SESSION["filename"]);