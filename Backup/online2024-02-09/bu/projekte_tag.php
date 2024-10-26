<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Projekt Tagesliste");


if (!isset($_POST['zeilen']) || empty($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 
$lim=$_POST['zeilen'];

if (!isset($_POST['order'])) {
	// $_POST['order']="firmanr,projectnr,datum";
	$_POST['order']="datum";
} 
if(!isset($_POST['datumvon'])) {
	$_POST['datumvon']="";
}
if(!isset($_POST['datumbis'])) {
	$_POST['datumbis']="";
}


/*
if (!isset($_POST['ansehen'])  && !isset($_POST['details'])) {
	$_POST['inarbeit']="0";
	$_POST['faellig']="1";
	$_POST['ueberfaellig']="1";
	$_POST['bezahlt']="1";
}
*/

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
<form action="projekte_tag.php" method="POST">

<div id="submenu_neu" style="height:8em">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="kunde"            <?php if ($_POST['order']=="kunde")            echo "checked";?>>Kunde - Datum<br>
<input type="radio" name="order" value="datum"           <?php if ($_POST['order']=="datum")           echo "checked";?>>Datum<br>
</div>

<div>
<h1>Filter</h1> <!-- Datum von bis -->
Mitarbeiter, Kunde, Info<br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
<input type="date" name="datumvon" value="<?php echo $_POST['datumvon']; ?>"> - <input type="date" name="datumbis" value="<?php echo $_POST['datumbis']; ?>"><br> 
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php echo $_POST['zeilen']; ?>"><br>
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
	$typ=array(
		"normaler Tag",
		"Offday",
		"kostenloser Offday",
		"überstunden",
		"doppelter Tagessatz"
	);

	if (isset($_POST['details']) && $_POST['details']) {
		echo '<tr><th>Kunde</th><th>Kunde</th><th>Netto</th><th>Brutto</th></tr>';
	} else {

		echo '<tr><th>Kunde</th><th colspan=2>Info</th><th>Aktion</th></tr>';
/*
        Kunden: Nr<br>Name
		---------------------
		Datum:
		KM:
		Arbeitstyp:  Text
		Mitarbeiter: Nr: Name
		---------------------
		Infotext
		---------------------
		Aktion
*/
	}


	$order="`datum`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "datum") {
			$order="`datum`";
		} else 
		if  ($_POST['order'] == "kunde") {
			$order="`firma`,`datum`";
		}
	}


	$where1="";
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("firma",$_POST['suche']);
		$w.=" or ".suche("info",$_POST['suche']);
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}
	
	if (!empty($_POST['datumvon'])) { 
		$w="`datum` >= '".$_POST['datumvon']."'";
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}

	if (!empty($_POST['datumbis'])) { 
		$w="`datum` <= '".$_POST['datumbis']."'";
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}
		
	
	$firmanr=$_SESSION['firmanr'];
	if ($where1) {
		$where1 .= " and bu_project_day.firmanr=$firmanr";
	} else {
		$where1  = " where bu_project_day.firmanr=$firmanr";
	}

	$join='left join bu_mitarbeiter on bu_mitarbeiter.recnum=bu_project_day.mitarbeiternr and bu_mitarbeiter.firmanr=bu_project_day.firmanr';
	$join.=' left join bu_kunden on bu_kunden.kdnr=bu_project_day.kdnr and bu_project_day.kdnr <>"" and  bu_mitarbeiter.firmanr=bu_project_day.firmanr';
	$fields = 'bu_project_day.*, bu_mitarbeiter.name, bu_kunden.firma'; 
	
	$request="select $fields from bu_project_day $join $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------

	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {		
		$action="";
		$action ='<form action="projekt_tag_bearbeiten.php" method="POST" style="display:inline;margin:0;padding:0;">';
		$action.='<input type = "hidden" name="recnum" value="'.$row['recnum'].'">';
		$action.='<input type = "submit" value="bearbeiten" name="find_recnum"></form>';
// echo htmlspecialchars($action);
		
		if (isset($_POST['details']) && $_POST['details']) {
			/*
			// echo '<tr style="margin-top:50px !important;border 5px red solid !important;">';
			echo '<tr>';
			echo '<td id="red">';
			echo '<i><b>Rechnung-Nr:</b></i>'.$row['renr'].'<br>';
			echo '<i><b>Datum:</b></i>'.date("d.m.Y",strtotime($row['datum'])).'<br>';
			echo '<i><b>Fällig:</b></i>'.date("d.m.Y",strtotime($row['faellig'])).'<br>';
			echo '<i><b>Hinweis:</b></i>'."$hinweis".'<br>';
			echo '<i><b>Aktion:</b></i>'.$action.'</td>';

			echo '<td id="red">';
			echo '<i><b>Kundennummer:</b></i>'.$row['kdnr'].'<br>';
			echo '<i><b>Firma:</b></i>'.$row['firmenname'].'<br>';
			echo '<i><b>Name:</b></i>'.$row['vorname'].' '.$row['nachname'].'</td>';

			echo '<td id="red">'.sprintf("%.2f",$row['netto']).'</td>';
			echo '<td id="red">'.sprintf("%.2f",$row['netto']*1.19).'</td>'; // Erst mal	
			echo '</tr>';
			echo '<tr>';
			echo '<td  colspan=4 style="border: 0px red solid;background-color:grey;height:2px;">'.'</td>'; // Erst mal	
			echo '</tr>';
			*/
			
		} else {
			
			$dt=new DateTime($row['datum']);
			$t=$row['arbeitstyp'];
			$at=$typ[$t];
			
			if (empty($row['firma'])) {
				$row['firma']="";
			}
			
			echo '<tr>';
			
			echo '<td id="red" style="text-align:center;"><b style="font-weight:1000;">'.$row['kdnr'].'</b><br><i>'.$row['firma'].'</i></td>';
			
			echo '<td id="red" colspan="2">';
			echo 'Datum: <i>'.$dt->format("d.m.Y").'</i><br>';
			echo 'KM: <i>'.$row['km'].'</i><br>';
			echo 'Arbeitstyp: <i>'.$at.'</i><br>';
			echo 'Mitarbeiter: <i>'.$row['name'].'</i><br>';
			echo '<hr>';
			echo nl2br($row['info']);
			
			echo '</td>';
			
			// echo '<td id="red">'.nl2br($row['info']).'</td>';

			echo '<td id="red">'.$action.'</td>';
		
			echo '</tr></form>';
			echo '<tr style="height:1px;background-color:gray;"><td colspan="4"></td></tr>';
		}
	}; 
	?>
</table>
</center>
<?php
showBottom();
?>
