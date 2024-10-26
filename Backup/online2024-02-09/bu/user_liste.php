<?php
include "session.php";
include "class/dbconnect.php";
include "menu.php";
include "class/class_user.php";
showHeader("Benutzer anzeigen");


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
<form action="user_liste.php" method="POST">

<div id="submenu_neu">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="mail"            <?php if ($_POST['order']=="mail")            echo "checked";?>>E-Mail<br>
</div>

<div>
<h1>Filter</h1>
Name,Mail <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
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
	echo '<tr><th>Name</th><th>Mail</th><th>Level</th><th>Aktion</th></tr>';

	$order="`bu_user`.`benutzername`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "name") {
			$order="`bu_user`.`benutzername`";
		} else 
		if  ($_POST['order'] == "mail") {
			$order="`mail`";
		}
	}


	$where1="where `firmanr` = '".$_SESSION['firmanr']."'";
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w=" ".suche("bu_user.benutzername",$_POST['suche']);
		$w.=" or ".suche("recnum",$_POST['suche']);
		$w.=" or ".suche("mail",$_POST['suche']);
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}
	
		 
	$request="select * from bu_user left join bu_rechte on bu_user.benutzername=bu_rechte.benutzername $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {		
		$action="";
		$action ='<form style="display:inline;marginm:0;padding:0;">';
		$action.='<input type = "hidden" name="recnum" value="'.$row['recnum'].'">';
		$action.='<input type = "submit" value="bearbeiten" name="find_usernr" formmethod="POST" formaction="user_eingabe.php">';
		$action.='</form>';
	
		echo '<tr>';
		echo '<td id="red">'.$row['benutzername'].'</td>';
		echo '<td id="red">'.$row['mail'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$row['level'].'</td>';
		echo '<td id="red" style="text-align:center;">'.$action.'</td>';
	
		echo '</tr>';
	}; 
	?>
</table>
</center>
<?php
showBottom();
?>
