<?php
function mask($s) {
	return str_replace("'","\'",$s);
}


	$msg="";
	if (isset($_POST['sichern'])) {
		include("dbconnect.php");

		if (!isset($_POST['prio'])) {
			$_POST['prio']="0";
		} else {			
			$r="UPDATE `bu_firma` SET `prio`='0'  WHERE `prio`='1'";
			$result = $db->query($r);
			if (!$result) {
				$msg.="Fehler beim Speichern der Daten aufgetreten:<br>$r<br>".$db->error."<br>";
			}
		}

		$post=mask($_POST);
		
		$request="INSERT INTO `bu_firma`(`firma`, `strasse`, `plz`, `ort`, `vorname`, `nachname`, `inhaber`, `bankname`, `iban`, `bic`, `hrname`, `hra`, `ustid`, `betriebsnr`, `prio`, `logo`) 
		                       VALUES ('".  $post['firma']."','".
										    $post['strasse']."','".
											$post['plz']."','".
											$post['ort']."','".
											$post['vorname']."','".
											$post['nachname']."','".
											$post['inhaber']."','".
											$post['bankname']."','".
											$post['iban']."','".
											$post['bic']."','".
											$post['hrname']."','".
											$post['hra']."','".
											$post['ustid']."','".
											$post['betriebsnr']."','".
											$post['prio']."','".
											$post['logo']."')";
		
		
		$result = $db->query($request);
		if (!$result) {
			$msg.="Fehler beim Speichern der Daten aufgetreten:<br>$request<br>".$db->error."<br>";
		}

								 
		
		
	} else {
		$_POST=array(
			'firma'     => "",
			'strasse'   => "",
			'plz'       => "",
			'ort'       => "",
			'vorname'   => "",
			'nachname'  => "",
			'inhaber'   => "",
			'bankname'  => "",
			'iban'      => "",
			'bic'       => "",
			'hrname'    => "",
			'hra'       => "",
			'ustid'     => "",
			'betriebsnr'=> "",
			'prio'      => "",
			'logo'      => ""
		);
		
	}
?>
<!doctype html>
<html lang="de">
<head>
<link rel="stylesheet" href="standart.css">
</head>
<body><center>

		
		
<h1>Buchhaltung</h1>
<h2>Firmendaten anlegen</h2>
<?php
	if (isset($_POST['sichern'])) {
		if ($msg) {
			echo $msg;
		} else { 
			echo "<h1>Die Firmendaten sind eingetragen!<h1>";
			echo "<a href=\"index.php\">Weiter zum Menu</a>";
			exit;
		}
	}
?>
<form action="firmaNeu.php" method="POST">
<table>
<tr><th colspan=2><b>Adressangaben</b></th></tr>
<tr><th>Firmenname</th><td><input type="text" name="firma" value="<?php echo $_POST['firma'] ?>" size=60> </td></tr>
<tr><th>Strasse</th><td><input type="text" name="strasse" value="<?php echo $_POST['strasse'] ?>" size=60> </td></tr>
<tr><th>PLZ</th><td><input type="text" name="plz" value="<?php echo $_POST['plz'] ?>"> </td></tr>
<tr><th>Ort</th><td><input type="text" name="ort" value="<?php echo $_POST['ort'] ?>" size=60> </td></tr>
<tr><th>Vorname</th><td><input type="text" name="vorname" value="<?php echo $_POST['vorname'] ?>"  size=60> </td></tr>
<tr><th>Nachname</th><td><input type="text" name="nachname" value="<?php echo $_POST['nachname'] ?>" size=60> </td></tr>
<tr><th>Inhaber</th><td><input type="text" name="inhaber" value="<?php echo $_POST['inhaber'] ?>" size=60> </td></tr>

<tr><th colspan=2><b>Bankverbindung</b></th></tr>
<tr><th>Bankname</th><td><input type="text" name="bankname" value="<?php echo $_POST['bankname'] ?>" size=30> </td></tr>
<tr><th>IBAN</th><td><input type="text" name="iban" value="<?php echo $_POST['iban'] ?>" size=20> </td></tr>
<tr><th>BIC</th><td><input type="text" name="bic" value="<?php echo $_POST['bic'] ?>" size=10> </td></tr>

<tr><th colspan=2><b>Handelsregister</b></th></tr>
<tr><th>Handelsregister</th><td><input type="text" name="hrname" value="<?php echo $_POST['hrname'] ?>" size=15> </td></tr>
<tr><th>HRA-Nr</th><td><input type="text" name="hra" value="<?php echo $_POST['hra'] ?>" size=15> </td></tr>
<tr><th>Umsatzsteuer-ID</th><td><input type="text" name="ustid" value="<?php echo $_POST['ustid'] ?>" size=15> </td></tr>
<tr><th>Betriebsnummer</th><td><input type="text" name="betriebsnr" value="<?php echo $_POST['betriebsnr'] ?>" size=15> </td></tr>

<tr><th colspan=2><b>Standart</b></th></tr>
<tr><th>Bevorzugte Adresse</th><td><input type="checkbox" name="prio" value="1" <?php if ($_POST['prio'] == "1") {echo "CHECKED";} ?>> </td></tr>

<tr><th>Logo</th><td><input type="text" name="logo" value="<?php echo $_POST['logo'] ?>" size=100> </td></tr>
<tr><th>&nbsp;</th><td><input type="submit" name="sichern" value=" Übernehmen "> </td></tr>
</table>
<br>
<input type="submit" name="zurueck" value="Menü" formaction="index.php">

</form>


