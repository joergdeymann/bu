<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Artikel aussuchen");


if (!isset($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 
$lim=$_POST['zeilen'];

if (!isset($_POST['order'])) {
	$_POST['order']="firma";
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
<form action="artikel_auswahl.php" method="POST">

<div id="submenu_neu">
<div>
<h1>Sortierung</h1>
<label><input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name</label><br>
<label><input type="radio" name="order" value="artikelnr"   <?php if ($_POST['order']=="artikelnr")           echo "checked";?>>Artikelnummer</label><br>
</div>

<div>
<h1>Filter</h1>
Name,Artikelnummer <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen" value="Liste"><br>
<!-- input type="submit" name="details" value="Details"><br-->
</div>
</div>

</form>
<table>
	
<?php

	if (isset($_POST['details']) && $_POST['details']) {
		// echo '<tr><th>Kunde</th><th>Kunde</th><th>Netto</th><th>Brutto</th></tr>';
	} else { 
		echo '<tr><th>Art.Nr</th><th>Name</th><th>Bestand</th><th>Aktion</th></tr>';
	}


	$order="`name`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "artikelnr") {
			$order="`artikelnr`";
		} else 
		if  ($_POST['order'] == "name") {
			$order="`nachname`,`vorname`";
		}
	}


	$where1="";
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("artikelnr",$_POST['suche']);
		// $w.=" or ".suche("firma",$_POST['suche']);
		// $w.=" or ".suche("kdnr",$_POST['suche']);
		if ($where1 == "") {
			$where1="where ($w)";
		} else {
			$where1.="and ($w)";			
		}
	}
	
	$firmanr=$_SESSION['firmanr'];
	if ($where1) {
			$where1.=" and auftraggeber=$firmanr";
	} else {
		$where1 = " where auftraggeber=$firmanr";
	}
		 
	$request="select * from bu_artikel $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	
	// Alle möglichen Rechnung Angaben aus rechnung.php
	$f=array(
	"renr",
	"datum",
	"kdnr",
	"leistungsmonat",
	"leistungsjahr",
	"leistungswoche",
	"leistungsdatum",
	"leistungsdatumbis",
	"layout"
	);

	while($row = $result->fetch_assoc()) {		
		$action="";
		$action ='<form style="display:inline;marginm:0;padding:0;">';
		$action.='<input type = "hidden" name="artikelnr" value="'.$row['artikelnr'].'">';
		$action.='<input type = "submit" value="Wahl" name="return_artikelnr" formmethod="POST" formaction="rechnung.php">';

		foreach($f as $k) {
			if (isset($_POST[$k])) {
				$action.='<input type = "hidden" name = "'.$k.'" value = "'.$_POST[$k].'">';
			}
		}
		$action.='</form>';
		
		if (isset($_POST['details']) && $_POST['details']) {
		} else {
			echo '<tr>';
			echo '<td id="red">'.$row['artikelnr'].'</td>';
			echo '<td id="red">'.$row['name'].'</td>';
			echo '<td id="red" style="text-align: right;padding-right:5px;">'.$row['lagerbestand'].'</td>';
			echo '<td id="red">'.$action.'</td>';
		
			echo '</tr>';
		}
	}; 
	?>
</table>
</center>
<?php
showBottom();
?>
