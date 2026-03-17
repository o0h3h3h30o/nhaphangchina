-- ============================================
-- HỆ THỐNG VẬN CHUYỂN KÝ GỬI TRUNG QUỐC - VIỆT NAM
-- Database: nhaphangchina
-- ============================================

CREATE DATABASE IF NOT EXISTS `nhaphangchina` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nhaphangchina`;

-- ============================================
-- 1. USERS & AUTH
-- ============================================

CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `phone` VARCHAR(20) DEFAULT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('user','staff','accountant','admin') NOT NULL DEFAULT 'user',
    `status` ENUM('active','locked','pending') NOT NULL DEFAULT 'pending',
    `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `phone_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB;

CREATE TABLE `user_profiles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `full_name` VARCHAR(100) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `city` VARCHAR(100) DEFAULT NULL,
    `district` VARCHAR(100) DEFAULT NULL,
    `ward` VARCHAR(100) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `note` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `user_verifications` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('email','phone','password_reset') NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_verifications_token` (`token`)
) ENGINE=InnoDB;

CREATE TABLE `user_flags` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `flag` VARCHAR(50) NOT NULL COMMENT 'vip, risky, debt',
    `note` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_user_flags` (`user_id`, `flag`)
) ENGINE=InnoDB;

CREATE TABLE `user_activity_logs` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_activity_user` (`user_id`),
    INDEX `idx_activity_action` (`action`)
) ENGINE=InnoDB;

-- ============================================
-- 2. WALLET & TRANSACTIONS
-- ============================================

CREATE TABLE `wallets` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL UNIQUE,
    `balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `locked_balance` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `wallet_transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `wallet_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('topup','consignment_charge','delivery_charge','refund','withdrawal','manual_adjustment') NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `balance_before` DECIMAL(15,2) NOT NULL,
    `balance_after` DECIMAL(15,2) NOT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL COMMENT 'consignment_order, topup_request, withdrawal_request',
    `reference_id` INT UNSIGNED DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`wallet_id`) REFERENCES `wallets`(`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    INDEX `idx_transactions_wallet` (`wallet_id`),
    INDEX `idx_transactions_type` (`type`),
    INDEX `idx_transactions_ref` (`reference_type`, `reference_id`),
    INDEX `idx_transactions_created` (`created_at`)
) ENGINE=InnoDB;

-- ============================================
-- 3. BANK ACCOUNTS
-- ============================================

CREATE TABLE `user_bank_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `bank_name` VARCHAR(100) NOT NULL,
    `account_number` VARCHAR(50) NOT NULL,
    `account_holder` VARCHAR(100) NOT NULL,
    `branch` VARCHAR(100) DEFAULT NULL,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `verified` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_bank_user` (`user_id`)
) ENGINE=InnoDB;

-- ============================================
-- 4. TOP-UP REQUESTS
-- ============================================

CREATE TABLE `topup_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `code` VARCHAR(30) NOT NULL UNIQUE,
    `amount` DECIMAL(15,2) NOT NULL,
    `bank_name` VARCHAR(100) DEFAULT NULL,
    `transfer_content` VARCHAR(255) DEFAULT NULL,
    `receipt_image` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('pending','processing','approved','rejected','expired') NOT NULL DEFAULT 'pending',
    `approved_by` INT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `reject_reason` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    INDEX `idx_topup_user` (`user_id`),
    INDEX `idx_topup_status` (`status`),
    INDEX `idx_topup_code` (`code`)
) ENGINE=InnoDB;

-- ============================================
-- 5. WITHDRAWAL REQUESTS
-- ============================================

CREATE TABLE `withdrawal_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `code` VARCHAR(30) NOT NULL UNIQUE,
    `bank_account_id` INT UNSIGNED NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `status` ENUM('pending','approved','processing','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
    `approved_by` INT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `completed_at` DATETIME DEFAULT NULL,
    `reject_reason` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`bank_account_id`) REFERENCES `user_bank_accounts`(`id`),
    INDEX `idx_withdrawal_user` (`user_id`),
    INDEX `idx_withdrawal_status` (`status`)
) ENGINE=InnoDB;

-- ============================================
-- 6. SHIPPING RATES (Admin config)
-- ============================================

CREATE TABLE `shipping_rates` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `route` VARCHAR(100) NOT NULL DEFAULT 'CN-VN',
    `cargo_type` VARCHAR(50) NOT NULL DEFAULT 'general',
    `rate_per_kg` DECIMAL(10,2) NOT NULL,
    `min_weight` DECIMAL(10,2) NOT NULL DEFAULT 0.50,
    `rounding_method` ENUM('0.5kg','1kg','actual') NOT NULL DEFAULT '0.5kg',
    `extra_fee_fragile` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `extra_fee_bulky` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `extra_fee_special` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `effective_from` DATE NOT NULL,
    `effective_to` DATE DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_rates_active` (`is_active`, `effective_from`)
) ENGINE=InnoDB;

