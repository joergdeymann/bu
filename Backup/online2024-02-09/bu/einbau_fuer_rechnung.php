<?php
$msg="";

//---------------------------------------------------------------------------------------------------------------
// Datum in verschieden Variationen Woche umwandel in den 1. Tag der Woche
//---------------------------------------------------------------------------------------------------------------

// $values['firmanr']=$_SESSION['firmanr'];


if (isset($_POST['drucken'])) {
	$request="update `bu_re` set versandart=2,versanddatum=CURRENT_DATE() where firmanr='".$_SESSION['firmanr']."' and renr='".$_POST['renr']."'";
	// echo $request."<br>";
	// echo $request;
	// echo $request;
	$result = $db->query($request) or die(mysql_fehler()); 
	if ($result) {
		$msg="Rechnung zu Versenden markiert:".date("d.m.Y")."<br>";
	}
}





/*
	Posten löschen
   
*/
if (isset($_POST['del'])) {
	echo "Delete:".$_GET['pos']."von ". $_POST['renr']."<br>";
	$request="DELETE from `bu_re_posten` where `pos`='".$_GET['pos']."' and firmanr='".$_SESSION['firmanr']."' and renr='".$_POST['renr']."'";
	$result = $db->query($request) or die(mysql_fehler()); 
	if ($result) {
		$msg="Posten ".$_GET['pos']." von Rechnung ".$_POST['renr']." erfolgreich gelöscht<br>";
	}
	
	
}





// Anzahl der gespeicherten Posten herausfinden auch beim Speichern (Add Button)
if (isset($values['renr'])) { 
	//Posten
	$request="select max(`pos`) as posten from `bu_re_posten` where firmanr='".$_SESSION['firmanr']."' and `renr` = '".$values['renr']."'";
	$result = $db->query($request) or die(mysql_fehler());
	$row = $result->fetch_assoc();
	
	if (empty($row['posten'])) {
		$posten['nr']=1;
	} else {
		$posten['nr']=$row['posten']+1;
		
	}
}

if (isset($_POST['add'])) {
	// update -> $request="insert into `bu_re_posten set `renr`='".$values['renr']."', `pos`='".$posten['nr']."', `anz`='".$_POST['anz']."', `einheit`='".$_POST['zuschlag']."', `km`='".$_POST['km']."', `netto`='".$_POST['netto']."'";
	if (empty($_POST['anz'])) {
		$_POST['anz']=0;
	}
	if (empty($_POST['netto'])) {
		$_POST['netto']=0;
	}
	
	$request="insert into `bu_re_posten` (`firmanr`,`renr`,`pos`,`anz`,`einheit`,`zuschlag`,`km`,`netto`) VALUES ('".$_SESSION['firmanr']."','".$values['renr']."','".$posten['nr']."','".$_POST['anz']."','".$_POST['einheit']."','".$_POST['zuschlag']."','".$_POST['km']."','".$_POST['netto']."')";
// echo $request;
	
	$result = $db->query($request) or die(mysql_fehler());
	if ($result) {
		$msg="Neuer Posten angelegt";
		$posten['nr']++;
	} else {
		$msg="<b style=\"background-color:orange\">Posten konnte nicht angelegt werden</b>";
	}
	
	
	// $result = $db->query($request);
}
	
	


showHeader("Rechnung erstellen/ändern",1);

// $rnd=time();

?>	
<!--
<iframe id="PDF" height="300px" width="280px" style="position:absolute;right:0;bottom:0;display:none;overflow:auto;"></iframe>
transform: scale(0.25);
-->
<!-- Dieser geht und ist wichtig -->
<!--
<iframe id="PDF" class="PDF" height="280px" width="200px" style="position:absolute;right:0;bottom:0;display:none;" ></iframe>
-->

<!-- iframe id="PDF" style="position:absolute;right:10px;bottom:0;display:none;width:210px;height:300px;scale: 1.0;transform:scale(0.5);origin:0 0;"></iframe-->

<!-- iframe id="PDF" style="position:absolute;right:10px;bottom:0;display:none;width:210px;height:300px;scale: 1.0;zoom:10%;"></iframe-->

<!-- iframe id="PDF" style="position:absolute;right:10px;bottom:0;display:none;width:420px;height:600px;scale: 0.5;origin:0 0;"></iframe -->
<iframe id="PDF" style="position:absolute;right:-100px;bottom:-300;display:none;width:420px;height:600px;transform:scale(0.5);origin:bottom left;"></iframe>

<!-- Dieser mit eigener id ist unabhängig, die src wird trotzdem nicht ordentlich gezeigt -->
<!--
<iframe id="PD" height="280px" width="200px" style="position:absolute;right:0;bottom:0" src="rechnung_print.php?renr=20220001" ></iframe>
-->

