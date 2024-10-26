<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Projekt - Mitarbeiter auswählen");


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
<form action="mitarbeiter_auswahl.php" method="POST">
<?php
foreach($_POST as $k => $v) {
	echo '<input type="hidden" name="'.$k.'" value="'.$v.'">';
}
?>

<div id="submenu_neu">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="nr"           <?php if ($_POST['order']=="nr")           echo "checked";?>>Nr<br>
</div>

<div>
<h1>Filter</h1>
Name,Firma,Rechnungsnummer <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen" value="Liste"><br>
</div>
</div>

</form>
<table id="liste">

<?php
	showMitarbeiter();	
		
?>
</table>
</center>
<?php
showBottom();
?>
<?php

function showMitarbeiter() {
	global $db;
	
	if (isset($_POST['details']) && $_POST['details']) {
		echo '<tr><th>Name</th><th>Nr</th><th>Eintritt</th><th>Urlaub</th><th>Action</th></tr>';
	} else { 
		echo '<tr><th>Name</th><th>Nr</th><th>Eintritt</th><th>Urlaub</th><th>Action</th></tr>';
	}


	$order="`firma`,`name`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="`firmanr`,`name`";
		} else 
		if  ($_POST['order'] == "nr") {
			$order="`firmanr`,`nr`";
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
	$request="select * from bu_mitarbeiter $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {		
		$action="";
		$action.='<form style="display:inline;margin:0;padding:0;">';
		foreach($_POST as $k => $v) {
			$action.= '<input type="hidden" name="'.$k.'" value="'.$v.'">';
		}
		$action.='<input type = "hidden" name="mitarbeiternr" value="'.$row['nr'].'">';
		$action.='<input type = "submit" value="wählen" name="find_mitarbeiter" formmethod="POST" formaction="projekt_tag_bearbeiten.php"></form>';

	
		$dt=new DateTime($row['entree']);
		
		echo '<tr>';
		echo '<td id="red">'.$row['name'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$row['nr'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$dt->format("d.m.Y").'</td>';
		echo '<td id="red" style="text-align:center;">'.$row['jahresurlaub'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$action.'</td>';
	
		echo '</tr>';
	}; 
}	
?>