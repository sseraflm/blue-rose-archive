-- MariaDB dump 10.20-13.0.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: blue_rose
-- ------------------------------------------------------
-- Server version	13.0.2-MariaDB

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
-- Table structure for table `song_ado_data`
--

DROP TABLE IF EXISTS `song_ado_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `song_ado_data` (
  `song_id` int(10) unsigned NOT NULL,
  `artist_name` varchar(100) NOT NULL DEFAULT 'Ado',
  `release_date` date NOT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `niconico_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`song_id`),
  CONSTRAINT `fk_ado_data_song` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `song_credits`
--

DROP TABLE IF EXISTS `song_credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `song_credits` (
  `song_id` int(10) unsigned NOT NULL,
  `composer` varchar(255) DEFAULT NULL,
  `lyricist` varchar(255) DEFAULT NULL,
  `arranger` varchar(255) DEFAULT NULL,
  `producer` varchar(255) DEFAULT NULL,
  `featuring` varchar(255) DEFAULT NULL,
  `guitar` varchar(255) DEFAULT NULL,
  `bass` varchar(255) DEFAULT NULL,
  `drums` varchar(255) DEFAULT NULL,
  `piano` varchar(255) DEFAULT NULL,
  `mix_engineer` varchar(255) DEFAULT NULL,
  `mastering_engineer` varchar(255) DEFAULT NULL,
  `programmer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`song_id`),
  CONSTRAINT `fk_credits_song` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `song_original_data`
--

DROP TABLE IF EXISTS `song_original_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `song_original_data` (
  `song_id` int(10) unsigned NOT NULL,
  `artist` varchar(255) NOT NULL,
  `release_date` date DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `niconico_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`song_id`),
  CONSTRAINT `fk_original_data_song` FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `songs`
--

DROP TABLE IF EXISTS `songs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `songs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(120) NOT NULL,
  `title_romaji` varchar(255) NOT NULL,
  `title_ja` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT 0,
  `album` varchar(150) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_title_romaji` (`title_romaji`),
  KEY `idx_title_ja` (`title_ja`),
  KEY `idx_title_en` (`title_en`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-11-13 18:33:10