<!-- Dieser Frame ist für das Speichern über PHP -->
<iframe id="PRINTED"  style="display:none;" ></iframe>

<!--
<script>
window.onpageshow = function(evt) {
    // If persisted then it is in the page cache, force a reload of the page.
    if (evt.persisted) {
        document.body.style.display = "none";
        location.reload();
    }
};
</script>
-->

<center>
<?php
echo '<form action="rechnung.php" method="POST">'; 
?>

<!-- input type="hidden" name="recnum" value="?php echo $recnum ?>" -->
<!-- ?php var_dump($values); ?-->
<!-- ?php echo "Recnum = $recnum / ".$values['recnum'];  ?-->
<table>
<tr><th>Rechnungsnummer</th><td>          <input type="hidden" name="renr"            size="15" value="<?php 
if (!empty($values['renr']) ) {
	echo $values['renr'];     
}
?>"><?php

if (empty($values['renr']) ) {
	echo "<b id=\"rand\">Neue Rechnung</b>";
} else {
	echo "<b id=\"rand\">".$values['renr']."</b>";     
}
if (empty($values['datum']) ) {
	$dt = new DateTime();
	$values['datum']=$dt->format('Y-m-d');
}
if (empty($values['leistung']) ) {
	$dt = new DateTime($values['datum']);
	$values['leistung']=$dt->format('Y-m');
}
if (empty($values['woche']) ) {
	$dt = new DateTime($values['datum']);
	$values['woche']=$dt->format('W');
	// echo 	"<h1>".$values['woche']."</h1>";exit;
}



?>


<input type="submit" name="find" value="Suchen" formaction="rechnung_suchen.php"></td></tr>
<tr><th>Rechnungsdatum</th><td>           <input type="date"  name="datum"                     value="<?php echo $values['datum']    ?>"></td></tr>

<?php
switch ($dbfirma->get("re_input_leistung") ) {
	case 1:
	
	
		echo '<tr><th>Leistungsmonat</th><td>';
		echo '<select name="leistungsmonat">';
		$a = array(
		"Januar",
		"Februar",
		"März",
		"Apri",
		"Mai",
		"Juni",
		"Juli",
		"August",
		"September",
		"Oktober",
		"November",
		"Dezember"
		);

		$dt    = new DateTime($values['datum']);
		$month = (int)$dt->format("m");
		
		if ($_POST['leistungsmonat']) {
			$month=(int)$_POST['leistungsmonat'];
		}
		
		// echo "Monat:".$month."<br>";


		$i=0;
		foreach($a as $v) {
			
			$i++;
			// echo $month.":".$i."-".$v."<br>";
			$opt="";
			if ($i == $month) {
				$opt="selected";
			} else {
				$opt="";
			}
			echo '<option '.$opt.' value="'.$i.'">'.$v.'</option>';
			
		}
		echo '</select>';
		echo '<label>';
		echo '<select name="leistungsjahr">';
		$dt = new DateTime($values['datum']);
		$jahr=$dt->format("Y");
		if ($_POST['leistungsjahr']) {
			$jahr=(int)$_POST['leistungsjahr'];
		}
		echo "<option value=".($jahr-2).">".($jahr-2)."</option>";
		echo "<option value=".($jahr-1).">".($jahr-1)."</option>";
		echo "<option value=".($jahr)  ." selected>".($jahr)  ."</option>";
		echo "<option value=".($jahr+1).">".($jahr+1)."</option>";	
		echo "<option value=".($jahr+2).">".($jahr+2)."</option>";	
		echo "</select>";
		echo "</label>";
		echo "</td></tr>";

		break;
	
	case 2:
		/* Anpassen in kunde_suchen.php, wenn fertig */
		
		echo '<tr><th>Leistungszeitraum</th><td><input type="date"  name="leistung" value="'.$values['leistung'].'"></td></tr>';
		break;

	case 3:
		/* Anpassen in kunde_suchen.php, wenn fertig */
		echo '<tr><th>Leistungswoche</th><td>';
		echo '<input size=5 type="number"  name="woche" value="'.$values['woche'].'">';

		echo '<label>';
		echo '<select name="leistungsjahr">';
		$dt = new DateTime($values['datum']);
		$jahr=$dt->format("Y");
		if ($_POST['leistungsjahr']) {
			$jahr=(int)$_POST['leistungsjahr'];
		}
		echo "<option value=".($jahr-2).">".($jahr-2)."</option>";
		echo "<option value=".($jahr-1).">".($jahr-1)."</option>";
		echo "<option value=".($jahr)  ." selected>".($jahr)  ."</option>";
		echo "<option value=".($jahr+1).">".($jahr+1)."</option>";	
		echo "<option value=".($jahr+2).">".($jahr+2)."</option>";	
		echo "</select>";
		echo "</label>";

		echo '</td></tr>';
		break;
}

