-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Jul 2023 um 23:24
-- Server-Version: 10.4.25-MariaDB
-- PHP-Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Datenbank: `bu`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bu_urlaub`
--

CREATE TABLE `bu_urlaub` (
  `recnum` int(11) NOT NULL,
  `mitarbeiternr` varchar(10) NOT NULL COMMENT 'bu_mitabeiter.nr',
  `firmanr` int(11) NOT NULL COMMENT 'bu_firma.recnum',
  `von` datetime NOT NULL,
  `bis` datetime NOT NULL,
  `art` tinyint(4) NOT NULL COMMENT '0=Urlaub, \r\n1=Krank, \r\n2=Unbezahlt',
  `status` tinyint(4) NOT NULL COMMENT 'Urlaub\r\n======\r\n0=Beantragt, \r\n1=Genehmigt, \r\n2=abgelehnt\r\n\r\nKrank\r\n=====\r\n0=Krankenschein fehlt, \r\n1=Krankenschein vorhanden',
  `info` varchar(250) NOT NULL COMMENT 'zB. zuwenig Mitarbeiter frei',
  `gelesen` tinyint(4) NOT NULL COMMENT 'Mitarbeiter hat die Info gelesen ob Urlaub genehmigt wurde oder abgelehnt wurde.\r\n0 = gelesen\r\n1 = offen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bu_urlaub`
--
ALTER TABLE `bu_urlaub`
  ADD PRIMARY KEY (`recnum`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bu_urlaub`
--
ALTER TABLE `bu_urlaub`
  MODIFY `recnum` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
