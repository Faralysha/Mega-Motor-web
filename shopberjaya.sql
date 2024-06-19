-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2024 at 10:47 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopberjaya`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_size` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_brand` varchar(255) DEFAULT NULL,
  `product_detail_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `order_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL,
  `product_size` varchar(50) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_brand` varchar(255) NOT NULL,
  `product_rate` int(10) DEFAULT NULL,
  `invoice_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `user_id`, `order_id`, `product_id`, `product_size`, `product_name`, `product_brand`, `product_rate`, `invoice_number`) VALUES
(67, 1, 85, 84, 'M', 'TSUNAMI', 'GRACSHAW', 5, '202406101342076231'),
(68, 1, 85, 93, 'FREE SIZE', 'BALACLAVA FULL FACE MASK', 'ALPINESTAR', 5, '202406101342076231'),
(70, 1, 87, 84, 'M', 'TSUNAMI', 'GRACSHAW', 5, '202406101700190200'),
(73, 1, 90, 92, 'NMAX', 'BRAKE CALIPER S-SERIES (ORANGE)', 'RCB', 4, '202406101710485151'),
(75, 1, 92, 93, 'FREE SIZE', 'BALACLAVA FULL FACE MASK', 'ALPINESTAR', 5, '202406120557267037'),
(83, 13, 105, 84, 'S', 'TSUNAMI', 'GRACSHAW', 4, '202406140710194680');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `pnumber` varchar(255) DEFAULT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `name`, `email`, `phone`, `pnumber`, `message`) VALUES
(10, 1, 'Eizrfan', 'user01@gmail.com', '0122552323', '0122552323', 'hi'),
(13, 13, 'Noraziean', 'user02@gmail.com', '0129806847', NULL, 'Great');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `total_payment` decimal(10,2) NOT NULL,
  `placed_on` varchar(50) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `tracknum` varchar(50) NOT NULL DEFAULT '',
  `status` int(11) DEFAULT 0,
  `invoice_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `total_payment`, `placed_on`, `payment_status`, `tracknum`, `status`, `invoice_number`) VALUES
