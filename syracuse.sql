-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 18, 2018 at 08:39 PM
-- Server version: 10.1.29-MariaDB
-- PHP Version: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `syracuse`
--

-- --------------------------------------------------------

--
-- Table structure for table `van_accounts`
--

CREATE TABLE `van_accounts` (
  id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(5) NOT NULL,
  `pass` varchar(64) NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- Dumping data for table `van_accounts`
--

INSERT INTO `van_accounts` (`id`, `name`, `pass`) VALUES
(1, 'j.w.knobbe@pl.hanze.nl', '$2y$10$cr1dI/PLvhM9UPq4.Q63buubK9u58m3gtdhtxiJ5aJsUOU6VXkTqu');

-- --------------------------------------------------------

--
-- Table structure for table `van_language`
--

CREATE TABLE `van_language` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` tinytext NOT NULL,
  `native` tinytext NOT NULL,
  `code` char(5) NOT NULL
) ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- Dumping data for table `van_language`
--

INSERT INTO `van_language` (`id`, `name`, `native`, `code`) VALUES
(1, 'English (US)', 'English', 'en_US');

-- --------------------------------------------------------

--
-- Table structure for table `van_setting`
--

CREATE TABLE `van_setting` (
  `identifier` tinytext NOT NULL,
  `val` text
) ENGINE=InnoDB CHARACTER SET=utf8mb4;

--
-- Dumping data for table `van_setting`
--

INSERT INTO `van_setting` (`identifier`, `val`) VALUES
('theme', 'delft'),
('path', 'C:/xampp/htdocs/Syracuse'),
('url', 'http://localhost/Syracuse'),
('language', '1'),
('salt', 'be4JqE98LzxMcv789E12cR');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `van_language`
--
ALTER TABLE `van_language`
  ADD PRIMARY KEY (`id`);
COMMIT;

CREATE TABLE van_session (
    id VARCHAR(32) NOT NULL PRIMARY KEY,
    data TEXT(40) NOT NULL,
    created_at BIGINT(20) NOT NULL
);

CREATE TABLE van_token (
    id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
    value CHAR(40) NOT NULL,
    created_at BIGINT(20) NOT NULL,
    length BIGINT DEFAULT 0,
    user_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES van_accounts(id)
) ENGINE=InnoDB CHARACTER SET=utf8mb4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
