<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);         // PHP Fehler anzeigen
ini_set('display_startup_errors', 1); // Beim Starten von PHP

echo "nl2br(\$v)  Test<br>";
echo "<hr>";
echo "1. \$v = null<br>";
$v= null;
echo nl2br($v);

echo "<hr>";
echo "2. $v = \"\"<br>";
$v= "";
echo nl2br($v);
echo "<hr>";
echo "3. $v = \"inhalt\"<br>";
$v= "inhalt";
echo nl2br($v);
?>