(85, 1, 'Eizrfan', '01234567', 'user01@gmail.com', '', 'Lot 71, Jalan Haji Johari, Bukit Cheraka Meru, Klang, Selangor - 41050', 'GRACSHAW TSUNAMI[M](1)- ALPINESTAR BALACLAVA FULL FACE MASK[FREE SIZE](1)', 165.00, 0.00, '10-Jun-2024', 'Accepted', 'SPXMY037662019915', 1, '202406101342076231'),
(87, 1, 'Eizrfan', '01234567', 'user01@gmail.com', '', 'Lot 71, Jalan Haji Johari, Bukit Cheraka Meru, Klang, Selangor - 41050', 'GRACSHAW TSUNAMI[M](1)', 150.00, 0.00, '10-Jun-2024', 'Accepted', 'ERC804027102MY', 1, '202406101700190200'),
(90, 1, 'Eizrfan', '01234567', 'user01@gmail.com', '', 'Lot 71, Jalan Haji Johari, Bukit Cheraka Meru, Klang, Selangor - 41050', 'RCB BRAKE CALIPER S-SERIES (ORANGE)[NMAX](1)', 145.00, 0.00, '10-Jun-2024', 'Accepted', 'SPXMY035274699975', 1, '202406101710485151'),
(92, 1, 'Eizrfan', '01234567', 'user01@gmail.com', '', 'Lot 71, Jalan Haji Johari, Bukit Cheraka Meru, Klang, Selangor - 12345', 'ALPINESTAR BALACLAVA FULL FACE MASK[FREE SIZE](1)', 15.00, 0.00, '12-Jun-2024', 'Accepted', 'SPXMY037662019915', 1, '202406120557267037'),
(105, 13, 'user02', '012678345', 'user02@gmail.com', '', 'Lot 71, Jalan Haji Johari, Bukit Cheraka Meru, Klang, Selangor - 41050', 'GRACSHAW TSUNAMI[S](1)', 150.00, 155.00, '14-Jun-2024', 'Accepted', 'ERC804027102MY', 1, '202406140710194680');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_detail_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_detail_id`, `quantity`, `price`, `status`, `serial_number`) VALUES
(26, 85, 154, 1, 150.00, 1, '84-1-M'),
(27, 85, 195, 1, 15.00, 1, '93-2-FREE SIZE'),
(29, 87, 154, 1, 150.00, 1, '84-1-M'),
(32, 90, 191, 1, 145.00, 1, '92-1-NMAX'),
(34, 92, 195, 1, 15.00, 1, '93-1-FREE SIZE'),
(47, 105, 156, 1, 150.00, 1, '84-1-S');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(100) NOT NULL,
  `quant` int(11) NOT NULL,
  `pro_rates` int(10) DEFAULT 0,
  `stock` varchar(50) DEFAULT 'In Stock',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `brand`, `price`, `image`, `quant`, `pro_rates`, `stock`, `description`) VALUES
(84, 'TSUNAMI', 'helemets&visor', 'GRACSHAW', 150.00, 'pro5.jpg', -2, 5, 'In Stock', 'Best suited for cafe racer riders and easy riders, Geomax is equipped with various rider-centric features.\r\n\r\n• Material ABS plastic\r\n• Detachable sponge (Sponge can be purchased separately from store)\r\n• Visor locking (safety when high speed)\r\n• D-Ring Buckle\r\n• SIRIM Approval\r\n\r\n1 Set Earphone + Button (CONVOY TALK)\r\n*Support Zello & other apps\r\n*Can use as normal earphone to listen to music\r\n'),
(92, 'BRAKE CALIPER S-SERIES (ORANGE)', 'helemets&visor', 'RCB', 145.00, 'pro35.jpeg', 8, 4, 'In Stock', 'Racing Boy Brake Caliper made of hard alloy using latest forging technology. Added high strength and durability. Various shape and anodizing colours. \r\n\r\nFront brake caliper 2 piston S series\r\nWhat are the advantages of S series?\r\n- 2 pistons over brake grip\r\n- Ready bracket can straight away install on it, don\'t need any complicated modification \r\n- Free brake pads\r\n- Full alloy body that is stainless and color that does not fade easily\r\n- There are many colors can match the motorcycles '),
(93, 'BALACLAVA FULL FACE MASK', 'helemets&visor', 'ALPINESTAR', 15.00, 'pro19.jpg', 1, 5, 'In Stock', NULL),
(94, 'GEOMAX', 'helemets&visor', 'GRACSHAW', 60.00, 'pro2.png', 4, 5, 'In Stock', NULL),
(98, 'GAIZER PATROTIK', 'helemets&visor', 'GRACSHAW', 150.00, 'pro36.jpeg', 5, 5, 'In Stock', NULL),
(99, 'GRIXENT G818 FRONTLINER', 'helemets&visor', 'GRACSHAW', 170.50, 'pro3.jpg', 11, 0, 'In Stock', NULL),
(100, 'GRIXENT G818 AERO YELLOW', 'helemets&visor', 'GRACSHAW', 170.50, 'pro4.jpg', 10, 0, 'In Stock', NULL),
(102, 'RAINCOAT', 'riding&gears', 'YAMAHA', 40.00, 'pro13.jpg', 11, 0, 'In Stock', NULL),
(103, ' Riding Jacket Protector Jacket With Inner Padding Black', 'riding&gears', 'ALPINESTAR', 160.00, 'pro15.jpg', 8, 0, 'In Stock', NULL),
(104, 'PILOT STREET 2 TIRE', 'tires', 'MICHELIN', 127.00, 'pro26.jpg', 12, 0, 'In Stock', NULL),
(105, 'Y15ZR V2 Exhaust System Std Stainless Steel', 'exhaust', 'APIDO', 222.00, 'pro22.jpg', 3, 0, 'In Stock', NULL),
(106, 'GIVI BOX B32N', 'others', 'GIVI', 273.00, 'pro31.jpg', 5, 0, 'In Stock', NULL),
(107, 'GIVI BOX B32N', 'others', 'GIVI', 273.00, 'pro31.jpg', 5, 0, 'In Stock', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE `product_details` (
  `product_detail_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `stock` varchar(20) NOT NULL DEFAULT 'Available',
  `size` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_details`
--

INSERT INTO `product_details` (`product_detail_id`, `product_id`, `serial_number`, `stock`, `size`) VALUES
(154, 84, '84-00000', 'Sold', 'M'),
(155, 84, '84-00000', 'Available', 'M'),
(156, 84, '84-00000', 'Sold', 'S'),
(157, 84, '84-00000', 'Available', 'S'),
(158, 84, '84-00000', 'Available', 'S'),
(195, 93, '93-00000', 'Sold', 'FREE SIZE'),
(196, 93, '93-00000', 'Available', 'FREE SIZE'),
(197, 93, '93-00000', 'Available', 'FREE SIZE'),
(198, 93, '93-00000', 'Available', 'FREE SIZE'),
(203, 94, '94-00000', 'Sold', 'S'),
(204, 94, '94-00000', 'Available', 'S'),
(205, 94, '94-00000', 'Available', 'S'),
(206, 94, '94-00000', 'Available', 'S'),
(207, 94, '94-00000', 'Sold', 'M'),
(208, 94, '94-00000', 'Available', 'M'),
(209, 94, '94-00000', 'Available', 'M'),
(210, 92, '92-00000', 'Available', 'LC'),
(211, 92, '92-00000', 'Available', 'LC'),
(212, 92, '92-00000', 'Available', 'NMAX'),
(213, 92, '92-00000', 'Available', 'NMAX'),
(214, 92, '92-00000', 'Available', 'NMAX'),
(215, 92, '92-00000', 'Available', 'Y15ZR'),
(216, 92, '92-00000', 'Available', 'Y15ZR'),
(217, 92, '92-00000', 'Available', 'Y15ZR'),
(234, 98, '98-00000', 'Available', 'S'),
(235, 98, '98-00000', 'Available', 'S'),
(236, 98, '98-00000', 'Available', 'M'),
(237, 98, '98-00000', 'Available', 'M'),
(238, 98, '98-00000', 'Available', 'M'),
(239, 99, '99-00000', 'Available', 'M'),
(240, 99, '99-00000', 'Available', 'M'),
(241, 99, '99-00000', 'Available', 'M'),
(242, 99, '99-00000', 'Available', 'M'),
(243, 99, '99-00000', 'Available', 'M'),
(244, 99, '99-00000', 'Available', 'L'),
(245, 99, '99-00000', 'Available', 'L'),
(246, 99, '99-00000', 'Available', 'L'),
(247, 99, '99-00000', 'Available', 'XL'),
(248, 99, '99-00000', 'Available', 'XL'),
(249, 99, '99-00000', 'Available', 'XL'),
(250, 100, '100-00000', 'Available', 'M'),
(251, 100, '100-00000', 'Available', 'M'),
(252, 100, '100-00000', 'Available', 'L'),
(253, 100, '100-00000', 'Available', 'L'),
(254, 100, '100-00000', 'Available', 'L'),
(255, 100, '100-00000', 'Available', 'L'),
(256, 100, '100-00000', 'Available', 'L'),
(257, 100, '100-00000', 'Available', 'XL'),
(258, 100, '100-00000', 'Available', 'XL'),
(259, 100, '100-00000', 'Available', 'XL'),
(264, 102, '102-00000', 'Available', 'S'),
(265, 102, '102-00000', 'Available', 'S'),
(266, 102, '102-00000', 'Available', 'S'),
(267, 102, '102-00000', 'Available', 'M'),
(268, 102, '102-00000', 'Available', 'M'),
(269, 102, '102-00000', 'Available', 'M'),
(270, 102, '102-00000', 'Available', 'M'),
(271, 102, '102-00000', 'Available', 'L'),
(272, 102, '102-00000', 'Available', 'L'),
(273, 102, '102-00000', 'Available', 'L'),
(274, 102, '102-00000', 'Available', 'L'),
(275, 103, '103-00000', 'Available', 'S'),
(276, 103, '103-00000', 'Available', 'S'),
(277, 103, '103-00000', 'Available', 'M'),
(278, 103, '103-00000', 'Available', 'M'),
(279, 103, '103-00000', 'Available', 'M'),
(280, 103, '103-00000', 'Available', 'L'),
(281, 103, '103-00000', 'Available', 'L'),
(282, 103, '103-00000', 'Available', 'L'),
(283, 104, '104-00000', 'Available', '70/90-14'),
(284, 104, '104-00000', 'Available', '70/90-14'),
(285, 104, '104-00000', 'Available', '70/90-14'),
(286, 104, '104-00000', 'Available', '80/90-14'),
(287, 104, '104-00000', 'Available', '80/90-14'),
(288, 104, '104-00000', 'Available', '80/90-14'),
(289, 104, '104-00000', 'Available', '80/90-14'),
(290, 104, '104-00000', 'Available', '80/90-14'),
(291, 104, '104-00000', 'Available', '100/90-14'),
(292, 104, '104-00000', 'Available', '100/90-14'),
(293, 104, '104-00000', 'Available', '100/90-14'),
(294, 104, '104-00000', 'Available', '100/90-14'),
(295, 105, '105-00000', 'Available', 'Y15ZR'),
(296, 105, '105-00000', 'Available', 'Y15ZR'),
(297, 105, '105-00000', 'Available', 'Y15ZR'),
(298, 106, '106-00000', 'Available', 'B32N'),
(299, 106, '106-00000', 'Available', 'B32N'),
(300, 106, '106-00000', 'Available', 'B32N'),
(301, 106, '106-00000', 'Available', 'B32N'),
(302, 106, '106-00000', 'Available', 'B32N'),
(303, 107, '107-00000', 'Available', 'B32N'),
(304, 107, '107-00000', 'Available', 'B32N'),
(305, 107, '107-00000', 'Available', 'B32N'),
(306, 107, '107-00000', 'Available', 'B32N'),
(307, 107, '107-00000', 'Available', 'B32N');

--
-- Triggers `product_details`
--
DELIMITER $$
CREATE TRIGGER `set_serial_number` BEFORE INSERT ON `product_details` FOR EACH ROW BEGIN
  SET NEW.serial_number = CONCAT(NEW.product_id, '-', LPAD(NEW.product_detail_id, 5, '0'));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size`, `quantity`) VALUES
