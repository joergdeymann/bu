<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_mitarbeiter.php";

$m=new mitarbeiter($db);


function mask($s) { // Apostrophe werden somit nicht zum Fehler
	return str_replace("'","\'",$s);
}

/*
	$_POST für den Start vorbereiten
*/
function usePOST() {
	// global $_POST;
	
	$POST = array();
	$dt = new DateTime();
	
	$POST['recnum'] = 0;
	$POST['nr'] = "";          
	$POST['name'] = "";       
	$POST['mo'] = 8;  
	$POST['di'] = 8;         
	$POST['mi'] = 8;         
	$POST['do'] = 8;         
	$POST['fr'] = 8;         
	$POST['sa'] = 0;         
	$POST['so'] = 0;         
	$POST['jahresurlaub'] = 35;
	$POST['resturlaub'] = 35;
	$POST['entree']  = $dt->format("Y-m-d");      
	$POST['startup'] = $dt->format("Y-m-d");      
	$POST['firmanr'] = $_SESSION['firmanr'];
	$POST['ueberstunden_ab'] = $dt->format("Y-m-d");
    $POST['mail'] = "";
	
	foreach($POST as $k => $v) {
		if (!isset($_POST[$k])) {
			$_POST[$k] = $POST[$k];
			// echo $k."<br>";
		}
	}
	// echo $dt->format("Y-m-d")."<br>";
	// echo $POST['startup']."<br>";
	// echo $_POST['startup']."<br>";
	
	
	// exit;
}

/*
	Datensatz vorbereiten
*/
function getDataset() {
	$row = array();
	$row['recnum']       	= $_POST['recnum']; // Hidden
	$row['nr']              = $_POST['nr'];
	$row['name']       	 	= $_POST['name'];
	$row['mo']           	= $_POST['mo'];
	$row['di']           	= $_POST['di'];
	$row['mi']           	= $_POST['mi'];
	$row['do']           	= $_POST['do'];
	$row['fr']           	= $_POST['fr'];
	$row['sa']           	= $_POST['sa'];
	$row['so']           	= $_POST['so'];
	$row['jahresurlaub'] 	= $_POST['jahresurlaub'];
	$row['resturlaub'] 	    = $_POST['resturlaub'];
	$row['entree']       	= $_POST['entree'];
	$row['startup']       	= $_POST['startup'];
	$row['firmanr']      	= $_SESSION['firmanr'];
	$row['mail']      	    = $_POST['mail'];
	$row['ueberstunden_ab'] = $_POST['ueberstunden_ab'];
	
	return $row; // mask($row);
}	
function setDataset($row) {
	foreach($row as $k => $v) {
		$_POST[$k] = $row[$k];
	}
}


$msgok="";
$msgerr="";


if (isset($_POST['save'])) {
	$row=getDataset();
	
	if ($_POST['recnum'] > 0) {
		if ($m->update($row)) {		
			$msgok="Mitarbeiter ".$row['name']." wurde geändert";
		} else {
			$msgerr="Kunde konnte nicht geändert werden<br>";
		}
	} else {
		if ($m->insert($row)) {		
			$msgok="Mitarbeiter ".$row['name']." wurde neu angelegt";
			$_POST['recnum']=$m->row['recnum'];
		} else {
			$msgerr="Kunde konnte nicht angelegt werden<br>";
		}
	}
}
	
	


$found=false;

if (isset($_POST['find_recnum'])) {
    if ($m->loadByRecnum($_POST['recnum'])) {
		setDataset($m->row);
		$found=true;
	}
}

if (isset($_POST['find_nr'])) {
    if ($m->loadByNr($_POST['nr'])) {
		setDataset($m->row);
		$found=true;
	}
}
	
if (isset($_POST['find_name'])) {
	$name=mask($_POST['name']);

    if ($m->loadByName($name)) {
		setDataset($m->row);
		$found=true;
	}
}
usePOST();


