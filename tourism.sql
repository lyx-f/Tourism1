-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2025 at 03:15 AM
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
-- Database: `tourism`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `status` enum('Accepted','Rejected','Pending','Cancelled') NOT NULL,
  `time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `booking_id`, `status`, `time`) VALUES
(45, 38, 'Pending', '2025-02-14 14:15:33'),
(46, 38, 'Accepted', '2025-02-14 14:15:57'),
(47, 38, 'Cancelled', '2025-02-14 14:16:29'),
(48, 37, 'Cancelled', '2025-02-14 14:23:57'),
(49, 39, 'Pending', '2025-02-14 14:25:45'),
(50, 39, 'Accepted', '2025-02-14 14:26:01'),
(51, 40, 'Pending', '2025-02-14 15:43:56');

-- --------------------------------------------------------

--
-- Table structure for table `admin_codes`
--

CREATE TABLE `admin_codes` (
  `id` int(11) NOT NULL,
  `admin_code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_codes`
--

INSERT INTO `admin_codes` (`id`, `admin_code`, `email`, `is_used`) VALUES
(25, '814871b5', 'racoonracoonra@gmail.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `departure_date` date NOT NULL,
  `arrival_date` date NOT NULL,
  `guests` int(11) NOT NULL,
  `room_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Accepted','Rejected','Cancelled') DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `first_name`, `last_name`, `email`, `phone`, `departure_date`, `arrival_date`, `guests`, `room_type`, `created_at`, `status`, `business_id`, `user_id`) VALUES
