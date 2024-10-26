<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_adresse.php";
include "class/class_output.php";

$directstart=pushPOST('adresse');

$out=new Output();

showHeader("Adresse anzeigen");


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
echo "<center>";

// print_r($_POST);

// if (count($_POST) > 0 and !empty($_SERVER['HTTP_REFERER'])) {
// } else {
?>

<center>

<form action="adresse_liste.php" method="POST">

<div id="submenu_neu">
<div>
<h1>Sortierung</h1>
<label><input type="radio" name="order" value="location"            <?php if ($_POST['order']=="location")            echo "checked";?>>Firmen / Location Name</label><br>
<label><input type="radio" name="order" value="name"   <?php if ($_POST['order']=="name")           echo "";?>>Name Vorname</label><br>
</div>

<div>
<h1>Filter</h1>
Name,Location,Firma <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen" value="Liste"><br>
<!-- input type="submit" name="details" value="Details"><br-->
</div>
</div>

</form>

<?php
// }
	echo '<table>';
	if (isset($_POST['details']) && $_POST['details']) {
		// echo '<tr><th>Kunde</th><th>Kunde</th><th>Netto</th><th>Brutto</th></tr>';
	} else { 
		echo '<tr><th>Location</th><th>Name</th><th>Aktion</th></tr>';
	}


	$order="`name`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "location") {
			$order="`name`";
		} else 
		if  ($_POST['order'] == "name") {
			$order="`nachname`,`vorname`";
		}
	}


	$where1="";
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("concat(nachname,' ',vorname)",$_POST['suche']);
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
			$where1.=" and firmanr=$firmanr";
	} else {
		$where1 = " where firmanr=$firmanr";
	}
		 
	$request="select * from bu_adresse $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------
	$adresse=new Adresse($db);
	$adresse->query($request);
	
	// $result = $db->query($request);
	// print_r($_POST);
	
	while($row = $adresse->next()) {		
		$action="";
		$action ='<form style="display:inline;margin:0;padding:0;" method="POST">';
		if (!$directstart and !empty($_SERVER['HTTP_REFERER'])) {
			foreach ($_POST as $k => $v) {
				$action.='<input type = "hidden" name="'.$k.'"    value="'.$v.'">';
			}
			
			$action.='<input type = "submit" value="auswählen" name="find_adresse" formmethod="POST" formaction="'.$_SERVER['HTTP_REFERER'].'">';
			$action.='<input type = "hidden" name="location"    value="'.$row['recnum'].'">';
		} else {
			$action.='<input type = "hidden" name="adresse_recnum"    value="'.$row['recnum'].'">';
			$action.='<input type = "submit" value="bearbeiten" name="btn_adresse" formmethod="POST" formaction="adresse.php">';
		}
		$action.='</form>';
		
		if (isset($_POST['details']) && $_POST['details']) {
		} else {
			echo '<tr>';
			echo '<td id="red">'.$row['name'].'</td>';
			echo '<td id="red">'.$row['vorname'].' '.$row['nachname'].'</td>';
			// echo '<td id="red" style="text-align: right;padding-right:5px;">'.$row['lagerbestand'].'</td>';
			echo '<td id="red">'.$action.'</td>';
		
			echo '</tr>';
		}
	}; 
	?>
</table>
</center>
<?php
if (!$directstart and !empty($_SERVER['HTTP_REFERER'])) {
	// echo "<center>";
	// echo $out->setFormAction($_SERVER['HTTP_REFERER']);
	// echo $out->formStart;
	// //echo $out->savePOST("projekt");
	// echo $out->getSubmit("projekt","Neue Adresse eingeben","adresse.php");
	// echo $out->formEnd;
	// echo "</center>";
	// Nur den alten POST merken in einer SESSION
	// if (basename($_SERVER['HTTP_REFERER']) != basename($_SERVER['SCRIPT_NAME']))  {
	if (basename($_SERVER['HTTP_REFERER']) == "projekt.php")  {
		$_SESSION['projekt']=$_POST;
	}
}
showBottom();
?>
