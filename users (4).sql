-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 17, 2025 at 02:24 PM
-- Server version: 5.7.24
-- PHP Version: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `users`
--

-- --------------------------------------------------------

--
-- Table structure for table `size`
--

CREATE TABLE `size` (
  `id` int(11) NOT NULL,
  `size` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `size`
--

INSERT INTO `size` (`id`, `size`) VALUES
(1, 38),
(2, 39),
(3, 40),
(4, 41),
(5, 42),
(6, 43),
(7, 44);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(2) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `prix` float NOT NULL,
  `couleur` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `nom`, `prix`, `couleur`, `image`) VALUES
(15, 'New Balance 550', 25.14, 'Blanc / Gris', '../assets/images/NB550.webp'),
(17, 'Nike Dunk Low Celtics', 15.16, NULL, '../assets/images/NKCELTICS.webp'),
(18, 'dad', 45, 'Blanc/Noir', '../assets/images/sneaker_67d29781c5a1f.jpg'),
(19, 'camplapus', 45, 'Blanc/Noir', '../assets/images/sneaker_67d297d6582cc.jpg'),
(20, 'dez', 65, 'ba', '../assets/images/sneaker_67d6f95e1b66d.png');

-- --------------------------------------------------------

--
-- Table structure for table `stock_size`
--

CREATE TABLE `stock_size` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `stock_size`
--

INSERT INTO `stock_size` (`id`, `stock_id`, `size_id`, `amount`) VALUES
(1, 15, 2, 2),
(3, 17, 2, 4),
(4, 18, 4, 0),
(5, 18, 6, 0),
(6, 20, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_site`
--

CREATE TABLE `user_site` (
  `user_id` int(11) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(128) NOT NULL,
  `Grade` varchar(20) NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_site`
--

INSERT INTO `user_site` (`user_id`, `prenom`, `nom`, `email`, `password`, `Grade`) VALUES
(1, 'Badr mimouni', '', 'badr_mim@outlook.fr', 'devine', ''),
(37, 'admin', 'admin', 'admin@sneak.com', '$2y$10$.iTGSnwqNTigaGJ0qjjHgeyMtGAr51sBOlvTnQpsRXb6UVUxtF4AK', 'admin'),
(39, 'Mimouni', 'Badr-Eddine', 'nbad@mail.com', 'badr', 'client'),
(66, 'Mimouni', 'Badr-Eddine', 'badr@mail.com', 'badr', 'client'),
(67, 'Mimouni', 'Badr-Eddine', 'badr1@mail.com', 'badr', 'client'),
(68, 'test', 'test', 'test@mail.com', 'test', 'client'),
(69, 's', 'oden', 's@m.com', 'badr', 'client'),
(70, 'b', 'test', 'b@m.com', '$2y$10$NbdxPkTxwOPTh7Bk8anwnOirPbJrwl4p26lvQ2cIXqolF/ozh3GYK', 'client'),
(71, 'badr', 'admin', 'admin@admin.fr', '$2y$10$yGotgejnjxZ8qbTsgAbbHeFdqzMnxxucEhwPHfDnTLD4sHoheefjy', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `size`
--
ALTER TABLE `size`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_size`
--
ALTER TABLE `stock_size`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_id` (`stock_id`),
  ADD KEY `size_id` (`size_id`);

--
-- Indexes for table `user_site`
--
ALTER TABLE `user_site`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `size`
--
ALTER TABLE `size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stock_size`
--
ALTER TABLE `stock_size`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_site`
--
ALTER TABLE `user_site`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `stock_size`
--
ALTER TABLE `stock_size`
  ADD CONSTRAINT `stock_size_ibfk_1` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`),
  ADD CONSTRAINT `stock_size_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `size` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
