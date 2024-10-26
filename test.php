<?php
	$name="https://abc/";
	$name="https://abc";
	$name="Ja";
	// $name="http://abc";
	
	// $name="https://abc";

	$name=trim($name);
	if (preg_match("/^https{0,1}:.*/",$name)) {
		echo "H";
		if (substr($name,-1,1) != "/") {
			$name.="/";
		}
		echo $name;
	} else {
		echo $name;
	}
?>