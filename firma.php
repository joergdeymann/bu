<?php
/*
$text="Hallo";
$v="text";
echo ${$v};
${$v}="Neu Hallo";
echo $text;
exit;
*/ 
include "session.php";
include "dbconnect.php";
include "menu.php";

function updateUser() {
	global $db;
	$request="UPDATE bu_user set last_firma=".$_SESSION['firmanr']." where benutzername='".$db->real_escape_string($_SESSION['username'])."'";
	$result = $db->query($request);	
}
function updateLayout() {
	if ($_SERVER['SERVER_NAME'] == "localhost") {
		echo "Bitte hier was machen: firma.php::updateLayout()";
		exit;
	}
	return;
	
	global $db;
	// Die Mahnsachen vorbereiten
	/* ############################ */
	/* Was mach ich eigentlich hier */
	/* Das muss unbedingt anders    */
	/* Beim Update ist ein fehler   */
	/* WHERE oder feldname  oder    */
	/*       beides fehlt           */
	/* ############################ */
	
	$request="
	CREATE TEMPORARY TABLE tmp SELECT * FROM bu_re_layout WHERE firmanr = 0;
	UPDATE tmp set recnum=null, '".$_SESSION['firmanr']."';
	INSERT INTO bu_re_layout SELECT * FROM tmp;";
	$result = $db->query($request);	
}	

function clearFields() {
	$_POST=array(
		'recnum'    	=> "0",
		'firma'     	=> "",
		'strasse'   	=> "",
		'plz'       	=> "",
		'ort'       	=> "",
		'vorname'   	=> "",
		'nachname'  	=> "",
		'web'  			=> "",
		'iname'   		=> "", 
		'itel'   		=> "",
		'imail'   		=> "", 
		'aname'   		=> "", 
		'atel'   		=> "",
		'amail'   		=> "", 
		'rname'   		=> "", 
		'rtel'   		=> "",
		'rmail'   		=> "", 
		'bankname'  	=> "",
		'iban'      	=> "",
		'bic'       	=> "",
		'hrname'    	=> "",
		'hra'       	=> "",
		'ustid'     	=> "",
		'betriebsnr'	=> "",
		'standart'  	=> "",
		'logo'      	=> "",
		'skonto_prozent'=> "",
		'skonto_tage'	=> "",
		'paypal_link' 	=> ""
	);
	return;
}

if (empty($_POST['paypal_link'])) 	$_POST['paypal_link']="";
if (empty($_POST['web'])) 			$_POST['web']="";


if (isset($_POST['zurueck'])) {
	exit;
}

$msg="";
$msgok="";
function mask($s) { // Apostrophe werden somit nicht zum Fehler
	return $s;
	// return str_replace("'","\'",$s);
}
/* 
	Aufruf von extern, zB: firma_liste
*/
if (isset($_POST['login'])) {
	$_SESSION['firmanr']=$_POST['firmanr'];
	$_SESSION['firmaname']=$_POST['firmaname'];

	updateUser();
	
}
	
