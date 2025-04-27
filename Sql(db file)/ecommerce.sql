-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 27, 2025 at 04:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `description`, `logo`, `created_at`) VALUES
(1, 'Apple', 'Premium electronics and devices', NULL, '2025-04-26 11:30:53'),
(2, 'Samsung', 'Electronics and home appliances', NULL, '2025-04-26 11:30:53'),
(3, 'Nike', 'Sports and casual wear', NULL, '2025-04-26 11:30:53'),
(4, 'Adidas', 'Sports and casual wear', NULL, '2025-04-26 11:30:53'),
(5, 'Sony', 'Electronics and entertainment', NULL, '2025-04-26 11:30:53'),
(6, 'LG', 'Home appliances and electronics', NULL, '2025-04-26 11:30:53'),
(7, 'Levi\'s', 'Denim and casual wear', NULL, '2025-04-26 11:30:53'),
(8, 'Calvin Klein', 'Fashion and accessories', NULL, '2025-04-26 11:30:53'),
(9, 'Dyson', 'Home appliances and cleaning', NULL, '2025-04-26 11:30:53'),
(10, 'KitchenAid', 'Kitchen appliances and tools', NULL, '2025-04-26 11:30:53'),
(11, 'Nespresso', 'Coffee machines and accessories', NULL, '2025-04-26 11:30:53'),
(12, 'Cuisinart', 'Kitchen appliances and cookware', NULL, '2025-04-26 11:30:53'),
(13, 'North Face', 'Outdoor clothing and equipment', NULL, '2025-04-26 11:30:53'),
(14, 'Instant Pot', 'Kitchen appliances and cookware', NULL, '2025-04-26 11:30:53'),
(15, 'Dell', 'Computers and electronics', NULL, '2025-04-26 11:30:53'),
(16, 'Penguin Books', 'Publishing company', NULL, '2025-04-26 11:30:53'),
(17, 'Bloomsbury', 'Publishing company', NULL, '2025-04-26 11:30:53'),
(18, 'HarperCollins', 'Publishing company', NULL, '2025-04-26 11:30:53'),
(19, 'Lululemon', 'Athletic apparel', NULL, '2025-04-26 11:30:53'),
(20, 'Under Armour', 'Sports apparel and accessories', NULL, '2025-04-26 11:30:53'),
(21, 'Reebok', 'Sports and fitness products', NULL, '2025-04-26 11:30:53'),
(22, 'Columbia', 'Outdoor clothing and equipment', NULL, '2025-04-26 11:30:53'),
(23, 'Patagonia', 'Outdoor clothing and equipment', NULL, '2025-04-26 11:30:53'),
(24, 'L\'Oreal', 'Beauty and cosmetics', NULL, '2025-04-26 11:30:53'),
(25, 'Estee Lauder', 'Beauty and cosmetics', NULL, '2025-04-26 11:30:53'),
(26, 'LEGO', 'Toys and building sets', NULL, '2025-04-26 11:30:53'),
(27, 'Hasbro', 'Toys and games', NULL, '2025-04-26 11:30:53'),
(28, 'Mattel', 'Toys and games', NULL, '2025-04-26 11:30:53'),
(29, 'Bosch', 'Automotive and power tools', NULL, '2025-04-26 11:30:53'),
(30, '3M', 'Automotive and industrial products', NULL, '2025-04-26 11:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Electronics', 'Electronic devices and accessories including smartphones, laptops, and home appliances', '2025-04-26 11:30:53'),
(2, 'Clothing', 'Fashion and apparel for men, women, and children', '2025-04-26 11:30:53'),
(3, 'Home & Kitchen', 'Home appliances, kitchenware, and home decor items', '2025-04-26 11:30:53'),
(4, 'Books', 'Books, educational materials, and stationery', '2025-04-26 11:30:53'),
(5, 'Sports & Outdoors', 'Sports equipment, outdoor gear, and fitness accessories', '2025-04-26 11:30:53'),
(6, 'Beauty & Health', 'Cosmetics, skincare, and health products', '2025-04-26 11:30:53'),
(7, 'Toys & Games', 'Toys, board games, and entertainment products', '2025-04-26 11:30:53'),
(8, 'Automotive', 'Car accessories, tools, and maintenance products', '2025-04-26 11:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_name` varchar(100) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_state` varchar(100) NOT NULL,
  `shipping_zip` varchar(20) NOT NULL,
  `shipping_country` varchar(100) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_name`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_zip`, `shipping_country`, `payment_method`, `status`, `created_at`) VALUES
