-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               5.5.35-0ubuntu0.12.04.2 - (Ubuntu)
-- Server Betriebssystem:        debian-linux-gnu
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Exportiere Datenbank Struktur für stadtrat
DROP DATABASE IF EXISTS `stadtrat`;
CREATE DATABASE IF NOT EXISTS `stadtrat` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `stadtrat`;


-- Exportiere Struktur von Tabelle stadtrat.sr_file
DROP TABLE IF EXISTS `sr_file`;
CREATE TABLE IF NOT EXISTS `sr_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content` longtext,
  `filename` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle stadtrat.sr_vorlage
DROP TABLE IF EXISTS `sr_vorlage`;
CREATE TABLE IF NOT EXISTS `sr_vorlage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `type` text,
  `date` int(11) DEFAULT NULL,
  `subject` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt


-- Exportiere Struktur von Tabelle stadtrat.sr_vorlage_file
DROP TABLE IF EXISTS `sr_vorlage_file`;
CREATE TABLE IF NOT EXISTS `sr_vorlage_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vorlageid` int(11) DEFAULT NULL,
  `fileid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Daten Export vom Benutzer nicht ausgewählt
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