echo '<tr><th>Kundennummer</th><td><input type="text"  name="kdnr" size="15" value="'.$values['kdnr'].'">';
echo '<input type="submit" name="findkunde" value="Suchen" formmethod="POST" formaction="kunde_suchen.php">';
echo '</td></tr>';

// if (isset($_POST['faellig'])) {
if (!empty($values['faellig'])) {
	echo '<tr><th>Fälligkeitsdatum</th><td><input type="date"  name="faellig" value="'.$values['faellig'].'"></td></tr>';
}

if ($dbfirma->get("re_input_individuell")) {
	echo '<tr><th>Rechnungsformular/Layout</th><td> <input type="text"  name="layout"         size="5"   value="'.$values['layout'].'"></td></tr>';
}
?>
<tr><td colspan=2 style="text-align:right;"><?php 
if (empty($values['renr']) ) {
	echo '<input type = "submit" name="save" value = "Weiter" style="font-size:1.5em;">';
} else {
	echo '<input type = "submit" name="change" value = "Ändern" style="font-size:1.5em;">';
}
?></td></tr>

<!-- if recnum= 0 then "anlegen" if recnum >0 then "ändern" "neu anlegen" -->


</table>
<h3><?php echo $msg ?></h3>


<br>
<?php
if (empty($values['renr']) ) {
} else {
	// <!-- Posten -->
	echo "<table>";
	if (isset($values['renr'])) { 
	
		echo "<tr><th>Pos</th><th>Menge,Einheit</th><th>Thema</th><th>Einzelpreis</th><th>Aktion</th></tr>";
		//Posten
		$request="select * from `bu_re_posten` where firmanr='".$_SESSION['firmanr']."' and `renr` = '".$values['renr']."' order by `pos`";
		$result = $db->query($request);
		while($row = $result->fetch_assoc()) {
		
			// $z1=$row['zuschlag'];
			// $z2=$row['zuschlag2'];
			$z=$row['zuschlag'];
			$z1=$zuschlag_value[$z];
			
			$z=$row['km'];
			$z2=$km_value[$z];
			
			$e=$row['einheit'];
			$einheit=$einheiten_mz[$e];

			if ($row['anz'] == 1) {
				$e=$row['einheit'];
				$einheit=$einheiten_value[$e];
			}
			
			
			echo "<tr>";
			echo "<td style=\"text-align:center\" >".$row['pos']."</td>";
			echo "<td>".$row['anz']." ".$einheit."</td>";
			echo "<td>".$z1." ".$z2."</td>";
			echo "<td style=\"text-align:right\" >".$row['netto']." € </td>";
			echo "<td><input type=\"submit\" value=\"Löschen\" name =\"del\" formaction=\"rechnung.php?pos=".$row['pos']."\" formmethod=\"post\"></td>";
			echo "</tr>";
			
		} 
	}
	echo '</table><br><br>';



	echo '<table>';
	echo '<tr><th>Pos</th><td>'.$posten['nr'].'</td><tr>';
	echo '<tr><th>Anzahl</th><td><input type="number" name="anz" style="width: 5em"></td><tr>';

	echo '<tr><th>Einheit</th><td><select name="einheit">';

	foreach($einheiten_value as $k => $v) {
	  echo '<option value="'.$k.'">'.$v.'</option>';
	}
	echo '  </select></td><tr>';

	echo '<tr><th>Zuschlag</th><td><select name="zuschlag">';
	
	foreach($zuschlag_value as $k => $v) {
	  echo '<option value="'.$k.'">'.$v.'</option>';
	}
	echo '</select>';

	echo '<select name="km">';

	foreach($km_value as $k => $v) {
	  echo '<option value="'.$k.'">'.$v.'</option>';
	}
	
	
	echo '</select>';



	echo '</td><tr>';
	
	echo '<tr><th>Einzelpreis</th><td>
		<input type="number" step="any"  name="netto" style="width: 9em">
	    <input type="radio" name="bruttonetto" checked value="netto">Netto
		<input type="radio" name="bruttonetto" value="brutto">Brutto
		<select id="mwst">
		  <option value="7">7.00%</option>
		  <option value="14" selected>19.00%</option>
		</select>
		<label for "mwst">MwSt</label>
		</td></tr>';

	echo '<tr><td colspan=2 style="text-align:right;">';
	echo '<input type = "submit" name="add" value = "Hinzufügen" style="font-size:1.5em;">';
	echo '</td></tr>';
	
	echo '</table>';

	// echo '<center><center>';
	echo '<center>';
	

}


	


