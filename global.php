<?php
echo "Hinweis \$GOLBALS['var'] ist nicht mehr beschreibbar<br>";
echo "also nimm: in der Funktion: globale \$var<br>";

	global $var;
	$var="Hallo";
	echo "root:$var<br>";
	fkt();
	echo "root:$var<br>";
	
	function fkt() {
		global $var;
		
		echo "in Funktion: $var<br>";
		$var="Variable in Funktiuon geändert<br>";
	}
?>
	