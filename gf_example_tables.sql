-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 14, 2017 at 07:00 PM
-- Server version: 5.0.91-log
-- PHP Version: 5.4.45

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: ``
--

-- --------------------------------------------------------

--
-- Table structure for table `gf_ppl`
--

CREATE TABLE IF NOT EXISTS `gf_ppl` (
  `id` int(11) NOT NULL auto_increment,
  `Name` char(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Generated_Form example' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `gf_ppl`
--

INSERT INTO `gf_ppl` (`id`, `Name`) VALUES
(1, 'Aspen Luther'),
(2, 'Elliot Aveline'),
(3, 'Charles Margery'),
(4, 'Gayle Detta'),
(5, 'Shaun Essence');

-- --------------------------------------------------------

--
-- Table structure for table `gf_stores`
--

CREATE TABLE IF NOT EXISTS `gf_stores` (
  `id` int(11) NOT NULL auto_increment,
  `Name` char(100) NOT NULL,
  `Description` char(255) NOT NULL,
  `Shelves` int(11) NOT NULL default '0',
  `Location` char(100) NOT NULL,
  `Logo` char(100) NOT NULL,
  `Created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `Timezone` char(100) NOT NULL default 'Europe/Warsaw',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Generated_Form example' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `gf_stores`
--

INSERT INTO `gf_stores` (`id`, `Name`, `Description`, `Shelves`, `Location`, `Logo`, `Created`, `Timezone`) VALUES
(1, 'Planettaxon', 'Retail telescopes &amp; binoculars', 1, '', '', '2017-07-18 15:51:23', 'Europe/Warsaw');

-- --------------------------------------------------------

--
-- Table structure for table `gf_storesppl`
--

CREATE TABLE IF NOT EXISTS `gf_storesppl` (
  `id` int(11) NOT NULL auto_increment,
  `storeID` int(11) NOT NULL,
  `personID` int(11) NOT NULL,
  `Grade` float NOT NULL default '1',
  `LastReview` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Generated_Form example' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