?>

<br><!-- input type="submit" name="zurueck" value="Menü" formaction="index.php" --> 

<?php 
	if ($values['renr']) {
		echo '<button type="submit" name="saveas" formaction="rechnung_pdf.php" formmethod="POST" formtarget="_self"><br>Speichern<br>&nbsp;</button>';
		
		
		/*
		// coole Schatten
		.bonbon:focus {
			box-shadow: rgba(0, 0, 0, 0.7) 0px .25em 1em, inset rgba(0, 0, 0, 0.15) 0px -.5em 1em;
		}
			background: #707070; 
			 linear-gradient(#707070, #FCFCFC); 
		*/
		
		// background-image: -ms-linear-gradient(top left, #707070 -50%, #FCFCFC 110.00000000000001%);
		// echo '<button type="button" name="druckenXX" onClick="printPDF(\''.$file.'\')">Drucken<br>für<br>XXVersand</button>';


		// echo '<button type="button" name="drucken" onClick="printPDF()">Drucken<br>für<br>Versand</button>';
		// $rnd=time();
		// echo $rnd;
		// echo '<button type="button" name="drucken" onClick="this.blur();printPDF(this.form)" formaction="rechnung.php?c='.$rnd.'" formmethod="POST">Drucken<br>für<br>Versand</button>';
		echo '<button type="button" name="drucken" onClick="this.blur();printPDF(this.form)" formaction="rechnung.php" formmethod="POST">Drucken<br>für<br>Versand</button>';
		echo '<button type="submit" name="mailto" formaction="rechnung_versenden.php" formmethod="POST" formtarget="_blank">per<br>Mail<br>versenden</button>';
	}

?>
	

</form>

</center>


<script>
	function setPDF(pdf) {
		// alert("Hallo");
		var ifr=document.getElementById("PDF");
		// var pdf= "rechnung_out.php?renr=20220023";

		//ifr.style.width="200px";
		//ifr.style.height="280px";
		ifr.style.display="initial"; //"none"; // initial;
		// ifr.style.overflow="hidden";
		ifr.style.border="0px";
		ifr.contentWindow.location.replace(pdf);
		// ifr.contentWindow.location.reload();
		
		// alert("Hallo2");
			
		// display:none;position:absolut;left:0;bottom:0;"
	
		
		// ifr.style.scrolling="no";
		// ifr.style.width="600px";
		// ifr.style.height="840px";

		// document.getElementById('PDF').contentWindow.location.replace(pdf);	
		// alert("Hallo setPDF");
		// await new Promise(r => setTimeout(r, 200));
	}
	function printPDF(form) {
		//& alert("WTF");
	 	document.getElementById("PDF").contentWindow.print();
	 	// document.getElementById("PDF").contentWindow.location.replace("index.php");
		// document.getElementById("PDF").remove();
		// window.frames[0].location = "https://www.w3schools.com/jsref/";
		// for(var i=0; i < parent.frames.length; i++)
		//  alert(window.frames[i].name);
		//-->
			 	
		// parent.PDF.close();
		
		//document.getElementById("PDF").contentWindow.dispose();	
	 	// document.getElementById("PDF").contentWindow.close();	
			
		// alert("WTF");
		
		var renr=document.getElementsByName("renr")[0].value;
		var kdnr=document.getElementsByName("kdnr")[0].value;
		var file="rechnung_printed.php?renr="+renr+"&kdnr="+kdnr;		
		var printed=document.getElementById("PRINTED");		
		printed.contentWindow.location.replace(file);
		
		
		
		
		
		// form.submit();	
	}
	
	
	function closePDF() {
	 	document.getElementById("PDF").contentWindow.close();	
		// await new Promise(r => setTimeout(r, 5000));
		// setPDF();
	}	
</script>



<?php
showBottom();
// Mahnstufe ist nur 0 hier 
// $file="rechnung_out.php?renr=".$values['renr']."&mahnstufe=".$values['mahnstufe']."&firmanr=".$values['firma'];
// $rnd=time();
// $file="rechnung_print.php?renr=".$values['renr']."&mahnstufe=0&firmanr=".$values['firmanr']."&c=$rnd";	

if ($values['renr']) {
	$file="rechnung_print.php?renr=".$values['renr']."&mahnstufe=0&firmanr=".$values['firmanr'];	
	// echo $file;
	echo "<script>";
	echo "setPDF('$file');";
	echo "</script>";
}
