-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: nafisah_nasyirah_perpustakaan
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_hp` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Aktif','Tidak Aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Administrator','admin','$2y$12$2ae0Qj6AbOB0/7wmoZ8pEuPd1hS1OZpQjIG2PmrIrpqygFXuyRkv2','','Aktif','2026-09-22 08:18:55','2026-09-22 08:18:55');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_books`
--

DROP TABLE IF EXISTS `archived_books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_published` smallint DEFAULT NULL,
  `category` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Dana BOS',
  `price` int DEFAULT NULL,
  `tanggal_penerimaan` date DEFAULT NULL,
  `tahun_penerimaan` year DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `available` int DEFAULT '0',
  `copy_serials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `backup_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `deleted_at` (`deleted_at`),
  KEY `expires_at` (`expires_at`),
  KEY `original_id` (`original_id`),
  KEY `code` (`code`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_books`
--

LOCK TABLES `archived_books` WRITE;
/*!40000 ALTER TABLE `archived_books` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archived_members`
--

DROP TABLE IF EXISTS `archived_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archived_members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `original_id` int NOT NULL,
  `username` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_code` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_year` smallint DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `original_created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `backup_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `deleted_at` (`deleted_at`),
  KEY `expires_at` (`expires_at`),
  KEY `original_id` (`original_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archived_members`
--

LOCK TABLES `archived_members` WRITE;
/*!40000 ALTER TABLE `archived_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `archived_members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_copies`
--

DROP TABLE IF EXISTS `book_copies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_copies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `copy_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','borrowed','lost','damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  KEY `copy_code` (`copy_code`)
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_copies`
--

LOCK TABLES `book_copies` WRITE;
/*!40000 ALTER TABLE `book_copies` DISABLE KEYS */;
INSERT INTO `book_copies` VALUES (1,37,'KOM-0001','available','2026-09-27 09:06:40'),(2,37,'KOM-0002','available','2026-09-27 09:06:40'),(3,37,'KOM-0003','available','2026-09-27 09:06:40'),(4,37,'KOM-0004','available','2026-09-27 09:06:40'),(5,37,'KOM-0005','available','2026-09-27 09:06:40'),(6,37,'KOM-0006','available','2026-09-27 09:06:40'),(7,37,'KOM-0007','available','2026-09-27 09:06:40'),(8,37,'KOM-0008','available','2026-09-27 09:06:40'),(9,37,'KOM-0009','available','2026-09-27 09:06:40'),(10,37,'KOM-0010','available','2026-09-27 09:06:40'),(11,38,'KOM-0007','available','2026-09-28 02:06:04'),(12,38,'KOM-0010','available','2026-09-28 02:06:04'),(13,190,'BK-003-01','available','2026-10-11 06:52:57'),(14,190,'BK-003-02','available','2026-10-11 06:52:57'),(15,190,'BK-003-03','available','2026-10-11 06:52:57'),(16,190,'BK-003-04','available','2026-10-11 06:52:57'),(17,190,'BK-003-05','available','2026-10-11 06:52:57'),(18,190,'BK-003-06','available','2026-10-11 06:52:57'),(19,190,'BK-003-07','available','2026-10-11 06:52:57'),(20,190,'BK-003-08','available','2026-10-11 06:52:57'),(21,195,'BK-003-01','borrowed','2026-10-11 13:46:39'),(22,195,'BK-003-02','available','2026-10-11 13:46:39'),(23,195,'BK-003-10','available','2026-10-11 13:46:39'),(24,195,'BK-003-13','available','2026-10-11 13:46:39'),(25,202,'BK-005-04','available','2026-10-12 01:24:32'),(26,202,'BK-005-08','available','2026-10-12 01:24:32'),(27,203,'PAN-008-103','available','2026-10-12 02:59:19'),(28,203,'PAN-008-104','available','2026-10-12 02:59:19'),(29,204,'BK-001-01','available','2026-11-04 10:11:31'),(30,204,'BK-001-02','available','2026-11-04 10:11:31'),(31,204,'BK-001-03','available','2026-11-04 10:11:31'),(32,204,'BK-001-04','available','2026-11-04 10:11:31'),(33,204,'BK-001-05','available','2026-11-04 10:11:31'),(34,204,'BK-001-06','available','2026-11-04 10:11:31'),(35,204,'BK-001-07','available','2026-11-04 10:11:31'),(36,204,'BK-001-08','available','2026-11-04 10:11:31'),(37,204,'BK-001-09','available','2026-11-04 10:11:31'),(38,204,'BK-001-10','available','2026-11-04 10:11:31'),(39,205,'BK-002-01','available','2026-11-04 10:11:31'),(40,205,'BK-002-02','available','2026-11-04 10:11:31'),(41,205,'BK-002-03','available','2026-11-04 10:11:31'),(42,205,'BK-002-04','available','2026-11-04 10:11:31'),(43,205,'BK-002-05','available','2026-11-04 10:11:31'),(44,206,'BK-003-01','available','2026-11-04 10:11:31'),(45,206,'BK-003-02','available','2026-11-04 10:11:31'),(46,206,'BK-003-03','available','2026-11-04 10:11:31'),(47,206,'BK-003-04','available','2026-11-04 10:11:31'),(48,206,'BK-003-05','available','2026-11-04 10:11:31'),(49,206,'BK-003-06','available','2026-11-04 10:11:31'),(50,206,'BK-003-07','available','2026-11-04 10:11:31'),(51,206,'BK-003-08','available','2026-11-04 10:11:31'),(52,206,'BK-003-09','available','2026-11-04 10:11:31'),(53,206,'BK-003-10','available','2026-11-04 10:11:31'),(54,206,'BK-003-11','available','2026-11-04 10:11:31'),(55,206,'BK-003-12','available','2026-11-04 10:11:31'),(56,206,'BK-003-13','available','2026-11-04 10:11:31'),(57,206,'BK-003-14','available','2026-11-04 10:11:31'),(58,206,'BK-003-15','available','2026-11-04 10:11:31'),(59,207,'BK-004-01','available','2026-11-04 10:11:31'),(60,207,'BK-004-02','available','2026-11-04 10:11:31'),(61,207,'BK-004-03','available','2026-11-04 10:11:31'),(62,207,'BK-004-04','available','2026-11-04 10:11:31'),(63,207,'BK-004-05','available','2026-11-04 10:11:31'),(64,207,'BK-004-06','available','2026-11-04 10:11:31'),(65,207,'BK-004-07','available','2026-11-04 10:11:31'),(66,207,'BK-004-08','available','2026-11-04 10:11:31'),(67,207,'BK-004-09','available','2026-11-04 10:11:31'),(68,207,'BK-004-10','available','2026-11-04 10:11:31'),(69,207,'BK-004-11','available','2026-11-04 10:11:31'),(70,207,'BK-004-12','available','2026-11-04 10:11:31'),(71,207,'BK-004-13','available','2026-11-04 10:11:31'),(72,207,'BK-004-14','available','2026-11-04 10:11:31'),(73,207,'BK-004-15','available','2026-11-04 10:11:31'),(74,207,'BK-004-16','available','2026-11-04 10:11:31'),(75,207,'BK-004-17','available','2026-11-04 10:11:31'),(76,207,'BK-004-18','available','2026-11-04 10:11:31'),(77,207,'BK-004-19','available','2026-11-04 10:11:31'),(78,207,'BK-004-20','available','2026-11-04 10:11:31'),(79,208,'BK-005-01','available','2026-11-04 10:11:31'),(80,208,'BK-005-02','available','2026-11-04 10:11:31'),(81,208,'BK-005-03','available','2026-11-04 10:11:31'),(82,208,'BK-005-04','available','2026-11-04 10:11:31'),(83,208,'BK-005-05','available','2026-11-04 10:11:31'),(84,208,'BK-005-06','available','2026-11-04 10:11:31'),(85,208,'BK-005-07','available','2026-11-04 10:11:31'),(86,208,'BK-005-08','available','2026-11-04 10:11:31'),(87,209,'Nov-009-01','available','2026-11-04 10:11:31'),(88,209,'Nov-009-02','available','2026-11-04 10:11:31'),(89,209,'Nov-009-03','available','2026-11-04 10:11:31'),(90,209,'Nov-009-04','available','2026-11-04 10:11:31'),(91,209,'Nov-009-05','available','2026-11-04 10:11:31'),(92,209,'Nov-009-06','available','2026-11-04 10:11:31'),(93,209,'Nov-009-07','available','2026-11-04 10:11:31'),(94,209,'Nov-009-08','available','2026-11-04 10:11:31'),(95,210,'sejajjajja-01','available','2026-11-04 10:11:31'),(96,210,'sejajjajja-02','available','2026-11-04 10:11:31'),(97,210,'sejajjajja-03','available','2026-11-04 10:11:31'),(98,210,'sejajjajja-04','available','2026-11-04 10:11:31'),(99,210,'sejajjajja-05','available','2026-11-04 10:11:31'),(100,210,'sejajjajja-06','available','2026-11-04 10:11:31'),(101,210,'sejajjajja-07','available','2026-11-04 10:11:31'),(102,210,'sejajjajja-08','available','2026-11-04 10:11:31'),(103,210,'sejajjajja-09','available','2026-11-04 10:11:31'),(104,210,'sejajjajja-10','available','2026-11-04 10:11:31'),(105,210,'sejajjajja-11','available','2026-11-04 10:11:31'),(106,210,'sejajjajja-12','available','2026-11-04 10:11:31'),(107,210,'sejajjajja-13','available','2026-11-04 10:11:31'),(108,210,'sejajjajja-14','available','2026-11-04 10:11:31'),(109,210,'sejajjajja-15','available','2026-11-04 10:11:31'),(110,210,'sejajjajja-16','available','2026-11-04 10:11:31'),(111,210,'sejajjajja-17','available','2026-11-04 10:11:31'),(112,210,'sejajjajja-18','available','2026-11-04 10:11:31'),(113,210,'sejajjajja-19','available','2026-11-04 10:11:31'),(114,210,'sejajjajja-20','available','2026-11-04 10:11:31'),(115,210,'sejajjajja-21','available','2026-11-04 10:11:31'),(116,210,'sejajjajja-22','available','2026-11-04 10:11:31'),(117,210,'sejajjajja-23','available','2026-11-04 10:11:31'),(118,210,'sejajjajja-24','available','2026-11-04 10:11:31'),(119,210,'sejajjajja-25','available','2026-11-04 10:11:31'),(120,210,'sejajjajja-26','available','2026-11-04 10:11:31'),(121,210,'sejajjajja-27','available','2026-11-04 10:11:31'),(122,210,'sejajjajja-28','available','2026-11-04 10:11:31'),(123,210,'sejajjajja-29','available','2026-11-04 10:11:31'),(124,210,'sejajjajja-30','available','2026-11-04 10:11:31'),(125,210,'sejajjajja-31','available','2026-11-04 10:11:31'),(126,210,'sejajjajja-32','available','2026-11-04 10:11:31'),(127,210,'sejajjajja-33','available','2026-11-04 10:11:31'),(128,210,'sejajjajja-34','available','2026-11-04 10:11:31'),(129,210,'sejajjajja-35','available','2026-11-04 10:11:31'),(130,210,'sejajjajja-36','available','2026-11-04 10:11:31'),(131,210,'sejajjajja-37','available','2026-11-04 10:11:31'),(132,210,'sejajjajja-38','available','2026-11-04 10:11:31'),(133,210,'sejajjajja-39','available','2026-11-04 10:11:31'),(134,210,'sejajjajja-40','available','2026-11-04 10:11:31'),(135,210,'sejajjajja-41','available','2026-11-04 10:11:31'),(136,210,'sejajjajja-42','available','2026-11-04 10:11:31'),(137,210,'sejajjajja-43','available','2026-11-04 10:11:31'),(138,210,'sejajjajja-44','available','2026-11-04 10:11:31'),(139,210,'sejajjajja-45','available','2026-11-04 10:11:31'),(140,210,'sejajjajja-46','available','2026-11-04 10:11:31'),(141,210,'sejajjajja-47','available','2026-11-04 10:11:31'),(142,210,'sejajjajja-48','available','2026-11-04 10:11:31'),(143,210,'sejajjajja-49','available','2026-11-04 10:11:31'),(144,210,'sejajjajja-50','available','2026-11-04 10:11:31'),(145,210,'sejajjajja-51','available','2026-11-04 10:11:31'),(146,210,'sejajjajja-52','available','2026-11-04 10:11:31'),(147,210,'sejajjajja-53','available','2026-11-04 10:11:31'),(148,210,'sejajjajja-54','available','2026-11-04 10:11:31'),(149,210,'sejajjajja-55','available','2026-11-04 10:11:31'),(150,210,'sejajjajja-56','available','2026-11-04 10:11:31'),(151,210,'sejajjajja-57','available','2026-11-04 10:11:31'),(152,210,'sejajjajja-58','available','2026-11-04 10:11:31'),(153,210,'sejajjajja-59','available','2026-11-04 10:11:31'),(154,210,'sejajjajja-60','available','2026-11-04 10:11:31'),(155,210,'sejajjajja-61','available','2026-11-04 10:11:31'),(156,210,'sejajjajja-62','available','2026-11-04 10:11:31'),(157,210,'sejajjajja-63','available','2026-11-04 10:11:31'),(158,210,'sejajjajja-64','available','2026-11-04 10:11:31'),(159,210,'sejajjajja-65','available','2026-11-04 10:11:31'),(160,210,'sejajjajja-66','available','2026-11-04 10:11:31'),(161,210,'sejajjajja-67','available','2026-11-04 10:11:31'),(162,210,'sejajjajja-68','available','2026-11-04 10:11:31'),(163,210,'sejajjajja-69','available','2026-11-04 10:11:31'),(164,210,'sejajjajja-70','available','2026-11-04 10:11:31'),(165,210,'sejajjajja-71','available','2026-11-04 10:11:31'),(166,210,'sejajjajja-72','available','2026-11-04 10:11:31'),(167,210,'sejajjajja-73','available','2026-11-04 10:11:31'),(168,210,'sejajjajja-74','available','2026-11-04 10:11:31'),(169,210,'sejajjajja-75','available','2026-11-04 10:11:31'),(170,210,'sejajjajja-76','available','2026-11-04 10:11:31'),(171,210,'sejajjajja-77','available','2026-11-04 10:11:31'),(172,210,'sejajjajja-78','available','2026-11-04 10:11:31'),(173,210,'sejajjajja-79','available','2026-11-04 10:11:31'),(174,210,'sejajjajja-80','available','2026-11-04 10:11:31'),(175,210,'sejajjajja-81','available','2026-11-04 10:11:31'),(176,210,'sejajjajja-82','available','2026-11-04 10:11:31'),(177,210,'sejajjajja-83','available','2026-11-04 10:11:31'),(178,210,'sejajjajja-84','available','2026-11-04 10:11:31'),(179,210,'sejajjajja-85','available','2026-11-04 10:11:31'),(180,210,'sejajjajja-86','available','2026-11-04 10:11:31'),(181,210,'sejajjajja-87','available','2026-11-04 10:11:31'),(182,210,'sejajjajja-88','available','2026-11-04 10:11:31'),(183,210,'sejajjajja-89','available','2026-11-04 10:11:31'),(184,210,'sejajjajja-90','available','2026-11-04 10:11:31'),(185,210,'sejajjajja-91','available','2026-11-04 10:11:31'),(186,210,'sejajjajja-92','available','2026-11-04 10:11:31'),(187,210,'sejajjajja-93','available','2026-11-04 10:11:31'),(188,210,'sejajjajja-94','available','2026-11-04 10:11:31'),(189,210,'sejajjajja-95','available','2026-11-04 10:11:31'),(190,210,'sejajjajja-96','available','2026-11-04 10:11:31'),(191,210,'sejajjajja-97','available','2026-11-04 10:11:31'),(192,210,'sejajjajja-98','available','2026-11-04 10:11:31'),(193,210,'sejajjajja-99','available','2026-11-04 10:11:31'),(194,210,'sejajjajja-100','available','2026-11-04 10:11:31'),(195,211,'Se-098-01','available','2026-11-04 10:11:31'),(196,211,'Se-098-02','available','2026-11-04 10:11:31'),(197,211,'Se-098-03','available','2026-11-04 10:11:31'),(198,211,'Se-098-04','available','2026-11-04 10:11:31'),(199,211,'Se-098-05','available','2026-11-04 10:11:31'),(200,211,'Se-098-06','available','2026-11-04 10:11:31'),(201,211,'Se-098-07','available','2026-11-04 10:11:31'),(202,211,'Se-098-08','available','2026-11-04 10:11:31'),(203,211,'Se-098-09','available','2026-11-04 10:11:31'),(204,211,'Se-098-10','available','2026-11-04 10:11:31'),(205,211,'Se-098-11','available','2026-11-04 10:11:31'),(206,211,'Se-098-12','available','2026-11-04 10:11:31'),(207,211,'Se-098-13','available','2026-11-04 10:11:31'),(208,211,'Se-098-14','available','2026-11-04 10:11:31'),(209,211,'Se-098-15','available','2026-11-04 10:11:31'),(210,211,'Se-098-16','available','2026-11-04 10:11:31'),(211,211,'Se-098-17','available','2026-11-04 10:11:31'),(212,211,'Se-098-18','available','2026-11-04 10:11:31'),(213,211,'Se-098-19','available','2026-11-04 10:11:31'),(214,211,'Se-098-20','available','2026-11-04 10:11:31'),(215,211,'Se-098-21','available','2026-11-04 10:11:31'),(216,211,'Se-098-22','available','2026-11-04 10:11:31'),(217,211,'Se-098-23','available','2026-11-04 10:11:31'),(218,211,'Se-098-24','available','2026-11-04 10:11:31'),(219,211,'Se-098-25','available','2026-11-04 10:11:31'),(220,211,'Se-098-26','available','2026-11-04 10:11:31'),(221,211,'Se-098-27','available','2026-11-04 10:11:31'),(222,211,'Se-098-28','available','2026-11-04 10:11:31'),(223,211,'Se-098-29','available','2026-11-04 10:11:31'),(224,211,'Se-098-30','available','2026-11-04 10:11:31'),(225,211,'Se-098-31','available','2026-11-04 10:11:31'),(226,211,'Se-098-32','available','2026-11-04 10:11:31'),(227,211,'Se-098-33','available','2026-11-04 10:11:31'),(228,211,'Se-098-34','available','2026-11-04 10:11:31'),(229,211,'Se-098-35','available','2026-11-04 10:11:31'),(230,211,'Se-098-36','available','2026-11-04 10:11:31'),(231,211,'Se-098-37','available','2026-11-04 10:11:31'),(232,211,'Se-098-38','available','2026-11-04 10:11:31'),(233,211,'Se-098-39','available','2026-11-04 10:11:31'),(234,211,'Se-098-40','available','2026-11-04 10:11:31'),(235,211,'Se-098-41','available','2026-11-04 10:11:31'),(236,211,'Se-098-42','available','2026-11-04 10:11:31'),(237,211,'Se-098-43','available','2026-11-04 10:11:31'),(238,211,'Se-098-44','available','2026-11-04 10:11:31'),(239,211,'Se-098-45','available','2026-11-04 10:11:31'),(240,211,'Se-098-46','available','2026-11-04 10:11:31'),(241,211,'Se-098-47','available','2026-11-04 10:11:31'),(242,211,'Se-098-48','available','2026-11-04 10:11:31'),(243,211,'Se-098-49','available','2026-11-04 10:11:31'),(244,211,'Se-098-50','available','2026-11-04 10:11:31'),(245,211,'Se-098-51','available','2026-11-04 10:11:31'),(246,211,'Se-098-52','available','2026-11-04 10:11:31'),(247,211,'Se-098-53','available','2026-11-04 10:11:31'),(248,211,'Se-098-54','available','2026-11-04 10:11:31'),(249,211,'Se-098-55','available','2026-11-04 10:11:31'),(250,211,'Se-098-56','available','2026-11-04 10:11:31'),(251,211,'Se-098-57','available','2026-11-04 10:11:31'),(252,211,'Se-098-58','available','2026-11-04 10:11:31'),(253,211,'Se-098-59','available','2026-11-04 10:11:31'),(254,211,'Se-098-60','available','2026-11-04 10:11:31'),(255,211,'Se-098-61','available','2026-11-04 10:11:31'),(256,211,'Se-098-62','available','2026-11-04 10:11:31'),(257,211,'Se-098-63','available','2026-11-04 10:11:31'),(258,211,'Se-098-64','available','2026-11-04 10:11:31'),(259,211,'Se-098-65','available','2026-11-04 10:11:31'),(260,211,'Se-098-66','available','2026-11-04 10:11:31'),(261,211,'Se-098-67','available','2026-11-04 10:11:31'),(262,211,'Se-098-68','available','2026-11-04 10:11:31'),(263,211,'Se-098-69','available','2026-11-04 10:11:31'),(264,211,'Se-098-70','available','2026-11-04 10:11:31'),(265,211,'Se-098-71','available','2026-11-04 10:11:31'),(266,211,'Se-098-72','available','2026-11-04 10:11:31'),(267,211,'Se-098-73','available','2026-11-04 10:11:31'),(268,211,'Se-098-74','available','2026-11-04 10:11:31'),(269,211,'Se-098-75','available','2026-11-04 10:11:31'),(270,211,'Se-098-76','available','2026-11-04 10:11:31'),(271,211,'Se-098-77','available','2026-11-04 10:11:31'),(272,211,'Se-098-78','available','2026-11-04 10:11:31'),(273,211,'Se-098-79','available','2026-11-04 10:11:31'),(274,211,'Se-098-80','available','2026-11-04 10:11:31'),(275,211,'Se-098-81','available','2026-11-04 10:11:31'),(276,211,'Se-098-82','available','2026-11-04 10:11:31'),(277,211,'Se-098-83','available','2026-11-04 10:11:31'),(278,211,'Se-098-84','available','2026-11-04 10:11:31'),(279,211,'Se-098-85','available','2026-11-04 10:11:31'),(280,211,'Se-098-86','available','2026-11-04 10:11:31'),(281,211,'Se-098-87','available','2026-11-04 10:11:31'),(282,211,'Se-098-88','available','2026-11-04 10:11:31'),(283,211,'Se-098-89','available','2026-11-04 10:11:31'),(284,211,'Se-098-90','available','2026-11-04 10:11:31'),(285,211,'Se-098-91','available','2026-11-04 10:11:31'),(286,211,'Se-098-92','available','2026-11-04 10:11:31'),(287,211,'Se-098-93','available','2026-11-04 10:11:31'),(288,211,'Se-098-94','available','2026-11-04 10:11:31'),(289,211,'Se-098-95','available','2026-11-04 10:11:31'),(290,211,'Se-098-96','available','2026-11-04 10:11:31'),(291,211,'Se-098-97','available','2026-11-04 10:11:31'),(292,211,'Se-098-98','available','2026-11-04 10:11:31'),(293,211,'Se-098-99','available','2026-11-04 10:11:31'),(294,211,'Se-098-100','available','2026-11-04 10:11:31');
/*!40000 ALTER TABLE `book_copies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_procurements`
--

DROP TABLE IF EXISTS `book_procurements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_procurements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `book_id` int unsigned NOT NULL,
  `tanggal_pengadaan` date NOT NULL,
  `jumlah` int unsigned NOT NULL,
  `jenis_pengadaan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber_pengadaan` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_proc_book` (`book_id`),
  CONSTRAINT `fk_proc_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_procurements`
--

LOCK TABLES `book_procurements` WRITE;
/*!40000 ALTER TABLE `book_procurements` DISABLE KEYS */;
/*!40000 ALTER TABLE `book_procurements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_published` smallint DEFAULT NULL,
  `tanggal_penerimaan` date DEFAULT NULL,
  `tahun_penerimaan` year DEFAULT NULL,
  `sumber_pengadaan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `call_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Dana BOS',
  `price` int DEFAULT NULL,
  `quantity` int DEFAULT '0',
  `copy_serials` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `available` int DEFAULT '0',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_buku` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (204,'BK-001','Laskar Pelangi','Andrea Hirata','Bentang Pustaka',2005,'2026-10-12',2026,'Dana BOS','Novel','800-899','Dana BOS',65000,10,'[\"BK-001-01\",\"BK-001-02\",\"BK-001-03\",\"BK-001-04\",\"BK-001-05\",\"BK-001-06\",\"BK-001-07\",\"BK-001-08\",\"BK-001-09\",\"BK-001-10\"]',10,'Kisah inspiratif masa kecil di Belitong',NULL,'2026-10-12 03:25:34','2026-11-05 02:01:20'),(205,'BK-002','Pemrograman Web Modern','Budi Raharjo','Informatika',2023,'2026-10-12',2026,'Dana BOS','Teknologi','600-699','Dana BOS',85000,5,'[\"BK-002-01\",\"BK-002-02\",\"BK-002-03\",\"BK-002-04\",\"BK-002-05\"]',5,'Panduan HTML, CSS, JavaScript dan PHP',NULL,'2026-10-12 03:25:34','2026-11-05 02:01:20'),(206,'BK-003','Buku Sejarah Indonesia','Sartono Kartodirdjo','Gramedia',2020,'2026-10-12',2026,'Hibah Pemerintah','Sosial','300-399','Hibah Pemerintah',0,15,'[\"BK-003-01\",\"BK-003-02\",\"BK-003-03\",\"BK-003-04\",\"BK-003-05\",\"BK-003-06\",\"BK-003-07\",\"BK-003-08\",\"BK-003-09\",\"BK-003-10\",\"BK-003-11\",\"BK-003-12\",\"BK-003-13\",\"BK-003-14\",\"BK-003-15\"]',15,'Sejarah nasional Indonesia lengkap',NULL,'2026-10-12 03:25:34','2026-11-05 02:01:20'),(207,'BK-004','Matematika Dasar Terapan','','',2022,'2026-10-12',2026,'Dana BOS','Sains','500-599','Dana BOS',NULL,20,'[\"BK-004-01\",\"BK-004-02\",\"BK-004-03\",\"BK-004-04\",\"BK-004-05\",\"BK-004-06\",\"BK-004-07\",\"BK-004-08\",\"BK-004-09\",\"BK-004-10\",\"BK-004-11\",\"BK-004-12\",\"BK-004-13\",\"BK-004-14\",\"BK-004-15\",\"BK-004-16\",\"BK-004-17\",\"BK-004-18\",\"BK-004-19\",\"BK-004-20\"]',20,'',NULL,'2026-10-12 03:25:34','2026-11-05 02:01:20'),(208,'BK-005','Ensiklopedia Sains Anak','','',NULL,'2026-10-12',2026,'Dana BOS','Umum','000-099','Dana BOS',NULL,8,'[\"BK-005-01\",\"BK-005-02\",\"BK-005-03\",\"BK-005-04\",\"BK-005-05\",\"BK-005-06\",\"BK-005-07\",\"BK-005-08\"]',8,'',NULL,'2026-10-12 03:25:35','2026-11-05 02:01:20'),(209,'Nov-009','novel','heri','',2026,'2026-10-12',2026,'Hibah Pemerintah','fiksi','800-899','Hibah Pemerintah',NULL,8,'[\"Nov-009-01\",\"Nov-009-02\",\"Nov-009-03\",\"Nov-009-04\",\"Nov-009-05\",\"Nov-009-06\",\"Nov-009-07\",\"Nov-009-08\"]',8,'novel fiksi',NULL,'2026-10-12 03:25:35','2026-11-05 02:01:20'),(210,'sejajjajja','jjjsjsj','iskanda','erlzg',2026,'2026-10-20',2026,'Dana BOS','Agama','200-299','Dana BOS',NULL,100,'[\"sejajjajja-01\",\"sejajjajja-02\",\"sejajjajja-03\",\"sejajjajja-04\",\"sejajjajja-05\",\"sejajjajja-06\",\"sejajjajja-07\",\"sejajjajja-08\",\"sejajjajja-09\",\"sejajjajja-10\",\"sejajjajja-11\",\"sejajjajja-12\",\"sejajjajja-13\",\"sejajjajja-14\",\"sejajjajja-15\",\"sejajjajja-16\",\"sejajjajja-17\",\"sejajjajja-18\",\"sejajjajja-19\",\"sejajjajja-20\",\"sejajjajja-21\",\"sejajjajja-22\",\"sejajjajja-23\",\"sejajjajja-24\",\"sejajjajja-25\",\"sejajjajja-26\",\"sejajjajja-27\",\"sejajjajja-28\",\"sejajjajja-29\",\"sejajjajja-30\",\"sejajjajja-31\",\"sejajjajja-32\",\"sejajjajja-33\",\"sejajjajja-34\",\"sejajjajja-35\",\"sejajjajja-36\",\"sejajjajja-37\",\"sejajjajja-38\",\"sejajjajja-39\",\"sejajjajja-40\",\"sejajjajja-41\",\"sejajjajja-42\",\"sejajjajja-43\",\"sejajjajja-44\",\"sejajjajja-45\",\"sejajjajja-46\",\"sejajjajja-47\",\"sejajjajja-48\",\"sejajjajja-49\",\"sejajjajja-50\",\"sejajjajja-51\",\"sejajjajja-52\",\"sejajjajja-53\",\"sejajjajja-54\",\"sejajjajja-55\",\"sejajjajja-56\",\"sejajjajja-57\",\"sejajjajja-58\",\"sejajjajja-59\",\"sejajjajja-60\",\"sejajjajja-61\",\"sejajjajja-62\",\"sejajjajja-63\",\"sejajjajja-64\",\"sejajjajja-65\",\"sejajjajja-66\",\"sejajjajja-67\",\"sejajjajja-68\",\"sejajjajja-69\",\"sejajjajja-70\",\"sejajjajja-71\",\"sejajjajja-72\",\"sejajjajja-73\",\"sejajjajja-74\",\"sejajjajja-75\",\"sejajjajja-76\",\"sejajjajja-77\",\"sejajjajja-78\",\"sejajjajja-79\",\"sejajjajja-80\",\"sejajjajja-81\",\"sejajjajja-82\",\"sejajjajja-83\",\"sejajjajja-84\",\"sejajjajja-85\",\"sejajjajja-86\",\"sejajjajja-87\",\"sejajjajja-88\",\"sejajjajja-89\",\"sejajjajja-90\",\"sejajjajja-91\",\"sejajjajja-92\",\"sejajjajja-93\",\"sejajjajja-94\",\"sejajjajja-95\",\"sejajjajja-96\",\"sejajjajja-97\",\"sejajjajja-98\",\"sejajjajja-99\",\"sejajjajja-100\"]',100,'',NULL,'2026-10-20 07:31:05','2026-11-05 02:01:20'),(211,'Se-098','Sejarah Indonesia','heri','erlangga',NULL,'2026-10-22',2026,'Dana BOS','Sosial','300-399','Dana BOS',5000,100,'[\"Se-098-01\",\"Se-098-02\",\"Se-098-03\",\"Se-098-04\",\"Se-098-05\",\"Se-098-06\",\"Se-098-07\",\"Se-098-08\",\"Se-098-09\",\"Se-098-10\",\"Se-098-11\",\"Se-098-12\",\"Se-098-13\",\"Se-098-14\",\"Se-098-15\",\"Se-098-16\",\"Se-098-17\",\"Se-098-18\",\"Se-098-19\",\"Se-098-20\",\"Se-098-21\",\"Se-098-22\",\"Se-098-23\",\"Se-098-24\",\"Se-098-25\",\"Se-098-26\",\"Se-098-27\",\"Se-098-28\",\"Se-098-29\",\"Se-098-30\",\"Se-098-31\",\"Se-098-32\",\"Se-098-33\",\"Se-098-34\",\"Se-098-35\",\"Se-098-36\",\"Se-098-37\",\"Se-098-38\",\"Se-098-39\",\"Se-098-40\",\"Se-098-41\",\"Se-098-42\",\"Se-098-43\",\"Se-098-44\",\"Se-098-45\",\"Se-098-46\",\"Se-098-47\",\"Se-098-48\",\"Se-098-49\",\"Se-098-50\",\"Se-098-51\",\"Se-098-52\",\"Se-098-53\",\"Se-098-54\",\"Se-098-55\",\"Se-098-56\",\"Se-098-57\",\"Se-098-58\",\"Se-098-59\",\"Se-098-60\",\"Se-098-61\",\"Se-098-62\",\"Se-098-63\",\"Se-098-64\",\"Se-098-65\",\"Se-098-66\",\"Se-098-67\",\"Se-098-68\",\"Se-098-69\",\"Se-098-70\",\"Se-098-71\",\"Se-098-72\",\"Se-098-73\",\"Se-098-74\",\"Se-098-75\",\"Se-098-76\",\"Se-098-77\",\"Se-098-78\",\"Se-098-79\",\"Se-098-80\",\"Se-098-81\",\"Se-098-82\",\"Se-098-83\",\"Se-098-84\",\"Se-098-85\",\"Se-098-86\",\"Se-098-87\",\"Se-098-88\",\"Se-098-89\",\"Se-098-90\",\"Se-098-91\",\"Se-098-92\",\"Se-098-93\",\"Se-098-94\",\"Se-098-95\",\"Se-098-96\",\"Se-098-97\",\"Se-098-98\",\"Se-098-99\",\"Se-098-100\"]',100,'',NULL,'2026-10-22 02:42:36','2026-11-05 02:01:20');
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buku_digital`
--

DROP TABLE IF EXISTS `buku_digital`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `buku_digital` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year` smallint DEFAULT NULL,
  `category` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Buku Pelajaran SMK',
  `file_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` enum('pdf','drive','epub','link') COLLATE utf8mb4_unicode_ci DEFAULT 'pdf',
  `pages` int DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `views` int DEFAULT '0',
  `status` enum('published','draft') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `is_free` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buku_digital`
--

LOCK TABLES `buku_digital` WRITE;
/*!40000 ALTER TABLE `buku_digital` DISABLE KEYS */;
INSERT INTO `buku_digital` VALUES (1,'Dasar-Dasar Teknik Jaringan Komputer & Telekomunikasi SMK/MAK Kelas X','Pusat Perbukuan Kemendikbudristek','Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi',2022,'Teknologi & Rekayasa','https://buku.kemdikbud.go.id/katalog/dasar-dasar-teknik-jaringan-komputer-dan-telekomunikasi-smkmak-kelas-x-semester-1','pdf',248,NULL,'Buku teks utama Kurikulum Merdeka untuk Program Keahlian Teknik Komputer dan Telekomunikasi. Membahas proses bisnis TJKT, perkembangan teknologi 5G, microwave, fiber optic, IPv6, dan dasar perakitan komputer.',145,'published',1,'2026-10-19 09:15:39','2026-10-19 11:23:37'),(2,'Rekayasa Perangkat Lunak: Pemrograman Web & Perangkat Bergerak SMK Kelas XI','Tim Pengembang Kurikulum Vokasi SMK','Direktorat Pembinaan SMK',2023,'Komputer & Informatika','https://drive.google.com/file/d/1Sample_Buku_Digital_RPL_SMK/view?usp=sharing','drive',312,NULL,'Panduan komprehensif pembelajaran pemrograman web modern (HTML5, CSS3, JavaScript, PHP, MySQL) dan pengenalan arsitektur aplikasi mobile berbasis Android/Flutter untuk siswa SMK kejuruan.',98,'published',1,'2026-10-19 09:15:39','2026-10-19 09:15:39'),(3,'Dasar-Dasar Desain Komunikasi Visual (DKV) SMK/MAK Kelas X','Kemendikbudristek RI','Pusat Kurikulum dan Perbukuan',2022,'Seni & Desain Kreatif','https://buku.kemdikbud.go.id/katalog/dasar-dasar-desain-komunikasi-visual-smkmak-kelas-x','pdf',196,NULL,'Materi resmi pembelajaran DKV mencakup prinsip desain grafis, tipografi, warna, ilustrasi vektor, fotografi digital, dan etika industri kreatif digital.',84,'published',1,'2026-10-19 09:15:39','2026-10-19 09:15:39'),(4,'Administrasi Infrastruktur Jaringan & Keamanan Sistem SMK Kelas XII','Ir. Hendra Gunawan & Tim Ahli Jaringan','Pustaka Vokasi Nusantara',2023,'Teknologi & Rekayasa','https://drive.google.com/file/d/1Sample_Buku_Digital_AIJ_SMK/view?usp=sharing','drive',278,NULL,'Konfigurasi VLAN, Routing Dinamis OSPF & BGP, Manajemen Bandwidth QoS, Firewall Filtering, VPN Tunneling, dan monitoring keamanan jaringan skala kampus/sekolah.',115,'published',1,'2026-10-19 09:15:39','2026-10-19 09:15:39'),(5,'Projek Kreatif dan Kewirausahaan (PKK) SMK/MAK Kelas XI & XII','Dra. Endang Purwanti & M. Fajar, S.Pd','Direktorat Jenderal Pendidikan Vokasi',2022,'Bisnis & Manajemen','https://drive.google.com/file/d/1Sample_Buku_Digital_PKK_SMK/view?usp=sharing','pdf',210,NULL,'Panduan merancang prototipe produk kejuruan, riset pasar digital, branding, perhitungan harga pokok produksi (HPP), dan strategi pemasaran melalui platform e-commerce.',73,'published',1,'2026-10-19 09:15:39','2026-10-19 09:15:39'),(6,'Kecerdasan Buatan (AI) & Literasi Digital Generasi Emas','Dr. Rahmat Hidayat, M.Kom','Pustaka Sains Terbuka (Open Access)',2024,'Sains & Teknologi','https://drive.google.com/file/d/1Sample_Buku_Digital_AI_Literasi/view?usp=sharing','drive',185,NULL,'Pengenalan konsep artificial intelligence, machine learning, prompt engineering, etika penggunaan AI bagi pelajar, serta tips keamanan siber pribadi di era digital.',130,'published',1,'2026-10-19 09:15:39','2026-10-19 09:15:39'),(7,'Sejarah Gusdur','Gusdur','',2026,'Buku Pelajaran SMK','https://drive.google.com/file/d/0B49krkb9SjaCdzhlemhETldOeDQ/view?usp=drive_open&resourcekey=0-rs6BgAHK_3ErWYheMc6eAA','pdf',NULL,NULL,'',3,'published',1,'2026-10-22 03:10:34','2026-10-23 03:14:32');
/*!40000 ALTER TABLE `buku_digital` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `call_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (2,'600-699','Teknologi','2026-09-23 11:00:55'),(3,'500-599','Sains','2026-09-23 11:00:55'),(4,'300-399','Sosial','2026-09-23 11:00:55'),(5,'400-499','Bahasa','2026-09-23 11:00:55'),(6,'200-299','Agama','2026-09-23 11:00:55'),(7,'700-799','Keterampilan','2026-09-23 11:00:55'),(40,'000-099','Umum','2026-09-27 04:12:43'),(44,'600-699','robotika','2026-09-27 04:29:42'),(51,'800-899','Novel','2026-10-06 11:08:26'),(125,'800-899','fiksi','2026-10-12 03:25:35'),(128,'000-099','Karya Umum','2026-11-05 02:01:20'),(129,'100-199','Filsafat dan Psikologi','2026-11-05 02:01:20'),(131,'300-399','Ilmu-Ilmu Sosial','2026-11-05 02:01:20'),(133,'500-599','Ilmu-Ilmu Alam dan Matematika','2026-11-05 02:01:20'),(134,'600-699','Teknologi dan Ilmu Terapan','2026-11-05 02:01:20'),(135,'700-799','Kesenian, Hiburan, dan Olahraga','2026-11-05 02:01:20'),(136,'800-899','Kesusastraan','2026-11-05 02:01:20'),(137,'900-999','Geografi dan Sejarah','2026-11-05 02:01:20');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clearances`
--

DROP TABLE IF EXISTS `clearances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clearances` (
  `id` int NOT NULL AUTO_INCREMENT,
  `certificate_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_id` int DEFAULT NULL,
  `member_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `member_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_by` int DEFAULT NULL,
  `purpose` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pengambilan Ijazah / Kelulusan',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificate_no` (`certificate_no`),
  KEY `member_id` (`member_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clearances`
--

LOCK TABLES `clearances` WRITE;
/*!40000 ALTER TABLE `clearances` DISABLE KEYS */;
/*!40000 ALTER TABLE `clearances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `karya`
--

DROP TABLE IF EXISTS `karya`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `karya` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_type` enum('siswa','guru') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'siswa',
  `author_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_class` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Video Praktik / Tutorial',
  `media_type` enum('video','dokumen','aplikasi','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT 'video',
  `drive_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `year` smallint DEFAULT NULL,
  `views` int DEFAULT '0',
  `status` enum('published','pending','draft','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `author_type` (`author_type`),
  KEY `media_type` (`media_type`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `karya`
--

LOCK TABLES `karya` WRITE;
/*!40000 ALTER TABLE `karya` DISABLE KEYS */;
INSERT INTO `karya` VALUES (1,'Video Praktik Instalasi Fiber Optic & Konfigurasi Router MikroTik','siswa','Ahmad Rizky Pratama & Tim TKJ','Kelas XII TKJ 1','Video Praktik / Tutorial','video','https://drive.google.com/file/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs/view?usp=sharing',NULL,'Dokumentasi video tutorial langkah demi langkah penyambungan kabel fiber optik (splicing) dan konfigurasi routing OSPF pada jaringan multi-LAN SMKN 5 Telkom.',2024,4,'published',NULL,NULL,'2026-10-19 08:38:45','2026-10-19 11:23:38'),(2,'Sistem Informasi Manajemen Kasir & Inventaris Mini Berbasis Web','siswa','Nabila Putri & Dimas Wahyu','Kelas XII RPL 2','Aplikasi & Software','dokumen','https://drive.google.com/drive/folders/1abcDEfgHIjklMNopqrstUVwxYZ123456?usp=sharing',NULL,'Laporan tugas akhir, manual book, dan repository kode aplikasi point-of-sale berbasis web responsif untuk UMKM di lingkungan sekolah.',2024,0,'published',NULL,NULL,'2026-10-19 08:38:45','2026-10-19 08:38:45'),(3,'Video Animasi 3D Edukasi Pencegahan Cyberbullying di Kalangan Remaja','siswa','Fajar Maulana & Tim Desain','Kelas XI DKV / Multimedia','Animasi & Multimedia','video','https://drive.google.com/file/d/1vWqZ9_sample_drive_vid_student/view?usp=sharing',NULL,'Film animasi 3D pendek yang mengedukasi siswa sekolah tentang bahaya perundungan digital dan bagaimana cara menjaga jejak digital yang positif.',2024,2,'published',NULL,NULL,'2026-10-19 08:38:45','2026-10-19 09:02:03'),(4,'Modul Ajar & Video Pembelajaran Cloud Computing & DevOps Siswa SMK','guru','Budi Santoso, S.Kom., M.T.','Guru Produktif Rekayasa Perangkat Lunak','Modul Ajar & Bahan Ajar','video','https://drive.google.com/drive/folders/1Teacher_Cloud_DevOps_Sample?usp=sharing',NULL,'Panduan lengkap modul ajar kurikulum merdeka serta serial rekaman video praktikum containerization dengan Docker dan implementasi pipeline CI/CD.',2024,1,'published',NULL,NULL,'2026-10-19 08:38:45','2026-10-19 09:03:32'),(5,'Karya Tulis Ilmiah: Peningkatan Literasi Digital Melalui Sistem Perpustakaan Modern','guru','Dra. Siti Rahmawati, M.Pd','Guru Bahasa & Koordinator Literasi','Karya Tulis Ilmiah','dokumen','https://drive.google.com/file/d/1Teacher_Jurnal_Literasi_Sample/view?usp=sharing',NULL,'Penelitian tindakan sekolah mengenai efektivitas katalog mandiri dan peminjaman online terhadap frekuensi membaca siswa di sekolah kejuruan.',2024,0,'published',NULL,NULL,'2026-10-19 08:38:45','2026-10-19 08:38:45'),(6,'laporan PKL','siswa','MUHAMMAD ARSYAD','Teknik Jaringan Komputer & Telekomunikasi','Video Praktik / Tutorial','video','https://drive.google.com/file/d/1udr1yuDxkWxv26ZX226CP0TZLomLL1nZ/view',NULL,'berikut laporan PKL saya di telkom tahun 2026',2026,2,'published',NULL,460,'2026-10-22 03:35:37','2026-10-23 03:02:39');
/*!40000 ALTER TABLE `karya` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `book_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `copy_serial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_date` date NOT NULL,
  `loan_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('pending','borrowed','returned','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`,`status`),
  KEY `book_id` (`book_id`,`status`),
  KEY `due_date` (`due_date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_time` int NOT NULL,
  `success` tinyint(1) DEFAULT '0',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ip_address` (`ip_address`,`attempt_time`),
  KEY `username` (`username`,`attempt_time`),
  KEY `attempt_time` (`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_categories`
--

DROP TABLE IF EXISTS `member_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `member_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Civitas Sekolah',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_categories`
--

LOCK TABLES `member_categories` WRITE;
/*!40000 ALTER TABLE `member_categories` DISABLE KEYS */;
INSERT INTO `member_categories` VALUES (64,'Teknik Informatika (PPLG)','Civitas Sekolah',1,'2026-09-27 02:53:15'),(66,'Broadcasting dan Perfilman','Civitas Sekolah',3,'2026-09-27 02:53:15'),(67,'Teknik Jaringan Komputer dan Telekomunikas (TJA)','Civitas Sekolah',4,'2026-09-27 02:53:15'),(69,'Guru & Tendik','Pendidik & Pegawai',6,'2026-09-27 02:53:15'),(71,'Tamu / Pihak Luar','Umum / Eksternal',8,'2026-09-27 02:53:15'),(72,'IPA','Lainnya',9,'2026-10-03 16:54:28');
/*!40000 ALTER TABLE `member_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_anggota` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `nomor_hp` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_registrasi` date NOT NULL DEFAULT (curdate()),
  `status` enum('Aktif','Tidak Aktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_anggota` (`id_anggota`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pengumuman',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Admin Perpustakaan',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('published','draft') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'published',
  `views` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,'dadaad','Pengumuman','dadaa','Admin Perpustakaan',NULL,'published',14,'2026-09-23 04:23:26','2026-09-26 18:22:23'),(2,'tesdsa','Prestasi','saddadadda','Tim Literasi',NULL,'published',0,'2026-09-23 04:23:26','2026-09-26 15:58:34'),(3,'tes','Koleksi Baru','adadaads','Kepala Perpustakaan',NULL,'published',0,'2026-09-23 04:23:26','2026-09-26 15:58:15');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `procurement_sources`
--

DROP TABLE IF EXISTS `procurement_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `procurement_sources` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `procurement_sources`
--

LOCK TABLES `procurement_sources` WRITE;
/*!40000 ALTER TABLE `procurement_sources` DISABLE KEYS */;
INSERT INTO `procurement_sources` VALUES (1,'Dana BOS','2026-09-27 04:01:51'),(2,'Sumbangan Siswa / Alumni','2026-09-27 04:01:51'),(3,'Sumbangan Guru / Pihak Luar','2026-09-27 04:01:51'),(4,'Hibah Perpusnas / Pemerintah','2026-09-27 04:01:51'),(5,'Pengadaan Mandiri Sekolah','2026-09-27 04:01:51'),(14,'sumbangan dinas','2026-09-27 04:29:42'),(23,'Hibah Pemerintah','2026-10-06 11:08:26');
/*!40000 ALTER TABLE `procurement_sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `setting_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('card_footer_note','Bila menemukan kartu ini, harap kembalikan ke perpustakaan.','2026-09-26 18:37:51'),('card_rules','1. Kartu ini adalah identitas resmi anggota perpustakaan SMKN 5 TELKOM Banda Aceh.\r\n2. Wajib dibawa setiap kali berkunjung dan melakukan peminjaman buku.\r\n3. Kartu tidak boleh dipinjamkan atau dialihkan kepada pihak lain.\r\n4. Anggota wajib menjaga keutuhan buku dan mengembalikan tepat waktu.\r\n5. Jika kartu hilang atau rusak, segera lapor ke petugas perpustakaan.','2026-10-11 15:40:13'),('card_subtitle_back','Perpustakaan {SEKOLAH}','2026-09-26 18:37:51'),('card_subtitle_front','Perpustakaan {SEKOLAH}','2026-10-12 03:18:57'),('card_title_back','TATA TERTIB ANGGOTA','2026-09-26 18:37:51'),('card_title_front','KARTU TANDA ANGGOTA','2026-09-26 18:37:51'),('card_valid_duration','3 Tahun','2026-10-11 15:38:59'),('clearance_doc_title','SURAT KETERANGAN BEBAS PUSTAKA','2026-09-28 06:44:56'),('clearance_intro_text','Kepala Perpustakaan {SEKOLAH} dengan ini menerangkan nama dibawah berikut :','2026-10-12 03:18:57'),('clearance_no_format','421.5/PERPUS-SMK5/BP/{ROMAN}/{YEAR}/{NO}','2026-09-28 06:51:26'),('footer_text_left','© 2026 <b>SMK NEGERI 5 TELKOM BANDA ACEH</b> · Perpustakaan Digital Berbasis Intranet Cepat','2026-10-12 03:18:57'),('footer_text_right','Akses Lokal Host & Wi-Fi Sekolah · Respons Cepat 0ms CDN','2026-10-11 15:01:28'),('hero_badge','Sistim Manajemen Data Informasi Perpustakaan','2026-09-26 16:29:16'),('hero_image','assets/uploads/hero_1790440156.jpg','2026-09-26 16:29:16'),('hero_subtitle','Membaca hari ini, membangun masa depan.','2026-09-23 10:12:48'),('hero_title','Selamat Datang di Perpustakaan Digital SMKN 5 TELKOM','2026-09-23 10:12:48'),('info_address','JL. Stadion H. Dimurtala No.5 Lampineung kota Banda Aceh Kode Pos 23125','2026-09-23 14:23:56'),('info_announcement','Pengumuman Uji Coba: Layanan Intranet Berjalan Normal','2026-09-23 10:17:58'),('info_email','smkn5telkombandaaceh@gmail.com','2026-09-23 14:23:56'),('info_hours','Senin - Kamis : 07.30 - 15.30 WIB\r\nJumat : 07.30- 11.30 WIB \r\nSabtu : 07.30- 13,0 WIB \r\nMinggu : Tutup','2026-09-25 04:58:10'),('info_phone','(0651) 7552314','2026-09-23 14:23:56'),('info_rules','Maksimal peminjaman: 2 eksemplar buku.\r\nDurasi peminjaman: 7 hari kalender.\r\nDapat diperpanjang 1 kali jika belum dipesan siswa lain.','2026-09-25 04:52:42'),('initial_books_seeded','1','2026-10-06 15:06:27'),('initial_loans_seeded','1','2026-10-06 15:06:27'),('initial_members_seeded','1','2026-09-27 03:06:39'),('karya_banner_badge','Etalase Karya & Inovasi Sekolah','2026-10-19 09:00:59'),('karya_banner_subtitle','Pusat dokumentasi digital karya kreatif, video praktikum kejuruan, modul ajar, dan karya inovasi civitas akademika SMK NEGERI 5 TELKOM BANDA ACEH yang terhubung langsung dengan Google Drive Atau Youtube dan dapat dipindai dengan QR Code.','2026-10-19 09:00:59'),('karya_banner_title','Karya Siswa & Guru','2026-10-19 09:00:13'),('kop_image','assets/img/kop_smkn5.png','2026-09-28 06:44:56'),('kop_line1','PEMERINTAH ACEH','2026-09-28 06:51:26'),('kop_line2','DINAS PENDIDIKAN','2026-09-28 06:44:56'),('kop_line3','SMK NEGERI 5 TELKOM BANDA ACEH','2026-10-11 15:28:02'),('kop_line4','JL. Stadion H. Dimurtala No.5 Lampineung kota Banda Aceh Kode Pos 23125','2026-09-28 06:51:26'),('kop_line5','Telp. (0651) 7552314 | Email: smkn5telkombandaaceh@gmail.com','2026-10-12 03:18:57'),('kop_logo_left','assets/img/logo_aceh_pancacita.png','2026-09-28 06:51:26'),('kop_logo_right','assets/uploads/kop_right_1791699436.jpg','2026-10-11 06:17:16'),('kop_mode','custom','2026-09-28 06:44:56'),('kop_show_logo_left','1','2026-09-28 06:44:56'),('kop_show_logo_right','1','2026-09-28 06:44:56'),('nav_show_about','1','2026-09-23 10:12:48'),('nav_show_books','1','2026-09-23 10:12:48'),('nav_show_home','1','2026-09-23 10:12:48'),('nav_show_info','1','2026-09-23 10:12:48'),('nav_show_news','1','2026-09-23 10:12:48'),('navbar_logo','assets/uploads/logo_1790439647.jpg','2026-09-26 16:20:47'),('navbar_subtitle','SMK NEGERI 5 TELKOM BANDA ACEH','2026-10-12 03:18:57'),('navbar_title','Sistem Manajemen Data Informasi Perpustakaan','2026-10-11 15:42:18'),('report_city','Banda Aceh','2026-09-26 19:04:34'),('report_head_name','Heri susanto','2026-10-12 03:18:57'),('report_head_nip','NIP. 12121212121','2026-10-11 15:28:02'),('report_head_title','Kepala Perpustakaan','2026-09-23 11:20:12'),('report_staff_name','fikri','2026-10-12 03:18:57'),('report_staff_nip','NIP.','2026-09-26 19:04:34'),('report_staff_title','Petugas Perpustakaan','2026-09-23 11:20:12'),('school_name','SMK NEGERI 5 TELKOM BANDA ACEH','2026-10-11 15:28:02'),('school_short','SMKN 5 TELKOM','2026-10-11 15:28:02'),('system_installed','1','2026-10-14 07:36:50'),('theme_navbar_bg','#0f172a','2026-09-27 10:48:37'),('theme_navbar_text','#f8fafc','2026-09-27 10:48:37'),('theme_primary_color','#0284c7','2026-09-27 10:48:37'),('theme_sidebar_bg','#0f172a','2026-09-27 10:48:37');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` enum('admin','member') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_code` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry_year` smallint DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `member_code` (`member_code`),
  KEY `role` (`role`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin','$2y$10$mbFeWpmGkf7ILAu2oDIdq.YkBVug/5RMXqAG7.7AY9ntpU.aMxX7G','Administrator','ADM-01','Perpustakaan',2026,NULL,NULL,NULL,'active','2026-11-05 09:27:17','::1','2026-09-23 01:48:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int DEFAULT NULL,
  `visitor_name` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time DEFAULT NULL,
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_date` (`visit_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visits`
--

LOCK TABLES `visits` WRITE;
/*!40000 ALTER TABLE `visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `visits` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-05  9:36:56
