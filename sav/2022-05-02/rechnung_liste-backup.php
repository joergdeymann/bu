<?php
include("dbconnect.php");
include "menu.php";
showHeader("Rechnung anzeigen");

// Rechnung Bezhalen button
if (isset($_POST['button_bezahlt'])) {
	$request="update `bu_re` set `bezahlt`=CURRENT_DATE where `renr` = '".$_POST['renr']."'";
	// echo $request;
	$result = $db->query($request);
	
}

if (!isset($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 
$lim=$_POST['zeilen'];

if (!isset($_POST['order'])) {
	$_POST['order']="faelligkeit";
} 


if (!isset($_POST['ansehen'])  && !isset($_POST['details'])) {
	$_POST['inarbeit']="0";
	$_POST['faellig']="1";
	$_POST['ueberfaellig']="1";
	$_POST['bezahlt']="1";
}

if (!isset($_POST['inarbeit'])) {
	$_POST['inarbeit']="0";
}
if (!isset($_POST['faellig'])) {
	$_POST['faellig']="0";
	echo "fällig nicht geasetzt";
}
if (!isset($_POST['ueberfaellig'])) {
	$_POST['ueberfaellig']="0";
}
if (!isset($_POST['bezahlt'])) {
	$_POST['bezahlt']="0";
}

function suche($key,$suchstring) {	
	echo $key.":".$suchstring."<br>";
	$suche=explode(" ",$suchstring);
	$where="";
	foreach($suche as $s) {
echo "Foreach<br>";		
		if (!empty($where)) {
			$where.=" or ";
		} else {
//			$where.="("
		}
		$s=trim($s);
		$where.="$key like '%$s%'";
	}
	if (empty($where)) {
		$s=trim($suchstring);
		$where="`$key` like '%$s%'";
	}
	return $where;
	// $key.=")";	
}		

?>
<script type="text/javascript">  
function checkAll() {
	check(document.getElementsByName("inarbeit"));
	check(document.getElementsByName("faellig"));
	check(document.getElementsByName("ueberfaellig"));
	check(document.getElementsByName("bezahlt"));
	check(document.getElementsByName("alle"));
	uncheck(document.getElementsByName("keine"))
	/*
    var checkboxes = document.getElementsByName("what");  
    var numberOfCheckedItems = 0;  
    // for(var i = 0; i < checkboxes.length; i++)  
    for(var i = 0; i < 4; i++)  
    {  
        checkboxes[i].checked=true;  
    }  
    checkboxes[5].checked=false; 
	*/	
}

function uncheck(cb) {
	cb[0].checked = false;	
}
function check(cb) {
	cb[0].checked = true;	
}

function checkNone() {
	// document.getElementsByName("inarbeit")[0].checked = false;
	// var cb=document.getElementsByName("faellig");
	// cb[0].checked = false;
	uncheck(document.getElementsByName("inarbeit"));
	uncheck(document.getElementsByName("faellig"));
	uncheck(document.getElementsByName("ueberfaellig"));
	uncheck(document.getElementsByName("bezahlt"));
	uncheck(document.getElementsByName("alle"));
	uncheck(document.getElementsByName("keine"))
	
	/*
    var checkboxes = document.getElementsByName("what");  
    var numberOfCheckedItems = 0;  
    // for(var i = 0; i < checkboxes.length; i++)  
    for(var i = 0; i < 5; i++)  
    {  
        checkboxes[i].checked=false;  
    }  
    checkboxes[5].checked=true;  
*/
}

function checkClear() {
	uncheck(document.getElementsByName("alle"));
	uncheck(document.getElementsByName("keine"))
}
</script>

<center>
<form action="rechnung_liste.php" method="POST" >

<div id="submenu"><div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="faelligkeit"     <?php if ($_POST['order']=="faelligkeit")     echo "checked";?>>Fälligkeit<br>

<input type="radio" name="order" value="rechnungsdatum"  <?php if ($_POST['order']=="rechnungsdatum")  echo "checked";?>>Rechnungsdatum<br>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="firma"           <?php if ($_POST['order']=="firma")           echo "checked";?>>Firmenname<br>
</div>
<div>
<h1>Rechnungstypen</h1>
<table cellspacing=0 cellpadding=0>
<tr><td>
<nobr><input type="checkbox" name="inarbeit"     value="1" <?php if ($_POST['inarbeit'] == 1)     echo "checked" ?> onClick="checkClear()">in Arbeit<br></nobr>
<nobr><input type="checkbox" name="faellig"      value="1" <?php if ($_POST['faellig'] == 1)      echo "checked" ?> onClick="checkClear()">fällig<br></nobr>
<nobr><input type="checkbox" name="ueberfaellig" value="1" <?php if ($_POST['ueberfaellig'] == 1) echo "checked" ?> onClick="checkClear()">überfällig<br></nobr>
</td><td>
<nobr><input type="checkbox" name="bezahlt"      value="1" <?php if ($_POST['bezahlt'] == 1)      echo "checked" ?> onClick="checkClear()">bezahlt<br></nobr>
<nobr><input type="checkbox" name="alle"         value="1" 										  		            onClick="checkAll()">alle<br></nobr>
<nobr><input type="checkbox" name="keine"        value="1"  									 		            onClick="checkNone()">keine<br></nobr>
</td></tr></table>
</div>

<!-- script type="text/javascript">  
checkAll();
</script-->

<div>
<h1>Filter</h1>
Name,Firma,Rechnungsnummer <br><input type="text" name="name" style="width: 90%"  value ="<?php if (isset($_POST['name'])) echo $_POST['name'];?>"><br>
Anzahl Zeilen: <input type="number" name="zeilen" style="width:4em" value ="<?php if (isset($_POST['zeilen'])) {echo $_POST['zeilen'];} else {echo '50';}?>"><br>
</div>

<div>
<h1>Aktion</h1>
<input type="submit" name="ansehen" value="Liste"><br>
<input type="submit" name="details" value="Details"><br>
</div>

</div>
<!-- div id="clearfloat"></div><br-->

</form>
<table>
<?php
// 	echo '<tr><td colspan="9"><h3><center>Unbezahlte überfällige Rechnungen nach Fälligkeit</center></h3></td></tr>';
if (isset($_POST['details']) && $_POST['details']) {
	echo '<tr><th>Rechnung</th><th>Kunde</th><th>Netto</th><th>Brutto</th></tr>';
} else { 
	echo '<tr><th>Fälligkeit</th><th>Rechnungsdatum</th><th>Rechnungs-Nr</th><th>Kundennummer</th><th>Firmanname</th><th>Kundenname</th><th>Netto</th><th>Brutto</th><th>Hinweis</th><th>Aktion</th></tr>';
}

// SELECT bu_re.*, bu_kunden.* FROM `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr;
/*
	$r="SELECT bu_re.*,bu_kunden.nachname, bu_kunden.vorname, bu_kunden.firma as firmenname FROM `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr";
	$result = $db->query($r);
	$row = $result->fetch_assoc();
var_dump($row);
exit;
*/
	

	$order="`faellig`";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "faelligkeit") {
			$order="`faellig`";
		} else 
		if  ($_POST['order'] == "rechnungsdatum") {
			$order="`datum`";
		} else 
		if  ($_POST['order'] == "name") {
			$order="`nachname`,`vorname`,`faellig`";
		} else 
		if  ($_POST['order'] == "firma") {
			$order="`firmenname`,`faellig`";
		}
	}
	
	/*
	1. Ausschliessen von Bezahlten
	SELECT * from bu_re where bezahlt is null

	2. Ausschliessen von Faelligen, die nicht bezahlt wurden
	SELECT * from bu_re where (not (now() between datum and faellig) and bezahlt is null);

	3. nur Überfällige Ausschliessen
	SELECT * from bu_re where (not (now() > faellig)  and bezahlt is null);

	4. keine Entwürfe
	SELECT * from bu_re where versandart > 0;

	*/
	
	
	// var_dump ($_POST['what']);
	// exit;
	$where1="";
	if ($_POST['bezahlt'] != "1") {
		if ($where1 == "") {
			$where1 = "where ";
		} else {
			$where1.= " and ";
		}
		$where1.="`bezahlt` is null";
	} 
	if ($_POST['inarbeit'] != "1") {
		if ($where1 == "") {
			$where1 = "where ";
		} else {
			$where1.= " and ";
		}
		$where1.= "`versandart` > 0";
	}
	
	if ($_POST['faellig'] != "1") {
		if ($where1 == "") {
			$where1 = "where ";
		} else {
			$where1.= " and ";
		}
		// $where1.="`faellig` not between `datum` and now()";
		$where1.="(not (now() between datum and faellig) or bezahlt is not null or versandart = 0) ";
	} 
	
	if ($_POST['ueberfaellig'] != "1") {
		if ($where1 == "") {
			$where1 = "where ";
		} else {
			$where1.= " and ";
		}
		//$where1.="`faellig` <= now()";
		$where1.="(not (now() > faellig)  or bezahlt is not null or versandart = 0)";
	} 


	if (isset($_POST['name']) && $_POST['name']) {
		$w = suche("vorname",$_POST['name']);
		$w.=" or ".suche("nachname",$_POST['name']);
		$w.=" or ".suche("bu_kunden.firma",$_POST['name']);
		$w.=" or ".suche("renr",$_POST['name']);
		if ($where1 == "") {
			$where1="where ($w)";
		} else {
			$where1.="and ($w)";
			
		}
	}
	
	
	// if ($_POST['what'] == "inarbeit") {
	// }
	$r1="select concat(vorname,' ',nachname) from bu_kunden where bu_re.kdnr=bu_kunden.kdnr limit 1";
	// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
	$r2="select SUM(netto) from bu_re_posten where bu_re.renr=bu_re_posten.renr";

	// $request="select *,($r1) as kdname,($r2) as netto from `bu_re` where `bezahlt` is NULL order by $order";
	$request="select bu_re.*, bu_kunden.nachname,bu_kunden.vorname, bu_kunden.firma as firmenname ,($r2) as netto from `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr $where1 order by $order limit $lim";
