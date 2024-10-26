-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 10. Apr 2022 um 19:22
-- Server-Version: 10.4.22-MariaDB
-- PHP-Version: 8.1.2

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
CREATE DATABASE IF NOT EXISTS `bu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bu`;

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
  `inhaber` varchar(30) NOT NULL,
  `bankname` varchar(30) NOT NULL,
  `iban` varchar(25) NOT NULL,
  `bic` varchar(12) NOT NULL,
  `hrname` varchar(30) NOT NULL,
  `hra` varchar(10) NOT NULL,
  `ustid` varchar(12) NOT NULL,
  `betriebsnr` varchar(20) NOT NULL,
  `prio` tinyint(1) NOT NULL COMMENT 'Standart 1=Ja 0 Nein',
  `logo` varchar(200) NOT NULL,
  `rechnungs_layout` int(4) NOT NULL DEFAULT 0 COMMENT 'Standartformular für die Firma, 0 =  Erstes formular in der Datenbank'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_firma`
--

INSERT INTO `bu_firma` (`recnum`, `firma`, `strasse`, `plz`, `ort`, `vorname`, `nachname`, `inhaber`, `bankname`, `iban`, `bic`, `hrname`, `hra`, `ustid`, `betriebsnr`, `prio`, `logo`, `rechnungs_layout`) VALUES
(1, '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', 0, '16', 0),
(2, '0', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deymann', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee', '17283', 0, 'noch keins', 0),
(3, '0', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deymann', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee', '17283', 0, 'noch keins', 0),
(4, '0', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deymann', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee', '17283', 0, 'noch keins', 0),
(5, 'Die Deymann\'s', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deymann', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 1', 'DE 6543', '87654321', 1, 'http://www.die-deymanns.de/logo/logo.png', 0),
(6, 'Die Deymann\'s', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deymann', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee', '17283', 0, 'noch keins', 0),
(7, 'Die Deymann\'s', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deymann', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee', '17283', 0, 'noch keins', 0),
(8, 'Die Deymann\'s', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deyma\'nn', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee', '17283', 0, 'noch keins', 0),
(9, 'Die Deymann\'s', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deyma\'nn', 'Sparkasse Emsland', 'DE47blablub', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee2', '17283', 0, 'noch keins', 0),
(10, 'Die Deymann\'s', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deyma\'nn', 'Sparkasse Emsland', 'DE47blablub 2', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee2', '17283', 0, 'noch keins', 0),
(11, 'Die Deymann\'s', 'Lipperring', '49733', 'Niedersachsen - Haren (Ems)', 'Jörg', 'Deymann', 'Jörg Deyma\'nn', 'Sparkasse Emsland', 'DE47blablub 2', 'NOLADE21EMS', 'H oola', '162 ', 'DE Waldfee3', '17283', 0, 'noch keins', 0),
(12, 'All Transport 24', 'Bonifaciusstraße 160', '45309', 'Essen', 'Rasched', 'Tamitz', 'Miriam Stamm', 'Commerzbank', 'DE88 123456789', 'COLODA123', 'Essen', 'HRA 10585', 'DE308548718', '98765', 0, 'https://www.all-transport24.de/wp-content/uploads/2019/10/logo.png', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_kunden`
--

