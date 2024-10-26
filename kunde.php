<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_adresse.php";


$fields=array(
	"recnum",
	"kdnr",
	"firma",
	"vorname",
	"nachname",
	"strasse",
	"plz",
	"ort",
	"tel_privat", 
	"tel_dienst", 
	"tel_mobil",
	"mail_privat",
	"mail_dienst",
	"skonto_prozent",
	"skonto_tage"
);



	$recnum="";
	$kdnr="";
	$firma="";
	$vorname="";
	$nachname="";
	$strasse="";	
	$plz="";
	$ort="";
	$tel_privat=""; 
	$tel_dienst=""; 
	$tel_mobil="";
	$mail_privat="";
	$mail_dienst="";
	$info_text="";
	$skonto_prozent="-1";
	$skonto_tage="";
	$skonto_individuell=1; // 1 = Nein 2= Ja

/*
function clearFields() {
	$recnum="";
	$kdnr="";
	$firma="";
	$vorname="";
	$nachname="";
	$strasse="";	
	$plz="";
	$ort="";
	$tel_privat=""; 
	$tel_dienst=""; 
	$tel_mobil="";
	$mail_privat="";
	$mail_dienst="";

	$info_text="";
}
clearFields();
	*/	
function mask($s) { // Apostrophe werden somit nicht zum Fehler
	return str_replace("'","\'",$s);
}

/*
if (isset($_POST['kdnr']) ) {
	$kdnr=$_POST['kdnr'];
}
if (isset($_POST['firma'])) {
	$firma=$_POST['firma'];
}
if (isset($_POST['vorname'])) {
	$vorname=$_POST['vorname'];
}
if (isset($_POST['nachname']) ) {
	$nachname=$_POST['nachname'];
}
if (isset($_POST['strasse'])) {
	$strasse=$_POST['strasse'];
}
if (isset($_POST['plz'])) {
	$plz=$_POST['plz'];
}
if (isset($_POST['ort'])) {
	$ort=$_POST['ort'];
}
if (isset($_POST['tel_privat'])) {
	$tel_privat=$_POST['tel_privat'];
}
if (isset($_POST['tel_dienst'])) {
	$tel_dienst=$_POST['tel_dienst'];
}
if (isset($_POST['tel_mobil'])) {
	$tel_mobil=$_POST['tel_mobil'];
}
if (isset($_POST['mail_privat'])) {
	$mail_privat=$_POST['mail_privat'];
}
if (isset($_POST['mail_dienst'])) {
	$mail_dienst=$_POST['mail_dienst'];
}
if (isset($_POST['recnum']) ) {
	$recnum=$_POST['recnum'];
}
*/
$recnum=0;
if (isset($_POST['recnum'])) {
	$recnum=$_POST['recnum'];
}

$msgok="";
$msgerr="";

if (isset($_POST['save'])) {
	$row=mask($_POST); // gepostete Felder
	$set="";
	$f=$fields;        // Felder inklusive recnum
	array_shift($f);   // Recnum eleminieren
	
	$sav_individuell=$row['skonto_individuell'];
	$sav_prozent=$row['skonto_prozent'];
	if ($row['skonto_individuell'] == 1) {  // 1= Nein 2=Ja
		$row['skonto_prozent']=-1;
		unset($row['skonto_individuell']);		
	}
	
	$kunde=$row['vorname']." ".$row['nachname'];
	if (!empty($row['firma'])) $kunde=$row['firma'];
	if ($recnum>0) {
		//UPDATE
		foreach ($f as $k) {
			if ($set) {
				$set.=",";
			}
			$v=$row[$k];
			$set.="`".$k."`='".$v."'";
		}		
		$request="update `bu_kunden` set $set where recnum=$recnum";
		$msgok="Kunde ".$kunde." wurde geändert";
	} else {		
		// INSERT
		$values=array();
		foreach ($f as $k) {
			$values[]=$row[$k];		
		}
		$request="insert into `bu_kunden` (`".join("`,`",$f)."`,`auftraggeber`) values ('".join("','",$values)."','".$_SESSION['firmanr']."')";
		$msgok="Kunde ".$kunde." wurde neu angelegt";
	}
		
	// echo $request;
	
	$result = $db->query($request);
	if (!$result) {
		if ($recnum>0) {
			$msgerr="Kunde $kunde konnte nicht bearbeitet/geändert werden<br>";
		} else {
			$msgerr="Kunde $kunde konnte nicht angelegt werden<br>";
		}
	} else {
		$recnum=0;
	}
	
	
	$row['skonto_individuell'] = $sav_individuell;
	$row['skonto_prozent']=$sav_prozent;
	foreach ($row as $k => $v) {
		${$k}=$v;
	}
	if ($skonto_individuell == 1) {
		$skonto_prozent=0;
		$skonto_tage=0;
	}
	
	// für die nachbearbeitung
	// if (empty($recnum)) {
	//	$recnum=$db->insert_id;
	// }		
		
	
} else

