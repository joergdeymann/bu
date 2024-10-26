<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_urlaub.php";
include "class/class_kalender.php";
include "class/class_datum.php";
showHeader("Mitarbeiter Kalender anzeigen");

$kalender=new Kalender();
$day = array("so","mo","di","mi","do","fr","sa","so");

/*
	Funktionen
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
function addWhere(&$where,$add) {
/*
	if ($where=="") {
		$where=" where ";
	} else {
		$where.=" and";
	}
*/
	
	if ($where!="") {
		$where.=" and";
	}
	$where.=" $add";
}

/* 
	Eingaben überprüfen
*/
if(!isset($_POST['datumvon'])) {
	$dt=new DateTime();
	$_POST['datumvon']=$dt->format("Y-01-01");
}
if(!isset($_POST['datumbis'])) {
	$dt=new DateTime();
	$_POST['datumbis']=$dt->format("Y-12-31");
}

if (!isset($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 

if (!isset($_POST['order'])) {
	$_POST['order']="name";
} 



?>

<center>

<?php 
if (!empty($msg) ) {
	echo '<b style="font-size:2em;">'.$msg.'</b>';
}
?>

<form action="mitarbeiter_liste.php" method="POST">
<div id="submenu_neu" style="height:8em">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="nr"           <?php if ($_POST['order']=="nr")           echo "checked";?>>Nr<br>
</div>

<div>
<h1>Filter</h1>
Name,Mitarbeiternummer <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
<input type="date" name="datumvon" value="<?php echo $_POST['datumvon']; ?>"> - <input type="date" name="datumbis" value="<?php echo $_POST['datumbis']; ?>"><br> 
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen"          value="Liste"><br>
<input type="submit" name="button_zeiten"    value="Zeiten"><br>
<input type="submit" name="button_urlaub"    value="Urlaub">&nbsp;
<input type="submit" name="button_kalender"  value="Kalender" formmethod="post" formaction="mitarbeiter_kalender.php"><br>
<input type="submit" name="button_krank"     value="Krankheit"><br>
<!-- input type="submit" name="details" value="Details"><br-->
</div>
</div>

</form>

<?php
//
// Suche zusammenschustern aus den Eingaben
//
	$order="`firma`,`name`,`von`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="bu_urlaub.firmanr,`name`,`von`";
		} else 
		if  ($_POST['order'] == "nr") {
			$order="bu_urlaub.firmanr,`nr`,`von`";
		}
	}


	$firmanr=$_SESSION['firmanr'];
	$where1 = "bu_urlaub.firmanr=$firmanr";

	if (isset($_POST['datumvon'])) {	
		$von=(new DateTime($_POST['datumvon']))->format("Y-m-d");
	}
	if (isset($_POST['datumbis'])) {	
		$bis=(new DateTime($_POST['datumbis']))->format("Y-m-d");
	}
	
	if (isset($_POST['datumvon']) && isset($_POST['datumbis'])) {		
		$where1.=" and (
			 (bu_urlaub.von between '".$von."' and '".$bis."') 
		  or (bu_urlaub.bis between '".$von."' and '".$bis."') 
		  or (bu_urlaub.von < '".$von."' and bu_urlaub.bis > '".$bis."')
		)";
		
	} else if (isset($_POST['datumvon'])) {
		$where1.=" and (bu_urlaub.von >='".$von."')";	// Überprüfen (sollte jetzt passen)
	} else if (isset($_POST['datumbis'])) {
		$where1.=" and (bu_urlaub.bis <='".$bis."')";
	}
	

		
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("nr",$_POST['suche']);	
		$where1.=" and ($w)";			
	}
	
	$lim=$_POST['zeilen'];


$request="
	select *,bu_mitarbeiter.* from bu_urlaub
	right join bu_mitarbeiter
	on      bu_mitarbeiter.firmanr = bu_urlaub.firmanr
		and bu_mitarbeiter.nr      = bu_urlaub.mitarbeiternr

	where $where1
	order by $order 
";

// echo $request."<br>";
$result = $db->query($request);
	// Abgefangen bisher: 0, 4,5,6,9,10
$name="";
while($row = $result->fetch_assoc()) {
	if ($name != $row['name']) {
		$name=$row['name'];
		$kalender->new($name);
	}
	$dt=new DateTime($row['von']);
	$dt_bis=new DateTime($row['bis']);
	$interval=new DateInterval("P1D");

	$typ=0;
	$u=0;
	// Normaler Urlaub
	if ($row['art'] == 0) {
		$typ=4;
		$u=1;
	}
	// Unbezahlter Urlaub
	if ($row['art'] == 2) {
		$typ=5;
		$u=1;
	}
	if ($u == 1) {
		// Beantragt
		if ($row['status'] == 0) {
			$typ=6;
		}
		//Genehmigt
		if ($row['status'] == 1) {
			// Typ bleibt $typ=4;
		}
		//Abgelehnt
		if ($row['status'] == 2) {
			$typ=0; // Als wenn nichts eingetragen ist
		}	
	}	

	// Krank 
	if ($row['art'] == 1) {
		$typ=9;
		// Ohne Kankenschein
		if ($row['status'] == 0) {
			$typ=10;
		}					
	}
	
	
	// Sonderurlaub 9 oder Mutterschutz 8
	if ($row['extra'] >0) {
		$typ=6+$row['extra'];
	}	
		
	while($dt<=$dt_bis) {
		$kalender->add($dt->format("Y-m-d"),$typ);
		$dt->add($interval);
	}
	
	
}