CREATE TABLE `shipping_rate_histories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `shipping_rate_id` INT UNSIGNED NOT NULL,
    `changes` JSON DEFAULT NULL,
    `changed_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`shipping_rate_id`) REFERENCES `shipping_rates`(`id`)
) ENGINE=InnoDB;

-- ============================================
-- 7. CONSIGNMENT ORDERS
-- ============================================

CREATE TABLE `consignment_orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `order_code` VARCHAR(30) NOT NULL UNIQUE,
    `cn_tracking_code` VARCHAR(100) DEFAULT NULL,
    `product_name` VARCHAR(255) DEFAULT NULL,
    `product_description` TEXT DEFAULT NULL,
    `package_count` INT NOT NULL DEFAULT 1,
    `estimated_weight` DECIMAL(10,2) DEFAULT NULL,
    `actual_weight` DECIMAL(10,2) DEFAULT NULL,
    `declared_value` DECIMAL(15,2) DEFAULT NULL,
    `cargo_type` VARCHAR(50) NOT NULL DEFAULT 'general',
    `cn_warehouse` VARCHAR(100) DEFAULT NULL,
    `vn_receiver_name` VARCHAR(100) DEFAULT NULL,
    `vn_receiver_phone` VARCHAR(20) DEFAULT NULL,
    `vn_receiver_address` TEXT DEFAULT NULL,
    `vn_receiver_city` VARCHAR(100) DEFAULT NULL,
    `vn_receiver_district` VARCHAR(100) DEFAULT NULL,
    `vn_receiver_ward` VARCHAR(100) DEFAULT NULL,
    `note` TEXT DEFAULT NULL,
    `status` ENUM('draft','submitted','received_cn','packed_for_truck','in_transit_cn_vn','received_vn','fee_calculated','waiting_payment','ready_for_delivery','ready_for_pickup','delivering','completed','cancelled','lost_issue') NOT NULL DEFAULT 'draft',
    `shipping_fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `service_fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `extra_fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `total_fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `fee_snapshot` JSON DEFAULT NULL COMMENT 'Snapshot of rate config at charge time',
    `paid` TINYINT(1) NOT NULL DEFAULT 0,
    `paid_at` DATETIME DEFAULT NULL,
    `truck_trip_id` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    INDEX `idx_consignment_user` (`user_id`),
    INDEX `idx_consignment_status` (`status`),
    INDEX `idx_consignment_code` (`order_code`),
    INDEX `idx_consignment_cn_tracking` (`cn_tracking_code`),
    INDEX `idx_consignment_truck` (`truck_trip_id`)
) ENGINE=InnoDB;

CREATE TABLE `consignment_packages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `consignment_order_id` INT UNSIGNED NOT NULL,
    `package_code` VARCHAR(50) DEFAULT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `weight` DECIMAL(10,2) DEFAULT NULL,
    `length` DECIMAL(10,2) DEFAULT NULL,
    `width` DECIMAL(10,2) DEFAULT NULL,
    `height` DECIMAL(10,2) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `consignment_status_histories` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `consignment_order_id` INT UNSIGNED NOT NULL,
    `from_status` VARCHAR(30) DEFAULT NULL,
    `to_status` VARCHAR(30) NOT NULL,
    `note` TEXT DEFAULT NULL,
    `changed_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders`(`id`) ON DELETE CASCADE,
    INDEX `idx_status_history_order` (`consignment_order_id`)
) ENGINE=InnoDB;

-- ============================================
-- 8. TRACKING
-- ============================================

CREATE TABLE `tracking_events` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `consignment_order_id` INT UNSIGNED NOT NULL,
    `event_type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `location` VARCHAR(255) DEFAULT NULL,
    `handler` VARCHAR(100) DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `event_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders`(`id`) ON DELETE CASCADE,
    INDEX `idx_tracking_order` (`consignment_order_id`),
    INDEX `idx_tracking_event_at` (`event_at`)
) ENGINE=InnoDB;

-- ============================================
-- 9. TRUCK TRIPS
-- ============================================

CREATE TABLE `truck_trips` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `trip_code` VARCHAR(30) NOT NULL UNIQUE,
    `truck_name` VARCHAR(100) DEFAULT NULL,
    `plate_number` VARCHAR(30) DEFAULT NULL,
    `route` VARCHAR(100) NOT NULL DEFAULT 'CN-VN',
    `origin_warehouse` VARCHAR(100) DEFAULT NULL,
    `destination_warehouse` VARCHAR(100) DEFAULT NULL,
    `loading_date` DATE DEFAULT NULL,
    `departure_date` DATE DEFAULT NULL,
    `estimated_arrival` DATE DEFAULT NULL,
    `actual_arrival` DATE DEFAULT NULL,
    `status` ENUM('draft','loading','departed','border_processing','arrived_vn','completed','issue') NOT NULL DEFAULT 'draft',
    `note` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_truck_status` (`status`),
    INDEX `idx_truck_code` (`trip_code`)
) ENGINE=InnoDB;

ALTER TABLE `consignment_orders`
    ADD FOREIGN KEY (`truck_trip_id`) REFERENCES `truck_trips`(`id`) ON DELETE SET NULL;