if ($msgerr == "" and !empty($msgok) and !empty($_SESSION['projekt'])) {
	$_SESSION['projekt']['kunde_recnum']=$recnum;
}

$found=false;

if (isset($_POST['kunde_recnum'])) {
	$request="select * from `bu_kunden` where `recnum` = '".$_POST['kunde_recnum']."'";
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}

if (isset($_POST['find_kdnr'])) {
	$kdnr=mask($_POST['kdnr']);
	
	$request="select * from `bu_kunden` where `kdnr` = '".$kdnr."' and auftraggeber=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}
if (isset($_POST['find_name'])) {
	$vorname=mask($_POST['vorname']);
	$nachname=mask($_POST['nachname']);
	
	$request="select * from `bu_kunden` where `vorname` like '%".$vorname."%' and `nachname` like '%".$nachname."%' and auftraggeber=".$_SESSION['firmanr'];
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if ($row) {
		$found=true;
	}
}

if ($found) {
	$recnum=     $row['recnum'];
	$kdnr=       $row['kdnr'];
	$firma=      $row['firma'];
	$vorname=    $row['vorname'];
	$nachname=   $row['nachname'];
	$strasse=    $row['strasse'];	
	$plz=        $row['plz'];
	$ort=        $row['ort'];
	$tel_privat= $row['tel_privat']; 
	$tel_dienst= $row['tel_dienst']; 
	$tel_mobil=  $row['tel_mobil'];
	$mail_privat=$row['mail_privat'];
	$mail_dienst=$row['mail_dienst'];
	$skonto_prozent=$row['skonto_prozent'];
	$skonto_tage=$row['skonto_tage'];
	$skonto_individuell=2;
	
	if ($skonto_prozent == -1) {
		$skonto_individuell=1;
		$skonto_prozent=0;
		$skonto_tage=0;
	}
}
showHeader("Kunden");


?>	
<center>
<?php
	if (isset($_POST['save'])) {
		if ($msgerr) {
			echo "<h1>$msgerr</h1>";
		} else { 
			if ($msgok) {
				echo "<h1>$msgok</h1>";
			} else {
				echo "<h1>Die Kundendaten wurden erfolgreich geändert!<h1>";
			}
			// clearFields();		
		
			// echo "<a href=\"index.php\">Weiter zum Menu</a>";
			// exit;
		}
	}
	
	
	
?>

	
<form action="kunde.php" method="POST";>
<input type="hidden" name="recnum" value="<?php echo $recnum ?>">
<table>
<tr><th>Kundennummer</th><td>      <input type="text" name="kdnr"        size="10" value="<?php echo $kdnr ?>"><input type="submit" name="find_kdnr" value="Suchen"></td></tr>
<tr><th>Firma</th><td>             <input type="text" name="firma"       size="50" value="<?php echo $firma ?>"></td></tr>
<tr><th>Vorname</th><td>           <input type="text" name="vorname"     size="50" value="<?php echo $vorname ?>"></td></tr>
<tr><th>Nachname</th><td>          <input type="text" name="nachname"    size="50" value="<?php echo $nachname ?>"><input type="submit" name="find_name" value="Name Suchen"></td></tr>
<tr><th>Straße</th><td>            <input type="text" name="strasse"     size="50" value="<?php echo $strasse ?>"></td></tr>
<tr><th>PLZ</th><td>               <input type="text" name="plz"         size="10" value="<?php echo $plz ?>"></td></tr>
<tr><th>Ort</th><td>               <input type="text" name="ort"         size="50" value="<?php echo $ort ?>"></td></tr>
<tr><th>Telefon privat</th><td>    <input type="text" name="tel_privat"  size="20" value="<?php echo $tel_privat ?>"></td></tr>
<tr><th>Telefon dienstlich</th><td><input type="text" name="tel_dienst"  size="20" value="<?php echo $tel_dienst ?>"></td></tr>
<tr><th>Telefon mobil</th><td>     <input type="text" name="tel_mobil"   size="20" value="<?php echo $tel_mobil ?>"></td></tr>
<tr><th>E-Mail dienstlich</th><td> <input type="text" name="mail_privat" size="50" value="<?php echo $mail_privat ?>"></td></tr>
<tr><th>E-Mail für Rechnug</th><td><input type="text" name="mail_dienst" size="50" value="<?php echo $mail_dienst ?>"></td></tr>
<tr><th colspan=2><b>Skonto</b></th></tr>
<tr><th>Skonto individuell</th><td>
<?php
	$checked1=false;
	$checked2=false;
	// echo 	$skonto_individuell;
	${'checked'.$skonto_individuell} ="checked";
?>
<input type="radio" value="1" name="skonto_individuell" <?php echo $checked1 ?>>Nein&nbsp;<input type="radio" value="2" name="skonto_individuell" <?php echo $checked2 ?>>Ja </td></tr>

<tr><th>Skonto Satz</th><td> <input type="number" name="skonto_prozent" value="<?php echo $skonto_prozent; ?>" size=5> % <i>(0 = kein Skonto)</i></td></tr>
<tr><th>Skonto Frist</th><td><input type="number" name="skonto_tage"    value="<?php echo $skonto_tage; ?>"    size=5> Tage</td></tr>


<tr><td colspan=2>
<!-- if recnum= 0 then "anlegen" if recnum >0 then "ändern" "neu anlegen" -->
<?php
  if ($recnum > 0) {
	  $value = "übernehmen";
  } else {
	  $value = "anlegen";
  }

  echo '<input type = "submit" name="save" value = "'.$value.'">';

?>


<!-- input type = "submit" name="find" value = "Suchen" --></td></tr>
</table>
<br>
<!-- input type="submit" name="zurueck" value="Menü" formaction="index.php" -->

</form>





</center>
<?php
	$display_address=false;
	
	if (!empty($recnum)) {
		
		$zuordnung=array("Rechnung","Angebot","Lieferadresse","Firmensitz","Allgemein","Mail CC","Mail BCC");
		$request="select * from `bu_adresse` where  `firmanr`='".$_SESSION['firmanr']."' and `kunde_recnum`='".$recnum."' order by `name`";
		// echo $request;
		// -------------------------------------------------------------
		$adresse=new Adresse($db);
		$adresse->query($request);
		// echo "*".$adresse->result->num_rows;
		
		if ($adresse->result->num_rows > 0) $display_address=true;
	}
	
	if ($display_address) {
		// $action ='<form style="display:inline;margin:0;padding:0;" method="POST">';

		echo "<center><table>";
		echo '<tr><th>Location/Firma</th><th>Name</th><th>Zuordnung</th><th>Aktion</th></tr>';
		while($row = $adresse->next()) {		
			$action="";
			$action ='<form style="display:inline;margin:0;padding:0;" method="POST">';
			$action.='<input type = "hidden" name="adresse_recnum"    value="'.$row['recnum'].'">';
			$action.='<input type = "submit" value="bearbeiten" name="btn_adresse" formmethod="POST" formaction="adresse.php">';
		
/*
			if (!$directstart and !empty($_SERVER['HTTP_REFERER'])) {
				foreach ($_POST as $k => $v) {
					$action.='<input type = "hidden" name="'.$k.'"    value="'.$v.'">';
				}
				
				$action.='<input type = "submit" value="auswählen" name="find_adresse" formmethod="POST" formaction="'.$_SERVER['HTTP_REFERER'].'">';
				$action.='<input type = "hidden" name="location"    value="'.$row['recnum'].'">';
			} else {
			}
*/
			$action.='</form>';
			
			echo '<tr>';
			echo '<td id="red" style="text-align:center;">'.$row['name'].'</td>';
			echo '<td id="red" style="text-align:center;">'.$row['vorname'].' '.$row['nachname'].'</td>';
			echo '<td id="red" style="text-align:center;">'.$zuordnung[$row['zuordnung']].'</td>';
			// echo '<td id="red" style="text-align: right;padding-right:5px;">'.$row['lagerbestand'].'</td>';
			echo '<td id="red" style="text-align:center;">'.$action.'</td>';
		
			echo '</tr>';
		}; 
		echo "</table></center></form>";
	}


showBottom();
?>

