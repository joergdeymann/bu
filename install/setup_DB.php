<?php
include "../dbconnect.php";
include "../menu.php";
showHeader("Installation fuer den ersten Gebrauch");
$firmanr="0";
$layout="3";

!!
if (isset($_GET['firmanr'])) {
	$firmanr=$_GET['firmanr'];
}
if (isset($_POST['firmanr'])) {
	$firmanr=$_POST['firmanr'];
}
echo "<hr>Firmanr:$firmanr<br>";
echo "<hr>bu_re_layout<br>";
$request="INSERT INTO `bu_re_layout` (`firmanr`, `nr`, `name`, `mahnstufe`, `ueberschrift`, `logo`, `retext`, `vorlage`, `hr`, `prio`, `mahngebuehr`, `mahntext`, `zahlungsziel_dauer`) VALUES
($firmanr, 0s, 'Angebot', -1, 'Angebot', '', 'Angebotsstext', '', '', 1, 0.00, 'Angebotstext2', 0),
($firmanr, 0, 'Rechnung', 0, 'Rechnung', '', 'Rechnungstext', '', '', 1, 0.00, 'mahntext', 14),
($firmanr, 0, 'Mahnung', 1, 'Mahnung', '', 'Bitte überweisten Sie den noch offenstehenden Betrag', '', '', 1, 5.00, 'Danke für Ihr Vertrauen!', 14),
($firmanr, 0, 'Zweite Mahnung', 2, 'Zweite Mahnung', '', 'Bitte überweisten Sie den noch offenstehenden Betrag', '', '', 1, 5.00, 'Danke für Ihr Vertrauen!', 14),
($firmanr, 0, 'Dritte Mahnung', 3, 'Dritte Mahnung', '', 'Bitte überweisten Sie den noch offenstehenden Betrag', '', '', 1, 5.00, 'Danke für Ihr Vertrauen!', 14);
";
// echo $request;

$result = $db->query($request);
if (!$result) {
	$msg="Fehler aufgetaucht!<br>";
} else {
	$msg="Alles erfolgreich angelegt<br>";
}
echo $msg;

exit;


// Sendmail nicht mehr benutzt
echo "<hr>bu_sendmail<br>";
$request="INSERT INTO `bu_sendmail` (`firmanr`, `vorlagenr`, `mahnstufe`, `subject`, `content`, `signature`) VALUES
($firmanr, 0, 0, 'Rechnung RE\$re[\'renr\']: \$re[\'info\'] ', 'Sehr geehrte Damen und Herren!<br> Hiermit erhalte Sie die Rechnung RE\$re[\'renr\'].<br> <table> <!-- Posten Start --> <tr> <td>\$pos[\'anz\'] \$pos[\'einheit\']</td> <td><b>\$pos[\'re_text\']</b><br>\$pos[\'beschreibung\']</td> </tr> <!-- Posten Ende --> </table><br>Mit freundlichen Grüßen<br><br>Jörg Deymann<br>(Die Deymann\'s)', '<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">\r\n	<tbody>\r\n	  <tr>\r\n		<td valign=\"top\">\r\n				<img 	\r\n						src=\"mail/Logo-Mail.png\"\r\n						title=\"Die Deymann\'s\" alt=\"Firmenlogo Die Deymann\'s\"\r\n						width=\"400\" height=\"225\" border=\"0\">\r\n			&nbsp;&nbsp; <br>\r\n		</td>\r\n		<td valign=\"top\" align=\"center\">Die Deymann\'s<br>\r\n		  Lipperring 36<br>\r\n		  49733 Haren<br>\r\n	   \r\n		  <hr>USt-Id: DE338249165<hr>\r\n		  <p style=\"margin-left:5px;margin-right:5px;text-align:left;\">Telefon:<br>\r\n		  Susi Veit: +49 1515 69 39 31<br>\r\n		  </p>\r\n		</td>\r\n	  </tr>\r\n	</tbody>\r\n</table>\r\n');
";
$result = $db->query($request);
if (!$result) {
	$msg="Fehler aufgetaucht!<br>";
} else {
	$msg="Alles erfolgreich angelegt<br>";
}
echo $msg;

?>