(37, 'Enderio', 'Enderio', 'enderio69@gmail.com', '0912345678', '2025-02-15', '2025-02-14', 1, 'single', '2025-02-14 13:13:24', 'Cancelled', 443, 27),
(38, 'Enderio', 'Enderio', 'enderio69@gmail.com', '09123456789', '2025-02-15', '2025-02-14', 1, 'single', '2025-02-14 13:15:33', 'Cancelled', 444, 27),
(39, 'Enderio', 'Enderio', 'enderio69@gmail.com', '12345678', '2025-02-15', '2025-02-14', 1, 'single', '2025-02-14 13:25:45', 'Accepted', 444, 27),
(40, 'enderio', 'enderio', 'enderio69@gmail.com', '09510312859', '2025-02-15', '2025-02-14', 1, 'single', '2025-02-14 14:43:56', 'Pending', 444, 27);

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT 'jessaenderio97@gmail.com',
  `phone` varchar(40) NOT NULL DEFAULT '""',
  `name` varchar(255) NOT NULL,
  `location` text NOT NULL DEFAULT '""',
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `image_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(40) NOT NULL DEFAULT '""',
  `category` varchar(40) NOT NULL DEFAULT '""',
  `user_id` int(11) NOT NULL DEFAULT 21
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `email`, `phone`, `name`, `location`, `description`, `price`, `image_url`, `created_at`, `status`, `category`, `user_id`) VALUES
(443, 'joyeeblanco@gmail.com', '9939502417', 'DUDAY CAFE', 'Maragusan ', 'wala lang', 1.00, '1346530.jpeg', '2025-01-21 13:31:43', '\"\"', '\"\"', 11),
(444, 'racoonracoonra@gmail.com', '09123456789', 'Dinagsaan', 'Banaybanay', 'Beautiful mountain', 1000.00, 'IMG_20240420_231233_869.jpg', '2025-02-14 12:25:50', '\"\"', '\"\"', 26);

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `category` enum('attractions','inns','hotels','food') NOT NULL DEFAULT 'attractions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `latitude`, `longitude`, `status`, `category`) VALUES
(10, 'Cabana de Playa', 6.90045364, 126.29257847, 'inactive', 'attractions'),
(11, 'Dahican C La Cabaña cabin rental', 6.92174995, 126.28100117, 'active', 'attractions'),
(12, 'Subangan Tides Resort', 6.90016913, 126.29253838, 'active', 'attractions'),
(13, 'Sabangan Beach', 6.34413657, 126.19777161, 'active', 'attractions'),
(14, 'Biao Beach Resort', 6.36627482, 126.19981310, 'active', 'attractions'),
(15, 'CyCar Beach Resort', 6.35311822, 126.19676136, 'active', 'attractions'),
(16, 'Lanca Shorline Campsite', 6.34559853, 126.19799141, 'active', 'attractions'),
(17, 'LiannaDavid\'s Campsite', 6.33857467, 126.19897762, 'active', 'attractions'),
(18, 'Little Bora Campsite', 6.34106329, 126.19876541, 'active', 'attractions'),
(19, 'Pontana Beach House', 6.33987173, 126.19903603, 'active', 'attractions'),
(20, 'Payag ni Jose Beach Resort', 6.33715404, 126.19990609, 'active', 'attractions'),
(21, 'Tiny Hauz Beach Lanca', 6.34269840, 126.19819950, 'active', 'attractions'),
(22, 'Yolly Beach Resort', 6.33914498, 126.19893705, 'active', 'attractions'),
(23, 'An-Fran\'s Dahican Camping Site', 6.92626073, 126.28059968, 'active', 'attractions'),
(24, 'Belacita Dahican Beach House', 6.91735658, 126.28418332, 'active', 'attractions'),
(25, 'Bungalow Vacation Rentals', 6.91701896, 126.28397459, 'active', 'attractions'),
(26, 'Cala Beach Resort', 6.88595181, 126.27733984, 'active', 'attractions'),
(27, 'Casa Blanca Mati', 6.88594919, 126.27781251, 'active', 'attractions'),
(28, 'Botona Beach Resort', 6.92119918, 126.28150105, 'active', 'attractions'),
(29, 'Dahican Surf Resort', 6.92597759, 126.28055118, 'active', 'attractions'),
(30, 'Darrporrt Campsite', 6.78870646, 126.22195077, 'active', 'attractions'),
(31, 'D\'Dos Andanas Beach House', 6.92199725, 126.28071597, 'active', 'attractions'),
(32, 'Wind & Waves Beach Resort', 6.88321540, 126.31729595, 'active', 'attractions'),
(33, 'Franco Beach House', 6.92190540, 126.28036572, 'active', 'attractions'),
(34, 'GREGORIO-DITA WHITE SAND BEACH RESORT', 6.86804242, 126.29607852, 'active', 'attractions'),
(35, 'MASAO View Resort and Spa', 6.86677899, 126.29887237, 'active', 'attractions'),
(36, 'Toms Hometel', 6.95884879, 126.21001151, 'active', 'hotels'),
(37, 'YKG hotel', 6.94411499, 126.24134242, 'active', 'hotels'),
(38, 'PUJADA ISLANDTOUR/PIER ONE', 6.84531600, 126.28684676, 'active', 'attractions'),
(39, 'La-nes Kapehan', 6.92154916, 126.28075594, 'active', 'attractions'),
(40, 'Baywalk Hotel', 6.95042527, 126.21851499, 'active', 'hotels'),
(41, 'Casa Rosa', 6.95530784, 126.20929154, 'active', 'attractions'),
(42, 'Budget Inn', 6.95078760, 126.22336955, 'active', 'inns'),
(43, 'D\' Eterna Dormitel', 6.96003559, 126.21967433, 'active', 'hotels'),
(44, 'Hotel Rosario Mati', 6.95686189, 126.21494916, 'active', 'hotels'),
(45, 'HRSS Hotel Mati', 6.94726611, 126.23722842, 'active', 'hotels'),
(46, 'J.E.S.A. Chew Lodge', 6.95601629, 126.20609821, 'active', 'inns'),
(47, 'La-ne\'s Katulganan', 6.95237489, 126.21862494, 'active', 'inns'),
(48, 'Villa Merced Hotel', 6.95228546, 126.21854023, 'active', 'hotels'),
(49, 'Oriental Prince Suites and Arcade', 6.94608191, 126.25609570, 'active', 'attractions'),
(50, 'Popoy\'s Backpackers Inn', 6.94486549, 126.23756577, 'active', 'inns'),
(51, 'Lane\'s Kalapyahan Beach Resort', 6.92175341, 126.28101034, 'active', 'attractions'),
(52, 'Pacific Breeze Beach Resort', 6.91730935, 126.28416577, 'active', 'attractions'),
(53, 'Surf Village Hostel', 6.91694814, 126.28265794, 'active', 'attractions'),
(54, 'Honey\'s Hotel and Restaurant', 6.94474880, 126.23522362, 'active', 'food'),
(55, 'NEW IYLE LONDONER\'S INN', 6.94573910, 126.25515779, 'active', 'attractions'),
(56, 'Jular Apartelle & Bed and Breakfast', 6.95590263, 126.20623360, 'active', 'attractions'),
(57, 'Marriett (Bed & Breakfast)', 6.95611491, 126.20930606, 'active', 'attractions'),
(58, 'Traveller\'s Inn', 6.95345719, 126.21774260, 'active', 'attractions'),
(59, 'Ok Rooms Inn', 6.95991379, 126.20649978, 'active', 'attractions'),
(60, 'Prince and Princess Pension House', 6.95371814, 126.21785369, 'active', 'attractions'),
(61, 'Tay Franc\'s Apartelle', 6.95348576, 126.22056264, 'active', 'inns'),
(62, 'Three Princes Hostel', 6.94772951, 126.23862352, 'active', 'attractions'),
(63, 'RedullaTraveler\'s inn', 6.95860087, 126.20671094, 'active', 'inns'),
(64, 'Hotel Yakal', 6.95383839, 126.19749183, 'active', 'hotels'),
(65, 'A57 Techno Park', 6.95775557, 126.26830003, 'active', 'attractions'),
(66, 'Aloha Beach House Mati', 6.89726809, 126.29487466, 'active', 'attractions'),
(67, 'Bahay kubo Dahican Beach Resort', 6.92089530, 126.28150761, 'active', 'attractions'),
(68, 'Bahia Resort', 6.88570221, 126.27881638, 'active', 'attractions'),
(69, 'Bangunay Breeze', 6.89381933, 126.30127478, 'active', 'attractions'),
(70, 'Blue Bless Beach Resort', 6.87538476, 126.28762802, 'active', 'attractions'),
(71, 'Costa Lucas Beach Resort', 6.91814151, 126.28320743, 'active', 'attractions'),
(72, 'Dahican Cove', 6.91797675, 126.28382375, 'active', 'attractions'),
(73, 'Destino Dahican', 6.91381640, 126.28681782, 'active', 'attractions'),
(74, 'Ever Joy Resort', 6.94020420, 126.18975432, 'active', 'attractions'),
(75, 'Tropical Kanakbai, Dahican Beach', 6.93103098, 126.28384876, 'active', 'attractions'),
(76, 'Kubo sa Dahican', 6.90346050, 126.29020073, 'active', 'attractions'),
(77, 'Jambay Beach Resort', 6.87214997, 126.29123625, 'active', 'attractions'),
(78, 'Sakura Spring Resort', 6.76714572, 126.24374043, 'active', 'attractions'),
(79, 'Pagyanan ti mati', 6.86878416, 126.29424876, 'active', 'attractions'),
(80, 'POLONG CAMP MATI', 6.89725153, 126.29613436, 'active', 'attractions'),
(81, 'Praia Vista', 6.89423336, 126.30122862, 'active', 'attractions'),
(82, 'Resurreccion Beach Resort', 6.87311550, 126.28989792, 'active', 'attractions'),
(83, 'Mayo Beach Resort Lagoon', 6.99611555, 126.32780807, 'active', 'attractions'),
(84, 'Sand beach resort', 6.90664830, 126.28987896, 'active', 'attractions'),
(85, 'Sheepy’s Surfside Beach Resort', 6.89420412, 126.30087790, 'active', 'attractions'),
(86, 'Surf\'s Up Resort', 6.89802474, 126.29537964, 'active', 'attractions'),
(92, 'BALAY NI DE-NICE', 6.94513015, 126.26441002, 'active', 'attractions'),
(96, 'Subangan Museum', 6.94416024, 126.24833822, 'active', 'attractions'),
(97, 'Bakery Ni Ian Remedio', 6.96588409, 126.27805710, 'active', 'attractions');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `message`, `timestamp`) VALUES
(1, 'halaman ko', 'hcuh@gmail.com', 'ok', '2025-01-22 19:38:47');

-- --------------------------------------------------------

--
-- Table structure for table `pending_verifications`
--

CREATE TABLE `pending_verifications` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_verifications`
--