/*
	Arbeitszeiten
*/
	$order="`firma`,`name`,`von`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="`name`,`time`,bu_zeit.recnum";
		} else 
		if  ($_POST['order'] == "nr") {
			$order="`nr`,`time`,bu_zeit.recnum";
		}
	}

	$where1 = "";
	if (isset($_POST['datumvon'])) {	
		$von=(new DateTime($_POST['datumvon']))->format("Y-m-d");
	}
	if (isset($_POST['datumbis'])) {	
		$bis=(new DateTime($_POST['datumbis']))->format("Y-m-d");
	}	
	if (isset($_POST['datumvon']) && isset($_POST['datumbis'])) {
		addWhere($where1,"(bu_zeit.time between '".$von."' and '".$bis."')");
		
		// $where1.=" and (bu_zeit.time between '".$von."' and '".$bis."')";		
	} else if (isset($_POST['datumvon'])) {
		addWhere($where1,"(bu_zeit.time >='".$von."')");
		// $where1.=" and (bu_zeit.time >='".$von."')";	
	} else if (isset($_POST['datumbis'])) {
		addWhere($where1,"(bu_zeit.time <='".$bis."')");
		// $where1.=" and (bu_zeit.time <='".$bis."')";
	}
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("name",$_POST['suche']);
		$w.=" or ".suche("nr",$_POST['suche']);	
		addWhere($where1," ($w)");
		//$where1.=" and ($w)";			
	}
	
	$lim=$_POST['zeilen'];


	$request="
		SELECT * FROM bu_zeit
		RIGHT JOIN bu_mitarbeiter
			ON  bu_mitarbeiter.recnum  = bu_zeit.usernr
		WHERE $where1
		ORDER BY $order
	";
	


// echo $request."<br>";
$result = $db->query($request);

$date=new Datum();
	// Abgefangen bisher: 0, 4,5,6,9,10
$name="";
$as=new DateTime("1900-01-01"); // Damit die erste Prüfung ok geht
while($row = $result->fetch_assoc()) {
	if ($name != $row['name']) {
		$name=$row['name'];
		$kalender->set($name);
	}
	
	if ($row['type'] == "AS") {
		$dt=new DateTime($row['time']);
		// Im Falle von meheren Logins alles zusammenrechnen
		if ($dt->format("Y-m-d") != $as->format("Y-m-d")) {
			$date=new Datum();
		}			
		$as=new DateTime($dt->format("Y-m-d H:i:00"));
	}
	
	if ($row['type'] == "PS") {
		$dt=new DateTime($row['time']);
		$ps=new DateTime($dt->format("Y-m-d H:i:00"));
	}
	if ($row['type'] == "PE") {
		$dt=new DateTime($row['time']);
		$pe=new DateTime($dt->format("Y-m-d H:i:00"));
		$date->sub($ps,$pe);
	}

	if ($row['type'] == "AE") {
		$dt=new DateTime($row['time']);
		$ae=new DateTime($dt->format("Y-m-d H:i:00"));
		$date->add($as,$ae);
		// echo $as->format("Y-m-d H:i:s"). "-".$ae->format("Y-m-d H:i:s")."<br>";		
		$w=$day[$as->format("w")];
		$hours=(int)$date->getTime("H");
		$minutes=(int)$date->getTime("i");
		if ($minutes>=15) {
			$hours++;
		}
		// echo $as->format("Y-m-d H:i:s"). "-".$ae->format("Y-m-d H:i:s")."Minuten:".$minutes."<br>";		
		
		
		if ($hours > $row[$w] ) {
			$typ=3;
		} else {
			$typ=2;
		}
		if ($hours == 0) {
			$typ=0;
		}
		$kalender->add($as,$typ,$hours);
		// echo $hours."<br>";
	}
	
	
	
	// $dt=(new DateTime($row['time']))->format("Y-m-d");
	// $typ=2;  // Normale Arbeitszeit (3= AZ mit überstunden)
	// $kalender->add($dt,$typ,2);
}

/*
   Ausgabe
*/
echo '<div style="display:inline-block;text-align:center;">';
$header=false;
$start=false;
$dt_bis=new DateTime($_POST['datumbis']);
$dt_von=new DateTime($_POST['datumvon']);

$kalender->setStart($dt_von->format('Y-m-d'));
while ($kalender->start < $dt_bis) {
	foreach($kalender->liste as $key => $value) {
		// echo $key."<br>";
		$kalender->set($key);
		if ($header == false) { 
			$header=true;
			echo $kalender->getHeader();
		}
		echo $kalender->getBody();
	}
	if (sizeof($kalender->liste) > 1) {
		echo $kalender->getFooter()."<br>";
		$header=false;
	}
	$kalender->nextMonth();
} 	
if ($header) {
	echo $kalender->getFooter()."<br>";
	$header=false;
}

echo "</div>";

echo $kalender->legende();
echo "<br>";	
showBottom();
	

		






?>


