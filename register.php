<?php
session_start();
include "dbconnect.php";
include "menu.php";


$msg="";
if (isset($_POST['loginpw']) && isset($_POST['loginpw2'])  && isset($_POST['loginname']) ) {
	if ($_POST['loginpw'] != $_POST['loginpw2']) {
		$msg="Passwörter stimmen nicht überein!";
	} else {
		$request="select * from bu_user where benutzername='".$_POST['loginname']."';";
		$result = $db->query($request);
		if ($result->num_rows > 0) {
			$msg="Benutzer existiert schon";
		} else {
			// Speichern des Passwort-Hash
			$request  = sprintf("INSERT INTO bu_user(benutzername,passwort,mail) VALUES('%s','%s','%s');",
						$db->real_escape_string($_POST['loginname']),
						password_hash($_POST['loginpw'], PASSWORD_DEFAULT),
						$_POST['mail']);
			$result = $db->query($request);
			$_SESSION['username']=$_POST['loginname'];			
			header("location:firma.php");
		}
		
	}
}


if (!isset($_POST['loginname'])) {
	$_POST['loginname']="";
}
if (!isset($_POST['loginpw'])) {
	$_POST['loginpw']="";
}
if (!isset($_POST['loginpw2'])) {
	$_POST['loginpw2']="";
}
if (!isset($_POST['mail'])) {
	$_POST['mail']="";
}


showHeader("Willkommen bei der Registrierung bei uns");
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
	padding-top:50px; 	
	border:4px solid darkblue; // #5BBB2B;
	border-bottom:2px solid darkblue;// #5BBB2B;
	border-right:2px solid darkblue; //#5BBB2B;
	border-radius:60px 5px;
	">
<form action="register.php" method="POST">
<table>
<tr><th>Loginname:</th><td><input type="text" name="loginname" value="<?php echo $_POST['loginname']; ?>"></td></tr>
<tr><th>Passwort:</th><td><input type="password" name="loginpw" value="<?php echo $_POST['loginpw']; ?>"></td></tr>
<tr><th>Passwort wiederholen:</th><td><input type="password" name="loginpw2" value="<?php echo $_POST['loginpw2']; ?>"></td></tr>
<tr><th>Mail:</th><td><input type="text" name="mail" value="<?php echo $_POST['mail']; ?>" width="200px"></td></tr>
</table><br>
<input type="submit" name="submit" value="Registrieren">
<a style="margin-left:20px;" href ="login.php">Login</a>
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