INSERT INTO `pending_verifications` (`id`, `email`, `phone`, `name`, `location`, `description`, `price`, `image_url`, `status`, `created_at`, `user_id`) VALUES
(53, '', '', 'sabelism', 'doc', 'dc', 1.00, 'Screen Shot 2024-09-26 at 5.26.49 PM.png', 'verified', '2024-12-15 21:17:34', 11),
(54, '', '', 'sabelism', 'doc', 'dc', 1.00, 'Screen Shot 2024-09-26 at 5.26.49 PM.png', 'verified', '2024-12-15 21:24:12', 11),
(55, '', '', 'sabelism', 'doc', 'dc', 1.00, 'Screen Shot 2024-09-26 at 5.26.49 PM.png', 'verified', '2024-12-16 08:34:07', 11),
(56, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 123', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 08:41:32', 11),
(57, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 09:09:49', 11),
(58, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 09:14:47', 11),
(59, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 09:27:22', 11),
(60, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 09:29:50', 11),
(61, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 09:34:16', 11),
(62, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:12:23', 11),
(63, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:13:25', 11),
(64, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:14:37', 11),
(65, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:15:40', 11),
(66, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:17:19', 11),
(67, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:22:07', 11),
(68, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:24:51', 11),
(69, 'jessaenderio97@gmail.com', '9939502417', 'sabelism 12356', 'doc', 'as', 1.00, 'Screen Shot 2024-11-18 at 10.48.16 PM.png', 'verified', '2024-12-16 10:59:47', 11),
(98, 'joyeeblanco@gmail.com', '9939502417', 'DUDAY CAFE', 'Maragusan ', 'wala lang', 1.00, '1346530.jpeg', 'verified', '2025-01-21 13:30:57', 11),
(99, 'racoonracoonra@gmail.com', '09123456789', 'Dinagsaan', 'Banaybanay', 'Beautiful mountain', 1000.00, 'IMG_20240420_231233_869.jpg', 'verified', '2025-02-14 12:25:26', 26);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `business_owner` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `business_owner`) VALUES
(11, 'sabel', 'lyxferrel@gmail.com', '$2y$10$934arbp.lCxHuG1n2OXece5wMr/BKfxlH1T18Zo9T9QN4HsWvUYS2', 'admin', 1),
(26, 'Doday', 'racoonraconra@gmail.com', '$2y$10$JNYKJwVh0.fBtsL3X67AdOKyXsj3yTTGpasLVOk6o6.gjcXeVmiVi', 'user', 1),
(27, 'Enderio', 'enderio69@gmail.com', '$2y$10$lCClb76Xq/HPoeVTobFVGeXffSm.GUyitWQA8kLcAU8YZrWwdUcu.', 'user', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `admin_codes`
--
ALTER TABLE `admin_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_code` (`admin_code`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_ibfk_1` (`business_id`);

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_business` (`user_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_verifications`
--
ALTER TABLE `pending_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `admin_codes`
--
ALTER TABLE `admin_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=445;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pending_verifications`
--
ALTER TABLE `pending_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `businesses`
--
ALTER TABLE `businesses`
  ADD CONSTRAINT `fk_user_business` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD CONSTRAINT `feedbacks_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pending_verifications`
--
ALTER TABLE `pending_verifications`
  ADD CONSTRAINT `pending_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
