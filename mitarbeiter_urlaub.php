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
	// Text
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
		if (empty($_POST['art'])) {
			$art=2;
		} else {
			$art=0;
		}
// echo $art."<br>";
// echo $_POST['status']."<br>";
// exit;

		$u=$m->getUrlaub($dt_von,$dt_bis); // Eingegebener Urlaub
		if ($u>200) {
			$msg='<b style="color:red">Urlaub mehr als 300 Tage angegeben</b>';
		} else {
			if (empty($_POST['recnum'])) {
				$row=array();
				$row['von']=$dt_von->format("Y-m-d H:i:s");
				$row['bis']=$dt_bis->format("Y-m-d H:i:s");
				$row['mitarbeiternr']=$m->row['nr'];
				$row['firmanr']= $m->row['firmanr'];
				$row['status'] = $_POST['status'];  // 0=Beantragt 1=genehmigt, 2= abgelehnt
				$row['art']    = $art;              // 0=Urlaub 1=Krank 2=unbezahlt
				$row['gelesen']= 1;  // 0=gelesen 1=ungelsesen
				$row['extra']  = $_POST['extra'];   // 0=Standrt, 1=Mutterschutz, 2=Sonderurlaub
				$row['info']   = $_POST['info'];    // Text
				
				$urlaub->insert($row);
				
				$_POST['recnum']=$urlaub->row['recnum'];
				foreach($row as $k => $v) {
					$urlaub->row[$k]=$v;
				}
				
				// Nur Urlaub abziehen wenn Genehmigt und bezahlter Urlaub
				if (($row['art'] == 0) && ($row['status'] == 1) && ($row['extra'] == 0)) {			
					$row=array();
					$row['resturlaub']=$m->row['resturlaub']-$u; // Gibt es das Feld schon ?
					$row['recnum']=$m->row['recnum'];		
					$m->update($row);
				}
				
			
				$msg="Urlaub eingereicht";
				
			} else {
				// $urlaub->loadByRecnum($_POST['recnum']);
				$u_alt=$m->getUrlaub($urlaub->row['von'],$urlaub->row['bis']);
				
				// Alter Urlaub
				// Nur alten Urlaub wieder hinzufügen wenn 
				// - Genehmigt 
				// - bezahlter Urlaub
				// - kein extra
				
				$urlaub_diff=0;
				if (($urlaub->row['art'] == 0) && ($urlaub->row['status'] == 1) && ($urlaub->row['extra']==0))  {
					// echo "Urlaub alt:$u_alt<br>";
					$urlaub_diff=$u_alt;
				}				

				// Nur Neuen Urlaub abziehen wenn 
				// - Genehmigt und 
				// - bezahlter Urlaub
				// - kein Extra
				if (($art == 0) && ($_POST['status'] == 1) && ($_POST['extra'] == 0)) {
					//echo "Urlaub neu:$u<br>";
					$urlaub_diff-=$u;
					//echo "Urlaub neu:$urlaub_diff<br>";
				}				
				
				if ($urlaub_diff != 0) {
				// if ($u_alt != $u) {
					$row=array();
					// $row['resturlaub']=$m->row['resturlaub']+$u_alt-$u; // Gibt es das Feld schon ?
					$row['resturlaub'] =$m->row['resturlaub']+$urlaub_diff; // Resturlaub
					$row['recnum']=$m->row['recnum'];
					// echo "Urlaub alt:".$m->row['resturlaub']."<br>";
					// echo "Urlaub neu:".$row['resturlaub']."<br>";
					
					$m->update($row);
				}
				
				$row=array();
				$row['recnum'] = $urlaub->row['recnum'];
				$row['von']    = $dt_von->format("Y-m-d H:i:s");
				$row['bis']    = $dt_bis->format("Y-m-d H:i:s");
				$row['status'] = $_POST['status'];  // 0=Beantragt 1=genehmigt, 2= abgelehnt
				$row['art']    = $art;              // 0=Urlaub 1=Krank 2=unbezahlt
				$row['gelesen']= 1;  // 0=gelesen 1=ungelsesen
				$row['extra']  = $_POST['extra'];   // 0=Standrt, 1=Mutterschutz, 2=Sonderurlaub
				$row['info']   = $_POST['info'];    // Text
	
				$urlaub->update($row);

				$urlaub->row['status'] = $row['status'];  // 0=Beantragt 1=genehmigt, 2= abgelehnt
				$urlaub->row['art']    = $row['art'];              // 0=Urlaub 1=Krank 2=unbezahlt
				
				

				$msg="Urlaub verändert";			
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

		
	
// showHeader("Urlaub von ".$m->row['name']." bearbeiten");
showHeader("Urlaub bearbeiten");

echo '<form action="mitarbeiter_urlaub.php" method="POST">';
echo '<center>';
if (!empty($msg)) {
	echo "<h1>$msg</h1>";
}
echo '<table id="liste">';
echo '<tr><th colspan=2>Urlaub von '.$m->row['name'].' ('.$m->row['nr'].')';
echo '</th></tr>';


// $dt_von=new DateTime($_POST['datum_von']);
// $dt_bis=new DateTime($_POST['datum_bis']);

$status_checked=array("","","");
$extra_checked= array("","","");
if (isset($_POST['recnum'])) {
	$update_value="Urlaub ändern";
	$input_recnum='<input type="hidden" name="recnum" value="'.$_POST['recnum'].'">';

	$art_checked="";
	if ($urlaub->row['art'] == 0) {
		$art_checked="checked";
	}

	$s=$urlaub->row['status'];
	$status_checked[$s]="checked";

	$s=$urlaub->row['status'];
	$extra_checked[$s]="checked";
	
	
} else {
	$update_value="Urlaub einreichen";
	$input_recnum="";
	$art_checked="checked";
	$status_checked[1]="checked";
	$extra_checked[0]="checked";
}



echo '<tr><th>von </th><td><input type="date" name="datum_von" value="'.$dt_von->format("Y-m-d").'"><input type="time" name="zeit_von" value="'.$dt_von->format("H:i").'"></td></tr>';
echo '<tr><th>bis </th><td><input type="date" name="datum_bis" value="'.$dt_bis->format("Y-m-d").'"><input type="time" name="zeit_bis" value="'.$dt_bis->format("H:i").'"></td></tr>';
echo '<tr><th>Art:</th><td>';
echo '<input type="radio" name="extra"  value="0"'.$extra_checked[0].'> Standart';
echo '<input type="radio" name="extra"  value="1"'.$extra_checked[1].'> Mutterschutz';
echo '<input type="radio" name="extra"  value="2"'.$extra_checked[2].'> Sonderurlaub';
echo '</td></tr>';
echo '<tr><th>Urlaub bezahlt</th><td><input type="checkbox" name="art" '.$art_checked.'></td></tr>';
echo '<tr><th>Status</th><td>';
echo '<input type="radio" name="status" value="0" '.$status_checked[0].'> beantragt';
echo '<input type="radio" name="status" value="1" '.$status_checked[1].'> genehmigt';
echo '<input type="radio" name="status" value="2" '.$status_checked[2].'> abgelehnt';
echo '</td></tr>';
echo '<tr><th>Info</th><td><textarea name="info" style="width:90%;height:5rem;">'.$_POST['info'].'</textarea></td></tr>';
echo '<tr><td colspan=2 style="text-align:center;">';
echo '<input type="submit" name="update" value="'.$update_value.'">';
echo '<input type="hidden" name="mitarbeiter_recnum" value="'.$m->row['recnum'].'">';
echo $input_recnum;
echo '</td></tr>';
echo '</table></center></form>';


showBottom();

?>