(134, 84, 'M', 2),
(135, 84, 'S', 3),
(151, 93, 'FREE SIZE', 4),
(153, 94, 'S', 4),
(154, 94, 'M', 3),
(155, 92, 'LC', 2),
(156, 92, 'NMAX', 3),
(157, 92, 'Y15ZR', 3),
(164, 98, 'S', 2),
(165, 98, 'M', 3),
(166, 99, 'M', 5),
(167, 99, 'L', 3),
(168, 99, 'XL', 3),
(169, 100, 'M', 2),
(170, 100, 'L', 5),
(171, 100, 'XL', 3),
(173, 102, 'S', 3),
(174, 102, 'M', 4),
(175, 102, 'L', 4),
(176, 103, 'S', 2),
(177, 103, 'M', 3),
(178, 103, 'L', 3),
(179, 104, '70/90-14', 3),
(180, 104, '80/90-14', 5),
(181, 104, '100/90-14', 4),
(182, 105, 'Y15ZR', 3),
(183, 106, 'B32N', 5),
(184, 107, 'B32N', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'user',
  `pnumber` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `pnumber`) VALUES
(1, 'Eizrfan', 'user01@gmail.com', 'd856cb5610edb80e3a666a7472013da1', 'user', '0122552323'),
(6, 'Faralysha', 'nfaralysha@gmail.com', '78a292cb70eefdf9e05ba97656cdad3d', 'admin', '01139909076'),
(9, 'Staff1', 'staff1@gmail.com', '4d7d719ac0cf3d78ea8a94701913fe47', 'staff', '0122552323'),
(13, 'user02', 'user02@gmail.com', '8bd108c8a01a892d129c52484ef97a0d', 'user', '0129806847');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`product_detail_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `product_detail_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `history_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `product_details`
--
ALTER TABLE `product_details`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `product_details_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
