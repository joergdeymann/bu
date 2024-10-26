<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
// print_r($_POST);
if (!empty($_SERVER["HTTP_REFERER"]) and basename($_SERVER["HTTP_REFERER"]) != basename($_SERVER['SCRIPT_NAME']) and (count($_POST)>0)) {
	$_SESSION['POST']=$_POST;
	$_SESSION['HTTP_REFERER']=$_SERVER["HTTP_REFERER"];
}
$referer=basename($_SESSION["HTTP_REFERER"]);


if ($referer == "angebot.php") {
	$title="Kunde suchen für das Angebot";
} else 

if ($referer  == "einstellung_layout.php") {
	$title="Kunde suchen für Layouteinstellungern";
} else {
	$title="Kunde suchen für die Rechnung";
}
showHeader($title);

if (isset($_POST['findkunde'])) {
	// if (empty($_POST['POST'])) $_POST['POST']=$_POST;
	// echo htmlspecialchars(print_r($_POST,true));
}

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
<form action="kunde_suchen.php" method="POST">
<?php
	// kdnr ist unwichtig, da ich sie hier auswähle
	// Zahlungsziel auch da es mit dem Kunden zusammenhängt
	//  die Wahl kann auch sein generelles Zahlungsziel für andere Kunden, was ich noch Proggen muss
	// !!! die Felder mit rechnung.php abstimmen !!!!
/*	
	$p=array("renr","datum","woche","leistungsmonat","leistungsjahr","layout","faellig");
	foreach($p as $k) {
		if (isset($_POST[$k])) {
			echo '<input type="hidden" name="'.$k.'" value="'.$_POST[$k].'">';
			// echo htmlspecialchars('<input type="hidden" name="'.$k.'" value="'.$_POST[$k].'">')."<br>";
		}
    }
*/
?>

<div id="submenu_neu">
<div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="firma"           <?php if ($_POST['order']=="firma")           echo "checked";?>>Firmenname<br>
</div>

<div>
<h1>Filter</h1>
Name,Firma,Rechnungsnummer <br><input type="text" name="suche" style="width: 90%"  value ="<?php if (isset($_POST['suche'])) echo $_POST['suche'];?>"><br>
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
		echo '<tr><th>Kunde</th><th>Kunde</th><th>Netto</th><th>Brutto</th></tr>';
	} else { 
		echo '<tr><th>Kundennnummer</th><th>Firma</th><th>Name</th><th>Aktion</th></tr>';
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


	$where1="";
	if (isset($_POST['suche']) && $_POST['suche']) {
		$w = suche("vorname",$_POST['suche']);
		$w.=" or ".suche("nachname",$_POST['suche']);
		$w.=" or ".suche("firma",$_POST['suche']);
		$w.=" or ".suche("kdnr",$_POST['suche']);
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
		 
	$request="select * from bu_kunden $where1 order by $order limit $lim";
	// echo $request;
	// -------------------------------------------------------------
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {		
		$action="";
		$action ='<form style="display:inline;margin:0;padding:0;">';

		//print_r($_POST);
		// echo "<br>";
		foreach($_SESSION['POST'] as $k => $v) {
			// if (isset($_POST[$k])) {
			if (isset($v)) {
				// $action.= '<input type="hidden" name="'.$k.'" value="'.$_POST[$k].'">';
				$action.= '<input type="hidden" name="'.$k.'" value="'.htmlspecialchars($v).'">';
			}
		}
		$action.= '<input type = "hidden" name="kdnr" value="'.$row['kdnr'].'">';
		
		// echo htmlentities($action)."<br>";
		
		$referer=basename($_SESSION["HTTP_REFERER"]);
		if ($referer == "rechnung.php") {
			$action.='<input type = "submit" value="wählen" name="find_kdnr" formmethod="POST" formaction="'.$referer.'">';
		} else 
		if ($referer == "einstellung_layout.php") {
			$action.='<input type = "hidden" name="kunde" value="'.$row['firma'].'">';
			$action.='<input type = "submit" value="wählen" name="find_kdnr" formmethod="POST" formaction="einstellung_layout.php">';
		} else 
		if ($referer == "angeot.php") {
			$action.='<input type = "submit" value="wählen" name="find_kdnr" formmethod="POST" formaction="angebot.php">';
		} else {
			$action.='<input type = "submit" value="wählen" name="find_kdnr" formmethod="POST" formaction="'.$referer.'">';
		}
		// reset($_SESSION['POST']);
		// reset($_SESSION['HTTP_REFERER']);
		
		$action.="</form>";
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
			echo '<tr>';
			echo '<td id="red">'.$row['kdnr'].'</td>';
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
