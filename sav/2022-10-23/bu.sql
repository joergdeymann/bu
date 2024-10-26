-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 23. Okt 2022 um 15:48
-- Server-Version: 10.4.17-MariaDB
-- PHP-Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `bu`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_firma`
--

CREATE TABLE `bu_firma` (
  `recnum` int(8) NOT NULL,
  `firma` varchar(30) NOT NULL,
  `strasse` varchar(30) NOT NULL,
  `plz` varchar(8) NOT NULL,
  `ort` varchar(30) NOT NULL,
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  `inhaber` varchar(30) NOT NULL COMMENT 'Veraltet,nicht nutzen',
  `aname` varchar(200) NOT NULL COMMENT 'Ansprechpartner Name',
  `amail` varchar(250) NOT NULL COMMENT 'Ansprechpartner Mail',
  `atel` varchar(20) NOT NULL COMMENT 'Ansprechpartner Tel',
  `iname` varchar(200) NOT NULL COMMENT 'Inhaber Name',
  `imail` varchar(250) NOT NULL COMMENT 'Inhaber Mail',
  `itel` varchar(20) NOT NULL COMMENT 'Inhaber Telefon',
  `rname` varchar(200) NOT NULL COMMENT 'Rechnung Name',
  `rmail` varchar(250) NOT NULL COMMENT 'Rechnung Mail',
  `rtel` varchar(20) NOT NULL COMMENT 'Rechnung Telefon',
  `bankname` varchar(30) NOT NULL,
  `iban` varchar(25) NOT NULL,
  `bic` varchar(12) NOT NULL,
  `hrname` varchar(30) NOT NULL,
  `hra` varchar(10) NOT NULL,
  `ustid` varchar(12) NOT NULL,
  `betriebsnr` varchar(20) NOT NULL,
  `standart` tinyint(1) NOT NULL COMMENT 'Standart 1=Ja 0 Nein',
  `logo` varchar(200) NOT NULL,
  `rechnungs_layout` int(4) NOT NULL DEFAULT 0 COMMENT 'Standartformular für die Firma, 0 =  Erstes formular in der Datenbank',
  `mwstsatz` tinyint(2) NOT NULL DEFAULT 19
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_firma`
--

