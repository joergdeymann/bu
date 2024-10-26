<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);         // PHP Fehler anzeigen
ini_set('display_startup_errors', 1); // Beim Starten von PHP
function errHandle($errNo, $errStr, $errFile, $errLine) {
	$stop=true;
	$w="";
	switch ($errNo) {
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_USER_ERROR:
			$error_type_str = "Fatal error";
			break;
		case E_RECOVERABLE_ERROR:
			$error_type_str = "Recoverable fatal error";
			break;
		case E_WARNING:
			$w .= "1";
		case E_CORE_WARNING:
			$w .= "2";
		case E_COMPILE_WARNING:
			$w .= "3";
		case E_USER_WARNING:
			$w .= "4";
			$error_type_str = "Warning";
			break;
		case E_PARSE:
			$error_type_str = "Parse error";
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			$error_type_str = "Notice";
			break;
		case E_STRICT:
			$error_type_str = "Strict Standards";
			break;
		case E_DEPRECATED:
			$error_type_str = "Deprecated";
			break;
		case E_USER_DEPRECATED:
			$error_type_str = "User Deprecated";
			break;
		default:
			$error_type_str = "Unknown error";
			break;
	}	
	if ($errNo == E_DEPRECATED and basename($errFile) == "qrencode.php")  {
		$stop=false;
	}
	if ($errNo == E_WARNING) {
		if ($errStr == 'foreach() argument must be of type array|object, null given') {
			$stop=false;
			// echo $w;exit;
		}
		$stop=false;
		
	}
	// fwrite(STDERR, "PHP $error_type_str:  $errStr in $errFile on line $errLine\n");
     //    exit(1);	
    $msg = "$errStr in $errFile on line $errLine";
	
	if ($stop) {
		if ($errNo == E_NOTICE || $errNo == E_WARNING) {
			echo "<b>Fehlernummer:</b> <br>";
			echo "<b>Fehlertyp:</b> $errNo: $error_type_str<br>";
			echo "<b>Fehlertext:</b> $errStr<br>";
			echo "<b>Datei:</b> $errFile<br>";
			echo "<b>Zeile:</b> $errLine<br>";
			// if (is_object
			
			// throw new ErrorException($error_type_str.":".$msg, $errNo);
			exit;
		} else {
				echo "<b>Fehlernummer:</b> <br>";
				echo "<b>Fehlertyp:</b> $errNo: $error_type_str<br>";
				echo "<b>Fehlertext:</b> $errStr<br>";
				echo "<b>Datei:</b> $errFile<br>";
				echo "<b>Zeile:</b> $errLine<br>";
				exit;
		}
	}
	// echo "nicht gestoppt";
}
set_error_handler('errHandle',E_ALL);

function pushPOST($key) {
	if (!isset($_SESSION[$key])) {
		// $_POST speichern nur beim ersten mal 
		
		if (count($_POST)==0 or basename($_SERVER['HTTP_REFERER']) == basename($_SERVER['SCRIPT_NAME']))  {
			// 1. Hier wurde direkt gestartet, es muss auch der SCRIPTname als Referer gespeichert werden
			$directstart=true;
			$_SESSION['HTTP_REFERER']=$_SERVER['SCRIPT_NAME'];// "adresse.php";
		} else {
			//2. wir kommen von ausserhalb und der Referer muss gespeichert werden
			$directstart=false;
			$_SESSION['HTTP_REFERER']=$_SERVER['HTTP_REFERER'];// "adresse.php";
			$_SESSION['adresse']=$_POST;
		}
	} else {
		// Alter Post wurde gespeichert und man war zwischendurch woanders jetzt kommt manzurück
		// alte Werte laden, wenn man woanders war
		if (empty($_SESSION['HTTP_REFERER'])) {
			$directstart=true;
		} else {
			$directstart=false;
			$_SERVER['HTTP_REFERER']=$_SESSION['HTTP_REFERER'];
		}
	}
	return $directstart;
	
}

function popPOST($key) {
	if (isset($_SESSION[$key])) {
		if (count($_POST)>0) {
			
			$POST=$_POST;
			
			$_POST=$_SESSION[$key];
			
			foreach($POST as $k => $v) {
				$_POST[$k]=$v;
			}

		} else {
			$_POST=$_SESSION[$key];
		}
		// echo "UNSET";
		unset($_SESSION[$key]);
		unset($_SESSION['HTTP_REFERER']);
		unset($POST);
	}
}



if(session_status() !== PHP_SESSION_ACTIVE) session_start(); //session_start();



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