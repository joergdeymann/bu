<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
showHeader("Rechnung anzeigen");

// Rechnung Bezhalen button
if (isset($_POST['button_bezahlt'])) {
	$request="update `bu_re` set `bezahlt`=CURRENT_DATE where `firmanr` = ".$_SESSION['firmanr']." and `renr` = '".$_POST['renr']."' and typ='0'";
	// echo $request;
	$result = $db->query($request);
	
}
if (isset($_POST['button_unbezahlt'])) {
	// echo "Unbezahlt geklickt";exit;
	$request="update `bu_re` set `bezahlt`=NULL where `firmanr` = ".$_SESSION['firmanr']." and `renr` = '".$_POST['renr']."' and typ='0'";
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
	$_POST['cb_faellig']="1";
	$_POST['ueberfaellig']="1";
	$_POST['bezahlt']="1";
}

if (!isset($_POST['inarbeit'])) {
	$_POST['inarbeit']="0";
}
if (!isset($_POST['cb_faellig'])) {
	$_POST['cb_faellig']="0";
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
	check(document.getElementsByName("cb_faellig"));
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
	uncheck(document.getElementsByName("cb_faellig"));
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
<nobr><input type="checkbox" name="cb_faellig"   value="1" <?php if ($_POST['cb_faellig'] == 1)   echo "checked" ?> onClick="checkClear()">fällig<br></nobr>
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
			$order="bu_re.faellig DESC";
		} else 
		if  ($_POST['order'] == "rechnungsdatum") {
			$order="bu_re.datum DESC";
		} else 
		if  ($_POST['order'] == "name") {
			$order="nachname,vorname,faellig DESC";
		} else 
		if  ($_POST['order'] == "firma") {
			$order="firma,faellig DESC";
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
	$where1="where bu_re.firmanr = ".$_SESSION['firmanr']." and bu_re.typ=0";
	if ($_POST['bezahlt'] != "1") {
		$where1.=" and bu_re.bezahlt is null";
	} 
	if ($_POST['inarbeit'] != "1") {
		$where1.= " and bu_re.versandart > 0";
	}
	
	if ($_POST['cb_faellig'] != "1") {
		$where1.=" and (not (now() between bu_re.datum and bu_re.faellig) or bu_re.bezahlt is not null or bu_re.versandart = 0) ";
	} 
	
	if ($_POST['ueberfaellig'] != "1") {
		$where1.=" and (not (now() > bu_re.faellig)  or bu_re.bezahlt is not null or bu_re.versandart = 0)";
	} 


	if (isset($_POST['name']) && $_POST['name']) {
		$w = suche("bu_kunden.vorname",$_POST['name']);
		$w.=" or ".suche("bu_kunden.nachname",$_POST['name']);
		$w.=" or ".suche("bu_kunden.firma",$_POST['name']);
		$w.=" or ".suche("bu_re.renr",$_POST['name']);
		$where1.=" and ($w)";			
	}
	
	$std_layout=0; // Kein Layout ausgewählt
	
	// if ($_POST['what'] == "inarbeit") {
	// }
	// $r1="select concat(vorname,' ',nachname) from bu_kunden where `auftraggeber` = ".$_SESSION['firmanr']." and bu_re.kdnr=bu_kunden.kdnr limit 1";
	// MWST seperat rechenen 0 = standart asu tabelle FEHLT noch
	// $r2="select SUM(netto) from bu_re_posten where `firmanr` = ".$_SESSION['firmanr']." and bu_re.renr=bu_re_posten.renr";


	// $request="select *,($r1) as kdname,($r2) as netto from `bu_re` where `bezahlt` is NULL order by $order";
	// $request="select bu_re.*, bu_kunden.nachname,bu_kunden.vorname, bu_kunden.firma as firmenname ,($r2) as netto from `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr // $where1 order by $order limit $lim";

	$request="
	select bu_kunden.*,bu_re_posten.*,bu_re.*, SUM(bu_re_posten.netto*bu_re_posten.anz) as netto, SUM(bu_re_posten.netto*bu_re_posten.anz*(1+bu_re_posten.mwst/100)) as brutto from bu_re
	left join bu_re_posten on bu_re.renr=bu_re_posten.renr and bu_re.firmanr=bu_re_posten.firmanr and bu_re_posten.typ=bu_re.typ
	left join bu_kunden on bu_re.firmanr=bu_kunden.auftraggeber and bu_re.kdnr=bu_kunden.kdnr
	$where1 
	group by bu_re.renr
	order by $order 
	limit $lim;";
// Group by; vorher: bu_re_posten.renr Zeigt keine Rechnungen mit leeren Posten an
//                   bu_re.renr Zeigt alle Rechnungen an

	// echo $request."<br>";
    // echo $request;
	$result = $db->query($request);
	
/*	
Neu:	Zeit 0.102
$request="select bu_kunden.*,bu_re_posten.*,bu_re.*, SUM(bu_re_posten.netto), SUM(bu_re_posten.netto*(1+bu_re_posten.mwst/100)) from bu_re
left join bu_re_posten on bu_re.renr=bu_re_posten.renr and bu_re.firmanr=bu_re_posten.firmanr
left join bu_kunden on bu_re.firmanr=bu_kunden.auftraggeber and bu_re.kdnr=bu_kunden.kdnr
where bu_re.firmanr = 14 and `versandart` > 0 order by `faellig` limit 50
group by bu_re_posten.renr"

Vergleich: 0.116
select bu_re.*, bu_kunden.nachname,bu_kunden.vorname, bu_kunden.firma as firmenname ,(select SUM(netto) from bu_re_posten where `firmanr` = 14 and bu_re.renr=bu_re_posten.renr) as netto,(select SUM(netto*(1+mwst/100)) from bu_re_posten where `firmanr` = 14 and bu_re.renr=bu_re_posten.renr) as brutto from `bu_re` left join bu_kunden on bu_re.kdnr=bu_kunden.kdnr where `firmanr` = 14 and `versandart` > 0 order by `faellig` limit 50
*/

	$r="";
	
	while($row = $result->fetch_assoc()) {
		// echo "<pre>";print_r($row);exit;
		// $row['issend']=0; // 0 = Rechnung in bearbeitung,1 = Rechnung raus
		// $row['bezahlt']=0; // Bezahlt am (Datum)
		/*
		echo $request;
		echo "<br>";
		var_dump($row);
		echo "<br>";
		*/	
		$ueberfaellig=false;
			
			
			
		$action="";	
		$action_details="";
		if ($row['versandart'] == 0 ) {
			$hinweis="Entwurf";
			$action.='<input type = "submit" value="bearbeiten" name="find" formmethod="POST" formaction="rechnung.php">';
		} else
		if ($row['bezahlt']) {
			$hinweis='<b style="color:green">Bezahlt</b>';
			$action='<input type = "submit" value="Details" name="Details">';
			$action_details=' <input type = "submit" value="unbezahlt" name="button_unbezahlt">';
  		} else 
		if (date("Y-m-d") > $row['faellig'] ) {
			$hinweis='<b style="color:red">Überfällig</b>';
			$action.='<input type = "submit" value="bezahlt" name="button_bezahlt" formmethod="POST" formaction="rechnung_liste.php">';
			$action.='<input type = "submit" value="mahnen"  name="button_mahn"    formmethod="POST" formaction="rechnung_aktion.php">';
			$ueberfaellig=true;

		} else {
			$hinweis='<b style="color:#FFEE00;">Fällig</b>';
			$action.='<input type = "submit" value="bezahlt" name="button_bezahlt" formmethod="POST" formaction="rechnung_liste.php">';
		}
		echo '<form style="display:inline;margin:0;padding:0;" method="POST">';
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
		
		if ($ueberfaellig) {
			if ($row['layout'] == 0) { // Das muss bu_re Layout sein 0 = Standart Layout nehemn von firma
				if ($std_layout == 0) {					
					// $request="select * from bu_re_layout where `firmanr` = '".$_SESSION['firmanr']."' and  prio=1 limit 1" ; // Alt
					
					// $request="select rechnungs_layout from bu_firma where `recnum` = '".$_SESSION['firmanr']."' and `kdnr` = '".$row['kdnr']."'" ;
					$request="select * from bu_mahn 
							  left join bu_re_layout on bu_mahn.mahnstufe=bu_re_layout.mahnstufe and bu_mahn.firmanr=bu_re_layout.firmanr 
							  where bu_mahn.firmanr = '".$_SESSION['firmanr']."' and  bu_mahn.renr='".$row['renr']."' and bu_re_layout.nr='".$row['layout']."' 
							  order by bu_mahn.mahnstufe";


					$request="
							SELECT * from bu_mahn
							LEFT JOIN bu_re_layout
							ON bu_re_layout.firmanr=bu_mahn.firmanr
							AND bu_re_layout.mahnstufe=bu_mahn.mahnstufe
							AND bu_re_layout.nr=0
							AND bu_re_layout.kdnr=
							(
							select max(kdnr) 
							from bu_re_layout
							WHERE bu_re_layout.firmanr=bu_mahn.firmanr
							AND bu_re_layout.mahnstufe=bu_mahn.mahnstufe
							AND bu_re_layout.nr='".$row['layout']."'
							AND (bu_re_layout.kdnr='0' or bu_re_layout.kdnr='".$row['kdnr']."')
								
							)
							WHERE bu_mahn.firmanr='".$_SESSION['firmanr']."'
							AND  bu_mahn.renr='".$row['renr']."'
							AND bu_re_layout.nr='".$row['layout']."';
					";
			
							  
							  
							  
					// echo $request."<br>";
					
					$r = $db->query($request);
					
					//if ($r) {
					// 	$row_layout = $r->fetch_assoc();
					// 	$std_layout=$row['layout']; // Komisch
					// }
				}
				// $row['layout']=$std_layout;         // Komisch
				
			}
			/*
			$request="select * from bu_mahn 
					  left join bu_re_layout on bu_mahn.mahnstufe=bu_re_layout.mahnstufe and bu_mahn.firmanr=bu_re_layout.firmanr 
					  where bu_mahn.firmanr = '".$_SESSION['firmanr']."' and  bu_mahn.renr='".$row['renr']."' and bu_re_layout.nr='".$row['layout']."' 
					  order by bu_mahn.mahnstufe";
			//echo $request."<br>";
			$r = $db->query($request);
			
			*/
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
			// echo '<i><b>Aktion:</b></i>'.$action.'</td>';

			echo '<td id="red">';
			echo '<i><b>Kundennummer:</b></i>'.$row['kdnr'].'<br>';
			echo '<i><b>Firma:</b></i>'.$row['firma'].'<br>';
			echo '<i><b>Name:</b></i>'.$row['vorname'].' '.$row['nachname'].'</td>';

			echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row['netto']).' € </td>';
			echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row['brutto']).' € </td>'; // Erst mal	
			echo '</tr>';

			if ($ueberfaellig) {
				$mahnsumme=0;
				while($row_mahn = $r->fetch_assoc()) {
					if (date("Y-m-d") > $row_mahn['faellig'] ) {
						$hinweis='<b style="color:red">Überfällig</b>';
					} else {
						$hinweis='<b style="color:orange">Fällig</b>';
					} 
						
					echo '<tr>';
					echo '<td id="red" colspan=2><i><b style="width:200px;display:inline-block;">'.$row_mahn['name'].':</b></i>'.date("d.m.Y",strtotime($row_mahn['datum'])).', <b><i>fällig:</i></b>'.date("d.m.Y",strtotime($row_mahn['faellig']));
					echo ', '.$hinweis;
					echo '</td>';
					echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row_mahn['mahngebuehr']).' € </td>'; 
					echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row_mahn['mahngebuehr']*1.19).' € </td>'; 

					echo '</tr>';
					$mahnsumme+=$row_mahn['mahngebuehr'];
				}
				echo '<tr>';
					echo '<td id="red" colspan="2" style="text-align:right;"><b>Summe:</b></td>';
					echo '<td id="red" style="text-align:right;font-weight:900;">'.sprintf("%.2f",$row['netto']+$mahnsumme).' € </td>'; 
					echo '<td id="red" style="text-align:right;font-weight:900;">'.sprintf("%.2f",($row['brutto']+$mahnsumme*1.19)).' € </td>'; 
				echo '</tr>';
				
			}
			echo '<tr><td colspan=4>';
			echo '<i><b>Aktion:</b></i>'.$action.$action_details;
			// if (!empty($action_details)) echo $action_details;
			echo '</td></tr>';
			
			echo '<tr>';
			echo '<td  colspan=4 style="border: 0px red solid;background-color:grey;height:2px;">'.'</td>'; // Erst mal	
			echo '</tr>';
			
			
			
		} else {
			echo '<tr>';
			echo '<td id="red">'.date("d.m.Y",strtotime($row['faellig'])).'</td>';
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
			
			if ($ueberfaellig) {
				// echo "Bin hierXX";exit;
				$mahnsumme=0;
				$hinweis="";
				while($row_mahn = $r->fetch_assoc()) {
					// echo "Bin hier";exit;
					if (date("Y-m-d") > $row_mahn['faellig'] ) {
						$hinweis='<b style="color:red">Überfällig</b>';
					} else {
						$hinweis='<b style="color:orange">Fällig</b>';
					} 
						
					echo '<tr>';
					echo '<td id="red">'.date("d.m.Y",strtotime($row_mahn['faellig'])).'</td>';
					echo '<td id="red">'.date("d.m.Y",strtotime($row_mahn['datum'])).'</td>';
					echo '<td id="red" colspan=3>'.$row_mahn['name'].'</td>';
					echo '<td id="red" style="text-align:right;"> Aufschlag:</td>';
					echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row_mahn['mahngebuehr']).' € </td>'; 
					echo '<td id="red" style="text-align:right;">'.sprintf("%.2f",$row_mahn['mahngebuehr']*1.19).' € </td>'; 
					echo '<td id="red">'."$hinweis".'</td>';
					echo '<td id="red">'." ".'</td>';

					echo '<td id="red">&nbsp;</td>';

					echo '</tr>';
					$mahnsumme+=$row_mahn['mahngebuehr'];
				}
				if ($hinweis!="") {
					echo '<tr>';
						echo '<td id="red" colspan="6" style="text-align:right;"><b>Summe:</b></td>';
						echo '<td id="red" style="text-align:right;font-weight:900;">'.sprintf("%.2f",$row['netto']+$mahnsumme).' € </td>'; 
						echo '<td id="red" style="text-align:right;font-weight:900;">'.sprintf("%.2f",($row['brutto']+$mahnsumme*1.19)).' € </td>'; 
						echo '<td id="red">&nbsp;</td>';
						echo '<td id="red">&nbsp;</td>';
					echo '</tr>';
				}
				
			}
				
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
