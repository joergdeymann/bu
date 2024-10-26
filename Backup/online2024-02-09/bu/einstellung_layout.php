<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Einstellungen - Rechungslayouts");
$recnum=0;

$field=array(
'recnum' => 0,
'nr' => 0,
'mahnstufe' => 0,
'name' => "",
'ueberschrift' => "",
'retext' => "",
'posttext' => "",

);
/*
foreach($fieldlist as $key => $value) {
	if (!isset($_POST[$key])) {
		$_POST[$key]=$value;
	}
}
*/
	
if (isset($_POST['layout_suchen']) && $_POST['layout_suchen']) {
	$request="SELECT * from bu_re_layout where firmanr='".$_SESSION['firmanr']."' and nr=".$_POST['nr']." and mahnstufe=".$_POST['mahnstufe'];
	$result = $db->query($request);
	if ($result) {
		$row = $result->fetch_assoc(); 
		foreach($row as $key => $value) {
			$field[$key]=$value;
		}
	}
}

?>
<!-- 
               Linke Seite - Eingabe
-->			   
<div id="left"><center>
<form action="einstellung_layout.php" method="POST" style="width:100%">
<input type="hidden" name="recnum" value="<?php echo $field['recnum'] ?>">
<table>
<tr><th>Vorlagenr</th><td>             <input type="number" name="nr"              size="10" value="<?php echo $field['nr']; ?>"></td></tr>
<tr><th>Mahnstufe</th><td>             <input type="number" name="mahnstufe"       size="50" value="<?php echo $field['mahnstufe']; ?>"></td></tr>
<tr><td colspan=2><input type="submit" name="layout_suchen" value="Suchen"><input type="submit" name="layout_liste" value="Liste" formmethod="POST" formaction="einstellung_layout_liste.php"></td></tr>
</table>
<br>
<table  style="width:99%">
<tr><th colspan="2">Rechnungsangaben</th></tr>
<tr><th width="20%">Rechnungsbezeichnung</th>	<td>  <input type="text" name="name"     style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo $field['name']; ?>"></td></tr>
<tr><th>Rechnungstitel</th>			<td>  <input type="text" name="nachname"    style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo $field['ueberschrift']; ?>"></td></tr>
<tr><th>Anschreiben</th>			<td>  <textarea name="anschreiben" rows="3" style="width:calc(100% - 2px);padding:0;margin:0;"><?php echo $field['retext']; ?></textarea></td></tr>
<tr><th>Schlusswort</th>			<td>  <textarea name="schlusswort" rows="3" style="width:calc(100% - 2px);padding:0;margin:0;"><?php echo $field['posttext']; ?></textarea></td></tr>
<tr><th colspan="2">Mail anschreiben</th></tr>
<tr><th>Betreff</th>			    <td>  <input type="text" name="subject"     style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo "" ?>"></td></tr>
<tr><th>Anschreiben</th>			<td>  <textarea name="anschreiben" rows="6" style="width:calc(100% - 2px);padding:0;margin:0;"><?php echo "" ?></textarea></td></tr>
<tr><th>Logo</th>			        <td>  <input type="text" name="maillogo"    style="width:calc(100% - 4px);padding:0;margin:0;" value="<?php echo "" ?>"></td></tr>
<tr><th>Visitenkarte</th>			<td>  <textarea name="visitenkarte" rows="6" style="width:calc(100% - 2px);padding:0;margin:0;"><?php echo "" ?></textarea></td></tr>

</table>
<br>	
<button name="save"><?php if ($field['recnum']==0) {echo "<br>Anlegen<br>&nbsp;";} else {echo "<br>ändern<br>&nbsp;";} ?></button>
<button name="vorschau"><br>Vorschau<br>&nbsp;</button>
</form>
<br>


</center></div>



<!-- 
               Rechte Seite - Layout anzeigen
-->			   
<div id="right">
</div>
<?php
showBottom();
?>
