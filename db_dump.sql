/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: vytrvalec
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `ext_translations`
--

DROP TABLE IF EXISTS `ext_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ext_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locale` varchar(8) NOT NULL,
  `object_class` varchar(191) NOT NULL,
  `field` varchar(32) NOT NULL,
  `foreign_key` varchar(64) NOT NULL,
  `content` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_unique_idx` (`locale`,`object_class`,`field`,`foreign_key`),
  KEY `translations_lookup_idx` (`locale`,`object_class`,`foreign_key`),
  KEY `general_translations_lookup_idx` (`object_class`,`foreign_key`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ext_translations`
--

LOCK TABLES `ext_translations` WRITE;
/*!40000 ALTER TABLE `ext_translations` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `ext_translations` VALUES
(5,'cs','App\\Entity\\Charity','description','1','Anička a Jiřík'),
(6,'cs','App\\Entity\\Charity','description','2','Kubík(11 let) se během těhotenství vyvíjel jako zdravé miminko. Bohužel při porodu došlo ke komplikacím, během nichž se přidusil. Tato událost ovlivnila vývoj jeho mozku a následně mu byla diagnostikována dětská mozková obrna (DMO).'),
(7,'cs','App\\Entity\\Charity','description','3','Ve věku sedmi měsíců byl Nicolasovi diagnostikován nádor na mozku, který bohužel zasáhl oblast zrakových nervů. Po první operaci, při níž se podařilo nádor částečně odstranit, přišel Nicolas o zrak. Nádor zasáhl i hormonální centrum mozku, a tak nyní potřebuje každodenní podávání hormonů spolu s léky na epilepsii, růst a ředění krve.\r\n\r\nNicolasova léčba je velmi nákladná. Pomozme mu společně – svou energií, sportem a solidaritou!'),
(8,'cs','App\\Entity\\Charity','description','5','Lilly trpí syndromem Dravetové – vzácnou a závažnou formou epilepsie, která je doprovázena lehkým až středně těžkým mentálním postižením.'),
(9,'cs','App\\Entity\\Charity','description','7','Vilémek je téměř devítiletý chlapec, který kvůli nedokysličení při porodu žije s diagnózou dětské mozkové obrny. Je zcela imobilní a odkázaný na pomoc druhých.\r\n\r\nJeho maminka je absolventkou Fakulty pedagogické Západočeské univerzity v Plzni.'),
(10,'cs','App\\Entity\\Faculty','name','1','Fakulta aplikovaných věd'),
(11,'cs','App\\Entity\\Faculty','name','2','Fakulta designu a umění Ladislava Sutnara'),
(12,'cs','App\\Entity\\Faculty','name','3','Fakulta ekonomická'),
(13,'cs','App\\Entity\\Faculty','name','4','Fakulta elektrotechnická'),
(14,'cs','App\\Entity\\Faculty','name','5','Fakulta filozofická'),
(15,'cs','App\\Entity\\Faculty','name','6','Fakulta pedagogická'),
(16,'cs','App\\Entity\\Faculty','name','7','Fakulta právnická'),
(17,'cs','App\\Entity\\Faculty','name','8','Fakulta strojní'),
(18,'cs','App\\Entity\\Faculty','name','10','Fakulta zdravotnických studií'),
(19,'cs','App\\Entity\\Faculty','name','11','Rektorát'),
(20,'cs','App\\Entity\\Faculty','name','12','Univerzita třetího věku'),
(21,'cs','App\\Entity\\Faculty','name','13','Ústav jazykové přípravy'),
(22,'cs','App\\Entity\\Faculty','name','14','Nové technologie - výzkumné centrum'),
(23,'cs','App\\Entity\\Faculty','name','15','Knihovna'),
(24,'cs','App\\Entity\\Faculty','name','16','Centrum informatizace a výpočetní techniky'),
(25,'cs','App\\Entity\\Faculty','name','17','ŠUZ Nečtiny'),
(26,'cs','App\\Entity\\Faculty','name','18','Ústav tělesné výchovy a sportu');
/*!40000 ALTER TABLE `ext_translations` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-06-21 20:24:01
