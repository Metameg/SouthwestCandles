﻿-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: southwest_candles
-- ------------------------------------------------------
-- Server version	8.0.36



--
-- Table structure for table `products`
--
USE  u273186481_swcandles;
DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `image_url` varchar(500) DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;

INSERT INTO `products` VALUES (10000001,'Carrie Blue','A calming fragrance with subtle floral notes.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000002,'Santal 33/Hotel Zaza','Feel like your in Hotel Zaza',NULL,'2025-01-28 22:18:26',NULL,'NEW'),(10000003,'Amaretto Sour','Sweet and nutty aroma with a hint of citrus.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000004,'Lemon Bestie','Fresh and zesty citrus scent.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000005,'Ever Leather','Strong and bold leather fragrance.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000006,'Eucalyptus Mint','Cooling and invigorating minty blend.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000007,'Wild Jasmine','Exotic and sensual jasmine aroma.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000009,'Downy Spring Fresh','Clean and fresh scent inspired by spring.',NULL,'2025-01-28 22:18:26',NULL,NULL),(10000010,'Leather','Classic leather fragrance with a smoky edge.',NULL,'2025-01-28 22:18:26',NULL,'NEW');

UNLOCK TABLES;


--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;

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
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;

UNLOCK TABLES;
--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;

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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;

UNLOCK TABLES;




-- Dump completed on 2025-01-29 12:08:24
