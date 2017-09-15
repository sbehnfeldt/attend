-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (armv7l)
--
-- Host: localhost    Database: attend
-- ------------------------------------------------------
-- Server version	5.5.40-0+wheezy1

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
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `check_in` int(11) DEFAULT NULL,
  `check_out` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_students_idx` (`student_id`),
  CONSTRAINT `fk_students` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classrooms`
--

DROP TABLE IF EXISTS `classrooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classrooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classrooms`
--

LOCK TABLES `classrooms` WRITE;
/*!40000 ALTER TABLE `classrooms` DISABLE KEYS */;
INSERT INTO `classrooms` VALUES (7,'123\'s'),(8,'ABC\'s'),(9,'Pre-K');
/*!40000 ALTER TABLE `classrooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schedules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int(10) unsigned NOT NULL,
  `mon_am` tinyint(1) NOT NULL DEFAULT '1',
  `mon_noon` tinyint(1) NOT NULL DEFAULT '1',
  `mon_pm` tinyint(1) NOT NULL DEFAULT '1',
  `tue_am` tinyint(1) NOT NULL DEFAULT '1',
  `tue_noon` tinyint(1) NOT NULL DEFAULT '1',
  `tue_pm` tinyint(1) NOT NULL DEFAULT '1',
  `wed_am` tinyint(1) NOT NULL DEFAULT '1',
  `wed_noon` tinyint(1) NOT NULL DEFAULT '1',
  `wed_pm` tinyint(1) NOT NULL DEFAULT '1',
  `thu_am` tinyint(1) NOT NULL DEFAULT '1',
  `thu_noon` tinyint(1) NOT NULL DEFAULT '1',
  `thu_pm` tinyint(1) NOT NULL DEFAULT '1',
  `fri_am` tinyint(1) NOT NULL DEFAULT '1',
  `fri_noon` tinyint(1) NOT NULL DEFAULT '1',
  `fri_pm` tinyint(1) NOT NULL DEFAULT '1',
  `start_date` date NOT NULL,
  `entered_at` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `student_date_unique` (`student_id`,`start_date`),
  KEY `fk_student_idx` (`student_id`),
  CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8 COMMENT='Table indicating when students are scheduled to attend';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedules`
--

LOCK TABLES `schedules` WRITE;
/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
INSERT INTO `schedules` VALUES (61,210,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576038),(62,211,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576071),(63,212,1,1,1,0,0,0,1,1,1,0,0,0,1,1,1,'2016-04-13',1460576110),(64,213,0,1,1,0,1,1,0,1,1,0,1,1,0,1,1,'2016-04-13',1460576144),(65,214,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576173),(66,215,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576194),(67,216,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576219),(68,217,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576243),(69,218,1,1,1,0,0,0,1,1,1,1,1,1,0,0,0,'2016-04-13',1460576282),(70,219,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,'2016-04-13',1460576342),(71,220,1,0,0,1,0,0,1,0,0,0,0,0,0,0,0,'2016-04-13',1460576378),(72,221,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576416),(73,222,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576443),(74,223,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576473),(75,224,1,1,0,1,1,0,0,0,0,0,0,0,1,1,0,'2016-04-13',1460576509),(76,225,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576542),(77,226,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576561),(79,228,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576614),(80,229,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-04-13',1460576633),(81,230,1,0,0,1,0,0,1,0,0,1,0,0,0,0,0,'2016-04-13',1460576665),(84,233,1,1,1,1,1,0,1,1,1,1,1,0,1,1,1,'2016-04-13',1460576781),(85,234,0,0,0,0,0,0,1,1,0,1,1,0,1,1,0,'2016-04-13',1460576811),(86,235,0,0,0,1,0,0,1,0,0,1,0,0,0,0,0,'2016-04-13',1460576840),(122,266,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074363),(123,267,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074379),(124,268,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074402),(125,269,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074419),(126,270,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074450),(127,271,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074488),(128,272,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074506),(129,273,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,'2016-05-12',1463074529),(130,274,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074552),(131,275,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074575),(132,276,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074598),(133,277,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074627),(134,278,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074656),(135,279,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074688),(136,280,0,0,0,1,1,1,0,0,0,1,1,1,1,1,1,'2016-05-12',1463074719),(137,281,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-05-12',1463074743),(140,224,1,1,0,0,0,0,0,0,0,1,1,0,1,1,0,'2016-05-23',1464033875),(141,219,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,'2016-06-03',1464981509),(143,284,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,'2016-06-01',1464982588),(146,285,1,1,0,0,0,0,1,1,0,0,0,0,1,1,0,'2016-06-13',1465822684),(147,286,1,1,1,1,1,1,0,0,0,1,1,1,0,0,0,'2016-06-13',1465822737),(149,268,1,1,0,1,1,0,1,1,0,1,1,0,1,1,0,'2016-07-04',1467674983),(150,271,1,1,0,1,1,0,1,1,0,1,1,0,1,1,1,'2016-07-04',1467675008),(151,285,1,1,0,0,0,0,1,1,0,0,0,0,1,1,1,'2016-07-04',1467675042),(152,279,1,1,0,1,1,0,1,1,0,1,1,0,1,1,0,'2016-07-04',1467675062),(153,288,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-07-04',1467675210),(154,223,1,1,1,1,1,1,1,1,1,1,1,1,0,0,0,'2016-07-04',1467675747),(155,224,0,0,0,1,1,0,1,1,0,1,1,0,0,0,0,'2016-07-04',1467675803),(156,230,0,0,0,1,1,0,1,1,0,1,1,0,0,0,0,'2016-07-04',1467675886),(157,233,1,1,1,0,0,0,1,1,1,1,1,1,0,0,0,'2016-07-04',1467675931),(160,211,0,0,0,1,0,0,1,0,0,1,0,0,0,0,0,'2016-07-04',1467676604),(166,224,1,1,0,0,0,0,0,0,0,1,1,0,1,1,0,'2016-07-11',1468238957),(167,230,1,0,0,1,0,0,1,0,0,1,0,0,0,0,0,'2016-07-11',1468239139),(168,211,0,0,0,1,1,0,1,1,0,1,1,0,0,0,0,'2016-07-11',1468239293),(171,224,0,0,0,1,1,0,1,1,0,1,1,0,0,0,0,'2016-08-01',1470060568),(172,292,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-08-01',1470068133),(175,271,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-08-01',1470071747),(176,285,1,1,1,0,0,0,1,1,0,0,0,0,1,1,1,'2016-08-01',1470071933),(179,280,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,'2016-08-01',1470072178),(181,234,1,1,0,1,1,0,1,1,1,0,0,0,0,0,0,'2016-08-01',1470072218),(182,219,0,0,0,1,1,1,1,1,1,1,1,1,0,0,0,'2016-08-01',1470073523),(183,294,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,'2016-09-01',1473116580),(184,295,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-01',1473116629),(185,296,0,0,0,1,0,0,0,0,0,1,0,0,1,0,0,'2016-09-01',1473116673),(186,297,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-01',1473116876),(187,211,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-01',1473116981),(188,212,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473116999),(189,219,0,0,0,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117128),(190,223,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117181),(191,224,0,0,0,0,0,0,1,1,0,1,1,0,1,1,0,'2016-09-05',1473117206),(192,230,1,1,1,1,1,1,1,1,1,1,0,0,1,0,0,'2016-09-05',1473117318),(193,233,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117341),(194,279,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117376),(195,234,1,1,1,0,0,0,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117398),(196,235,0,0,0,1,1,0,1,1,0,1,1,0,0,0,0,'2016-09-05',1473117415),(197,298,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117517),(198,299,1,0,0,1,0,0,1,0,0,1,0,0,1,0,0,'2016-09-05',1473117536),(199,300,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117566),(200,268,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473117923),(201,271,0,0,1,0,0,1,0,0,1,0,0,1,0,0,1,'2016-09-05',1473118003),(202,272,0,0,1,0,0,1,0,0,1,0,0,1,0,0,1,'2016-09-05',1473118017),(203,280,0,1,1,0,1,1,0,1,1,0,1,1,0,1,1,'2016-09-05',1473118176),(204,301,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473118284),(206,303,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473118314),(207,304,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473118332),(208,305,1,1,0,0,0,0,1,1,0,0,0,0,1,1,0,'2016-09-05',1473118369),(209,306,1,1,1,1,1,1,1,1,1,0,0,0,1,1,1,'2016-09-05',1473118400),(210,307,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,'2016-09-05',1473118444),(211,308,1,0,0,1,0,0,1,0,0,0,0,0,0,0,0,'2016-09-05',1473118471),(212,309,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473118494),(213,310,0,0,0,1,1,0,1,1,0,1,1,0,0,0,0,'2016-09-05',1473118521),(214,311,1,1,1,1,1,1,0,0,0,1,1,1,0,0,0,'2016-09-05',1473118657),(218,314,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0000-00-00',1473119108),(219,315,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-05',1473119206),(220,316,0,0,0,1,1,0,1,1,0,1,1,0,0,0,0,'2016-09-05',1473119241),(221,317,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-01',1473119274),(222,318,0,0,0,1,1,0,1,1,0,0,0,0,1,1,0,'2016-09-07',1473258841),(223,314,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2016-09-01',1473714820),(224,311,0,0,0,1,1,1,0,0,0,1,1,1,1,1,1,'2016-11-01',1473865559),(225,311,0,0,0,1,1,1,0,0,0,1,1,1,1,1,1,'2016-10-31',1473865602);
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `family_name` varchar(45) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `enrolled` int(1) NOT NULL DEFAULT '0',
  `classroom_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_student_classroom_idx` (`classroom_id`),
  CONSTRAINT `fk_student_classroom` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (210,'Czarnecki','Patrick',1,9),(211,'Dauber','Kyle',1,9),(212,'DeRossett','Brady',1,9),(213,'Falk','Brigid',1,9),(214,'Flately','James',1,9),(215,'Green','Yardley',1,9),(216,'Himmelheber','Cole',1,9),(217,'Khan','Idris',1,9),(218,'Kuhns','Evan',1,9),(219,'LaMontagne','Isaac',1,9),(220,'Martinez','Diego',1,9),(221,'Miller','Isabella',1,9),(222,'Moore','Mia',1,9),(223,'Moravek','R.J.',1,9),(224,'Nolan-Sellers','Kamryn',1,9),(225,'Pachtinger','Sophia',1,9),(226,'Paterson','Orion',1,9),(228,'Reading','Kaylee',1,9),(229,'Ritter','Sloane',1,9),(230,'Rohrer','Quinn',1,9),(233,'Szabo','Andras',1,9),(234,'Walker','Brooklyn',1,9),(235,'Whitmore','Haylie',1,9),(266,'Evans','Gracie',1,8),(267,'Ferrante','Gemma',1,8),(268,'Fioretti','Catelyn',1,8),(269,'Garber','Hannah',1,8),(270,'Hadley','Sequoah',1,8),(271,'Horoszewski','Alexander',1,8),(272,'Horoszewski','Grayson',1,8),(273,'Hughes','Ella',1,7),(274,'Ingram','Brock',1,8),(275,'LaRosa','Lilly',1,8),(276,'Moore','Max',1,7),(277,'Schubert','Evan',1,8),(278,'Thayer','Harper',1,8),(279,'Thomas','Kevin',1,9),(280,'Wagner','Leo',1,8),(281,'Wilson','Riley',1,8),(284,'Reed','Declan',1,9),(285,'Peddicord','John',1,8),(286,'LaManna','Jordyn',1,8),(288,'Dlugasch','Benjamin',1,8),(292,'Dong','Ethan',1,8),(294,'Berg','Ava',1,9),(295,'Borgos','Tressa',1,9),(296,'Bua','Nino',1,9),(297,'Corado','Juliana',1,9),(298,'Perry','Nathaniel',1,9),(299,'Senego','Meesam',1,9),(300,'Siersema','Henry',1,9),(301,'Berriel','Ethan',1,8),(303,'Carter','Jasmine',1,8),(304,'Crespo','Khloe',1,8),(305,'Fox','Aaron',1,8),(306,'Gauthier','Layla',1,8),(307,'Schwartzer','Lila',1,8),(308,'Scartocci','Mia',1,8),(309,'Petshow','Erich',1,8),(310,'Wharton','Juliette',1,8),(311,'DeLuca','Mackenzie',1,8),(314,'Townsend','CJ',1,7),(315,'Bartolone','Reilly',1,7),(316,'Murasky','Layla',1,7),(317,'Petshow','Violet',1,7),(318,'Hammerschmidt','Olivia',1,7);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-10 15:55:02
