<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_urlaub.php";
include "class/class_mitarbeiter.php";

$m=new mitarbeiter($db);
$m->loadByRecnum($_POST['mitarbeiter_recnum']);

$urlaub=new Urlaub($db);

if (empty($_POST['info'])) {
	$_POST['info']="";
}

if (isset($_POST['recnum'])) {
	// echo "RECNUM vorhanden";
	$urlaub->loadByRecnum($_POST['recnum']);
}


if (isset($_POST['find_recnum'])) {             //von Ausserhalb einzelne Zeile
	$dt_von=new DateTime($urlaub->row['von']);
	$dt_bis=new DateTime($urlaub->row['bis']);
	$_POST['info'] = $urlaub->row['info'];
	
	$_POST['datum_von']=$dt_von->format("Y-m-d");
	$_POST['datum_bis']=$dt_bis->format("Y-m-d");
	$_POST['zeit_von']=$dt_von->format("H:i:s");
	$_POST['zeit_bis']=$dt_bis->format("H:i:s");
} else {

	/*
		übergaben von Mitarbeiterliste
		- mitarbeiter_recnum
		
	*/
	if (!empty($_POST['datum_von']) && !empty($_POST['datum_bis']) ) {
		$dt_von=new DateTime($_POST['datum_von']." ".$_POST['zeit_von'].":00");
		$dt_bis=new DateTime($_POST['datum_bis']." ".$_POST['zeit_bis'].":59");	
		
		$u=$m->getUrlaub($dt_von,$dt_bis); // Eingegebener Urlaub nur für Überlaufcheck
		if ($u>200) {
			$msg='<b style="color:red">mehr als 200 Tage arbeitsunfähig</b>';
		} else {
			if (empty($_POST['recnum'])) {
				$row=array();
				$row['von']=$dt_von->format("Y-m-d H:i:s");
				$row['bis']=$dt_bis->format("Y-m-d H:i:s");
				$row['mitarbeiternr']=$m->row['nr'];
				$row['firmanr']= $m->row['firmanr'];
				$row['status'] = $_POST['status'];  // 0=Beantragt 1=genehmigt, 2= abgelehnt
				$row['art']    = 1;                 // hier immer 1: 0=Urlaub 1=Krank 2=unbezahlt
				$row['gelesen']= 1;  				// 0=gelesen 1=ungelsesen
				$row['extra']  = 0;                 // hier immer 0: 0=Standrt, 1=Mutterschutz, 2=Sonderurlaub
				$row['info']   = $_POST['info'];    // Text
				
				$urlaub->insert($row);
				
				$_POST['recnum']=$urlaub->row['recnum'];
				foreach($row as $k => $v) {
					$urlaub->row[$k]=$v;
				}
				
				$msg="Krankmekdung eingetragen";
				
			} else {
				// $urlaub->loadByRecnum($_POST['recnum']);
				
				$row=array();
				$row['recnum'] = $urlaub->row['recnum'];
				$row['von']    = $dt_von->format("Y-m-d H:i:s");
				$row['bis']    = $dt_bis->format("Y-m-d H:i:s");
				$row['status'] = $_POST['status'];  // 0=Beantragt 1=genehmigt, 2= abgelehnt
				$row['art']    = 1;                 // 0=Urlaub 1=Krank 2=unbezahlt
				$row['gelesen']= 1;                 // 0=gelesen 1=ungelsesen
				$row['extra']  = 0;                 // 0=Standrt, 1=Mutterschutz, 2=Sonderurlaub
				$row['info']   = $_POST['info'];    // Text
	
				$urlaub->update($row);
				
				// Das sollte in der Klasse passieren
				$urlaub->row['status'] = $row['status']; // 0=Beantragt 1=genehmigt, 2= abgelehnt
				$urlaub->row['art']    = $row['art'];    // 0=Urlaub 1=Krank 2=unbezahlt
				
				

				$msg="Krankmeldung verändert";			
			}
		}

			
			
			
	} else {
		$dt=new DateTime();
		
		if (empty($_POST['datum_von'])) {
			$dt_von=new DateTime($dt->format("Y-m-d 00:00:00"));
			$_POST['datum_von']=$dt_von->format("Y-m-d");
			$_POST['zeit_von']=$dt_von->format("H:i");
		} 	
		if (empty($_POST['datum_bis'])) {
			$dt_bis=new DateTime($dt->format("Y-m-d 23:59:59"));		
			$_POST['datum_bis']=$dt_bis->format("Y-m-d");
			$_POST['zeit_bis']=$dt_bis->format("H:i");
		}
		
		// Wenn einer der Werte doch gesetzt ist fehlt das
		$dt_von=new DateTime($_POST['datum_von']." ".$_POST['zeit_von'].":00");
		$dt_bis=new DateTime($_POST['datum_bis']." ".$_POST['zeit_bis'].":59");	

	}
}

		
	
showHeader("Krankenzeiten eintragen");

echo '<form action="mitarbeiter_krank.php" method="POST">';
echo '<center>';
if (!empty($msg)) {
	echo "<h1>$msg</h1>";
}
echo '<table id="liste">';
echo '<tr><th colspan=2>Krankmeldung von '.$m->row['name'].' ('.$m->row['nr'].')';
echo '</th></tr>';


// $dt_von=new DateTime($_POST['datum_von']);
// $dt_bis=new DateTime($_POST['datum_bis']);

$status_checked=array("","","");
$extra_checked= array("","","");
if (isset($_POST['recnum'])) {
	$update_value="Krankmeldung ändern";
	$input_recnum='<input type="hidden" name="recnum" value="'.$_POST['recnum'].'">';

	$s=$urlaub->row['status'];
	$status_checked[$s]="checked";

	$s=$urlaub->row['status'];
} else {
	$update_value="Krankmeldung eintragen";
	$input_recnum="";
	$status_checked[1]="checked";
}



echo '<tr><th>von </th><td><input type="date" name="datum_von" value="'.$dt_von->format("Y-m-d").'"><input type="time" name="zeit_von" value="'.$dt_von->format("H:i").'"></td></tr>';
echo '<tr><th>bis </th><td><input type="date" name="datum_bis" value="'.$dt_bis->format("Y-m-d").'"><input type="time" name="zeit_bis" value="'.$dt_bis->format("H:i").'"></td></tr>';
echo '<tr><th>Status</th><td>';
echo '<input type="radio" name="status" value="0" '.$status_checked[0].'> ohne AU';
echo '<input type="radio" name="status" value="1" '.$status_checked[1].'> mit AU';
echo '</td></tr>';
echo '<tr><th>Kommentar</th><td>';
echo '<textarea name="info" style="width: 80%;height:5em;">'.$_POST['info'].'</textarea>';
echo '</td></tr>';

echo '<tr><td colspan=2 style="text-align:center;">';
echo '<input type="submit" name="update" value="'.$update_value.'">';
echo '<input type="hidden" name="mitarbeiter_recnum" value="'.$m->row['recnum'].'">';
echo $input_recnum;
echo '</td></tr>';
echo '</table></center></form>';


showBottom();

?>