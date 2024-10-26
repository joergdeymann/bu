<?php
session_start();
// session_destroy();
// exit;

include "dbconnect.php";
include "menu.php";





$msg="";
if (isset($_POST['loginname']) && isset($_POST['loginpw'])) {
// Abfragen, ob der User das richtige Passwort übermittelt hat
	$request = sprintf("SELECT passwort,last_firma FROM bu_user WHERE benutzername='%s';",
				$db->real_escape_string($_POST['loginname']));
				
	$result = $db->query($request);
	$row = $result->fetch_assoc();	
	if ($row && password_verify($_POST['loginpw'], $row['passwort'])) {
		// echo 'Willkommen, ' . htmlspecialchars($_POST['loginname']) . '!';
		$_SESSION['username']=$_POST['loginname'];
		
		
		// Hier noch die Firma laden, falls Daternsatz gefunden wird ansonsten die Firmendaten neu eingeben
		if ($row['last_firma'] > 0) {
			// Firma wurde gespeichert
			$request = "SELECT * FROM bu_rechte WHERE benutzername='".$db->real_escape_string($_POST['loginname'])."' and firmanr=".$row['last_firma'];						
		} else {
			// Firma wurde nicht gespeichert: User neu angelegt
			$request = "SELECT * FROM bu_rechte WHERE benutzername='".$db->real_escape_string($_POST['loginname'])."' limit 1;";			
		}
		
		$result = $db->query($request);
		if ($result->num_rows == 0) {
			header("location:firma.php");
		} else {			
			$row = $result->fetch_assoc();	
			$_SESSION['firmanr']=$row['firmanr'];

			$request = "SELECT * FROM bu_firma WHERE recnum='".$row['firmanr']."' limit 1;";
			$result = $db->query($request);
			if ($result) {
				$row = $result->fetch_assoc();	
				$_SESSION['firmaname']=$row['firma'];	
			}
			header("location:menu_rechnung.php");
		}
		exit;
	} else {
		$msg='Authentifizierung für ' . htmlspecialchars($_POST['loginname']) . ' fehlgeschlagen.';
	}
}
	


if (!isset($_POST['loginname'])) {
	$_POST['loginname']="";
}
if (!isset($_POST['loginpw'])) {
	$_POST['loginpw']="";
}


showHeader("Login");
?>
<br>
<center>
<?php
	if ($msg) {
		echo "<h1>$msg</h1>";
	}
?>
<div style="
	width:400px; 
	height:200px;
	border:white 5px groove;
	padding-top:100px; 	
	border:4px solid darkblue; // #5BBB2B;
	border-bottom:2px solid darkblue;// #5BBB2B;
	border-right:2px solid darkblue; //#5BBB2B;
	border-radius:60px 5px;
	// box-shadow:3px 3px 4px #8CA0B2;
	">
<form action="login.php" method="POST">
<table>
<tr><th>Loginname:</th><td><input type="text" name="loginname" value="<?php echo $_POST['loginname']; ?>"></td></tr>
<tr><th>Passwort:</th><td><input type="password" name="loginpw" value="<?php echo $_POST['loginpw']; ?>"></td></tr>
</table><br>
<input type="submit" name="loginbutton" value="Login">
<a style="margin-left:20px;" href ="register.php">Neu Registrieren</a>
</form>
</div>
</center>

<?php
showBottom();
?>






<?php
/*
// Speichern des Passwort-Hash
$query  = sprintf("INSERT INTO users(name,pwd) VALUES('%s','%s');",
            pg_escape_string($username),
            password_hash($password, PASSWORD_DEFAULT));
$result = pg_query($connection, $query);

// Abfragen, ob der User das richtige Passwort übermittelt hat
$query = sprintf("SELECT pwd FROM users WHERE name='%s';",
            pg_escape_string($username));
$row = pg_fetch_assoc(pg_query($connection, $query));

if ($row && password_verify($password, $row['pwd'])) {
    echo 'Willkommen, ' . htmlspecialchars($username) . '!';
} else {
    echo 'Authentifizierung für ' . htmlspecialchars($username) . 'fehlgeschlagen.';
}
*/
?>
