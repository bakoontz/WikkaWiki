-- MySQL dump 10.13  Distrib 5.7.21, for Win64 (x86_64)
--
-- Host: localhost    Database: wikkawiki_141
-- ------------------------------------------------------
-- Server version	5.7.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wikka_acls`
--

DROP TABLE IF EXISTS `wikka_acls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_acls` (
  `page_tag` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `read_acl` text COLLATE utf8_unicode_ci NOT NULL,
  `write_acl` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_read_acl` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_post_acl` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`page_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikka_comments`
--

DROP TABLE IF EXISTS `wikka_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_tag` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `parent` int(10) unsigned DEFAULT NULL,
  `status` enum('deleted') COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_page_tag` (`page_tag`),
  KEY `idx_time` (`time`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikka_links`
--

DROP TABLE IF EXISTS `wikka_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_links` (
  `from_tag` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `to_tag` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  UNIQUE KEY `from_tag` (`from_tag`,`to_tag`),
  KEY `idx_to` (`to_tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikka_pages`
--

DROP TABLE IF EXISTS `wikka_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `body` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `owner` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `latest` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `note` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_tag` (`tag`),
  KEY `idx_time` (`time`),
  KEY `idx_owner` (`owner`),
  KEY `idx_latest` (`latest`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikka_referrer_blacklist`
--

DROP TABLE IF EXISTS `wikka_referrer_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_referrer_blacklist` (
  `spammer` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  KEY `idx_spammer` (`spammer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikka_referrers`
--

DROP TABLE IF EXISTS `wikka_referrers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_referrers` (
  `page_tag` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `referrer` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  KEY `idx_page_tag` (`page_tag`),
  KEY `idx_time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikka_sessions`
--

DROP TABLE IF EXISTS `wikka_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_sessions` (
  `sessionid` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `userid` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `session_start` datetime NOT NULL,
  PRIMARY KEY (`sessionid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wikka_users`
--

DROP TABLE IF EXISTS `wikka_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wikka_users` (
  `name` varchar(75) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `revisioncount` int(10) unsigned NOT NULL DEFAULT '20',
  `changescount` int(10) unsigned NOT NULL DEFAULT '50',
  `doubleclickedit` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `signuptime` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
  `show_comments` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `status` enum('invited','signed-up','pending','active','suspended','banned','deleted') COLLATE utf8_unicode_ci DEFAULT NULL,
  `theme` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `default_comment_display` int(11) NOT NULL DEFAULT '3',
  `challenge` varchar(8) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`name`),
  KEY `idx_signuptime` (`signuptime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-08-28  3:13:07
