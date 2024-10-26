<?php
$logo="XXX/Hallo.jpz";
$logo_sub="ext";

echo preg_replace("/\..*?$/","_$logo_sub$0",$logo);
?>