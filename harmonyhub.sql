-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2024 at 05:40 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `harmonyhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `product_image` varchar(255) DEFAULT NULL,
  `checkbox` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `description` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `description`) VALUES
(1, 'Acoustic'),
(2, 'Les Paul'),
(3, 'Stratocaster'),
(4, 'Telecaster'),
(5, 'Accessories');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` char(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `city` varchar(64) NOT NULL,
  `role_id` int(11) DEFAULT 2,
  `status_id` int(11) DEFAULT 1,
  `profilepicture` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `firstname`, `lastname`, `email`, `password`, `city`, `role_id`, `status_id`, `profilepicture`) VALUES
(58, 'Jemuel', 'malaga', 'mavilovesmary@gmail.com', '$2y$10$myzgpwCfGo77kM5Ihdg5Z.2R5hVUYrpVdhaiF1FNsdEBZUSNLx6uS', 'duhat', 1, 1, 'att.h52NTO4WmReyn9qQbCZcsvCzzFvRhHt8QRXucbHdTHg.jpg'),
(61, 'kai', 'neko', 'kai@gmail.com', '$2y$10$plWheJy6AX0eXYLv93MWheNXZGnrcNX8jGYDu5XqTVUsdp17UdGhm', 'pasig', 2, 1, NULL),
(62, 'kai', 'posa', 'pusa@gmail.com', '$2y$10$SaSix.AyiA/eTXNvN3AEsulygQze6sv3zLIXAswictGfZIB8DPFku', 'cainta', 1, 1, 'WIN_20240118_20_02_58_Pro.jpg'),
(64, 'gcash', 'kulay blue', 'gcash@gmail.com', '$2y$10$wL4mu5l2YQgmCn885/ohXeN8AsKN2LwgqU0HuqblSa8.XY3zois82', 'cellphone', 2, 1, '../media/profiles/674625f55d2d6_1732650485.jpg'),
(65, 'Korai', 'Hoshiumi', 'eban@gmail.com', '$2y$10$4isAP327CdGHjhyaH896wONvNKBpfV4NVY7qGQeidQ29Pgp9cQAeS', 'Tokyo', 2, 1, '../media/profiles/6746bd9aa6d5c_1732689306.jpg'),
(66, 'Gary', 'Tom', 'gary@gmail.com', '$2y$10$5TyvvmjS.Fsi7yRBCKvgKe88FHqu5K4yB1hcvv3kJ0YZ40t0gbxYq', 'Bikini Bottom', 2, 1, '../media/profiles/674850c7c6d79_1732792519.jpg'),
(67, 'Bruno', 'Piad', 'bruno@gmail.com', '$2y$10$MuRzFKUamqgtacKnUIFgUuCPSu9c8NpEQ8TWkwj6PHYkJDZ4wX4qS', 'Taguig', 2, 1, '../media/profiles/674874de45ce2_1732801758.jpg'),
(68, 'customer', 'one', 'customerone@gmail.com', '$2y$10$Hh8FfleSaCIN6pRhnZLlpOyAQxgbp5RFDyHuqepc2hhupeu51hf4i', 'taguig', 1, 1, '../media/profiles/674d72982fbdf_1733128856.png'),
(69, 'admin', 'user', 'admin@gmail.com', '$2y$10$rF8Da8f3ReATlWOaqfbTf.FAley7nc9gzxaYBRdqxbRU3NHRVmnmC', 'tup taguig', 1, 1, '../media/profiles/6750d8168fa0a_1733351446.png'),
(70, 'customer', 'user', 'customer@gmail.com', '$2y$10$DIJd4G388sOY/ybJDk3ZBOuLNulwKbqpiIlJzfP1qnkaog9D5CeyG', 'tup taguig', 2, 1, '../media/profiles/6750d88dbae0f_1733351565.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orderinfo`
--

CREATE TABLE `orderinfo` (
  `orderinfo_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_placed` date NOT NULL,
  `date_shipped` date DEFAULT NULL,
  `shipping` decimal(7,2) DEFAULT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderinfo`
--

INSERT INTO `orderinfo` (`orderinfo_id`, `customer_id`, `date_placed`, `date_shipped`, `shipping`, `status_id`) VALUES
(1, 67, '2024-12-05', NULL, NULL, 1),
(2, 67, '2024-12-05', NULL, NULL, 1),
(3, 67, '2024-12-05', NULL, NULL, 1),
(4, 67, '2024-12-05', NULL, NULL, 1),
(5, 67, '2024-12-05', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orderline`
--

CREATE TABLE `orderline` (
  `orderinfo_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderline`
--

INSERT INTO `orderline` (`orderinfo_id`, `prod_id`, `quantity`) VALUES
(1, 29, 1),
(2, 29, 1),
(3, 29, 1),
(4, 32, 4),
(5, 31, 4);

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

CREATE TABLE `order_status` (
  `os_id` int(11) NOT NULL,
  `description` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`os_id`, `description`) VALUES
(1, 'ordered'),
(2, 'shipping'),
(3, 'received'),
(4, 'cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_image` varchar(255) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `description`, `price`, `stock`, `category_id`, `product_image`) VALUES
(28, 'acoustic1', 1000.00, 100, 1, 'acoustic1.jfif'),
(29, 'acoustic2', 2000.00, 100, 1, 'acoustic2.jfif'),
(30, 'acoustic3', 3000.00, 100, 1, 'acoustic3.jfif'),
(31, 'acoustic4', 4000.00, 100, 1, 'acoustic4.jfif'),
(32, 'lespaul1', 1000.00, 100, 2, 'lespaul1.jfif'),
(33, 'lespaul2', 2000.00, 100, 2, 'lespaul2.jfif'),
(34, 'lespaul3', 3000.00, 100, 2, 'lespaul3.jfif'),
(35, 'lespaul4', 4000.00, 100, 2, 'lespaul4.jfif'),
(36, 'stratocaster1', 1000.00, 100, 3, 'stratocaster1.jfif'),
(37, 'stratocaster2', 2000.00, 100, 3, 'stratocaster2.jfif'),
(38, 'stratocaster3', 3000.00, 100, 3, 'stratocaster3.jfif'),
(39, 'stratocaster4', 4000.00, 100, 3, 'stratocaster4.jfif'),
(40, 'telecaster1', 1000.00, 100, 4, 'telecaster1.jfif'),
(41, 'telecaster2', 2000.00, 100, 4, 'telecaster2.jfif'),
(42, 'telecaster3', 3000.00, 100, 4, 'telecaster3.jfif'),
(43, 'telecaster4', 4000.00, 100, 4, 'telecaster4.jfif'),
(44, 'accessories1', 1000.00, 100, 5, 'accessories1.jfif'),
(45, 'accessories2', 2000.00, 100, 5, 'accessories2.jfif'),
(46, 'accessories3', 3000.00, 100, 5, 'accessories3.jfif'),
(47, 'accessories4', 4000.00, 100, 5, 'accessories4.jfif'),
(48, 'guitars', 12345.00, 100, 1, 'background.jfif');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `orderinfo_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `rating` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `orderinfo_id`, `product_id`, `customer_id`, `review_text`, `rating`, `created_at`) VALUES
(29, 3, 29, 67, '**** ang ganda', 3, '2024-12-05 15:46:26'),
(30, 4, 32, 67, '**** ang ganda beh', 2, '2024-12-05 15:47:43'),
(31, 5, 31, 67, 'pota ang mahal', 2, '2024-12-05 15:54:39');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `description` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `description`) VALUES
(1, 'Admin'),
(2, 'Customer');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_id` int(11) NOT NULL,
  `description` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `description`) VALUES
(1, 'Active'),
(2, 'Deactivated');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `orderinfo`
--
ALTER TABLE `orderinfo`
  ADD PRIMARY KEY (`orderinfo_id`);

--
-- Indexes for table `orderline`
--
ALTER TABLE `orderline`
  ADD PRIMARY KEY (`orderinfo_id`,`prod_id`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`os_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `reviews_ibfk_1` (`orderinfo_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=284;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `orderinfo`
--
ALTER TABLE `orderinfo`
  MODIFY `orderinfo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `os_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `customer_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`);

--
-- Constraints for table `orderline`
--
ALTER TABLE `orderline`
  ADD CONSTRAINT `orderline_ibfk_1` FOREIGN KEY (`orderinfo_id`) REFERENCES `orderinfo` (`orderinfo_id`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`orderinfo_id`) REFERENCES `orderinfo` (`orderinfo_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