if (isset($_POST['neu'])) {
	clearFields();
	// unset ($_POST['firmanr']);
} else
if (isset($_POST['suchen']) && $_POST['firma']) {
	$suche = mask($_POST['firma']);		
	$request="select * from `bu_firma` where firma like '%".$suche."%' limit 1";
	$result = $db->query($request);
	
	if (!$result) {
		$msg.="Fehler beim Suchen aufgetreten:<br>$r<br>".$db->error."<br>";
	}
	
	$row = $result->fetch_assoc();
	if ($row) {
		foreach ($row as $k => $v) {
			$_POST[$k]=$v;
		}
	} else {
		clearFields();
	}
	
				
} else 
if (isset($_POST['sichern'])) {
	if (!isset($_POST['standart'])) {
		$_POST['standart']="0";
	} else {		
		// print_r($_SESSION);exit;
		$r="UPDATE `bu_firma` SET `standart`='0'  WHERE `standart`='1' and recnum='".$_SESSION['firmanr']."'"; // ################# DAS GEHT NUR LOKAL ##################### Bitte anders lösen ueber USER ## Das sollte die Lösung sein
		$result = $db->query($r);
		if (!$result) {
			$msg.="Fehler beim Speichern der Daten aufgetreten:<br>$r<br>".$db->error."<br>";
		}
	}
	$_POST=mask($_POST);
	// foreach($_POST as $k => $v) {
	// 	${$k}=$v;
	// }
	// echo $firma;
	// 	exit;

	if ($_POST['recnum']>0) {
		$msgok="Die Firmendaten wurden erfolgreich geändert!";
		$request="update  `bu_firma` set 
		`firma`     	='".$db->real_escape_string($_POST['firma'])."',  
		`strasse`   	='".$db->real_escape_string($_POST['strasse'])."', 
		`plz`       	='".$db->real_escape_string($_POST['plz'])."',  
		`ort`       	='".$db->real_escape_string($_POST['ort'])."',  
		`vorname`   	='".$db->real_escape_string($_POST['vorname'])."', 
		`nachname`  	='".$db->real_escape_string($_POST['nachname'])."',		
		`web`  			='".$db->real_escape_string($_POST['web'])."',		
		`iname`   		='".$db->real_escape_string($_POST['iname'])."', 
		`itel`   		='".$db->real_escape_string($_POST['itel'])."', 
		`imail`   		='".$db->real_escape_string($_POST['imail'])."', 
		`aname`   		='".$db->real_escape_string($_POST['aname'])."', 
		`atel`   		='".$db->real_escape_string($_POST['atel'])."', 
		`amail`   		='".$db->real_escape_string($_POST['amail'])."', 
		`rname`   		='".$db->real_escape_string($_POST['rname'])."', 
		`rtel`   		='".$db->real_escape_string($_POST['rtel'])."', 
		`rmail`   		='".$db->real_escape_string($_POST['rmail'])."', 
		`bankname`  	='".$db->real_escape_string($_POST['bankname'])."', 
		`iban`      	='".$db->real_escape_string($_POST['iban'])."', 
		`bic`       	='".$db->real_escape_string($_POST['bic'])."',  
		`paypal_link` 	='".$db->real_escape_string($_POST['paypal_link'])."',  	
		`hrname`    	='".$db->real_escape_string($_POST['hrname'])."', 
		`hra`       	='".$db->real_escape_string($_POST['hra'])."', 
		`ustid`     	='".$db->real_escape_string($_POST['ustid'])."', 
		`betriebsnr`	='".$db->real_escape_string($_POST['betriebsnr'])."', 
		`standart`      ='".$db->real_escape_string($_POST['standart'])."', 
		`skonto_prozent`='".$db->real_escape_string($_POST['skonto_prozent'])."',
		`skonto_tage`	='".$db->real_escape_string($_POST['skonto_tage'])."',
		`logo`      	='".$db->real_escape_string($_POST['logo'])."' where recnum=".$_POST['recnum'];
	} else {
		$msgok="Die Firmendaten wurden erfolgreich angelegt!";
		$request="insert into `bu_firma` (
		`firma`,     
		`strasse`,   
		`plz`,       
		`ort`,       
		`vorname`,   
		`nachname`,  
		`web`,  
		`iname`, 
		`itel`, 
		`imail`, 
		`aname`, 
		`atel`, 
		`amail`, 
		`rname`, 
		`rtel`, 
		`rmail`, 
		`bankname`,  
		`iban`,      
		`bic`,       
		`paypal_link`,       
		`hrname`,    
		`hra`,       
		`ustid`,     
		`betriebsnr`,
		`standart`,      			
		`skonto_prozent`,
		`skonto_tage`,	
		`logo`) VALUES
		('".$db->real_escape_string($_POST['firma'])."',  
		 '".$db->real_escape_string($_POST['strasse'])."', 
		 '".$db->real_escape_string($_POST['plz'])."',  
		 '".$db->real_escape_string($_POST['ort'])."',  
		 '".$db->real_escape_string($_POST['vorname'])."', 
		 '".$db->real_escape_string($_POST['nachname'])."', 
		 '".$db->real_escape_string($_POST['web'])."', 
	 	 '".$db->real_escape_string($_POST['iname'])."', 
		 '".$db->real_escape_string($_POST['itel'])."', 
		 '".$db->real_escape_string($_POST['imail'])."', 
		 '".$db->real_escape_string($_POST['aname'])."', 
		 '".$db->real_escape_string($_POST['atel'])."', 
		 '".$db->real_escape_string($_POST['amail'])."', 
		 '".$db->real_escape_string($_POST['rname'])."', 
		 '".$db->real_escape_string($_POST['rtel'])."', 
		 '".$db->real_escape_string($_POST['rmail'])."', 
		 '".$db->real_escape_string($_POST['bankname'])."', 
		 '".$db->real_escape_string($_POST['iban'])."', 
		 '".$db->real_escape_string($_POST['bic'])."',  
		 '".$db->real_escape_string($_POST['paypal_link'])."',  
		 '".$db->real_escape_string($_POST['hrname'])."', 
		 '".$db->real_escape_string($_POST['hra'])."', 
		 '".$db->real_escape_string($_POST['ustid'])."', 
		 '".$db->real_escape_string($_POST['betriebsnr'])."', 
		 '".$db->real_escape_string($_POST['standart'])."', 
		 '".$db->real_escape_string($_POST['skonto_prozent'])."',
		 '".$db->real_escape_string($_POST['skonto_tage'])."',
		 '".$db->real_escape_string($_POST['logo'])."')";
	}
	
	// echo $request;
	// echo $request."<br>";		
	$result = $db->query($request);
	if (!$result) {
		$msg.="Fehler beim Speichern der Daten aufgetreten:<br>$request<br>".$db->error."<br>";
	} else {
		// echo "Recnum=".$_POST['recnum'];
		// Neuer Kunde angelegt
		
		if ($_POST['recnum'] == 0) { 
			// echo "Recnum=0!";
			$recnum=$db->insert_id;
			//echo "Recnum Neu = ".$recnum;	
			$request="INSERT INTO bu_rechte (benutzername,firmanr) VALUES ('".$db->real_escape_string($_SESSION['username'])."',".$recnum.")";
			$result = $db->query($request);	
			//echo "$request<br>";	
			$_SESSION['firmaname']=$_POST['firma'];
			$_SESSION['firmanr']=$recnum;
			updateUser();	
			updateLayout();
			
			
			
			
		}
	}	
} else {
	
	if (isset($_SESSION['firmanr']) && $_SESSION['firmanr'] && !(isset($_POST['firmanr']) && $_POST['firmanr'])) {
		$_POST['firmanr']=$_SESSION['firmanr'];
	} 
	if (isset($_POST['firmanr']) && $_POST['firmanr']) {
		$request="select * from `bu_firma` where recnum=".$_POST['firmanr'];
		// echo "isset Firmanr:".$request;
		$result = $db->query($request);
		$row = $result->fetch_assoc();	
		foreach ($row as $k => $v) {
			$_POST[$k]=$row[$k];
		}
	} else {
		clearFields();		
	}
}

