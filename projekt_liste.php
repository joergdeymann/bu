<?php
include "session.php";
include "dbconnect.php";
include "menu.php";
include "class/class_table.php";
include "class/class_output.php";
include "class/class_projekt.php";


$msg="";
$err="";
$html="";
$out=new Output($db);
$projekt=new Projekt($db);

$out->setFormListAction("projekt.php");
showHeader("Projekt erfassen/ändern");
$projekt->loadByWhere("","start DESC");
echo '<center><table id="liste">';
echo '<tr><th>Datum</th><th style="text-align:left">Name</th><th>Aktion</th></tr>';
while ($projekt->next())  {
	echo "<tr>";
	
	echo '<td style="width:1px">';
	echo $out->DateTime($projekt->row['start']);
	echo "&nbsp;-&nbsp;";
	echo $out->DateTime($projekt->row['ende']);
	echo "</td>";
	
	echo "<td>";
	echo $projekt->row['name'];
	echo "</td>";

	echo '<td style="width:1px">';
	echo $out->formStart;
	echo $out->getHidden("recnum",$projekt->row['recnum']);
	echo $out->getHidden("projekt_recnum",$projekt->row['recnum']);
	echo $out->getSubmit("find_recnum","Bearbeiten");
	echo $out->formEnd;
	echo "</td>";
	
	echo "</tr>";
}
echo "</table></center>";




// echo $out->getAutoButton();
showBottom();

?>


