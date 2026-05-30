-- ============================================================
-- NETANIS JEWELOS — Complete Database Schema v2.0
-- ============================================================

CREATE DATABASE IF NOT EXISTS `premium_jewellery` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `premium_jewellery`;

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `gold_rates`;
DROP TABLE IF EXISTS `users`;

-- Users
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(200) NOT NULL,
  `email` VARCHAR(200) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('customer','admin','staff') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Gold & Diamond Rates
CREATE TABLE `gold_rates` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `rate_24kt` DECIMAL(10,2) NOT NULL,
  `rate_22kt` DECIMAL(10,2) NOT NULL,
  `rate_18kt` DECIMAL(10,2) NOT NULL,
  `rate_14kt` DECIMAL(10,2) NOT NULL,
  `silver_rate` DECIMAL(10,2) NOT NULL,
  `diamond_rate_per_ct` DECIMAL(10,2) NOT NULL DEFAULT 25000.00,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products (with diamond fields)
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_name` VARCHAR(255) NOT NULL,
  `sku` VARCHAR(100) NOT NULL UNIQUE,
  `short_description` TEXT,
  `full_description` LONGTEXT,
  `category` VARCHAR(100) DEFAULT NULL,
  `subcategory` VARCHAR(100) DEFAULT NULL,
  `collection_tag` VARCHAR(100) DEFAULT NULL,
  `metal_type` ENUM('Gold','Silver','Platinum') DEFAULT 'Gold',
  `purity` ENUM('24Kt','22Kt','18Kt','14Kt') DEFAULT '22Kt',
  `weight_grams` DECIMAL(10,3) NOT NULL DEFAULT 0,
  `making_charges_per_gram` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `making_discount_percent` DECIMAL(5,2) DEFAULT 0,
  `has_diamond` TINYINT(1) DEFAULT 0,
  `diamond_carat` DECIMAL(8,3) DEFAULT 0,
  `diamond_discount_percent` DECIMAL(5,2) DEFAULT 0,
  `discount_percent` DECIMAL(5,2) DEFAULT 0,
  `primary_image` TEXT DEFAULT NULL,
  `extra_images` TEXT DEFAULT NULL,
  `product_details_json` TEXT DEFAULT NULL,
  `gold_details_json` TEXT DEFAULT NULL,
  `diamond_details_json` TEXT DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `total_amount` DECIMAL(12,2) NOT NULL,
  `gst_amount` DECIMAL(12,2) DEFAULT 0,
  `order_status` ENUM('Pending','Confirmed','Packed','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `payment_status` ENUM('Unpaid','Paid','Refunded') DEFAULT 'Unpaid',
  `shipping_name` VARCHAR(200) DEFAULT NULL,
  `shipping_phone` VARCHAR(20) DEFAULT NULL,
  `shipping_address` TEXT DEFAULT NULL,
  `shipping_city` VARCHAR(100) DEFAULT NULL,
  `shipping_state` VARCHAR(100) DEFAULT NULL,
  `shipping_pincode` VARCHAR(10) DEFAULT NULL,
  `owner_notified` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price_at_purchase` DECIMAL(12,2) NOT NULL,
  `snapshot_json` TEXT DEFAULT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wishlist
CREATE TABLE `wishlist` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO `users` (`id`,`full_name`,`email`,`password_hash`,`phone`,`role`)
VALUES (1,'Guest User','guest@netanis.com','$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX','0000000000','customer');

-- Admin: admin@netanis.com / admin123
INSERT INTO `users` (`full_name`,`email`,`password_hash`,`phone`,`role`)
VALUES ('Netanis Admin','admin@netanis.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','9999999999','admin');

INSERT INTO `gold_rates` (`rate_24kt`,`rate_22kt`,`rate_18kt`,`rate_14kt`,`silver_rate`,`diamond_rate_per_ct`)
VALUES (7450.00,6820.00,5580.00,4340.00,94.00,25000.00);

INSERT INTO `products` (`product_name`,`sku`,`short_description`,`full_description`,`category`,`subcategory`,`collection_tag`,`metal_type`,`purity`,`weight_grams`,`making_charges_per_gram`,`making_discount_percent`,`has_diamond`,`diamond_carat`,`diamond_discount_percent`,`discount_percent`,`primary_image`,`product_details_json`,`gold_details_json`,`diamond_details_json`) VALUES

