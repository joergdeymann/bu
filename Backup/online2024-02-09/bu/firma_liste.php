<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Kunden anzeigen");


if (!isset($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 
$lim=$_POST['zeilen'];

if (!isset($_POST['order'])) {
	$_POST['order']="firma";
} 


if (!isset($_POST['ansehen'])  && !isset($_POST['details'])) {
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
<form action="firma_liste.php" method="POST">

<div id="submenu_neu">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="firma"           <?php if ($_POST['order']=="firma")           echo "checked";?>>Firmenname<br>
</div>

<div>
<h1>Filter</h1>
Name,Firma,Firmanummer <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen" value="Liste"><br>
<input type="submit" name="details" value="Details"><br>
</div>
</div>

</form>
<table>
	
<?php
	if (isset($_POST['details']) && $_POST['details']) {
		echo '<tr><th>Firma</th><th>Kontakte</th><th>Setup</th></tr>';
	} else { 
		echo '<tr><th>Firmanummer</th><th>Firmenname</th><th>Gründer</th><th>Aktion</th></tr>';
	}


	$order="`firma`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "firma") {
			$order="`firma`,`nachname`,`vorname`";
		} else 
		if  ($_POST['order'] == "name") {
			$order="`nachname`,`vorname`";
		}
	}


	$where1=" WHERE `benutzername` = '".$_SESSION['username']."'";
;
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("vorname",$_POST['suche']);
		$w.=" or ".suche("nachname",$_POST['suche']);
		$w.=" or ".suche("firma",$_POST['suche']);
		$w.=" or ".suche("recnum",$_POST['suche']);
		if ($where1 == "") {
			$where1=" where ($w)";
		} else {
			$where1.=" and ($w)";			
		}
	}
	
		 
	$request="select * from bu_firma left join bu_rechte on bu_firma.recnum=bu_rechte.firmanr $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {		
		$action="";
		$action ='<form style="display:inline;marginm:0;padding:0;">';
		$action.='<input type = "hidden" name="firmanr" value="'.$row['recnum'].'">';
		$action.='<input type = "hidden" name="firmaname" value="'.$row['firma'].'">';
		if ($_SESSION['firmanr'] == $row['recnum']) {
			$action.='<input type = "submit" value="bearbeiten" name="find_firmanr" formmethod="POST" formaction="firma.php">';
		} else
		if ($_SESSION['username'] == $row['benutzername']) {
			$action.='<input type = "submit" value="Login" name="login" formmethod="POST" formaction="firma.php">';
		}
		$action.='</form>';
		// $row['a_mail']="amail@web.de";
		// $row['i_mail']="imail@web.de";
		// $row['r_mail']="rmail@web.de";
		// $row['tel']="05932 1414";
		// $row['i_tel']="05932 1414";
		// $row['r_tel']="05932 1414";
		// $row['i_name']="Peter Pan";
		// $row['r_name']="Nils Holgersson";
	
		if (isset($_POST['details']) && $_POST['details']) {
			echo "<tr>";
			
			echo '<td>';
			echo '<img src="'.$row['logo'].'" style="width:100px;min-width:200px;"><br>';
			echo $row['firma'].'<br>';
			echo $row['vorname'].' '.$row['nachname'].'<br>';
			echo $row['strasse'].'<br>';
			echo $row['plz'].' '.$row['ort'].'<br>';
			echo '</td>';
			
			echo '<td>';
			echo '<i><b>Ansprechpartner:</b></i>'.$row['aname'].'<br>';
			echo '<i><b>Mail:</b></i><a href="mailto:'.$row['amail'].'">'.$row['amail'].'</a><br>';
			echo '<i><b>Telefon:</b></i>'.$row['atel'].'<br>';
			echo '<br>';
			echo '<i><b>Inhaber:</b></i>'.$row['iname'].'<br>';
			echo '<i><b>Mail:</b></i><a href="mailto:'.$row['imail'].'">'.$row['imail'].'</a><br>';
			echo '<i><b>Telefon:</b></i>'.$row['itel'].'<br>';
			echo '<br>';
			echo '<i><b>Rechnumng:</b></i>'.$row['rname'].'<br>';
			echo '<i><b>Mail:</b></i><a href="mailto:'.$row['rmail'].'">'.$row['rmail'].'</a><br>';
			echo '<i><b>Telefon:</b></i>'.$row['rtel'].'<br>';
			echo '</td>';
			
			echo '<td>';
			echo '<i><b>Bank:</b></i>'.$row['bankname'].'<br>';
			echo '<i><b>IBAN:</b></i>'.$row['iban'].'<br>';
			echo '<i><b>BIC:</b></i>'.$row['bic'].'<br>';
			echo '<br>';
			echo '<i><b>Betriebsnummer:</b></i>'.$row['betriebsnr'].'<br>';
			echo '<i><b>UStNr:</b></i>'.$row['ustid'].'<br>';
			echo '<i><b>HRA:</b></i>'.$row['hrname'].'<br>';
			echo '<i><b>HRANR:</b></i>'.$row['hra'].'<br>';
			echo '<br>';
			echo '</td>';
			echo '</tr>';
			
			echo '<tr><td colspan=3 style="text-align:right">'.$action.'<br><hr></td></tr>';
			
				
			
			
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
			echo '<tr>';
			echo '<td id="red">'.$row['recnum'].'</td>';
			echo '<td id="red">'.$row['firma'].'</td>';
			echo '<td id="red">'.$row['vorname'].' '.$row['nachname'].'</td>';
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
