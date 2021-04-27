-- MySQL dump 10.13  Distrib 5.6.23, for Linux (x86_64)
--
-- Host: localhost    Database: washgopc_GOPV7V2
-- ------------------------------------------------------
-- Server version	5.6.23-cll-lve

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
-- Table structure for table `commerce_flat_rate_service`
--

DROP TABLE IF EXISTS `commerce_flat_rate_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commerce_flat_rate_service` (
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT 'The machine-name of the flat rate service.',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT 'The human-readable title of the flat rate service.',
  `display_title` varchar(255) NOT NULL DEFAULT '' COMMENT 'The title of the flat rate service displayed to customers.',
  `description` mediumtext NOT NULL COMMENT 'A brief description of the flat rate service.',
  `rules_component` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether or not this service should have a default Rules component for enabling it for orders.',
  `amount` int(11) NOT NULL DEFAULT '0' COMMENT 'The amount of the base rate of the service.',
  `currency_code` varchar(32) NOT NULL COMMENT 'The currency code of the base rate of the service.',
  `data` longtext COMMENT 'A serialized array of additional data.',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores information about shipping services created...';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commerce_flat_rate_service`
--

LOCK TABLES `commerce_flat_rate_service` WRITE;
/*!40000 ALTER TABLE `commerce_flat_rate_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `commerce_flat_rate_service` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-08-26 12:19:22
