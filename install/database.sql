-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: nhaphangchina
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `api_tokens`
--

DROP TABLE IF EXISTS `api_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `idx_token` (`token`),
  CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cn_bags`
--

DROP TABLE IF EXISTS `cn_bags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cn_bags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bag_code` varchar(50) NOT NULL,
  `total_parcels` int(11) DEFAULT 0,
  `total_weight` decimal(10,2) DEFAULT 0.00,
  `status` enum('packing','sealed','in_transit','arrived_vn','unpacked') DEFAULT 'packing',
  `packed_by` int(10) unsigned DEFAULT NULL,
  `sealed_at` datetime DEFAULT NULL,
  `departed_at` datetime DEFAULT NULL,
  `arrived_at` datetime DEFAULT NULL,
  `unpacked_by` int(10) unsigned DEFAULT NULL,
  `unpacked_at` datetime DEFAULT NULL,
  `truck_trip_id` int(10) unsigned DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `bag_code` (`bag_code`),
  KEY `idx_status` (`status`),
  KEY `idx_bag_code` (`bag_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cn_warehouse_parcels`
--

DROP TABLE IF EXISTS `cn_warehouse_parcels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cn_warehouse_parcels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cn_tracking_code` varchar(100) NOT NULL,
  `consignment_order_id` int(10) unsigned DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT 0.00,
  `length_cm` decimal(10,2) DEFAULT NULL,
  `width_cm` decimal(10,2) DEFAULT NULL,
  `height_cm` decimal(10,2) DEFAULT NULL,
  `volume_weight` decimal(10,2) DEFAULT NULL,
  `chargeable_weight` decimal(10,2) DEFAULT NULL,
  `volume_divisor` int(11) DEFAULT 6000,
  `cargo_type` varchar(30) DEFAULT 'general',
  `bag_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `received_by` int(10) unsigned NOT NULL,
  `note` text DEFAULT NULL,
  `status` enum('received','packed','in_transit','arrived_vn','completed') DEFAULT 'received',
  `received_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tracking` (`cn_tracking_code`),
  KEY `idx_bag` (`bag_id`),
  KEY `idx_order` (`consignment_order_id`),
  KEY `idx_status` (`status`),
  KEY `idx_received_at` (`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consignment_orders`
--

DROP TABLE IF EXISTS `consignment_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignment_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `order_code` varchar(30) NOT NULL,
  `cn_tracking_code` varchar(100) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `product_description` text DEFAULT NULL,
  `package_count` int(11) NOT NULL DEFAULT 1,
  `estimated_weight` decimal(10,2) DEFAULT NULL,
  `actual_weight` decimal(10,2) DEFAULT NULL,
  `package_length` decimal(10,2) DEFAULT NULL,
  `package_width` decimal(10,2) DEFAULT NULL,
  `package_height` decimal(10,2) DEFAULT NULL,
  `volume_weight` decimal(10,2) DEFAULT NULL,
  `volume_divisor` int(11) DEFAULT NULL,
  `chargeable_weight` decimal(10,2) DEFAULT NULL,
  `declared_value` decimal(15,2) DEFAULT NULL,
  `cargo_type` varchar(50) NOT NULL DEFAULT 'general',
  `wooden_crating` tinyint(1) DEFAULT 0,
  `cn_warehouse` varchar(100) DEFAULT NULL,
  `vn_receiver_name` varchar(100) DEFAULT NULL,
  `vn_receiver_phone` varchar(20) DEFAULT NULL,
  `vn_receiver_address` text DEFAULT NULL,
  `vn_receiver_city` varchar(100) DEFAULT NULL,
  `vn_receiver_district` varchar(100) DEFAULT NULL,
  `vn_receiver_ward` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('draft','submitted','received_cn','packed_for_truck','in_transit_cn_vn','received_vn','fee_calculated','waiting_payment','ready_for_delivery','ready_for_pickup','delivering','completed','cancelled','lost_issue') NOT NULL DEFAULT 'draft',
  `shipping_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `service_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `extra_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `fee_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Snapshot of rate config at charge time' CHECK (json_valid(`fee_snapshot`)),
  `paid` tinyint(1) NOT NULL DEFAULT 0,
  `paid_at` datetime DEFAULT NULL,
  `truck_trip_id` int(10) unsigned DEFAULT NULL,
  `cn_parcel_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `idx_consignment_user` (`user_id`),
  KEY `idx_consignment_status` (`status`),
  KEY `idx_consignment_code` (`order_code`),
  KEY `idx_consignment_cn_tracking` (`cn_tracking_code`),
  KEY `idx_consignment_truck` (`truck_trip_id`),
  CONSTRAINT `consignment_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `consignment_orders_ibfk_2` FOREIGN KEY (`truck_trip_id`) REFERENCES `truck_trips` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consignment_packages`
--

DROP TABLE IF EXISTS `consignment_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignment_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `consignment_order_id` int(10) unsigned NOT NULL,
  `package_code` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `length` decimal(10,2) DEFAULT NULL,
  `width` decimal(10,2) DEFAULT NULL,
  `height` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `consignment_order_id` (`consignment_order_id`),
  CONSTRAINT `consignment_packages_ibfk_1` FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consignment_status_histories`
--

DROP TABLE IF EXISTS `consignment_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `consignment_status_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `consignment_order_id` int(10) unsigned NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `note` text DEFAULT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status_history_order` (`consignment_order_id`),
  CONSTRAINT `consignment_status_histories_ibfk_1` FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_orders`
--

DROP TABLE IF EXISTS `delivery_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `consignment_order_id` int(10) unsigned NOT NULL,
  `delivery_code` varchar(30) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `receiver_address` text NOT NULL,
  `receiver_city` varchar(100) DEFAULT NULL,
  `receiver_district` varchar(100) DEFAULT NULL,
  `receiver_ward` varchar(100) DEFAULT NULL,
  `shipper_id` int(10) unsigned DEFAULT NULL,
  `status` enum('waiting_delivery','assigned','delivering','delivered','delivery_failed','rescheduled','returned') NOT NULL DEFAULT 'waiting_delivery',
  `cod_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(15,2) NOT NULL DEFAULT 0.00,
  `scheduled_date` date DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_code` (`delivery_code`),
  KEY `idx_delivery_consignment` (`consignment_order_id`),
  KEY `idx_delivery_status` (`status`),
  KEY `idx_delivery_shipper` (`shipper_id`),
  CONSTRAINT `delivery_orders_ibfk_1` FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_proofs`
--

DROP TABLE IF EXISTS `delivery_proofs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_proofs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_order_id` int(10) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `delivery_order_id` (`delivery_order_id`),
  CONSTRAINT `delivery_proofs_ibfk_1` FOREIGN KEY (`delivery_order_id`) REFERENCES `delivery_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_status_histories`
--

DROP TABLE IF EXISTS `delivery_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_status_histories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_order_id` int(10) unsigned NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `note` text DEFAULT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `delivery_order_id` (`delivery_order_id`),
  CONSTRAINT `delivery_status_histories_ibfk_1` FOREIGN KEY (`delivery_order_id`) REFERENCES `delivery_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pickup_requests`
--

DROP TABLE IF EXISTS `pickup_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pickup_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `consignment_order_id` int(10) unsigned NOT NULL,
  `status` enum('requested','confirmed','scheduled','picked_up','missed','cancelled') NOT NULL DEFAULT 'requested',
  `scheduled_date` date DEFAULT NULL,
  `scheduled_time` time DEFAULT NULL,
  `note` text DEFAULT NULL,
  `receiver_name` varchar(255) DEFAULT NULL,
  `receiver_phone` varchar(20) DEFAULT NULL,
  `receiver_address` text DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `handled_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `consignment_order_id` (`consignment_order_id`),
  KEY `idx_pickup_user` (`user_id`),
  KEY `idx_pickup_status` (`status`),
  CONSTRAINT `pickup_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `pickup_requests_ibfk_2` FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pickup_status_histories`
--

DROP TABLE IF EXISTS `pickup_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pickup_status_histories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pickup_request_id` int(10) unsigned NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `note` text DEFAULT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pickup_request_id` (`pickup_request_id`),
  CONSTRAINT `pickup_status_histories_ibfk_1` FOREIGN KEY (`pickup_request_id`) REFERENCES `pickup_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_categories`
--

DROP TABLE IF EXISTS `post_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `section` varchar(50) NOT NULL DEFAULT 'gioi-thieu' COMMENT 'gioi-thieu, chinh-sach, quy-dinh, huong-dan, lien-he, tin-tuc',
  `category_id` int(11) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL COMMENT 'Font Awesome icon class',
  `is_published` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipping_rate_histories`
--

DROP TABLE IF EXISTS `shipping_rate_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_rate_histories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_rate_id` int(10) unsigned NOT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changes`)),
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `shipping_rate_id` (`shipping_rate_id`),
  CONSTRAINT `shipping_rate_histories_ibfk_1` FOREIGN KEY (`shipping_rate_id`) REFERENCES `shipping_rates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shipping_rates`
--

DROP TABLE IF EXISTS `shipping_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_rates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_group_id` int(10) unsigned DEFAULT NULL,
  `route` varchar(100) NOT NULL DEFAULT 'CN-VN',
  `cargo_type` varchar(50) NOT NULL DEFAULT 'general',
  `rate_per_kg` decimal(10,2) NOT NULL,
  `min_weight` decimal(10,2) NOT NULL DEFAULT 0.50,
  `rounding_method` enum('0.5kg','1kg','actual') NOT NULL DEFAULT '0.5kg',
  `extra_fee_fragile` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_fee_bulky` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extra_fee_special` decimal(10,2) NOT NULL DEFAULT 0.00,
  `volume_divisor` int(11) NOT NULL DEFAULT 6000,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_rates_active` (`is_active`,`effective_from`),
  KEY `fk_rate_group` (`user_group_id`),
  CONSTRAINT `fk_rate_group` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_bank_accounts`
--

DROP TABLE IF EXISTS `system_bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_bank_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(100) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `account_holder` varchar(100) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `topup_requests`
--

DROP TABLE IF EXISTS `topup_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `topup_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(30) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `transfer_content` varchar(255) DEFAULT NULL,
  `receipt_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','approved','rejected','expired') NOT NULL DEFAULT 'pending',
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_topup_user` (`user_id`),
  KEY `idx_topup_status` (`status`),
  KEY `idx_topup_code` (`code`),
  CONSTRAINT `topup_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tracking_events`
--

DROP TABLE IF EXISTS `tracking_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracking_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `consignment_order_id` int(10) unsigned NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `handler` varchar(100) DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `event_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tracking_order` (`consignment_order_id`),
  KEY `idx_tracking_event_at` (`event_at`),
  CONSTRAINT `tracking_events_ibfk_1` FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `truck_trip_status_histories`
--

DROP TABLE IF EXISTS `truck_trip_status_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `truck_trip_status_histories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `truck_trip_id` int(10) unsigned NOT NULL,
  `from_status` varchar(30) DEFAULT NULL,
  `to_status` varchar(30) NOT NULL,
  `note` text DEFAULT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `truck_trip_id` (`truck_trip_id`),
  CONSTRAINT `truck_trip_status_histories_ibfk_1` FOREIGN KEY (`truck_trip_id`) REFERENCES `truck_trips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `truck_trips`
--

DROP TABLE IF EXISTS `truck_trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `truck_trips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `trip_code` varchar(30) NOT NULL,
  `truck_name` varchar(100) DEFAULT NULL,
  `plate_number` varchar(30) DEFAULT NULL,
  `route` varchar(100) NOT NULL DEFAULT 'CN-VN',
  `origin_warehouse` varchar(100) DEFAULT NULL,
  `destination_warehouse` varchar(100) DEFAULT NULL,
  `loading_date` date DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `estimated_arrival` date DEFAULT NULL,
  `actual_arrival` date DEFAULT NULL,
  `status` enum('draft','loading','departed','border_processing','arrived_vn','completed','issue') NOT NULL DEFAULT 'draft',
  `note` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `trip_code` (`trip_code`),
  KEY `idx_truck_status` (`status`),
  KEY `idx_truck_code` (`trip_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_activity_logs`
--

DROP TABLE IF EXISTS `user_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_activity_user` (`user_id`),
  KEY `idx_activity_action` (`action`),
  CONSTRAINT `user_activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_bank_accounts`
--

DROP TABLE IF EXISTS `user_bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_bank_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `account_holder` varchar(100) NOT NULL,
  `branch` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_bank_user` (`user_id`),
  CONSTRAINT `user_bank_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_flags`
--

DROP TABLE IF EXISTS `user_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `flag` varchar(50) NOT NULL COMMENT 'vip, risky, debt',
  `note` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_flags` (`user_id`,`flag`),
  CONSTRAINT `user_flags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `ward` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_verifications`
--

DROP TABLE IF EXISTS `user_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_verifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `type` enum('email','phone','password_reset') NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_verifications_token` (`token`),
  CONSTRAINT `user_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','staff','accountant','admin') NOT NULL DEFAULT 'user',
  `user_group_id` int(10) unsigned DEFAULT NULL,
  `status` enum('active','locked','pending') NOT NULL DEFAULT 'pending',
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `phone_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_status` (`status`),
  KEY `fk_user_group` (`user_group_id`),
  CONSTRAINT `fk_user_group` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wallet_transactions`
--

DROP TABLE IF EXISTS `wallet_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallet_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `type` enum('topup','consignment_charge','delivery_charge','refund','withdrawal','manual_adjustment') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance_before` decimal(15,2) NOT NULL,
  `balance_after` decimal(15,2) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL COMMENT 'consignment_order, topup_request, withdrawal_request',
  `reference_id` int(10) unsigned DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_transactions_wallet` (`wallet_id`),
  KEY `idx_transactions_type` (`type`),
  KEY `idx_transactions_ref` (`reference_type`,`reference_id`),
  KEY `idx_transactions_created` (`created_at`),
  CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`),
  CONSTRAINT `wallet_transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wallets`
--

DROP TABLE IF EXISTS `wallets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wallets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `locked_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `withdrawal_requests`
--

DROP TABLE IF EXISTS `withdrawal_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `withdrawal_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(30) NOT NULL,
  `bank_account_id` int(10) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('pending','approved','processing','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by` int(10) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `reject_reason` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `bank_account_id` (`bank_account_id`),
  KEY `idx_withdrawal_user` (`user_id`),
  KEY `idx_withdrawal_status` (`status`),
  CONSTRAINT `withdrawal_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `withdrawal_requests_ibfk_2` FOREIGN KEY (`bank_account_id`) REFERENCES `user_bank_accounts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-17 19:27:30

-- ===========================
-- SEED DATA
-- ===========================

-- Default user groups
INSERT INTO `user_groups` (`name`, `code`, `description`, `is_default`) VALUES
('Khách lẻ', 'retail', 'Khách hàng mua lẻ', 1),
('Khách sỉ', 'wholesale', 'Khách hàng mua sỉ', 0),
('VIP', 'vip', 'Khách hàng VIP', 0);

-- Default shipping rate
INSERT INTO `shipping_rates` (`user_group_id`, `route`, `cargo_type`, `rate_per_kg`, `min_weight`, `rounding_method`, `volume_divisor`, `effective_from`, `is_active`) VALUES
(NULL, 'CN-VN', 'general', 35000.00, 0.50, '0.5kg', 6000, CURDATE(), 1),
(NULL, 'CN-VN', 'fragile', 45000.00, 0.50, '0.5kg', 6000, CURDATE(), 1),
(NULL, 'CN-VN', 'special', 55000.00, 0.50, '0.5kg', 6000, CURDATE(), 1);

-- Default system bank account
INSERT INTO `system_bank_accounts` (`bank_name`, `account_number`, `account_holder`, `branch`, `is_active`) VALUES
('Vietcombank', '0000000000', 'VAN CHUYEN HONG PHAT', 'Ho Chi Minh', 1);

-- Default post categories
INSERT INTO `post_categories` (`name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
('Tin công ty', 'tin-cong-ty', 'Tin tức nội bộ công ty', 1, 1),
('Kiến thức', 'kien-thuc', 'Kiến thức vận chuyển', 2, 1),
('Khuyến mãi', 'khuyen-mai', 'Chương trình khuyến mãi', 3, 1);

-- Default homepage posts
INSERT INTO `posts` (`title`, `slug`, `excerpt`, `content`, `section`, `icon`, `is_published`, `sort_order`) VALUES
('Vận chuyển nhanh chóng', 'van-chuyen-nhanh-chong', 'Giao hàng từ Trung Quốc về Việt Nam chỉ 3-5 ngày', '<p>Dịch vụ vận chuyển nhanh chóng, an toàn từ Trung Quốc về Việt Nam.</p>', 'gioi-thieu', 'fas fa-shipping-fast', 1, 1),
('Giá cả cạnh tranh', 'gia-ca-canh-tranh', 'Cam kết giá vận chuyển tốt nhất thị trường', '<p>Chúng tôi cam kết mức giá cạnh tranh nhất thị trường.</p>', 'gioi-thieu', 'fas fa-tags', 1, 2),
('An toàn tuyệt đối', 'an-toan-tuyet-doi', 'Hàng hóa được bảo quản cẩn thận, đền bù 100% nếu mất', '<p>Hàng hóa được đóng gói và bảo quản cẩn thận.</p>', 'gioi-thieu', 'fas fa-shield-alt', 1, 3),
('Chính sách vận chuyển', 'chinh-sach-van-chuyen', 'Quy định về vận chuyển hàng hóa', '<p>Chi tiết chính sách vận chuyển.</p>', 'chinh-sach', 'fas fa-truck', 1, 1),
('Chính sách thanh toán', 'chinh-sach-thanh-toan', 'Quy định về thanh toán', '<p>Chi tiết chính sách thanh toán.</p>', 'chinh-sach', 'fas fa-credit-card', 1, 2),
('Quy định đóng gói', 'quy-dinh-dong-goi', 'Yêu cầu đóng gói hàng hóa', '<p>Chi tiết quy định đóng gói.</p>', 'quy-dinh', 'fas fa-box', 1, 1),
('Hướng dẫn gửi hàng', 'huong-dan-gui-hang', 'Các bước gửi hàng từ Trung Quốc về Việt Nam', '<p>Hướng dẫn chi tiết quy trình gửi hàng.</p>', 'huong-dan', 'fas fa-book', 1, 1);
