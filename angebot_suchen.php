<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Angebot suchen");
?>

<center>
<form action="angebot.php" method="POST">
<table >
<tr><th>Angebotsnummer</th><td>  <input type="text" name="renr" size="15"></td><td><input type="submit" formaction="angebot.php" formmethod="POST" name="find" value="Bearbeiten" ></td></tr>
</form>

<form action="angebot_suchen.php" method="POST">
<tr><th>Kundennummer</th><td colspan="2">     <input type="text" name="kdnr" size="15"></td></tr>
<tr><th>Kundenname</th><td colspan="2">       <input type="text" name="name" size="90"></td></tr>

<tr><td colspan=3 style="text-align:right"><input type = "submit" name="find" value = "Suchen" style="font-size:1.5em;"></td></tr>
</form>
</table>

<br>
<?php
if (isset($_POST['name']) && $_POST['name']) {
	$name=$_POST['name'];
	$request="select kdnr from bu_kunden where auftraggeber='".$_SESSION['firmanr']."' and (vorname like '%$name%' or nachname like '%$name%') limit 1";
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if (empty($row)) {
		$msg="Kein Kunde gefunden mit der Kundennummer ".$_POST['kdnr'].".";
		echo $msg;
		exit;
	}
	$_POST['kdnr']=$row['kdnr'];
}

if (isset($_POST['kdnr']) && $_POST['kdnr']) {
	// $request="select * from `bu_re` where `kdnr`='".$_POST['kdnr']."' order by datum,renr";

	$request="SELECT concat(vorname,' ',nachname) from bu_kunden where auftraggeber='".$_SESSION['firmanr']."' and bu_kunden.kdnr='".$_POST['kdnr']."' limit 1";
	// echo $request;
	$result = $db->query($request);
	$row = $result->fetch_assoc();
	if (empty($row)) {
		$msg="Kein Kunde gefunden mit der Kundennummer ".$_POST['kdnr'].".";
		echo $msg;
		exit;
	}		
	
	$request="select *,(
		SELECT concat(vorname,' ',nachname) as name from bu_kunden 
			where  auftraggeber='".$_SESSION['firmanr']."' and bu_kunden.kdnr=bu_re.kdnr limit 1) as name 
		from `bu_re` where firmanr='".$_SESSION['firmanr']."' and `kdnr`='".$_POST['kdnr']."' and bu_re.`typ`='1'
		order by datum,renr;";
	$result = $db->query($request);
} else {
	$request="select *,concat(vorname,' ',nachname) as name from bu_re 
		LEFT JOIN bu_kunden
		on bu_kunden.kdnr = bu_re.kdnr
		where  bu_re.firmanr ='".$_SESSION['firmanr']."'
		and    bu_kunden.auftraggeber ='".$_SESSION['firmanr']."'
		and    bu_re.typ=1
		order by datum DESC,renr limit 50;";
	// echo $request;
	$result = $db->query($request);
}	

	
	echo "<table>";
	echo "<tr><th>Angebotsdatum</th><th>Angebots-Nr</th><th>Kundennummer</th><th>Kundenname</th><th>Aktion</th></tr>";
	while($row = $result->fetch_assoc()) {
		$d=date("d.m.Y",strtotime($row['datum']));
		echo '<form action="angebot.php" method="post"> ';
		echo "<tr>";
		echo "<td>$d</td>";
		echo "<td>".$row['renr']."</td>";
		echo "<td>".$row['kdnr']."</td>";
		echo "<td>".$row['name']."</td>";
		echo "<td>";
		echo '<input type="hidden" name="renr" value="'.$row['renr'].'">';
		echo '<input type="submit" value="Bearbeiten" name="find">';
		echo "</td>";
		echo "</tr>";
		echo "</form>";
	}	
	echo "</table>";
	
// }

?>



</center>
<?php
showBottom();
?>
