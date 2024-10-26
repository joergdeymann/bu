<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_urlaub.php";
showHeader("Urlaub der Mitarbeiter");

if(!isset($_POST['datumvon'])) {
	$_POST['datumvon']="";
}
if(!isset($_POST['datumbis'])) {
	$_POST['datumbis']="";
}

if (!isset($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 
$lim=$_POST['zeilen'];

if (!isset($_POST['order'])) {
	$_POST['order']="name";
} 


function suche($key,$suchstring) {	
	// echo $key.":".$suchstring."<br>";
	$suche=explode(" ",$suchstring);
	$where="";
	foreach($suche as $s) {
		if (!empty($where)) {
			$where.=" or ";
		} else 
		$s=trim($s);
		$where.="$key like '%$s%'";
	}
	if (empty($where)) {
		$s=trim($suchstring);
		$where="`$key` like '%$s%'";
	}
	return $where;
}		

?>

<center>
<form action="mitarbeiter_liste.php" method="POST">

<div id="submenu_neu" style="height:8em">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="nr"           <?php if ($_POST['order']=="nr")           echo "checked";?>>Nr<br>
</div>

<div>
<h1>Filter</h1>
Name,Firma,Rechnungsnummer <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
<input type="date" name="datumvon" value="<?php echo $_POST['datumvon']; ?>"> - <input type="date" name="datumbis" value="<?php echo $_POST['datumbis']; ?>"><br> 
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen" value="Liste"><br>
<input type="submit" name="button_zeiten" value="Zeiten"><br>
<input type="submit" name="button_urlaub" value="Urlaub"><br>
<!-- input type="submit" name="details" value="Details"><br-->
</div>
</div>

</form>
<table id="liste">

<?php
	if (isset($_POST['button_urlaub'])) {
		showUrlaub();
	} 	
?>
</table>
</center>
<?php
showBottom();
?>
<?php
/*
	Urlaubszeiten anzeigen
*/
function showUrlaub() {
	global $db;
	// global $urlaub;
	
	
	echo '<tr><th>Name</th><th>Nr</th><th>Urlaub von</th><th>Urlaub bis</th><th>Tage</th><th>Action</th></tr>';
	
	
	$order="`firma`,`name`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="`firmanr`,`name`,`von`";
		} else 
		if  ($_POST['order'] == "nr") {
			$order="`firmanr`,`nr`,`von`";
		}
	}


	$where1="";
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("nr",$_POST['suche']);
		if ($where1 == "") {
			$where1="where ($w)";
		} else {
			$where1.="and ($w)";			
		}
	}
	
		
	
	$firmanr=$_SESSION['firmanr'];
	if ($where1) {
		$where1.= " and firmanr=$firmanr";
	} else {
		$where1 = " where firmanr=$firmanr";
	}
	
	$lim=$_POST['zeilen'];

	$request="
		SELECT bu_mitarbeiter.name, bu_mitarbeiter.recnum as mitarbeiter_recnum, bu_urlaub.* 
		FROM `bu_urlaub` 
		right JOIN bu_mitarbeiter ON (bu_mitarbeiter.nr = bu_urlaub.mitarbeiternr) 
								AND (bu_mitarbeiter.firmanr = bu_urlaub.firmanr)
		$where1 
		ORDER BY $order
	";


	
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	$name="";
	while($row = $result->fetch_assoc()) {		
		$action="";
		// $action ='<form style="display:inline;margin:0;padding:0;"><input type = "hidden" name="recnum" value="'.$row['recnum'].'">';
		// $action.='<input type = "submit" value="bearbeiten" name="find_recnum" formmethod="POST" formaction="mitarbeiter.php"></form>';
									 
		$dt_von=new DateTime($row['von']);
		$dt_bis=new DateTime($row['bis']);
		$diff=$dt_von.diff($dt_bis);
		
		if ($row['name'] != $name) {
			echo '<tr><td colspan="4">&nbsp;</td></tr>';
			echo '<tr><th colspan="4">'.$name.' ('.$nr.')</th></tr>';
			echo '<tr><th>Urlaub von</th><th>Urlaub bis</th><th>Tage</th><th>Action</th></tr>';
			
			$name=$row['name'];
		}
		if (!isset($row['mitarbeiternr'])) {
			continue;
		}
		echo '<tr>';
		echo '<td id="red" style="text-align:center;">'.$dt_von->format("d.m.Y").'</td>';
		echo '<td id="red" style="text-align:center;">'.$dt_bis->format("d.m.Y").'</td>';
		echo '<td id="red" style="text-align:right;">'.$diff.days.'</td>';
		echo '<td id="red" style="text-align:center;">'.$action.'</td>';
	
		echo '</tr>';
	}; 
}	


/*
SELECT bu_mitarbeiter.name, bu_mitarbeiter.recnum as mitarbeiter_recnum, bu_urlaub.* 
FROM `bu_urlaub` 
right JOIN bu_mitarbeiter ON (bu_mitarbeiter.nr = bu_urlaub.mitarbeiternr) 
                        AND (bu_mitarbeiter.firmanr = bu_urlaub.firmanr)
WHERE bu_mitarbeiter.firmanr = 14 
ORDER BY `firmanr`,`name`,`von`
*/
?>