(1, 1, 999.99, 'ankit suthar', 'SUTHARO KI GHATI ,MUKAM ,POST OBRI,TH.SAGWARA', 'rajasthan', 'Rajasthan', '314401', 'India', 'credit_card', 'pending', '2025-04-26 11:31:58');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 1, 1, 999.99, '2025-04-26 11:31:58');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `is_member_exclusive` tinyint(1) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `member_price`, `is_member_exclusive`, `image`, `category_id`, `brand_id`, `sku`, `weight`, `created_at`) VALUES
(1, 'iPhone 13 Pro', '6.1-inch Super Retina XDR display, A15 Bionic chip, Pro camera system', 999.99, 444.00, 0, '680ccd55f2d34.jpeg', 1, 1, 'IP13P-001', 0.20, '2025-04-26 11:30:53'),
(2, 'Samsung Galaxy S21', '6.2-inch Dynamic AMOLED display, Exynos 2100, Triple camera system', 799.99, NULL, 0, '680cd1707c27c.jpeg', 1, 2, 'SGS21-001', 0.17, '2025-04-26 11:30:53'),
(3, 'MacBook Pro M1', '13-inch Retina display, Apple M1 chip, 8GB RAM, 256GB SSD', 1299.99, NULL, 0, '680cd1dd83f2d.jpeg', 1, 1, 'MBP-001', 1.40, '2025-04-26 11:30:53'),
(4, 'Sony WH-1000XM4', 'Industry-leading noise cancellation, 30-hour battery life, Hi-Res Audio', 349.99, NULL, 0, '680cd2521287e.jpeg', 1, 5, 'WHXM4-001', 0.25, '2025-04-26 11:30:53'),
(5, 'Samsung 65\" QLED TV', '4K UHD resolution, Quantum HDR, Smart TV with Bixby', 1499.99, NULL, 0, '680cd2a08a057.jpeg', 1, 2, 'QLED65-001', 28.00, '2025-04-26 11:30:53'),
(6, 'Nike Air Max 270', 'Lightweight and comfortable running shoes with Max Air cushioning', 150.00, NULL, 0, '680cd2c58575a.jpeg', 2, 3, 'NAX270-001', 0.50, '2025-04-26 11:30:53'),
(7, 'Adidas Originals T-Shirt', 'Classic cotton t-shirt with iconic three stripes design', 29.99, NULL, 0, '680cd2fb5db25.jpeg', 2, 4, 'ADTS-001', 0.20, '2025-04-26 11:30:53'),
(8, 'Levi\'s 501 Original Jeans', 'Classic straight fit jeans in authentic denim', 69.99, NULL, 0, '680cd31f625c8.jpeg', 2, 7, 'LV501-001', 0.60, '2025-04-26 11:30:53'),
(9, 'North Face Jacket', 'Waterproof and windproof jacket with breathable membrane', 199.99, NULL, 0, '680cd33f965a3.jpeg', 2, 13, 'NFJ-001', 0.80, '2025-04-26 11:30:53'),
(10, 'Calvin Klein Underwear Set', '3-pack of comfortable cotton boxer briefs', 39.99, NULL, 0, '680cd363ae0a9.jpeg', 2, 8, 'CKU-001', 0.30, '2025-04-26 11:30:53'),
(11, 'Instant Pot Duo', '7-in-1 pressure cooker, slow cooker, rice cooker, and more', 99.99, NULL, 0, '680cd383d5df4.jpeg', 3, 14, 'IPD-001', 5.00, '2025-04-26 11:30:53'),
(12, 'KitchenAid Stand Mixer', 'Professional 5-quart stand mixer with 10 speeds', 399.99, NULL, 0, '680cd3a82af71.jpeg', 3, 10, 'KSM-001', 12.00, '2025-04-26 11:30:53'),
(13, 'Dyson V11 Vacuum', 'Cordless vacuum with powerful suction and LCD screen', 599.99, NULL, 0, '680cd3c718335.jpeg', 3, 9, 'DV11-001', 6.00, '2025-04-26 11:30:53'),
(14, 'Nespresso Vertuo', 'Coffee machine with centrifusion technology, 5 cup sizes', 199.99, NULL, 0, '680cd3e742aa9.jpeg', 3, 11, 'NV-001', 3.50, '2025-04-26 11:30:53'),
(15, 'Cuisinart Food Processor', '14-cup food processor with stainless steel blades', 199.99, NULL, 0, '680cd40b88e19.jpeg', 3, 12, 'CFP-001', 4.00, '2025-04-26 11:30:53'),
(16, 'The Great Gatsby', 'Classic novel by F. Scott Fitzgerald', 12.99, NULL, 0, '680cd43f77dfa.jpeg', 4, 15, 'BGG-001', 0.30, '2025-04-26 11:30:53'),
(17, 'Atomic Habits', 'An Easy & Proven Way to Build Good Habits & Break Bad Ones', 16.99, NULL, 0, '680cd460e1a3d.jpeg', 4, 16, 'BAH-001', 0.40, '2025-04-26 11:30:53'),
(18, 'Harry Potter Box Set', 'Complete collection of all 7 Harry Potter books', 89.99, NULL, 0, '680cd47f784cd.jpeg', 4, 17, 'BHP-001', 3.50, '2025-04-26 11:30:53'),
(19, 'Sapiens: A Brief History of Humankind', 'Exploring the history of our species', 19.99, NULL, 0, '680cd49e5ac94.png', 4, 16, 'BSH-001', 0.50, '2025-04-26 11:30:53'),
(20, 'The Alchemist', 'International bestseller by Paulo Coelho', 14.99, NULL, 0, '680cd4bde3c37.jpeg', 4, 15, 'BAL-001', 0.30, '2025-04-26 11:30:53'),
(21, 'Yoga Mat', 'Non-slip, eco-friendly yoga mat with carrying strap', 29.99, NULL, 0, '680cd4dda3794.jpeg', 5, 19, 'SOYM-001', 1.00, '2025-04-26 11:30:53'),
(22, 'Dumbbell Set', 'Adjustable dumbbells from 5-25 lbs', 149.99, NULL, 0, '680cd50ec795e.jpeg', 5, 20, 'SODB-001', 12.00, '2025-04-26 11:30:53'),
(23, 'Tent 4-Person', 'Waterproof camping tent with rainfly', 129.99, NULL, 0, '680cd537a5588.jpeg', 5, 22, 'SOT-001', 8.00, '2025-04-26 11:30:53'),
(24, 'Bicycle Helmet', 'Lightweight, ventilated helmet with MIPS technology', 59.99, NULL, 0, '680cd55499918.jpeg', 5, 23, 'SOH-001', 0.40, '2025-04-26 11:30:53'),
(25, 'Running Shoes', 'Cushioned running shoes with breathable mesh', 89.99, NULL, 0, '680cd5773a01f.jpeg', 5, 21, 'SORS-001', 0.60, '2025-04-26 11:30:53'),
(26, 'Vitamin C Serum', 'Brightening serum with 20% vitamin C', 29.99, NULL, 0, '680cd597b85b3.jpeg', 6, 24, 'BHVC-001', 0.10, '2025-04-26 11:30:53'),
(27, 'Electric Toothbrush', 'Sonic toothbrush with 3 cleaning modes', 79.99, NULL, 0, '680cd5b664096.jpeg', 6, 25, 'BHET-001', 0.30, '2025-04-26 11:30:53'),
(28, 'Hair Dryer', 'Ionic hair dryer with multiple heat settings', 49.99, NULL, 0, '680cd5d6d0884.jpeg', 6, 24, 'BHHD-001', 0.70, '2025-04-26 11:30:53'),
(29, 'Face Mask Set', '5-piece sheet mask set for different skin concerns', 19.99, NULL, 0, '680cd5f5d3306.jpeg', 6, 25, 'BHFM-001', 0.20, '2025-04-26 11:30:53'),
(30, 'Perfume Set', '3-piece mini perfume set with different scents', 39.99, NULL, 0, '680cd6152fbb8.jpeg', 6, 25, 'BHPS-001', 0.30, '2025-04-26 11:30:53'),
(31, 'LEGO Star Wars Set', 'Millennium Falcon building set with minifigures', 159.99, NULL, 0, '680cd638ad16e.jpeg', 7, 26, 'TGLG-001', 1.50, '2025-04-26 11:30:53'),
(32, 'Board Game Collection', 'Classic board games including Monopoly and Scrabble', 49.99, NULL, 0, '680cd6543b933.jpeg', 7, 27, 'TGBG-001', 2.00, '2025-04-26 11:30:53'),
(33, 'Remote Control Car', '1:14 scale RC car with 2.4GHz remote', 39.99, NULL, 0, '680cd6745fb7c.jpeg', 7, 28, 'TGRC-001', 0.80, '2025-04-26 11:30:53'),
(34, 'Jigsaw Puzzle', '1000-piece landscape puzzle', 19.99, NULL, 0, '680cd6bfb2930.jpeg', 7, 27, 'TGPZ-001', 0.50, '2025-04-26 11:30:53'),
(35, 'Building Blocks Set', '100-piece colorful building blocks', 29.99, NULL, 0, '680cd6dd3a828.jpeg', 7, 26, 'TGBB-001', 1.00, '2025-04-26 11:30:53'),
(36, 'Car Phone Mount', 'Magnetic phone mount for car dashboard', 19.99, NULL, 0, '680cd6f62ca33.jpeg', 8, 29, 'ACPM-001', 0.10, '2025-04-26 11:30:53'),
(37, 'Jump Starter', 'Portable car jump starter with USB ports', 89.99, NULL, 0, '680cd71036865.jpeg', 8, 29, 'ACJS-001', 1.20, '2025-04-26 11:30:53'),
(38, 'Car Vacuum', 'Cordless car vacuum with LED light', 49.99, NULL, 0, '680cd72a39ec7.jpeg', 8, 29, 'ACCV-001', 0.80, '2025-04-26 11:30:53'),
(39, 'Tire Pressure Gauge', 'Digital tire pressure gauge with backlight', 14.99, NULL, 0, '680cd7467cd43.jpeg', 8, 30, 'ACTP-001', 0.10, '2025-04-26 11:30:53'),
(40, 'Car Air Freshener', 'Set of 3 natural car air fresheners', 9.99, NULL, 0, 'air-freshener.jpg', 8, 30, 'ACAF-001', 0.20, '2025-04-26 11:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_member` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_member`, `created_at`, `is_admin`) VALUES
(1, 'ankit suthar', 'ankitstr25@gmail.com', '$2y$10$TOo0z0ucqmcXevJs1kwGeeJPrfgjM/A4b9ilVKKxN8oFVL8jA9IJu', 1, '2025-04-26 11:31:40', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
