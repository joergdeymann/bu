<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Angebote anzeigen");


if (!isset($_POST['zeilen'])) {
	$_POST['zeilen']=50;
} 
$lim=$_POST['zeilen'];

if (!isset($_POST['order'])) {
	$_POST['order']="rechnungsdatum";
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
}
if (!isset($_POST['ueberfaellig'])) {
	$_POST['ueberfaellig']="0";
}
if (!isset($_POST['bezahlt'])) {
	$_POST['bezahlt']="0";
}

function suche($key,$suchstring) {	
	// echo $key.":".$suchstring."<br>";
	$suche=explode(" ",$suchstring);
	$where="";
	foreach($suche as $s) {
// echo "Foreach<br>";		
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
<form action="angebot_liste.php" method="POST" >

<div id="submenu"><div>
<h1>Sortierung</h1>
<input type="radio" name="order" value="rechnungsdatum"  <?php if ($_POST['order']=="rechnungsdatum")  echo "checked";?>>Angebotssdatum<br>
<input type="radio" name="order" value="name"            <?php if ($_POST['order']=="name")            echo "checked";?>>Name<br>
<input type="radio" name="order" value="firma"           <?php if ($_POST['order']=="firma")           echo "checked";?>>Firmenname<br>
</div>
<div>
<h1>Angebotstypen</h1>
<table cellspacing=0 cellpadding=0>
<tr><td>
<nobr><input type="checkbox" name="inarbeit"     value="1" <?php if ($_POST['inarbeit'] == 1)     echo "checked" ?> onClick="checkClear()">in Arbeit<br></nobr>
</td><td>
<nobr><input type="checkbox" name="alle"         value="1" 										  		            onClick="checkAll()">alle<br></nobr>
<nobr><input type="checkbox" name="keine"        value="1"  									 		            onClick="checkNone()">keine<br></nobr>
</td></tr></table>
</div>

<!-- script type="text/javascript">  
checkAll();
</script-->

<div>
<h1>Filter</h1>
Name,Firma,Angebotsnummer <br><input type="text" name="name" style="width: 90%"  value ="<?php if (isset($_POST['name'])) echo $_POST['name'];?>"><br>
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
	echo '<tr><th>Angebot</th><th>Kunde</th><th>Netto</th><th>Brutto</th></tr>';
} else { 
	echo '<tr><th>Angebotsdatum</th><th>Angebots-Nr</th><th>Kundennummer</th><th>Firmanname</th><th>Kundenname</th><th>Netto</th><th>Brutto</th><th>Hinweis</th><th>Aktion</th></tr>';
}

// SELECT bu_re.*, bu_kunden.* FROM `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr;
/*
	$r="SELECT bu_re.*,bu_kunden.nachname, bu_kunden.vorname, bu_kunden.firma as firmenname FROM `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr";
	$result = $db->query($r);
	$row = $result->fetch_assoc();
var_dump($row);
exit;
*/
	

	$order="bu_re.`datum` DESC,bu_re.`renr` DESC";
	if (isset($_POST['order']) ) {
		if  ($_POST['order'] == "rechnungsdatum") {
			$order="bu_re.datum DESC,bu_re.`renr` DESC";
		} else 
		if  ($_POST['order'] == "name") {
			$order="nachname,vorname,bu_re.datum DESC,bu_re.`renr` DESC";
		} else 
		if  ($_POST['order'] == "firma") {
			$order="firma,bu_re.datum DESC,bu_re.`renr` DESC";
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
	$where1="where bu_re.firmanr = ".$_SESSION['firmanr']." and bu_re.typ=1";

//	if ($_POST['bezahlt'] != "1") {
//		$where1.=" and bu_re.bezahlt is null";
//	} 

//	if ($_POST['inarbeit'] != "1") {
//		$where1.= " and bu_re.versandart > 0";
//	}
	
//	if ($_POST['faellig'] != "1") {
//		$where1.=" and (not (now() between bu_re.datum and bu_re.faellig) or bu_re.bezahlt is not null or bu_re.versandart = 0) ";
//	} 
	
//	if ($_POST['ueberfaellig'] != "1") {
//		$where1.=" and (not (now() > bu_re.faellig)  or bu_re.bezahlt is not null or bu_re.versandart = 0)";
//	} 


	if (isset($_POST['name']) && $_POST['name']) {
		$w = suche("bu_kunden.vorname",$_POST['name']);
		$w.=" or ".suche("bu_kunden.nachname",$_POST['name']);
		$w.=" or ".suche("bu_kunden.firma",$_POST['name']);
		$w.=" or ".suche("bu_re.renr",$_POST['name']);
		$where1.=" and ($w)";			
	}
	
	$std_layout=0; // Kein Layout ausgewählt
	
	$request="
	select bu_kunden.*,bu_re_posten.*,bu_re.*, 
	SUM(bu_re_posten.netto*bu_re_posten.anz) as netto, 
	SUM(bu_re_posten.netto*bu_re_posten.anz*(1+bu_re_posten.mwst/100)) as brutto 
	
	from bu_re
	left join bu_re_posten 
	on bu_re.renr=bu_re_posten.renr 
	and bu_re.firmanr=bu_re_posten.firmanr 
	and bu_re_posten.typ=bu_re.typ
	
	left join bu_kunden 
	on bu_re.firmanr=bu_kunden.auftraggeber 
	and bu_re.kdnr=bu_kunden.kdnr
	
	$where1 
	group by bu_re.renr
	order by $order 
	limit $lim;";
	
	
/*
	$request="select bu_kunden.*,bu_re_posten.*,bu_re.*, SUM(bu_re_posten.netto) as netto, SUM(bu_re_posten.netto*(1+bu_re_posten.mwst/100)) as brutto from bu_re
	left join bu_re_posten on bu_re.renr=bu_re_posten.renr and bu_re.firmanr=bu_re_posten.firmanr and bu_re.typ=1
	left join bu_kunden on bu_re.firmanr=bu_kunden.auftraggeber and bu_re.kdnr=bu_kunden.kdnr
	$where1 
	group by bu_re.renr
	order by $order 
	limit $lim;";
*/

	// echo $request."<br>";
    // echo $request;
	$result = $db->query($request);


	
	while($row = $result->fetch_assoc()) {
		$ueberfaellig=false;
			
			
			
		$action="";	
		if ($row['versandart'] == 0 ) {
			$hinweis="Entwurf";
			$action.='<input type = "submit" value="bearbeiten" name="find" formmethod="POST" formaction="angebot.php">';
		} else {
			$hinweis='<b style="color:#006700">Versendet</b>';
			$action.='<input type = "submit" value="bearbeiten" name="find" formmethod="POST" formaction="angebot.php">';
			// $action.='<input type = "submit" value="bezahlt" name="button_bezahlt" formmethod="POST" formaction="angebot_liste.php">';
		}
		echo '<form style="display:inline;marginm:0;padding:0;" method="POST">';
		echo '<input type = "hidden" name="renr" value="'.$row['renr'].'">';
		
		echo '<input type = "hidden" name="zeilen" value="'.$_POST['zeilen'].'">';
		echo '<input type = "hidden" name="order" value="'.$_POST['order'].'">';
		echo '<input type = "hidden" name="inarbeit" value="'.$_POST['inarbeit'].'">';


		/* 
		   Details
		*/
				
		if (isset($_POST['details']) && $_POST['details']) {
			// echo '<tr style="margin-top:50px !important;border 5px red solid !important;">';
			
			echo '<tr>';
			echo '<td id="red">';
			echo '<i><b>Rechnung-Nr:</b></i>'.$row['renr'].'<br>';
			echo '<i><b>Datum:</b></i>'.date("d.m.Y",strtotime($row['datum'])).'<br>';
			echo '<i><b>Hinweis:</b></i>'."$hinweis".'<br>';
			// echo '<i><b>Aktion:</b></i>'.$action.'</td>';

			echo '<td id="red">';
			echo '<i><b>Kundennummer:</b></i>'.$row['kdnr'].'<br>';
			echo '<i><b>Firma:</b></i>'.$row['firma'].'<br>';
			echo '<i><b>Name:</b></i>'.$row['vorname'].' '.$row['nachname'].'</td>';

			echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row['netto']).' € </td>';
			echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row['brutto']).' € </td>'; // Erst mal	
			echo '</tr>';

			echo '<tr><td colspan=4>';
			echo '<i><b>Aktion:</b></i>'.$action;
			echo '</td></tr>';
			
			echo '<tr>';
			echo '<td  colspan=4 style="border: 0px red solid;background-color:grey;height:2px;">'.'</td>'; // Erst mal	
			echo '</tr>';
			
			
			
		} else {
			echo '<tr>';
			echo '<td id="red">'.date("d.m.Y",strtotime($row['datum'])).'</td>';
			echo '<td id="red">'.$row['renr'].'</td>';
			echo '<td id="red">'.$row['kdnr'].'</td>';
			echo '<td id="red">'.$row['firma'].'</td>';
			echo '<td id="red">'.$row['vorname'].' '.$row['nachname'].'</td>';
			echo '<td id="red" style="text-align:right;"><nobr>'.sprintf("%.2f",$row['netto']).' € </nobr></td>';
			echo '<td id="red" style="text-align:right;"><nobr>'.sprintf("%.2f",$row['brutto']).' € </nobr></td>'; // Erst mal
			echo '<td id="red">'."$hinweis".'</td>';

			echo '<td id="red">'.$action.'</td>';
		
			echo '</tr>';				
		}
		echo '</form>';
		
	}; 


	

?>
</table>
</center>
<?php
showBottom();
?>