CREATE TABLE `truck_trip_status_histories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `truck_trip_id` INT UNSIGNED NOT NULL,
    `from_status` VARCHAR(30) DEFAULT NULL,
    `to_status` VARCHAR(30) NOT NULL,
    `note` TEXT DEFAULT NULL,
    `changed_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`truck_trip_id`) REFERENCES `truck_trips`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 10. DELIVERY ORDERS
-- ============================================

CREATE TABLE `delivery_orders` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `consignment_order_id` INT UNSIGNED NOT NULL,
    `delivery_code` VARCHAR(30) NOT NULL UNIQUE,
    `receiver_name` VARCHAR(100) NOT NULL,
    `receiver_phone` VARCHAR(20) NOT NULL,
    `receiver_address` TEXT NOT NULL,
    `receiver_city` VARCHAR(100) DEFAULT NULL,
    `receiver_district` VARCHAR(100) DEFAULT NULL,
    `receiver_ward` VARCHAR(100) DEFAULT NULL,
    `shipper_id` INT UNSIGNED DEFAULT NULL,
    `status` ENUM('waiting_delivery','assigned','delivering','delivered','delivery_failed','rescheduled','returned') NOT NULL DEFAULT 'waiting_delivery',
    `cod_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `delivery_fee` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `scheduled_date` DATE DEFAULT NULL,
    `delivered_at` DATETIME DEFAULT NULL,
    `note` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders`(`id`),
    INDEX `idx_delivery_consignment` (`consignment_order_id`),
    INDEX `idx_delivery_status` (`status`),
    INDEX `idx_delivery_shipper` (`shipper_id`)
) ENGINE=InnoDB;

CREATE TABLE `delivery_status_histories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `delivery_order_id` INT UNSIGNED NOT NULL,
    `from_status` VARCHAR(30) DEFAULT NULL,
    `to_status` VARCHAR(30) NOT NULL,
    `note` TEXT DEFAULT NULL,
    `changed_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`delivery_order_id`) REFERENCES `delivery_orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `delivery_proofs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `delivery_order_id` INT UNSIGNED NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `note` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`delivery_order_id`) REFERENCES `delivery_orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 11. PICKUP REQUESTS
-- ============================================

CREATE TABLE `pickup_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `consignment_order_id` INT UNSIGNED NOT NULL,
    `status` ENUM('requested','confirmed','scheduled','picked_up','missed','cancelled') NOT NULL DEFAULT 'requested',
    `scheduled_date` DATE DEFAULT NULL,
    `scheduled_time` TIME DEFAULT NULL,
    `note` TEXT DEFAULT NULL,
    `completed_at` DATETIME DEFAULT NULL,
    `handled_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`consignment_order_id`) REFERENCES `consignment_orders`(`id`),
    INDEX `idx_pickup_user` (`user_id`),
    INDEX `idx_pickup_status` (`status`)
) ENGINE=InnoDB;

CREATE TABLE `pickup_status_histories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pickup_request_id` INT UNSIGNED NOT NULL,
    `from_status` VARCHAR(30) DEFAULT NULL,
    `to_status` VARCHAR(30) NOT NULL,
    `note` TEXT DEFAULT NULL,
    `changed_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`pickup_request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- 12. SYSTEM BANK ACCOUNTS (for top-up)
-- ============================================

CREATE TABLE `system_bank_accounts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `bank_name` VARCHAR(100) NOT NULL,
    `account_number` VARCHAR(50) NOT NULL,
    `account_holder` VARCHAR(100) NOT NULL,
    `branch` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- 13. INSERT DEFAULT DATA
-- ============================================

-- Admin account (password: admin123)
INSERT INTO `users` (`username`, `email`, `phone`, `password_hash`, `role`, `status`, `email_verified`, `phone_verified`) VALUES
('admin', 'admin@nhaphangchina.com', '0900000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 1, 1);

INSERT INTO `user_profiles` (`user_id`, `full_name`) VALUES (1, 'Administrator');
INSERT INTO `wallets` (`user_id`, `balance`) VALUES (1, 0.00);

-- Default shipping rate
INSERT INTO `shipping_rates` (`route`, `cargo_type`, `rate_per_kg`, `min_weight`, `rounding_method`, `effective_from`, `is_active`, `created_by`) VALUES
('CN-VN', 'general', 25000.00, 0.50, '0.5kg', CURDATE(), 1, 1),
('CN-VN', 'fragile', 35000.00, 0.50, '0.5kg', CURDATE(), 1, 1),
('CN-VN', 'special', 45000.00, 0.50, '0.5kg', CURDATE(), 1, 1);

-- System bank accounts
INSERT INTO `system_bank_accounts` (`bank_name`, `account_number`, `account_holder`, `branch`) VALUES
('Vietcombank', '1234567890', 'CONG TY NHAP HANG CHINA', 'Ho Chi Minh'),
('Techcombank', '0987654321', 'CONG TY NHAP HANG CHINA', 'Ho Chi Minh');
