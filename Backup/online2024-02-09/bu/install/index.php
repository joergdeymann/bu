<?php 

//chdir("..");
include "../dbconnect.php";
include "../menu.php";
showHeader("Installation fuer den ersten Gebrauch");
/*
	Vorlagen css und html
	Firmanr/Layoutnr/rechnung<Mahnstufe>.html
	
	0/0/rechnung0.html
	0/0/rechnung1.html
	0/0/rechnung2.html
	0/0/rechnung3.html

	0/1/rechnung0.html
	0/1/rechnung1.html
	0/1/rechnung2.html
	0/1/rechnung3.html
*/


/*
	1. Tabellen zuerst anlegen
	2. Standart Layout für Rechnungen anlegen
*/

/*
$request="
CREATE TABLE `bu_mahn` (
  `recnum` int(8) NOT NULL,
  `renr` int(8) DEFAULT NULL,
  `mahnstufe` int(2) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `faellig` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `bu_re_layout` (
  `recnum` int(11) NOT NULL,
  `nr` tinyint(3) NOT NULL,
  `name` varchar(200) NOT NULL COMMENT 'name für das Layout',
  `mahnstufe` tinyint(3) NOT NULL DEFAULT 0 COMMENT '0= rechnung\r\n1 = Zahlungserinnerung 1\r\n2 = Zahlungserinnerung 2\r\n3 = Mahnung 1\r\n4 = Mahnung 2\r\n5 = inkasso \r\nBeispiel',
  `ueberschrift` varchar(200) NOT NULL DEFAULT 'Rechnung' COMMENT 'Überschriften\r\nRechnung\r\nMahnung\r\nZahlungserinnerung\r\n',
  `logo` varchar(100) NOT NULL COMMENT 'http link überschreibt das Firmenlogo',
  `retext` varchar(1000) NOT NULL,
  `vorlage` varchar(60) NOT NULL COMMENT 'Vorlage für die Rechnung',
  `hr` varchar(12) NOT NULL,
  `prio` tinyint(1) NOT NULL COMMENT 'Diesen Anzeigen wenn es nicht explizit angegeben wurde',
  `mahngebuehr` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mahntext` varchar(200) DEFAULT NULL COMMENT 'Text in der Tabelle der Mahnung'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/
$request="
INSERT INTO bu_re_layout 
(firmanr,nr,mahnstufe,name                  ,ueberschrift                            ,retext                                                                                  ,mahngebuehr,zahlungsziel_dauer) VALUES
(0      ,0 ,        0,'Rechnung'            ,'R E C H N U N G'                      ,'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:'              ,'0.00'     ,14),
(0      ,0 ,        1,'Zahlungserinnerung'  ,'Z A H L U N G S E R I N N E R U N G'  ,'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:'            ,'0.00'     ,14),
(0      ,0 ,        2,'Mahnung'             ,'M A H N U N G'                        ,'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:' ,'5.00'     ,14),
(0      ,0 ,        3,'2. Mahnung'          ,'2 . M A H N U N G'                    ,'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.','10.00'    ,28);

";

$result = $db->query($request);
if (!$result) {
	$msg="Fehler aufgetaucht!<br>";
} else {
	$msg="Alles erfolgreich angelegt<br>";
}
echo $msg;




					 
showBottom();
?>