showHeader("Mitarbeiter bearbeiten und anlegen");


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
	
	$dt = new DateTime($_POST['entree']);	
	$dt_startup = new DateTime($_POST['startup']);	
	
?>

<style>
span {
	width:6em;
	display:inline-block;
	white-space:nowrap;
}
</style>
	
<form action="mitarbeiter.php" method="POST";>
<input type="hidden" name="recnum" value="<?php echo $_POST['recnum']; ?>">
<table>
<tr><th>Mitarbeiternummer</th><td> <input type="text" name="nr"          size="10" value="<?php echo $_POST['nr']      ?>"><input type="submit" name="find_nr" value="Suchen"></td></tr>
<tr><th>Name</th><td>              <input type="text" name="name"        size="50" value="<?php echo $_POST['name']    ?>"><input type="submit" name="find_name" value="Suchen"></td></tr>
<tr><th>Eintrittsdatum</th><td>    <input type="date" name="entree"                value="<?php echo $_POST['entree']  ?>"></td></tr>
<tr><th>Anfang Zeitaufzeichnung</th><td>    <input type="date" name="startup"      value="<?php echo $_POST['startup'] ?>"></td></tr>
<tr><th>Anfang Überstundenaufzeichnung</th><td>    <input type="date" name="ueberstunden_ab"      value="<?php echo $_POST['ueberstunden_ab'] ?>"></td></tr>
<tr><th>Festgesetzter Jahresurlaub</th><td><input type="number" name="jahresurlaub" value="<?php echo $_POST['jahresurlaub']  ?>"></td></tr>
<tr><th>restlicher Urlaub</th><td><input type="number" name="resturlaub" value="<?php echo $_POST['resturlaub']  ?>"></td></tr>
<tr><th>E-Mail</th><td>            <input type="text" name="mail"        size="60" value="<?php echo $_POST['mail']    ?>"></td></tr>

<tr><th colspan=2>Arbeitszeiten (Stunden)</th></tr>

<tr><th>Montag</th><td>     <input type="number" name="mo"    size="3" value="<?php echo $_POST['mo'] ?>"></td></tr>
<tr><th>Dienstag</th><td>   <input type="number" name="di"    size="3" value="<?php echo $_POST['di'] ?>"></td></tr>
<tr><th>Mittwoch</th><td>   <input type="number" name="mi"    size="3" value="<?php echo $_POST['mi'] ?>"></td></tr>
<tr><th>Donnerstag</th><td> <input type="number" name="do"    size="3" value="<?php echo $_POST['do'] ?>"></td></tr>
<tr><th>Freitag</th><td>    <input type="number" name="fr"    size="3" value="<?php echo $_POST['fr'] ?>"></td></tr>
<tr><th>Samstag</th><td>    <input type="number" name="sa"    size="3" value="<?php echo $_POST['sa'] ?>"></td></tr>
<tr><th>Sonntag</th><td>    <input type="number" name="so"    size="3" value="<?php echo $_POST['so'] ?>"></td></tr>

<tr><td colspan=2>
<!-- if recnum= 0 then "anlegen" if recnum >0 then "ändern" "neu anlegen" -->
<br>
<?php
	if ($_POST['recnum'] == 0) {
		$text="neu anlegen";
	} else {
		$text="ändern";
	}
	echo '<input type = "submit" name="save" value = "'.$text.'">';
?>		
<!-- input type = "submit" name="save" value = "übernehmen" -->

<!-- input type = "submit" name="find" value = "Suchen" --></td></tr>
</table>
<br>
<!-- input type="submit" name="zurueck" value="Menü" formaction="index.php" -->

</form>
</center>
<?php
showBottom();

/*
<tr><th>Eintrittsdatum</th><td>    <input type="date" name="entree"                value="<?php echo $dt->format('Y-m-d')  ?>"></td></tr>
<tr><th>Anfang Zeitaufzeichnung</th><td>    <input type="date" name="startup"                value="<?php echo $dt_startup->format('Y-m-d')  ?>"></td></tr>
*/

?>