INSERT INTO `bu_firma` (`recnum`, `firma`, `strasse`, `plz`, `ort`, `vorname`, `nachname`, `inhaber`, `aname`, `amail`, `atel`, `iname`, `imail`, `itel`, `rname`, `rmail`, `rtel`, `bankname`, `iban`, `bic`, `hrname`, `hra`, `ustid`, `betriebsnr`, `standart`, `logo`, `rechnungs_layout`, `mwstsatz`) VALUES
(14, 'Die Deymann\'s', 'Lipperring', '49733', 'Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deymann', 'Susi Veit', 'susi.veit@die-deymanns.de', '0151111111', 'J.Deymann', 'joerg.deyamnn@die-deymanns.de', '059327399547', 'Jörg Deymann', 'rechnung@die-deymanns.de', '+4915117871172', 'Sparkasse Emsland', 'DE37 2665 0001 1091 0883 ', 'NOLADE21EMS', 'H oola', 'HRA 10585', 'DE4711', '87654321', 1, 'http://www.die-deymanns.de/logo/logo.png', 0, 19),
(17, '1', '2', '3', '4', '5', '6', '', '10', '11', '12', '7', '8', '9', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', 0, 'https://www.all-transport24.de/wp-content/uploads/2019/10/logo.png', 0, 19),
(18, 'Die Deymann\'s', 'Lipperring', '49733', 'Haren (Ems)', 'Jörg', 'Deymann', '', '', '', '', 'Jörg Deymann', 'joerg.deyamnn@die-deymanns.de', '+4915117871172', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19),
(19, 'Die Deymann\'s', 'Lipperring', '49733', 'Haren (Ems)', 'Jörg', 'Deymann', '', '', '', '', 'Jörg Deymann', 'joerg.deyamnn@die-deymanns.de', '+4915117871172', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19),
(20, 'Jörgi OH', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19),
(21, 'Jörgi OH', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19),
(22, 'Die Deymann\'s Jörg', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19),
(23, 'Test', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19),
(24, 'Niemandsfirma', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19),
(25, 'ABCD', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '', 0, 19);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_kunden`
--

CREATE TABLE `bu_kunden` (
  `recnum` int(8) NOT NULL,
  `auftraggeber` int(8) NOT NULL DEFAULT 0 COMMENT 'entspricht bu_firma.recnum\r\n0 = für alle',
  `kdnr` varchar(8) NOT NULL,
  `firma` varchar(30) NOT NULL COMMENT 'Firma des Kunden',
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  `strasse` varchar(30) NOT NULL,
  `plz` varchar(7) NOT NULL,
  `ort` varchar(30) NOT NULL,
  `tel_privat` varchar(30) NOT NULL,
  `tel_mobil` varchar(30) NOT NULL,
  `tel_dienst` varchar(30) NOT NULL,
  `mail_privat` varchar(60) NOT NULL,
  `mail_dienst` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_kunden`
--

INSERT INTO `bu_kunden` (`recnum`, `auftraggeber`, `kdnr`, `firma`, `vorname`, `nachname`, `strasse`, `plz`, `ort`, `tel_privat`, `tel_mobil`, `tel_dienst`, `mail_privat`, `mail_dienst`) VALUES
(17, 14, '10001', 'All Transport 24', 'Rasched', 'Tamitz', 'Bonifaciusstraße 160', '49733', 'Essen', 'Tel Priv', 'Tel Mobil', 'Tel Dienst', 'Email@email.de', 'Email-D@email.de'),
(21, 17, 'K100', 'K_Firm', 'Vor', 'Nach', 'St', 'PLZ', 'Ort', 'Telp', 'telm', 'teld', 'em', 'ed');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_mahn`
--

CREATE TABLE `bu_mahn` (
  `recnum` int(8) NOT NULL,
  `firmanr` int(8) NOT NULL DEFAULT 0,
  `renr` int(8) DEFAULT NULL,
  `mahnstufe` int(2) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `faellig` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_mahn`
--

INSERT INTO `bu_mahn` (`recnum`, `firmanr`, `renr`, `mahnstufe`, `datum`, `faellig`) VALUES
(7, 14, 20220001, 0, '2022-05-01', '2022-05-15'),
(8, 14, 20220001, 0, '2022-05-01', '2022-05-15'),
(9, 14, 20220002, 0, '2022-03-01', '2022-03-26'),
(10, 14, 20220002, 1, '2022-05-12', '2022-05-26'),
(11, 14, 20220003, 0, '2022-03-02', '2022-03-28'),
(13, 14, 20220003, 1, '2022-05-13', '2022-04-02'),
(14, 14, 20220003, 2, '2022-05-13', '2022-04-13'),
(16, 14, 20220003, 3, '2022-05-18', '2022-06-01'),
(17, 17, 20220001, 0, '2022-02-01', '0202-02-15'),
(18, 17, 20220001, 1, '2022-06-20', '2022-07-04');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_re`
--

CREATE TABLE `bu_re` (
  `recnum` int(8) NOT NULL,
  `firmanr` smallint(8) NOT NULL DEFAULT 0 COMMENT 'Firmennummer\r\n0 = Standart Firma\r\nsonst Firmennr ',
  `datum` date NOT NULL,
  `faellig` date NOT NULL,
  `leistung` date NOT NULL COMMENT 'Leistungsmonat',
  `renr` varchar(8) NOT NULL,
  `kdnr` varchar(8) NOT NULL,
  `layout` int(8) NOT NULL DEFAULT 0 COMMENT 'Layout Vorlage\r\n0 = Standart\r\nZahl = ausgewähltes Layout',
  `mahnstufe` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0 Rechnung\r\n1 = Zahlungserinnerung\r\n3 usw',
  `bezahlt` date DEFAULT NULL COMMENT 'Rechnung bezahlt am',
  `versandart` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Nicht verdendet\r\n1 = per Mail\r\n2 = per post',
  `versanddatum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_re`
--

INSERT INTO `bu_re` (`recnum`, `firmanr`, `datum`, `faellig`, `leistung`, `renr`, `kdnr`, `layout`, `mahnstufe`, `bezahlt`, `versandart`, `versanddatum`) VALUES
(50, 0, '2022-05-01', '2022-05-14', '2022-04-14', '20220001', '10001', 5, 0, NULL, 0, NULL),
(51, 14, '2022-05-01', '2022-05-15', '2022-04-11', '20220001', '10001', 0, 0, '2022-05-13', 2, 20220511),
(52, 14, '2022-03-01', '2022-03-26', '2022-02-01', '20220002', '10001', 0, 1, '2022-05-12', 2, 20220512),
(53, 14, '2022-03-02', '2022-03-28', '2022-02-02', '20220003', '10001', 0, 3, NULL, 2, 20220518),
(54, 17, '2022-02-01', '0202-02-15', '2022-01-01', '20220001', 'K100', 0, 1, NULL, 2, 20220620);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_rechte`
--

CREATE TABLE `bu_rechte` (
  `benutzername` varchar(30) NOT NULL,
  `firmanr` int(8) NOT NULL,
  `level` int(2) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_rechte`
--

INSERT INTO `bu_rechte` (`benutzername`, `firmanr`, `level`) VALUES
('ABCD', 25, 0),
('joergdeymann', 14, 0),
('joergdeymann', 17, 0),
('joergdeymann', 23, 0),
('Jörg\'s\'x', 22, 0),
('JörgiOH', 21, 0),
('niemand', 24, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_re_layout`
--

CREATE TABLE `bu_re_layout` (
  `recnum` int(11) NOT NULL,
  `firmanr` int(8) NOT NULL DEFAULT 0,
  `nr` tinyint(3) NOT NULL,
  `name` varchar(200) NOT NULL COMMENT 'name für das Layout',
  `mahnstufe` tinyint(3) NOT NULL DEFAULT 0 COMMENT '0= rechnung\r\n1 = Zahlungserinnerung 1\r\n2 = Zahlungserinnerung 2\r\n3 = Mahnung 1\r\n4 = Mahnung 2\r\n5 = inkasso \r\nBeispiel',
  `ueberschrift` varchar(200) NOT NULL DEFAULT 'Rechnung' COMMENT 'Überschriften\r\nRechnung\r\nMahnung\r\nZahlungserinnerung\r\n',
  `logo` varchar(100) NOT NULL COMMENT 'http link überschreibt das Firmenlogo',
  `retext` varchar(1000) NOT NULL,
  `vorlage` varchar(60) NOT NULL COMMENT 'Vorlage für die Rechnung',
  `hr` varchar(12) NOT NULL,
  `prio` tinyint(1) NOT NULL COMMENT 'Wird nicht mehr verwendet!! \r\nJetzt über bu_firma\r\n\r\nAlt: Diesen Anzeigen wenn es nicht explizit angegeben wurde',
  `mahngebuehr` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mahntext` varchar(200) DEFAULT NULL COMMENT 'Text in der Tabelle der Mahnung',
  `zahlungsziel_dauer` smallint(3) NOT NULL DEFAULT 14
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_re_layout`
--

INSERT INTO `bu_re_layout` (`recnum`, `firmanr`, `nr`, `name`, `mahnstufe`, `ueberschrift`, `logo`, `retext`, `vorlage`, `hr`, `prio`, `mahngebuehr`, `mahntext`, `zahlungsziel_dauer`) VALUES
(1, 14, 0, 'Rechnung', 0, 'Rechnung', '', 'Rechnungstext', '', '', 1, '0.00', 'mahntext', 14),
(6, 14, 0, 'Zahlungserinnerung', 1, 'Zahlungserinnerung', '', 'Bitte überweisten Sie den noch Offen stehenden Betrag', '', '', 1, '0.00', 'mahntext', 14),
(7, 14, 0, 'Mahnung', 2, 'Mahnung', '', 'Bitte überweisten Sie den noch Offen stehenden Betrag', '', '', 1, '5.00', 'Danke für Ihr Vertrauen!', 14),
(8, 14, 0, 'Zweite Mahnung', 3, 'Zweite Mahnung', '', 'Bitte überweisten Sie den noch Offen stehenden Betrag', '', '', 1, '5.00', 'Danke für Ihr Vertrauen!', 14),
(13, 0, 0, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(14, 0, 0, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(15, 0, 0, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(16, 0, 0, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(17, 17, 0, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(18, 17, 0, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(19, 17, 0, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(20, 17, 0, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(28, 0, 1, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(29, 0, 1, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(30, 0, 1, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(31, 0, 1, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(46, 17, 1, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(47, 17, 1, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(48, 17, 1, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(49, 17, 1, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(53, 17, 2, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(54, 17, 2, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(55, 17, 2, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(56, 17, 2, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(60, 17, 3, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(61, 17, 3, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(62, 17, 3, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(63, 17, 3, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(67, 17, 4, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(68, 17, 4, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(69, 17, 4, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(70, 17, 4, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(74, 17, 5, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(75, 17, 5, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(76, 17, 5, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(77, 17, 5, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(81, 17, 6, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(82, 17, 6, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(83, 17, 6, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(84, 17, 6, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(88, 17, 7, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(89, 17, 7, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(90, 17, 7, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(91, 17, 7, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(95, 17, 8, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(96, 17, 8, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(97, 17, 8, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(98, 17, 8, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(102, 17, 9, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(103, 17, 9, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(104, 17, 9, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(105, 17, 9, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(109, 17, 10, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(110, 17, 10, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(111, 17, 10, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(112, 17, 10, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(116, 17, 11, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(117, 17, 11, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(118, 17, 11, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(119, 17, 11, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(123, 17, 12, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(124, 17, 12, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(125, 17, 12, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(126, 17, 12, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(130, 17, 13, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(131, 17, 13, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(132, 17, 13, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(133, 17, 13, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(137, 17, 14, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(138, 17, 14, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(139, 17, 14, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(140, 17, 14, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(144, 17, 15, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(145, 17, 15, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(146, 17, 15, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(147, 17, 15, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(151, 17, 16, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(152, 17, 16, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(153, 17, 16, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(154, 17, 16, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28),
(158, 17, 17, 'Rechnung', 0, 'R E C H N U N G', '', 'Vielen Dank für Ihren Auftrag. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(159, 17, 17, 'Zahlungserinnerung', 1, 'Z A H L U N G S E R I N N E R U N G', '', 'Bitte Denken Sie an die Zahlung. Wir berechnen Ihnen für folgende Leistungen:', '', '', 0, '0.00', NULL, 14),
(160, 17, 17, 'Mahnung', 2, 'M A H N U N G', '', 'Sie haben die Rechnung immer noch nicht bezahlt. Wir erheben zusätzlich eine Mahngebühr:', '', '', 0, '5.00', NULL, 14),
(161, 17, 17, '2. Mahnung', 3, '2 . M A H N U N G', '', 'Letzte Mahnung. Wenn Sie immer noch nicht bezahlen beantragen wir ein Inkassounternehmen.', '', '', 0, '10.00', NULL, 28);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_re_posten`
--

CREATE TABLE `bu_re_posten` (
  `recnum` int(8) NOT NULL,
  `firmanr` int(8) NOT NULL DEFAULT 0,
  `renr` varchar(8) NOT NULL,
  `pos` tinyint(2) NOT NULL,
  `anz` int(8) NOT NULL,
  `einheit` tinyint(2) NOT NULL,
  `zuschlag` tinyint(2) NOT NULL,
  `km` tinyint(2) NOT NULL,
  `netto` decimal(10,2) NOT NULL,
  `mwst` tinyint(2) DEFAULT 19
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_re_posten`
--

INSERT INTO `bu_re_posten` (`recnum`, `firmanr`, `renr`, `pos`, `anz`, `einheit`, `zuschlag`, `km`, `netto`, `mwst`) VALUES
(52, 14, '20220001', 1, 2, 0, 1, 0, '15.00', 19),
(53, 14, '20220001', 2, 5, 0, 2, 0, '25.00', 19),
(54, 14, '20220001', 3, 5, 0, 2, 0, '25.00', 19),
(55, 14, '20220001', 4, 5, 0, 2, 0, '25.00', 19),
(57, 14, '20220002', 1, 20, 0, 0, 0, '9.50', 19),
(58, 14, '20220002', 2, 5, 0, 1, 0, '2.50', 19),
(59, 14, '20220003', 1, 1, 1, 0, 0, '300.00', 10),
(60, 17, '20220001', 1, 1, 1, 0, 0, '250.00', 19);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_user`
--

CREATE TABLE `bu_user` (
  `recnum` int(8) NOT NULL,
  `benutzername` varchar(30) NOT NULL,
  `passwort` varchar(60) NOT NULL,
  `mail` varchar(60) NOT NULL,
  `last_firma` int(8) NOT NULL DEFAULT 0 COMMENT 'zuletzt eingeloggt mit dieser Firma'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_user`
--

INSERT INTO `bu_user` (`recnum`, `benutzername`, `passwort`, `mail`, `last_firma`) VALUES
(1, 'joergdeymann', '$2y$10$Wy/xSKWmifJXkxxChDfisOYSUQH2Vk6N32VwA/sxnKNKdLHHLgGUG', 'joergdeymann@web.de', 17),
(2, 'joergdeymann1', '$2y$10$5p8m0iblUZsJI06J5gmBT.s5OaC8YHlv6YjwJuO11s8Kmkc/OH9ZK', 'joergdeymann@web.de', 0),
(3, 'JörgiOH', '$2y$10$y93BNNAv1qOZ6gP6Z9gfC.GZYUmfCPNztX5nyCws7uIR6CmEPdup.', 'xmail', 0),
(4, 'Jörg\'s\'x', '$2y$10$DdLdUSiNbBi29Ltg7dvMHuLUZWT2JqF4eUAwZyduZQ1CAcxlelgWy', '', 0),
(5, 'niemand', '$2y$10$0mLn8Gk4aAh8hGfDXum2h.fVXSPFCNP1ezaRIKgPFjYh6j.Tw1NDq', '', 0),
(6, 'ABC', '$2y$10$5Pdobq7kuveuB6RkXVPLMOX/Fwzj83tLPVdi7ouyZ5iDQ.TXLfnOe', '', 0),
(7, 'ABCD', '$2y$10$MsulIkkETbUE58F4WPyxrejo5tgadZKHvYpQhRnJ1xU8isj0zfIzq', '', 0);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bu_firma`
--
ALTER TABLE `bu_firma`
  ADD PRIMARY KEY (`recnum`);

--
-- Indizes für die Tabelle `bu_kunden`
--
ALTER TABLE `bu_kunden`
  ADD PRIMARY KEY (`recnum`);

--
-- Indizes für die Tabelle `bu_mahn`
--
ALTER TABLE `bu_mahn`
  ADD PRIMARY KEY (`recnum`);

--
-- Indizes für die Tabelle `bu_re`
--
ALTER TABLE `bu_re`
  ADD PRIMARY KEY (`recnum`);

--
-- Indizes für die Tabelle `bu_rechte`
--
ALTER TABLE `bu_rechte`
  ADD PRIMARY KEY (`benutzername`,`firmanr`) USING BTREE;

--
-- Indizes für die Tabelle `bu_re_layout`
--
ALTER TABLE `bu_re_layout`
  ADD PRIMARY KEY (`recnum`);

--
-- Indizes für die Tabelle `bu_re_posten`
--
ALTER TABLE `bu_re_posten`
  ADD PRIMARY KEY (`recnum`);

--
-- Indizes für die Tabelle `bu_user`
--
ALTER TABLE `bu_user`
  ADD PRIMARY KEY (`recnum`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bu_firma`
--
ALTER TABLE `bu_firma`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT für Tabelle `bu_kunden`
--
ALTER TABLE `bu_kunden`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT für Tabelle `bu_mahn`
--
ALTER TABLE `bu_mahn`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `bu_re`
--
ALTER TABLE `bu_re`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT für Tabelle `bu_re_layout`
--
ALTER TABLE `bu_re_layout`
  MODIFY `recnum` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT für Tabelle `bu_re_posten`
--
ALTER TABLE `bu_re_posten`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT für Tabelle `bu_user`
--
ALTER TABLE `bu_user`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
