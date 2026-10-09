-- MySQL dump 10.13  Distrib 26.7.0, for macos27.0 (arm64)
--
-- Host: localhost    Database: mini_sales
-- ------------------------------------------------------
-- Server version	26.7.0

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Điện thoại','2026-10-06 10:04:03','2026-10-06 10:04:03'),(2,'Máy tính','2026-10-06 10:04:03','2026-10-06 10:04:03'),(3,'Phụ kiện','2026-10-06 10:04:03','2026-10-06 10:04:03'),(4,'Thiết bị văn phòng','2026-10-06 10:04:03','2026-10-06 10:04:03'),(5,'Thiết bị mạng','2026-10-06 10:04:03','2026-10-06 10:04:03');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_profiles`
--

DROP TABLE IF EXISTS `employee_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_profiles_employee_id_unique` (`employee_id`),
  CONSTRAINT `employee_profiles_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_profiles`
--

LOCK TABLES `employee_profiles` WRITE;
/*!40000 ALTER TABLE `employee_profiles` DISABLE KEYS */;
INSERT INTO `employee_profiles` VALUES (1,1,'Nguyễn Văn Quản','quan.nguyen@minisales.test','0901234567','12 Láng Hạ, Đống Đa, Hà Nội',NULL,'2026-10-06 10:04:02','2026-10-06 10:04:02'),(2,2,'Trần Thị Bán','ban.tran@minisales.test','0912345678','45 Cầu Giấy, Hà Nội',NULL,'2026-10-06 10:04:02','2026-10-06 10:04:02'),(3,3,'Lê Minh Hàng','hang.le@minisales.test','0987654321','8 Nguyễn Trãi, Thanh Xuân, Hà Nội',NULL,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(4,4,'Phạm Anh Tuấn','tuan.pham@minisales.test','0976543210','20 Kim Mã, Ba Đình, Hà Nội',NULL,'2026-10-06 10:04:03','2026-10-06 10:04:03');
/*!40000 ALTER TABLE `employee_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'manager','$2y$12$g6l.IwhsYL9kdl762yopFOg9mRKKH7XOHhlS3ReRnGYjNBosWiK6C','manager','active','2026-10-06 10:04:02','2026-10-06 10:04:02'),(2,'staff','$2y$12$XL5MFZcGMGdFMIY.GgJ.POnsHQTGs19USUjWjb0nN3LdZwrznDFQW','staff','active','2026-10-06 10:04:02','2026-10-06 10:04:02'),(3,'hang.le','$2y$12$KuRTyHT9MAjEO9Q979//qu6S/in.UYvKxlnJGc3dCHieb1QOjvJEO','staff','active','2026-10-06 10:04:03','2026-10-06 10:04:03'),(4,'tuan.pham','$2y$12$2oD7bOfkHI4.RjP.o4cjBuEAINrVrhgrUoFBw1.KjSa5j7gK7k6xK','staff','inactive','2026-10-06 10:04:03','2026-10-06 10:04:03');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_sessions_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_10_06_000001_create_employees_table',1),(5,'2026_10_06_000002_create_employee_profiles_table',1),(6,'2026_10_06_000003_create_categories_table',1),(7,'2026_10_06_000004_create_products_table',1),(8,'2026_10_06_000005_create_orders_table',1),(9,'2026_10_06_000006_create_order_items_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_items_order_id_product_id_unique` (`order_id`,`product_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,1,1,22990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(2,1,8,1,390000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(3,2,6,1,15490000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(4,2,13,1,350000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(5,3,3,2,5990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(6,3,9,2,150000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(7,4,5,3,27990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(8,4,12,1,3490000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(9,5,10,1,5990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(10,6,2,1,21490000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(11,6,14,1,1290000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(12,7,10,1,5990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(13,7,9,1,150000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(14,8,7,1,19990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(15,8,13,2,350000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(16,9,1,2,22990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(17,9,8,2,390000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(18,10,3,1,5990000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(19,10,14,1,1290000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(20,11,12,1,3490000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(21,12,2,1,21490000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03'),(22,12,9,1,150000.00,'2026-10-06 10:04:03','2026-10-06 10:04:03');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_employee_id_foreign` (`employee_id`),
  CONSTRAINT `orders_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'Phạm Thu Trang','0934567890',23380000.00,'completed','2026-08-27 07:08:03','2026-10-06 10:04:03'),(2,4,'Đỗ Quang Huy','0945678901',15840000.00,'completed','2026-09-01 04:45:03','2026-10-06 10:04:03'),(3,3,'Vũ Thị Lan','0956789012',12280000.00,'completed','2026-09-08 05:11:03','2026-10-06 10:04:03'),(4,1,'Công ty TNHH An Phát','02437654321',87460000.00,'completed','2026-09-15 05:00:03','2026-10-06 10:04:03'),(5,2,'Ngô Bảo Châu','0966778899',5990000.00,'cancelled','2026-09-18 07:05:03','2026-10-06 10:04:03'),(6,3,'Bùi Anh Tuấn','0977889900',22780000.00,'completed','2026-10-05 06:13:03','2026-10-06 10:04:03'),(7,2,'Hoàng Mai Anh','0988990011',6140000.00,'completed','2026-10-06 09:10:03','2026-10-06 10:04:03'),(8,3,'Trịnh Văn Nam','0911223344',20690000.00,'processing','2026-10-01 04:45:03','2026-10-06 10:04:03'),(9,2,'Lý Thị Hoa','0922334455',46760000.00,'processing','2026-10-03 08:02:03','2026-10-06 10:04:03'),(10,2,'Đặng Gia Bảo','0933445566',7280000.00,'pending','2026-10-05 08:36:03','2026-10-06 10:04:03'),(11,3,'Mai Phương Thảo','0944556677',3490000.00,'pending','2026-10-06 07:30:03','2026-10-06 10:04:03'),(12,2,'Cao Minh Đức','0955667788',21640000.00,'pending','2026-10-06 04:37:03','2026-10-06 10:04:03');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'selling',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'iPhone 16 128GB',22990000.00,'Chip A18, màn hình 6.1 inch, camera 48MP.','products/seed-iphone-16-128gb.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(2,1,'Samsung Galaxy S25 256GB',21490000.00,'Màn hình Dynamic AMOLED 6.2 inch, pin 4000 mAh.','products/seed-samsung-galaxy-s25-256gb.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(3,1,'Xiaomi Redmi Note 14',5990000.00,'Pin 5500 mAh, sạc nhanh 33W.','products/seed-xiaomi-redmi-note-14.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(4,1,'OPPO A79 5G',6490000.00,'Mẫu cũ, đã ngừng nhập hàng.','products/seed-oppo-a79-5g.svg','stopped','2026-10-06 10:04:03','2026-10-06 10:04:03'),(5,2,'MacBook Air M3 13 inch',27990000.00,'Chip Apple M3, RAM 8GB, SSD 256GB.','products/seed-macbook-air-m3-13-inch.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(6,2,'Dell Inspiron 15 3530',15490000.00,'Core i5 thế hệ 13, RAM 8GB, SSD 512GB.','products/seed-dell-inspiron-15-3530.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(7,2,'ASUS TUF Gaming F15',19990000.00,'Core i7, RTX 4050, màn hình 144Hz.','products/seed-asus-tuf-gaming-f15.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(8,3,'Sạc nhanh Anker 20W',390000.00,'Cổng USB-C, hỗ trợ Power Delivery.','products/seed-sac-nhanh-anker-20w.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(9,3,'Cáp USB-C to USB-C 1m',150000.00,'Bọc dù, hỗ trợ sạc 60W.','products/seed-cap-usb-c-to-usb-c-1m.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(10,3,'Tai nghe AirPods Pro 2',5990000.00,'Chống ồn chủ động, hộp sạc USB-C.','products/seed-tai-nghe-airpods-pro-2.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(11,3,'Ốp lưng iPhone 14 trong suốt',120000.00,'Không còn hàng.','products/seed-op-lung-iphone-14-trong-suot.svg','stopped','2026-10-06 10:04:03','2026-10-06 10:04:03'),(12,4,'Máy in Canon LBP 2900',3490000.00,'Máy in laser đen trắng, khổ A4.','products/seed-may-in-canon-lbp-2900.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(13,4,'Chuột không dây Logitech M331',350000.00,'Click yên lặng, pin 24 tháng.','products/seed-chuot-khong-day-logitech-m331.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03'),(14,4,'Bàn phím cơ Akko 3087',1290000.00,'Layout TKL 87 phím, switch Akko.','products/seed-ban-phim-co-akko-3087.svg','selling','2026-10-06 10:04:03','2026-10-06 10:04:03');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