showHeader("Firmendaten");

?>

<center>

<?php
	if (isset($_POST['sichern'])) {
		if ($msg) {
			echo $msg;
		} else { 
			if ($msgok) {
				echo "<h1>$msgok</h1>";
			} else {
				echo "<h1>Die Firmendaten wurden erfolgreich geändert!<br>$msgok<h1>";
			}
			// clearFields();		
			
			// echo "<a href=\"index.php\">Weiter zum Menu</a>";
			// exit;
		}
	}
?>
<form action="firma.php" method="POST">
<input name="recnum" type="hidden" value="<?php echo $_POST['recnum'] ?>"> 
<table>
<tr><th colspan=2><b>Adressangaben</b></th></tr>
<tr><th>Firmenname</th><td><input type="text" name="firma" value="<?php echo $_POST['firma'] ?>" size=60> <input name="neu" type="submit" value="Neu"><!--input name="suchen" type="submit" value="Suchen"--></td></tr>
<tr><th>Strasse</th><td><input type="text" name="strasse" value="<?php echo $_POST['strasse'] ?>" size=60> </td></tr>
<tr><th>PLZ</th><td><input type="text" name="plz" value="<?php echo $_POST['plz'] ?>"> </td></tr>
<tr><th>Ort</th><td><input type="text" name="ort" value="<?php echo $_POST['ort'] ?>" size=60> </td></tr>
<tr><th>Vorname</th><td><input type="text" name="vorname" value="<?php echo $_POST['vorname'] ?>"  size=60> </td></tr>
<tr><th>Nachname</th><td><input type="text" name="nachname" value="<?php echo $_POST['nachname'] ?>" size=60> </td></tr>
<tr><th>Webseite</th><td><input type="text" name="web" value="<?php echo $_POST['web'] ?>" size=60> </td></tr>