('3 Heart Diamond Mangalsutra','T-MNG-2026-001','Elegant 3-heart design mangalsutra with diamonds in 18Kt gold.','A breathtaking mangalsutra featuring three small heart-shaped diamond clusters set in 18Kt yellow gold. Perfect for daily wear and special occasions.','Necklaces','Mangalsutra','Heritage Hearts','Gold','18Kt',3.24,1250.00,20,1,0.37,20,0,'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?q=80&w=800','{"Product Type":"Mangalsutra","Occasion":"Wedding, Festive","Style":"Traditional-Modern","Closure":"Adjustable Chain"}','{"Metal":"18Kt Yellow Gold","Purity":"75% Pure Gold","Hallmark":"BIS Certified","Colour":"Yellow"}','{"Diamond Type":"Natural","Clarity":"SI","Colour":"GH","Cut":"Round Brilliant","No. of Diamonds":12}'),

('Carelyn Diamond Ring','T-RNG-2026-002','Stunning cluster diamond ring in 18Kt gold.','The Carelyn ring features a spectacular cluster of round brilliant diamonds set in an intricate 18Kt gold halo design. IGI certified diamonds.','Rings','Diamond Rings','Solitaire Dreams','Gold','18Kt',2.28,1500.00,20,1,1.23,20,0,'https://images.unsplash.com/photo-1605100804763-247f67b3557e?q=80&w=800','{"Product Type":"Ring","Occasion":"Engagement, Anniversary","Style":"Cluster Halo","Size":"Customizable"}','{"Metal":"18Kt Yellow Gold","Purity":"75% Pure Gold","Hallmark":"BIS Certified","Colour":"Yellow"}','{"Diamond Type":"Natural","Clarity":"VS","Colour":"EF","Cut":"Round Brilliant","No. of Diamonds":35}'),

('Bloom Bud Gold Ring','T-RNG-2026-003','Delicate floral ring in 22Kt yellow gold.','Handcrafted with precision, this floral-inspired ring in 22Kt gold is perfect for daily wear. Features leaf motif with polished finish.','Rings','Gold Rings','Summer Stack','Gold','22Kt',5.25,499.00,0,0,0,0,10,'https://images.unsplash.com/photo-1583947215259-38e31be8751f?q=80&w=800','{"Product Type":"Ring","Occasion":"Daily Wear, Festive","Style":"Floral","Size":"Customizable"}','{"Metal":"22Kt Yellow Gold","Purity":"91.6% Pure Gold","Hallmark":"BIS Certified","Colour":"Yellow"}',NULL),

('Royal Heritage Necklace','T-NCK-2026-004','Ornate 22Kt gold necklace with traditional motifs.','A stunning heritage necklace crafted in 22Kt gold with traditional motifs. Perfect for weddings and festive occasions. Comes with matching chain.','Necklaces','Gold Necklaces','Royal Heritage','Gold','22Kt',18.50,450.00,0,0,0,0,5,'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?q=80&w=800','{"Product Type":"Necklace","Occasion":"Wedding, Festive","Style":"Traditional","Length":"18 inches"}','{"Metal":"22Kt Yellow Gold","Purity":"91.6% Pure Gold","Hallmark":"BIS Certified","Colour":"Yellow"}',NULL),

('Diamond Studs Classic','T-EAR-2026-005','Timeless solitaire diamond studs in 18Kt white gold.','Classic round brilliant diamond studs in 18Kt white gold. IGI certified, perfect for everyday luxury and gifting.','Earrings','Diamond Earrings','Eternal Solitaire','Gold','18Kt',1.80,1200.00,0,1,0.50,0,0,'https://images.unsplash.com/photo-1535632787350-4e68ef0ac584?q=80&w=800','{"Product Type":"Earrings","Occasion":"Daily Wear, Gifting","Style":"Solitaire Stud","Closure":"Push Back"}','{"Metal":"18Kt White Gold","Purity":"75% Pure Gold","Hallmark":"BIS Certified","Colour":"White"}','{"Diamond Type":"Natural","Clarity":"VS2","Colour":"EF","Cut":"Round Brilliant","No. of Diamonds":2}'),

('Classic Gold Bangle','T-BNG-2026-006','Traditional round bangle in 22Kt gold.','A classic round bangle in pure 22Kt gold. Available in standard sizes. Perfect for daily wear and gifting.','Bangles','Gold Bangles','Timeless Classics','Gold','22Kt',12.00,400.00,0,0,0,0,8,'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?q=80&w=800','{"Product Type":"Bangle","Occasion":"Daily Wear, Festive","Style":"Plain Round","Size":"2.4, 2.6, 2.8 inches"}','{"Metal":"22Kt Yellow Gold","Purity":"91.6% Pure Gold","Hallmark":"BIS Certified","Colour":"Yellow"}',NULL);
