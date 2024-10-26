<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
unset	($_SESSION['POST']);
unset	($_SESSION['HTTP_REFERER']);

$_SESSION['firmanr']=0; // Für die Vorlagenauswahlbearbeitung

showHeader("Einstellungen - Rechungslayouts");
$recnum=0;
$msg="";
$err=false;

$field=array(
'recnum' => 0,
'nr' => 0,
'mahnstufe' => 0,
'name' => "",
'ueberschrift' => "",
'retext' => "",
'schlusswort' => "",
'mahngebuehr' => "",
'zahlungsziel_dauer' => "",
'mail_subject' => "",
'mail_text' => "",
'mail_signature' => "",
'mail_logo' => "",
'kdnr' => 0,
'kunde' => ""


);

// Ferlder nur löschen wenn nicht in der Suche gewesen 
	foreach($field as $key => $value) {
		if (empty($_POST[$key])) {
			$_POST[$key]=$value;
		}
	}
	
$request="SELECT rechnungs_layout from bu_firma where recnum='".$_SESSION['firmanr']."'";
$result=$db->query($request);
$r=$result->fetch_assoc();
// echo $r['rechnungs_layout'];
// echo "<br>";
// echo  $_POST['nr'];

// print_r($_POST);
if (isset($_POST['save'])) {
	$msg="";
	$err=false;
	if ($_POST['recnum']>0) {
		// Update
		$request="UPDATE bu_re_layout 
				set name='".		$db->real_escape_string($_POST['name'])."'
				,ueberschrift='".	$db->real_escape_string($_POST['ueberschrift'])."'
				,retext='".			$db->real_escape_string($_POST['retext'])."'
				,schlusswort='".	$db->real_escape_string($_POST['schlusswort'])."'
				,mahngebuehr='".	$db->real_escape_string($_POST['mahngebuehr'])."'
				,zahlungsziel_dauer='".	$db->real_escape_string($_POST['zahlungsziel_dauer'])."'
				,mail_subject='".	$db->real_escape_string($_POST['mail_subject'])."'
				,mail_text='".		$db->real_escape_string($_POST['mail_text'])."'
				,mail_logo='".		$db->real_escape_string($_POST['mail_logo'])."'
				,mail_signature='".	$db->real_escape_string($_POST['mail_signature'])."'			
				,nr='".				$db->real_escape_string($_POST['nr'])."'
				,mahnstufe='".		$db->real_escape_string($_POST['mahnstufe'])."'
				,kdnr='".			$db->real_escape_string($_POST['kdnr'])."'
				where recnum=".		$_POST['recnum'];
// echo "<br>";
// echo $request;		
// echo "<br>";
		$result = $db->query($request);
		if ($result) {
			$msg="Layout texte erfolgfreich geändert";
		} else {
			$msg="Layouttexte konnten nicht geändert werden";
			$err=true;
		}
	} else {
/*
				$request="UPDATE bu_re_layout 
				set nr=".$_POST['nr']."
				,mahnstufe=".$_POST['mahnstufe']."
				,fmahnstufe=".$_POST['mahnstufe']."
				where recnum=".$_SESSION['recnum'];
*/
	}
	
}
if (isset($_POST['clear'])) {
	$_POST['kdnr']=0;
	$_POST['kunde']="";
}

if (isset($_POST['copy'])) {
	$msg="";
	$err=false;
	if ($_POST['recnum']>0) {
		// Insert
		if (empty($_POST['kdnr'])) $_POST['kdnr']=0;
		
		$request="INSERT INTO bu_re_layout 
				set name='".		$db->real_escape_string($_POST['name'])."'
				,ueberschrift='".	$db->real_escape_string($_POST['ueberschrift'])."'
				,retext='".			$db->real_escape_string($_POST['retext'])."'
				,schlusswort='".	$db->real_escape_string($_POST['schlusswort'])."'
				,mahngebuehr='".	$db->real_escape_string($_POST['mahngebuehr'])."'
				,zahlungsziel_dauer='".	$db->real_escape_string($_POST['zahlungsziel_dauer'])."'
				,mail_subject='".	$db->real_escape_string($_POST['mail_subject'])."'
				,mail_text='".		$db->real_escape_string($_POST['mail_text'])."'
				,mail_logo='".		$db->real_escape_string($_POST['mail_logo'])."'
				,mail_signature='".	$db->real_escape_string($_POST['mail_signature'])."'
				,firmanr='".		$db->real_escape_string($_SESSION['firmanr'])."'
				,nr='".				$db->real_escape_string($_POST['nr'])."'
				,mahnstufe='".		$db->real_escape_string($_POST['mahnstufe'])."'
				,kdnr='".			$db->real_escape_string($_POST['kdnr'])."'";
				
// echo "<br>";
// echo htmlspecialchars($request);		
// echo "<br>";
		$result = $db->query($request);
		if ($result) {
			$msg="Layout texte erfolgfreich hinzugefügt";
		} else {
			$msg="Layouttexte konnten nicht hinzugefügt werden";
			$err=true;
		}
	} 	
}


	
if (isset($_POST['layout_suchen']) && $_POST['layout_suchen']) {
	if (empty($_POST['kdnr'])) $_POST['kdnr']="0";
	$request="SELECT * from bu_re_layout where firmanr='".$_SESSION['firmanr']."' and nr=".$_POST['nr']." and mahnstufe=".$_POST['mahnstufe']." and kdnr='".$_POST['kdnr']."'";
	// echo $request;	
	$result = $db->query($request);
	if ($result) {
		if ($row = $result->fetch_assoc()) { 
			foreach($row as $key => $value) {
				$_POST[$key]=$value;
			}
		} else {
			foreach($field as $key => $value) {
				if (empty($_POST[$key])) $_POST[$key]=$field[$key];
			}
			$msg="Layout nicht vorhanden";
			$err=true;
		}
	}
}