// echo $request;
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {
		// $row['issend']=0; // 0 = Rechnung in bearbeitung,1 = Rechnung raus
		// $row['bezahlt']=0; // Bezahlt am (Datum)
		
		$ueberfaellig=false;
			
			
			
		$action="";
		if ($row['versandart'] == 0 ) {
			$hinweis="Entwurf";
			$action.='<input type = "submit" value="bearbeiten" name="find" formmethod="POST" formaction="rechnung.php">';
		} else
		if ($row['bezahlt']) {
			$hinweis='<b style="color:green">Bezahlt</b>';
			$action='<input type = "submit" value="Details" name="Details">';
  		} else 
		if (date("Y-m-d") > $row['faellig'] ) {
			$hinweis='<b style="color:red">Überfällig</b>';
			$action.='<input type = "submit" value="bezahlt" name="button_bezahlt" formmethod="POST" formaction="rechnung_liste.php">';
			$ueberfaellig=true;

		} else {
			$hinweis='<b style="color:#FF6700">Fällig</b>';
			$action.='<input type = "submit" value="bezahlt" name="button_bezahlt" formmethod="POST" formaction="rechnung_liste.php">';
		}
		echo '<form style="display:inline;marginm:0;padding:0;">';
		echo '<input type = "hidden" name="renr" value="'.$row['renr'].'">';
		
		echo '<input type = "hidden" name="zeilen" value="'.$_POST['zeilen'].'">';
		echo '<input type = "hidden" name="order" value="'.$_POST['order'].'">';
		echo '<input type = "hidden" name="inarbeit" value="'.$_POST['inarbeit'].'">';
		echo '<input type = "hidden" name="faellig" value="'.$_POST['faellig'].'">';
		echo '<input type = "hidden" name="ueberfaellig" value="'.$_POST['ueberfaellig'].'">';
		echo '<input type = "hidden" name="bezahlt" value="'.$_POST['bezahlt'].'">';

		/* 
		   H I E R   B I N   I C H
		*/
		/*
		if ($ueberfaellig) {
			$r="select * from bu_mahn where bu_mahn.re='".$row['renr']."' left join bu_mahnstufe on bu_mahn.mahnstufe=bu_re_layout.mahnstufe where bu_re_layout='".$row['layout']."'";
		// echo $request;
//			$result = $db->query($request);
			
		}
		/* Müll
		$request="select bu_re.*, bu_kunden.nachname,bu_kunden.vorname, bu_kunden.firma as firmenname ,($r2) as netto from `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr $where1 order by $order limit $lim";
	// echo $request;
		$result = $db->query($request);
	*/	
		
		if (isset($_POST['details']) && $_POST['details']) {
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
			
		} else {
			echo '<tr>';
			echo '<td id="red">'.date("d.m.Y",strtotime($row['faellig'])).'</td>';
			echo '<td id="red">'.date("d.m.Y",strtotime($row['datum'])).'</td>';
			echo '<td id="red">'.$row['renr'].'</td>';
			echo '<td id="red">'.$row['kdnr'].'</td>';
			echo '<td id="red">'.$row['firmenname'].'</td>';
			echo '<td id="red">'.$row['vorname'].' '.$row['nachname'].'</td>';
			echo '<td id="red">'.sprintf("%.2f",$row['netto']).'</td>';
			echo '<td id="red">'.sprintf("%.2f",$row['netto']*1.19).'</td>'; // Erst mal
			echo '<td id="red">'."$hinweis".'</td>';

			echo '<td id="red">'.$action.'</td>';
		
			echo '</tr>';
		}
		echo '</form>';
		
	}; 

	/*
	
	echo '<tr><td colspan="8"><h3><center>Unbezahlte Rechnungen nach Fälligkeit</center></h3></td></tr>';
	echo '<tr><th>Fälligkeit</th><th>Rechnungsdatum</th><th>Rechnungs-Nr</th><th>Kundennummer</th><th>Kundenname</th><th>Netto</th><th>Brutto</th><th>Aktion</th></tr>';
	$r1="select concat(vorname,' ',nachname) from bu_kunden where bu_re.kdnr=bu_kunden.kdnr limit 1";
	// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
	$r2="select SUM(netto) from bu_re_posten where bu_re.renr=bu_re_posten.renr";

	$request="select *,($r1) as kdname,($r2) as netto from `bu_re` where `bezahlt` < now() order by `faellig`";
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td id="yellow">'.date("d.m.Y",strtotime($row['faellig'])).'</td>';
		echo '<td id="yellow">'.date("d.m.Y",strtotime($row['datum'])).'</td>';
		echo '<td id="yellow">'.$row['renr'].'</td>';
		echo '<td id="yellow">'.$row['kdnr'].'</td>';
		echo '<td id="yellow">'.$row['kdname'].'</td>';
		echo '<td id="yellow">'.sprintf("%.2f",$row['netto']).'</td>';
		echo '<td id="yellow">'.sprintf("%.2f",$row['netto']*1.19).'</td>'; // Erst mal
		echo '<td id="yellow">'.'AKTION'.'</td>';
		echo '</tr>';
		
	}; 
	
	echo '<tr><td colspan="8"><h3><center>Bezahlte Rechnungen nach Fälligkeit</center></h3></td></tr>';
	echo '<tr><th>Fälligkeit</th><th>Rechnungsdatum</th><th>Rechnungs-Nr</th><th>Kundennummer</th><th>Kundenname</th><th>Netto</th><th>Brutto</th><th>Aktion</th></tr>';

	$r1="select concat(vorname,' ',nachname) from bu_kunden where bu_re.kdnr=bu_kunden.kdnr limit 1";
	// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
	$r2="select SUM(netto) from bu_re_posten where bu_re.renr=bu_re_posten.renr";

	$request="select *,($r1) as kdname,($r2) as netto from `bu_re` where `bezahlt` >= now() order by `faellig`";
	$result = $db->query($request);
	
	while($row = $result->fetch_assoc()) {
		echo '<tr>';
		echo '<td id="green">'.date("d.m.Y",strtotime($row['faellig'])).'</td>';
		echo '<td id="green">'.date("d.m.Y",strtotime($row['datum'])).'</td>';
		echo '<td id="green">'.$row['renr'].'</td>';
		echo '<td id="green">'.$row['kdnr'].'</td>';
		echo '<td id="green">'.$row['kdname'].'</td>';
		echo '<td id="green">'.sprintf("%.2f",$row['netto']).'</td>';
		echo '<td id="green">'.sprintf("%.2f",$row['netto']*1.19).'</td>'; // Erst mal
		echo '<td id="green">'.'AKTION'.'</td>';
		echo '</tr>';
		
	}; 
	*/

	

 	// select * from `bu_re` where `kdnr`='$kdnr' and `bezahlt` is NULL order by datum  //bestimmter Kunde
?>
</table>
</center>
<?php
showBottom();
?>
