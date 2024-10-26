<?php
/*
include "session.php";
include "menu.php";
if (!empty($_SESSION['projekt'])) {
	header("location:projekt.php");
}

showHeader("Projekte");
showBottom();
*/
?>
<?php
include "session.php";
if (!empty($_SESSION['projekt'])) { // Ist das wirklich nötig ? für die leichtere Navigation
	header("location:projekt.php");
}
include "menu.php";
$texte->add(array('header' => 'Projekte'));
showHeader($texte->translate('header'));
showBottom();
?>