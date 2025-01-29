-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: southwest_candles
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `image_url` varchar(500) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (10000001,'Carrie Blue','A calming fragrance with subtle floral notes.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000002,'Santal 33/Hotel Zaza','Feel like your in Hotel Zaza',NULL,'2025-01-28 22:18:26',NULL,'NEW'),(10000003,'Amaretto Sour','Sweet and nutty aroma with a hint of citrus.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000004,'Lemon Bestie','Fresh and zesty citrus scent.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000005,'Ever Leather','Strong and bold leather fragrance.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000006,'Eucalyptus Mint','Cooling and invigorating minty blend.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000007,'Wild Jasmine','Exotic and sensual jasmine aroma.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000008,'Amaretto Sour','Sweet and nutty aroma with a hint of citrus.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000009,'Downy Spring Fresh','Clean and fresh scent inspired by spring.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000010,'Leather','Classic leather fragrance with a smoky edge.',NULL,'2025-01-28 22:18:26',NULL,'NEW');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taxes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tax_calc_id` varchar(255) NOT NULL,
  `amount_total` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL,
  `tax_type` varchar(50) NOT NULL,
  `tax_date` varchar(50) DEFAULT NULL,
  `percentage` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_calc_id` (`tax_calc_id`),
  CONSTRAINT `taxes_ibfk_1` FOREIGN KEY (`tax_calc_id`) REFERENCES `transactions` (`tax_calc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tax_calc_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `line_items` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `shipping_option` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `payment_intent_id` varchar(255) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `latest_charge_id` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(10) DEFAULT NULL,
  `state` varchar(15) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tax_calc_id` (`tax_calc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-29 11:52:44
