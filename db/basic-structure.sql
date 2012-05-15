drop database if exists position_checker;
create database if not exists position_checker;
use position_checker;

-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Neděle 13. května 2012, 11:59
-- Verze MySQL: 5.5.10
-- Verze PHP: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Databáze: `position_checker`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `websites_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `websites_id` (`websites_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `newsletter`
--

CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `active` tinyint(4) DEFAULT '1',
  `websites_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `websites_id` (`websites_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `positions`
--

CREATE TABLE IF NOT EXISTS `positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `position` int(11) NOT NULL,
  `search_id` int(11) NOT NULL,
  `keywords_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `search_id` (`search_id`),
  KEY `keywords_id` (`keywords_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `search`
--

CREATE TABLE IF NOT EXISTS `search` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `url` text COLLATE utf8_czech_ci NOT NULL,
  `xpath` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `search` (`search`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `websites`
--

CREATE TABLE IF NOT EXISTS `websites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci AUTO_INCREMENT=1 ;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `keywords`
--
ALTER TABLE `keywords`
  ADD CONSTRAINT `keywords_ibfk_1` FOREIGN KEY (`websites_id`) REFERENCES `websites` (`id`);

--
-- Omezení pro tabulku `newsletter`
--
ALTER TABLE `newsletter`
  ADD CONSTRAINT `newsletter_ibfk_1` FOREIGN KEY (`websites_id`) REFERENCES `websites` (`id`);

--
-- Omezení pro tabulku `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`keywords_id`) REFERENCES `keywords` (`id`),
  ADD CONSTRAINT `positions_ibfk_2` FOREIGN KEY (`search_id`) REFERENCES `search` (`id`);
	
	
INSERT INTO `search` (`id`, `search`, `url`, `xpath`) VALUES
(1, 'google_cz', 'http://www.google.cz/search?q=[SEARCH_PHRASE]&start=[COUNT_FROM]&pws=0', '//body//*/li[./@*!="lclbox" and ./@*!="videobox" and @class="g"]//*/cite'),
(2, 'seznam_cz', 'http://search.seznam.cz/?q=[SEARCH_PHRASE]&count=10&from=[COUNT_FROM]', '//body//*/table[@class="result"]//*/span[@class="url"]');

