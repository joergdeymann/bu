<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_user.php";
// include "class/class_mitarbeiter.php";
$msg="";
$user=new User($db);
$rechte=new Rechte($db);

if (!empty($_POST['recnum']))	{
	$user->loadByRecnum($_POST['recnum']);
	$_POST['name']=$user->row['benutzername'];
	$_POST['mail']=$user->row['mail'];
	$_POST['firmanr']=$_SESSION['firmanr'];
	$rechte->loadByUniqueName($user->row['benutzername']);
	$_POST['level']=$rechte->row['level'];
}

if (empty($_POST['name']   )) {
	$_POST['name']="";
}	
if (empty($_POST['pw']     )) {
	$_POST['pw']="";
}	
if (empty($_POST['pw2']    )) {
	$_POST['pw2']="";
} 
if (empty($_POST['mail']   )) {
	$_POST['mail']="";
}	
if (empty($_POST['firmanr'])) {
	$_POST['firmanr']=$_SESSION['firmanr'];
}
if (empty($_POST['level']   )) {
	$_POST['level']="0";
}	

if (isset($_POST['find_name']) && isset($_POST['name'])) {
	if ($user->loadByName($_POST['name'])) {
		//echo "Hier:".$user->row['benutzername'];
		$_POST['name']=$user->row['benutzername'];
		$_POST['mail']=$user->row['mail'];
		$_POST['recnum']=$user->row['recnum'];
		$_POST['pw']="";
		$_POST['pw2']="";
		// $_POST['firmanr']=$user->row['firmanr'];
		if ($rechte->loadByUniqueName($user->row['benutzername'] )) {
			$_POST['level']=$rechte->row['level'];
		}			
		
	}
}
if (isset($_POST['save'])) {
	$msg="";
	if (empty($_POST['recnum'])) {
		if ($_POST['pw'] == $_POST['pw2']) {
			$row=array();
			$row['benutzername']=$_POST['name'];
			$row['mail']=$_POST['mail'];
			$row['last_firma']=$_POST['firmanr'];
			$user->insert($row);
			$user->setPassword($_POST['pw']);
			// $msg.="Passwort gespeichert<br>";
			$row=array();
			$row['benutzername']=$_POST['name'];
			$row['level']=$_POST['level'];
			$row['firmanr']=$_POST['firmanr'];
			$rechte->insert($row);
			$msg.="Benutzer angelegt<br>";
			$_POST['pw']="";
			$_POST['pw2']="";
			
		} else {
			$msg="Passwörter stimmen nicht überein";
		}
	} else {
		$msg="";
		$msg2="";
		$row=array();
		if (!empty($_POST['pw'])) {
			if  ($_POST['pw'] != $_POST['pw2']) {
				$msg="Passwörter stimmen nicht überein";
			} else {
				$user->row['recnum']=$_POST['recnum'];
				$user->setPassword($_POST['pw']);
				$msg2="und Passwort gespeichert<br>";
				$_POST['pw']="";
				$_POST['pw2']="";
			}

		} 
		if (empty($msg)) {
			// echo "POST_RECNUM=".$_POST['recnum']."*<br>";
			$row['recnum']=$_POST['recnum'];
			$row['benutzername']=$_POST['name'];
			$row['mail']=$_POST['mail'];
			$row['last_firma']=$_POST['firmanr'];
			$user->update($row);
			
			$row=array();
			$row['benutzername']=$_POST['name'];
			$row['level']=$_POST['level'];
			$row['firmanr']=$_POST['firmanr'];
			$rechte->update($row);
			$msg.="Benutzerdaten geändert ";
		}	$msg.=$msg2;
	}
		
		
		

}

	
showHeader("Benutzer anlegen / ändern");
echo '<center>';
echo '<h1>'.$msg.'</h1>';
echo '<form action="user_eingabe.php" method="POST">';
if (isset($_POST['recnum'])) {
	echo '<input type="hidden" name="recnum" value="'.$_POST['recnum'].'">';
}
echo '<table>';
echo '<tr><th>Name</th>                <td><input type="text"     name="name"     size="30" value="'.$_POST['name']   .'"><input type="submit" name="find_name" value="Suchen"></td></tr>';
if (empty($_POST['pw'])) {
	$value="";
} else {
	$value='value="'.$_POST['pw'].'"';
}
echo '<tr><th>Passwort</th>            <td><input type="password" name="pw"       size="50" '.$value.'></td></tr>';
if (empty($_POST['pw2'])) {
	$value="";
} else {
	$value='value="'.$_POST['pw2'].'"';
}

echo '<tr><th>Passwort wiederholen</th><td><input type="password" name="pw2"      size="50" '.$value.'></td></tr>';
echo '<tr><th>Mail</th>                <td><input type="text"     name="mail"     size="60" value="'.$_POST['mail']   .'"></td></tr>';
echo '<tr><th>Firmanr</th>             <td>'.$_POST['firmanr'].'<input type="hidden"   name="firmanr"  size="10" value="'.$_POST['firmanr'].'"></td></tr>';
echo '<tr><th>Level</th>               <td><input type="number"   name="level"    size="10" value="'.$_POST['level']   .'"></td></tr>';

echo '<tr><td colspan=2>';
if (isset($_POST['recnum'])) {
	$value="ändern";
} else {
	$value="anlegen";
}
echo '<input type = "submit" name="save" value = "'.$value.'">';
echo '</td></tr></table>';
echo '</center>';
?>