echo '<div id="left"><center>';
if (!empty($msg)) {
	if ($err) {
		echo "<div id=\"err\">$msg</div>";
	} else {
		echo "<div id='noerr'>$msg</div>";
	}
	
	// echo "<h1>$msg</h1>";
}
?>
<!-- 
               Linke Seite - Eingabe
-->			   

<form action="admin_einstellung_layout.php" method="POST" style="width:100%">
<input type="hidden" name="recnum" value="<?php echo $_POST['recnum'] ?>">
<table>
<tr>
<th>Vorlagenr</th><td style="width:25em;">             <input type="number" name="nr"              size="10" value="<?php echo $_POST['nr']; ?>">
&nbsp;

</td></tr>
<tr><th>Mahnstufe</th><td>             <input type="number" name="mahnstufe"       size="10" value="<?php echo $_POST['mahnstufe']; ?>"></td></tr>
<tr><td colspan=2>

<table cellspacing=0 cellpadding=0 style="width:100%"><tr><td>
<input type="submit" name="layout_suchen" value="Suchen"><input type="submit" name="layout_liste" value="Liste" formmethod="POST" formaction="einstellung_layout_liste.php">
</td><td style="text-align:right">
</td></tr></table>

</td></tr>
</table>
<br>
<table  style="width:99%">
<tr><th colspan="2">Rechnungsangaben</th></tr>
<tr><th width="20%">Rechnungsbezeichnung</th>	<td>  <input type="text" name="name"     style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo $_POST['name']; ?>"></td></tr>
<tr><th>Rechnungstitel</th>			<td>  <input type="text" name="ueberschrift"    style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo $_POST['ueberschrift']; ?>"></td></tr>
<tr><th>Anschreiben</th>			<td>  <textarea name="retext" rows="3" style="width:calc(100% - 4px);padding:0;margin:0;"><?php echo $_POST['retext']; ?></textarea></td></tr>
<tr><th>Schlusswort</th>			<td>  <textarea name="schlusswort" rows="3" style="width:calc(100% - 4px);padding:0;margin:0;"><?php echo $_POST['schlusswort']; ?></textarea></td></tr>
<tr><th>Mahngebühren</th>			<td>  <input type="text" name="mahngebuehr"   placeholder="0.00" pattern="^\d*(\.\d{0,2})?$" step="1"  style="width:10em;padding:0;margin:0;" value="<?php echo $_POST['mahngebuehr']; ?>"> €</td></tr>
<tr><th>Zahlungsziel</th>			<td>  <input type="number" name="zahlungsziel_dauer"  pattern="^\d$" step="1"   style="width:5em;padding:0;margin:0;" value="<?php echo $_POST['zahlungsziel_dauer']; ?>"> Tage</td></tr>
<tr><th colspan="2">Mail anschreiben</th></tr>
<tr><th>Betreff</th>			    <td>  <input type="text" name="mail_subject"     style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo $_POST['mail_subject'] ?>"></td></tr>
<tr><th>Anschreiben</th>			<td>  <textarea name="mail_text" rows="6" style="width:calc(100% - 2px);padding:0;margin:0;"><?php echo $_POST['mail_text'] ?></textarea></td></tr>
<tr><th>Logo</th>			        <td>  <input type="text" name="mail_logo"    style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo $_POST['mail_logo'] ?>"></td></tr>
<tr><th>Signatur</th>			<td>  <textarea name="mail_signature" rows="6" style="width:calc(100% - 2px);padding:0;margin:0;"><?php echo $_POST['mail_signature'] ?></textarea></td></tr>

</table>
<br>	
<button name="save"		 type="submit"><?php if ($_POST['recnum']==0) {echo "<br>Anlegen<br>&nbsp;";} else {echo "<br>ändern<br>&nbsp;";} ?></button>
<?php 
if ($_POST['recnum']!=0) {
	echo '<button name="copy"		 type="submit" >';	
	echo 'als Neu<br>Kopieren';
	echo "</button>";
} 
?>
<button name="vorschau"  type="submit"><br>Vorschau<br>&nbsp;</button>
</form>
<br>


</center></div>



<!-- 
               Rechte Seite - Layout anzeigen
-->			   
<div id="right">
<center>
Hier kommt die Anzeige der Vorlage Life hin
</center>
</div>
<?php
showBottom();
?>