<tr><th colspan=2><b>Inhaber</b></th></tr>
<tr><th>Name</th><td><input type="text" name="iname" value="<?php echo $_POST['iname'] ?>" size=60> </td></tr>
<tr><th>Mail</th><td><input type="text" name="imail" value="<?php echo $_POST['imail'] ?>" size=60> </td></tr>
<tr><th>Telefon</th><td><input type="text" name="itel" value="<?php echo $_POST['itel'] ?>" size=60> </td></tr>
<tr><th colspan=2><b>Ansprechpartner</b></th></tr>
<tr><th>Name</th><td><input type="text" name="aname" value="<?php echo $_POST['aname'] ?>" size=60> </td></tr>
<tr><th>Mail</th><td><input type="text" name="amail" value="<?php echo $_POST['amail'] ?>" size=60> </td></tr>
<tr><th>Telefon</th><td><input type="text" name="atel" value="<?php echo $_POST['atel'] ?>" size=60> </td></tr>
<tr><th colspan=2><b>Rechnung</b></th></tr>
<tr><th>Name</th><td><input type="text" name="rname" value="<?php echo $_POST['rname'] ?>" size=60> </td></tr>
<tr><th>Mail</th><td><input type="text" name="rmail" value="<?php echo $_POST['rmail'] ?>" size=60> </td></tr>
<tr><th>Telefon</th><td><input type="text" name="rtel" value="<?php echo $_POST['rtel'] ?>" size=60> </td></tr>

<tr><th colspan=2><b>Bankverbindung</b></th></tr>
<tr><th>Bankname</th><td><input type="text" name="bankname" value="<?php echo $_POST['bankname'] ?>" size=30> </td></tr>
<tr><th>IBAN</th><td><input type="text" name="iban" value="<?php echo $_POST['iban'] ?>" size=30> </td></tr>
<tr><th>BIC</th><td><input type="text" name="bic" value="<?php echo $_POST['bic'] ?>" size=20> </td></tr>
<tr><th>Paypal Link / Profil</th><td><input type="text" name="paypal_link" value="<?php echo $_POST['paypal_link'] ?>" size=40> <i>(https://paypal.me/Profilname/, oder Profilname)</i></td></tr>

<tr><th colspan=2><b>Handelsregister</b></th></tr>
<tr><th>Handelsregister</th><td><input type="text" name="hrname" value="<?php echo $_POST['hrname'] ?>" size=15> </td></tr>
<tr><th>HRA-Nr</th><td><input type="text" name="hra" value="<?php echo $_POST['hra'] ?>" size=15> </td></tr>
<tr><th>Umsatzsteuer-ID</th><td><input type="text" name="ustid" value="<?php echo $_POST['ustid'] ?>" size=15> </td></tr>
<tr><th>Betriebsnummer</th><td><input type="text" name="betriebsnr" value="<?php echo $_POST['betriebsnr'] ?>" size=15> </td></tr>

<tr><th colspan=2><b>Skonto</b></th></tr>
<tr><th>Skonto</th><td>
<!--
< ?php
	$checked1=false;
	$checked2=false;
	if (isset($_POST['skonto'])) {
		if ($_POST['skonto'] == 1)  {	
			$checked1=true;
		}
		if ($_POST['skonto'] == 2)  {	
			$checked2=true;
		}
	}
?>
<input type="radio" value="1" name="skonto" < ?php echo $checked1 ?>>Nein&nbsp;<input type="radio" value="2" name="skonto" < ?php echo $checked2 ?>>Ja: 
-->
<input type="number" name="skonto_prozent" value="<?php echo $_POST['skonto_prozent'] ?>" size=5> % <i>(0 = kein Skonto)</i></td></tr>
<tr><th>Skonto Frist</th><td><input type="number" name="skonto_tage" value="<?php echo $_POST['skonto_tage'] ?>" size=5> Tage</td></tr>

<tr><th colspan=2><b>Standart</b></th></tr>
<tr><th>Bevorzugte Adresse</th><td><input type="checkbox" name="standart" value="1" <?php if ($_POST['standart'] == "1") {echo "CHECKED";} ?>> </td></tr>

<tr><th>Logo</th><td><input type="text" name="logo" value="<?php echo $_POST['logo'] ?>" size=100> </td></tr>
<tr><th>&nbsp;</th><td><input type="submit" name="sichern" value="<?php
if (isset($_POST['recnum']) && $_POST['recnum']) {
	echo " Übernehmen ";
} else {
	echo " Neu Anlegen ";
}
?>"> </td></tr>
</table>
<br>
<!-- input type="submit" name="zurueck" value="Menü" formaction="index.php" -->
</form>
</center>
<?php
showBottom();