CREATE TABLE `bu_kunden` (
  `recnum` int(8) NOT NULL,
  `kdnr` varchar(8) NOT NULL,
  `firma` varchar(30) NOT NULL,
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  `strasse` varchar(30) NOT NULL,
  `plz` varchar(7) NOT NULL,
  `ort` varchar(10) NOT NULL,
  `tel_privat` varchar(30) NOT NULL,
  `tel_mobil` varchar(30) NOT NULL,
  `tel_dienst` varchar(30) NOT NULL,
  `mail_privat` varchar(60) NOT NULL,
  `mail_dienst` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_kunden`
--

INSERT INTO `bu_kunden` (`recnum`, `kdnr`, `firma`, `vorname`, `nachname`, `strasse`, `plz`, `ort`, `tel_privat`, `tel_mobil`, `tel_dienst`, `mail_privat`, `mail_dienst`) VALUES
(1, '1234', '', '123', '456', '', '', '', '', '', '', '', ''),
(2, '1', '', '2', '32', '4', '5', '6', '7', '9', '8', '10', '11'),
(3, '11', '', '22', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(4, '11', '', '223', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(5, '11', '', '223', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(6, '11', '', '223', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(7, '11', '', '223', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(8, '11', '', '223', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(9, '11', '', '223', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(10, '11', '', '223', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(11, '11', '', '2233', '33', '44', '55', '66', '77', '99', '88', '1010', '1111'),
(12, '7000', 'Biksale Solutions GmbH', '', '', 'Eugen-Sänger-Ring 7b', '85649', 'Brunntal', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_mahn`
--

CREATE TABLE `bu_mahn` (
  `recnum` int(8) NOT NULL,
  `renr` int(8) DEFAULT NULL,
  `mahnstufe` int(2) DEFAULT NULL,
  `datum` date DEFAULT NULL,
  `faellig` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_re`
--

CREATE TABLE `bu_re` (
  `recnum` int(8) NOT NULL,
  `datum` date NOT NULL,
  `faellig` date NOT NULL,
  `leistung` date NOT NULL COMMENT 'Leistungsmonat',
  `renr` varchar(8) NOT NULL,
  `kdnr` varchar(8) NOT NULL,
  `firma` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Firmennummer\r\n0 = Standart Firma\r\nsonst Firmennr ',
  `layout` tinyint(3) NOT NULL DEFAULT 0 COMMENT 'Layout Vorlage\r\n0 = Standart\r\nZahl = ausgewähltes Layout',
  `mahnstufe` tinyint(2) NOT NULL DEFAULT 0 COMMENT '0 Rechnung\r\n1 = Zahlungserinnerung\r\n3 usw',
  `bezahlt` date DEFAULT NULL COMMENT 'Rechnung bezahlt am',
  `versandart` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Nicht verdendet\r\n1 = per Mail\r\n2 = per post',
  `versanddatum` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_re`
--

INSERT INTO `bu_re` (`recnum`, `datum`, `faellig`, `leistung`, `renr`, `kdnr`, `firma`, `layout`, `mahnstufe`, `bezahlt`, `versandart`, `versanddatum`) VALUES
(1, '2022-03-05', '2022-03-06', '2022-03-04', '111222', '1234', 1, 0, 0, NULL, 0, NULL),
(2, '2022-03-01', '2022-03-03', '2022-03-02', '1', '2', 3, 0, 0, NULL, 0, NULL),
(3, '2022-03-01', '2022-03-03', '2022-03-02', '1', '2', 3, 0, 0, NULL, 0, NULL),
(4, '2022-03-01', '2022-03-03', '2022-03-02', '1', '2', 3, 0, 0, NULL, 0, NULL),
(5, '2022-03-09', '2022-03-24', '2022-03-08', '111222', '1234', 1, 0, 0, NULL, 0, NULL),
(6, '2022-03-09', '2022-03-24', '2022-03-08', '111222', '1234', 1, 0, 0, NULL, 0, NULL),
(7, '0000-00-00', '0000-00-00', '0000-00-00', '20220001', '', 0, 0, 0, NULL, 0, NULL),
(8, '0000-00-00', '0000-00-00', '0000-00-00', '20220002', '', 0, 0, 0, NULL, 0, NULL),
(9, '0000-00-00', '0000-00-00', '0000-00-00', '20220003', '', 0, 0, 0, NULL, 0, NULL),
(10, '0000-00-00', '0000-00-00', '0000-00-00', '20220004', '', 0, 0, 0, NULL, 0, NULL),
(11, '0000-00-00', '0000-00-00', '0000-00-00', '20220005', '', 0, 0, 0, NULL, 0, NULL),
(12, '0000-00-00', '0000-00-00', '0000-00-00', '20220006', '', 0, 0, 0, NULL, 0, NULL),
(13, '0000-00-00', '0000-00-00', '0000-00-00', '20220007', '', 0, 0, 0, NULL, 0, NULL),
(14, '0000-00-00', '0000-00-00', '0000-00-00', '20220008', '', 0, 0, 0, NULL, 0, NULL),
(15, '0000-00-00', '0000-00-00', '0000-00-00', '20220009', '', 0, 0, 0, NULL, 0, NULL),
(16, '0000-00-00', '0000-00-00', '0000-00-00', '20220010', '', 0, 0, 0, NULL, 0, NULL),
(17, '0000-00-00', '0000-00-00', '0000-00-00', '20220011', '', 0, 0, 0, NULL, 0, NULL),
(18, '0000-00-00', '0000-00-00', '0000-00-00', '20220012', '', 0, 0, 0, NULL, 0, NULL),
(19, '0000-00-00', '0000-00-00', '0000-00-00', '20220013', '', 0, 0, 0, NULL, 0, NULL),
(20, '0000-00-00', '0000-00-00', '0000-00-00', '20220014', '', 0, 0, 0, NULL, 0, NULL),
(21, '0000-00-00', '0000-00-00', '0000-00-00', '20220015', '', 0, 0, 0, NULL, 0, NULL),
(22, '0000-00-00', '0000-00-00', '0000-00-00', '20220016', '', 0, 0, 0, NULL, 0, NULL),
(23, '0000-00-00', '0000-00-00', '0000-00-00', '20220017', '', 0, 0, 0, NULL, 0, NULL),
(24, '0000-00-00', '0000-00-00', '0000-00-00', '20220018', '', 0, 0, 0, NULL, 0, NULL),
(25, '0000-00-00', '0000-00-00', '0000-00-00', '20220019', '', 0, 0, 0, NULL, 0, NULL),
(26, '0000-00-00', '0000-00-00', '0000-00-00', '20220020', '', 0, 0, 0, NULL, 0, NULL),
(27, '0000-00-00', '0000-00-00', '0000-00-00', '20220021', '', 0, 0, 0, NULL, 0, NULL),
(28, '0000-00-00', '0000-00-00', '0000-00-00', '20220022', '', 0, 0, 0, NULL, 0, NULL),
(29, '2022-03-12', '2022-03-15', '2022-02-11', '20220023', '7000', 0, 5, 0, NULL, 0, NULL),
(30, '2022-03-12', '2022-03-15', '2022-02-11', '20220024', '12345', 1, 0, 0, NULL, 0, NULL),
(31, '2022-03-12', '2022-03-26', '2022-02-12', '20220025', '1234', 1, 0, 0, NULL, 0, NULL),
(32, '2022-03-12', '2022-03-26', '2022-02-12', '20220026', '1234', 1, 0, 0, NULL, 0, NULL),
(33, '2022-03-12', '2022-03-26', '2022-02-12', '20220027', '1234', 1, 0, 0, NULL, 0, NULL),
(34, '2022-03-12', '2022-03-26', '2022-02-12', '20220028', '1234', 1, 0, 0, NULL, 0, NULL),
(35, '2022-03-12', '2022-03-26', '2022-02-12', '20220029', '1234', 1, 0, 0, NULL, 0, NULL),
(36, '2022-03-12', '2022-03-26', '2022-02-12', '20220030', '1234', 1, 0, 0, NULL, 0, NULL),
(37, '2022-03-12', '2022-03-26', '2022-02-12', '20220031', '1234', 1, 0, 0, NULL, 0, NULL),
(38, '2022-03-12', '2022-04-12', '2022-02-12', '20220032', '9876', 1, 0, 0, NULL, 0, NULL),
(39, '0000-00-00', '0000-00-00', '0000-00-00', '20220033', '', 0, 0, 0, NULL, 0, NULL),
(40, '0000-00-00', '0000-00-00', '0000-00-00', '20220034', '', 0, 0, 0, NULL, 0, NULL),
(41, '2022-03-01', '2022-03-03', '2022-02-02', '20220035', '4', 5, 0, 0, NULL, 0, NULL),
(42, '2022-03-12', '2022-03-12', '2022-02-10', '20220036', '11', 1, 0, 0, NULL, 0, NULL),
(43, '2022-04-05', '2022-04-19', '2022-03-03', '20220037', '1234', 0, 0, 0, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_re_layout`
--

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

--
-- Daten für Tabelle `bu_re_layout`
--

INSERT INTO `bu_re_layout` (`recnum`, `nr`, `name`, `mahnstufe`, `ueberschrift`, `logo`, `retext`, `vorlage`, `hr`, `prio`, `mahngebuehr`, `mahntext`) VALUES
(1, 5, '', 0, 'Rechnung', 'https://www.all-transport24.de/wp-content/uploads/2019/10/logo.png', 'Sehr geehrte Damen und Herren,\r\n\r\nVielen Dank in Ihre Vertrauen in die All Transport 24 e. K.\r\nWir stellen Ihnen hiermit folgende Leistung in Rechnung:', '', '', 1, '0.00', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_re_posten`
--

CREATE TABLE `bu_re_posten` (
  `recnum` int(8) NOT NULL,
  `renr` varchar(8) NOT NULL,
  `pos` tinyint(2) NOT NULL,
  `anz` int(8) NOT NULL,
  `einheit` tinyint(2) NOT NULL,
  `zuschlag` tinyint(2) NOT NULL,
  `km` tinyint(2) NOT NULL,
  `netto` decimal(10,2) NOT NULL,
  `mwst` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `bu_re_posten`
--

INSERT INTO `bu_re_posten` (`recnum`, `renr`, `pos`, `anz`, `einheit`, `zuschlag`, `km`, `netto`, `mwst`) VALUES
(10, '20220032', 1, 4, 0, 0, 0, '10.45', 0),
(11, '20220032', 2, 20, 1, 1, 1, '2.34', 0),
(12, '20220034', 1, 0, 0, 0, 0, '0.00', 0),
(13, '20220034', 2, 3, 0, 0, 0, '5.00', 0),
(14, '20220034', 3, 1, 0, 0, 0, '0.00', 0),
(15, '20220034', 4, 0, 1, 0, 0, '0.00', 0),
(16, '20220034', 5, 1, 0, 0, 0, '0.00', 0),
(17, '20220034', 6, 1, 1, 0, 0, '0.00', 0),
(18, '20220034', 7, 1, 2, 0, 0, '0.00', 0),
(20, '20220034', 8, 0, 0, 0, 2, '20.55', 0),
(21, '20220034', 9, 1, 2, 0, 2, '22.00', 0),
(22, '20220034', 10, 5, 0, 1, 0, '4.55', 0),
(23, '20220034', 11, 0, 0, 0, 0, '0.00', 0),
(24, '20220034', 12, 0, 0, 0, 0, '0.00', 0),
(25, '20220034', 13, 0, 0, 0, 0, '0.00', 0),
(26, '20220034', 14, 0, 0, 0, 0, '0.00', 0),
(27, '20220034', 15, 0, 0, 0, 0, '0.00', 0),
(28, '20220035', 1, 2, 0, 1, 2, '15.88', 0),
(29, '20220035', 2, 5, 1, 0, 0, '25.00', 0),
(30, '20220036', 1, 8, 0, 1, 1, '5.60', 0),
(31, '20220023', 1, 1, 0, 1, 2, '10.50', 0),
(32, '20220023', 2, 10, 0, 2, 2, '5.60', 0),
(34, '20220023', 4, 8, 1, 0, 1, '88.00', 0),
(35, '20220037', 1, 2, 0, 1, 2, '15.00', 0),
(36, '20220037', 2, 5, 1, 0, 0, '89.00', 0),
(37, '20220037', 3, 5, 1, 0, 0, '89.00', 0);

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
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bu_firma`
--
ALTER TABLE `bu_firma`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `bu_kunden`
--
ALTER TABLE `bu_kunden`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `bu_mahn`
--
ALTER TABLE `bu_mahn`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `bu_re`
--
ALTER TABLE `bu_re`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT für Tabelle `bu_re_layout`
--
ALTER TABLE `bu_re_layout`
  MODIFY `recnum` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `bu_re_posten`
--
ALTER TABLE `bu_re_posten`
  MODIFY `recnum` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
