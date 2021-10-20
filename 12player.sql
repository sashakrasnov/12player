-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2021 at 01:08 PM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `12player`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `fname` tinytext NOT NULL,
  `email` varchar(64) NOT NULL,
  `passw` varchar(32) NOT NULL,
  `org_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Orgnization id from config-file. "0" to control all orgs',
  `city_id` tinyint(3) UNSIGNED NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `auth_key` varchar(32) CHARACTER SET ascii NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `fname`, `email`, `passw`, `org_id`, `city_id`, `updated`, `auth_key`) VALUES
(1, '', 'mask@domain.tld', '38b72c853caaf8d809ce7ad39782530f', 0, 0, '2021-10-20 10:49:43', 'f99d33eb8df407743b4a20addf9c13dd'),
(2, '', 'artmm@nowhere.com', '38b72c853caaf8d809ce7ad39782530f', 1, 0, '2021-10-20 10:50:58', '9483f10e10b7b9fc2c7443d17e7d5977');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` tinytext NOT NULL COMMENT 'Title of the event',
  `descr` varchar(1024) DEFAULT NULL COMMENT 'Short description',
  `long_descr` varchar(8192) DEFAULT NULL COMMENT 'Long description',
  `org_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Organization id from cfg-file',
  `lang_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Language id from cfg-file',
  `dt` datetime NOT NULL COMMENT 'Date and time of the event',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Event status (-1: cancelled, 0: not confirmed, 1: confirmed)',
  `game_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Event type id from cfg-file',
  `city_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'City id of the event from cfg-file',
  `addr` tinytext NOT NULL COMMENT 'City address',
  `map` tinytext COMMENT 'Link to the map',
  `price` smallint(5) UNSIGNED NOT NULL COMMENT 'Price',
  `price_com` smallint(6) GENERATED ALWAYS AS (round((`price` / 0.9),0)) VIRTUAL COMMENT 'Price incl. commission',
  `count_min` smallint(5) UNSIGNED NOT NULL COMMENT 'Minimum number of tickets to be sold',
  `count_max` smallint(5) UNSIGNED NOT NULL COMMENT 'Maximum number of tickets to be sold',
  `count_free` smallint(5) UNSIGNED NOT NULL COMMENT 'Number of free of charge tickets',
  `count_paid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Number of sold tickets',
  `link` tinytext COMMENT 'Link to the report',
  `upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Row update timestamp',
  `admin_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Admin id',
  `img_ext_1` varchar(8) CHARACTER SET ascii DEFAULT NULL COMMENT 'First image extension',
  `img_ext_2` varchar(8) CHARACTER SET ascii DEFAULT NULL COMMENT 'Second image extension'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `descr`, `long_descr`, `org_id`, `lang_id`, `dt`, `status`, `game_id`, `city_id`, `addr`, `map`, `price`, `price_com`, `count_min`, `count_max`, `count_free`, `count_paid`, `link`, `upd`, `admin_id`, `img_ext_1`, `img_ext_2`) VALUES
(1, 'Event #1 Title', 'Event #1 Description', 'Event #1 Long Description', 1, 2, '2018-05-27 18:22:18', 1, 0, 1, 'Lenina st. 35-24', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.385534%2C55.584227&z=9.65', 456, 507, 12, 13, 3, 0, '', '2018-05-27 11:34:48', 0, NULL, NULL),
(5, 'Event #2 Title', 'Event #2 Description', 'Event #2 Long Description', 1, 1, '2018-05-30 23:55:00', 0, 2, 5, 'Molotova st. 11-1', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.400970%2C55.593883&z=10.9', 123, 137, 21, 123, 1, 0, '', '2018-05-27 15:35:53', 1, 'jpg', NULL),
(6, 'Event #3 Title', 'Event #3 Description', 'Event #3 Long Description', 1, 8, '2018-05-27 23:55:00', 1, 0, 4, 'Lenina st. 35-23', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.650722%2C55.833682&z=12.3', 123, 137, 21, 123, 1, 0, '', '2018-05-26 23:18:22', 1, NULL, NULL),
(13, 'Event #4 Title', 'Event #4 Description', 'Event #4 Long Description', 1, 2, '2018-06-01 17:55:00', 0, 0, 11, 'Lenina st. 35-21', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.343971%2C55.793550&z=13.23', 17, 19, 5, 20, 3, 0, '', '2018-05-20 23:17:45', 1, 'jpg', NULL),
(14, 'Event #5 Title', 'Event #5 Description', 'Event #5 Long Description', 1, 2, '2018-06-01 17:55:00', 0, 0, 11, 'Sovetskaya st. 1/2', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.261090%2C55.829408&z=14.59', 17, 19, 5, 20, 3, 0, 'http://domain.tld/report2/', '2018-05-20 23:17:45', 1, NULL, NULL),
(15, 'Event #6 Title', 'Event #6 Description', 'Event #6 Long Description', 1, 1, '2018-06-01 19:40:00', 0, 2, 1, 'Oktyabryat st. 11-2', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.254610%2C55.819473&z=16.06', 1000, 1111, 50, 100, 2, 0, 'http://domain.tld/report1/', '2018-05-26 23:27:41', 1, NULL, NULL),
(16, 'Event #7 Title', 'Event #7 Description', 'Event #7 Long Description', 1, 1, '2018-05-22 01:47:00', 0, 3, 1, 'Shirokay st. 22-1', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.282030%2C55.801131&z=17.62', 123, 137, 0, 3456, 234, 0, '', '2018-05-21 12:22:41', 1, 'gif', NULL),
(17, 'Event #8 Title', 'Event #8 Description', 'Event #8 Long Description', 1, 1, '2018-05-30 22:02:00', 0, 0, 4, 'Geroev-Lenintsev st. 22-2', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.350288%2C55.801891&z=16.79', 234, 260, 11, 234, 11, 0, '', '2018-05-27 15:38:27', 1, 'jpg', NULL),
(21, 'Event #9 Title', 'Event #9 Description', 'Event #9 Long Description', 3, 2, '2018-05-31 14:20:00', 0, 4, 7, 'Kotovskogo st. 22-3', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.406485%2C55.802645&z=15.57', 600, 667, 17, 30, 2, 0, '', '2018-05-20 23:17:45', 1, 'jpg', NULL),
(22, 'Event #10 Title', 'Event #10 Description', 'Event #10 Long Description', 2, 8, '2018-05-28 19:19:00', 0, 1, 11, 'Mira st. 1/4', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.460322%2C55.779557&z=15.57', 950, 1056, 17, 35, 2, 0, '', '2018-05-27 15:32:08', 1, 'jpg', NULL),
(23, 'Event #11 Title', 'Event #11 Description', 'Event #11 Long Description', 2, 1, '2018-06-05 00:00:00', 1, 1, 5, 'Kalinina st. 411', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.522620%2C55.771562&z=15.57', 900, 1000, 1, 13, 2, 13, 'http://domain.tld/report3/', '2018-05-27 13:44:54', 1, NULL, NULL),
(24, 'Event #12 Title', 'Event #12 Description', 'Event #12 Long Description', 2, 16, '2018-06-30 16:35:00', 0, 5, 10, 'Kirova st. 2', 'https://yandex.ru/maps/geo/moskva/53000094/?ll=37.561344%2C55.764311&z=15.57', 8000, 8889, 5, 20, 3, 0, '', '2018-06-17 15:45:22', 1, 'png', 'jpg');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `tg_id` bigint(20) UNSIGNED NOT NULL COMMENT 'Telegram user id',
  `dt` date NOT NULL COMMENT 'Date when the user is able to play the game',
  `city_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Id of the city where the user should be at the specified date (dt)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`tg_id`, `dt`, `city_id`) VALUES
(1000, '2018-05-27', 2),
(1000, '2018-05-28', 11),
(1000, '2018-06-01', 1),
(1000, '2018-05-26', 5),
(1000, '2018-06-20', 9),
(1000, '2018-05-29', 8),
(1000, '2018-05-22', 1),
(1001, '2018-06-06', 1),
(1001, '2018-06-07', 4),
(1001, '2018-06-08', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tg_admins`
--

CREATE TABLE `tg_admins` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `uname` varchar(32) NOT NULL COMMENT 'Telegram user name of chatbot admin',
  `org_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Id of the organization that would be managed by user. "0" for all orgs',
  `city_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Id of the city that would be managed by user. "0" for all cities'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tg_admins`
--

INSERT INTO `tg_admins` (`id`, `uname`, `org_id`, `city_id`) VALUES
(303, 'al_76', 3, 10),
(304, 'rg_reventlov', 3, 11),
(305, 'powell', 3, 6),
(306, 'donovan', 3, 6),
(307, 'susan_calvin', 3, 1),
(308, 'rd_olivaw', 3, 9),
(309, 'elijah_baley', 3, 5),
(310, 'powell', 3, 8),
(311, 'rj_panell', 3, 7),
(312, 'donovan', 3, 3),
(313, 'lawrence_robertson', 3, 2),
(314, 'robbie', 3, 4),
(315, 'david_starr', 1, 10),
(316, 'david_starr', 1, 6),
(317, 'bigman', 1, 6),
(318, 'cpt_anton', 1, 1),
(319, 'augustus_henree', 1, 9),
(320, 'hector_conway', 1, 4),
(321, 'han_fastolfe', 4, 1),
(322, 'vasilia_aliena', 4, 1),
(323, 'kelden_amadiro', 4, 1),
(324, 'hari_seldon', 2, 10),
(325, 'jord_fara', 2, 11),
(326, 'gaal_dornick', 2, 6),
(327, 'jerril', 2, 6),
(328, 'dokor_walto', 2, 1),
(329, 'eskel_gorov', 2, 1),
(330, 'linmar_ponyets', 2, 1),
(331, 'les_gorm', 2, 1),
(332, 'salvor_hardin', 2, 9),
(333, 'jaim_orsy', 2, 5),
(334, 'bor_alurin', 2, 8),
(335, 'lem_tarki', 2, 7),
(336, 'lors_avakim', 2, 3),
(337, 'levi_norast', 2, 2),
(338, 'poly_verisof', 2, 2),
(339, 'sef_sermak', 2, 2),
(340, 'linge_chen', 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tg_users`
--

CREATE TABLE `tg_users` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'Telegram user id',
  `uname` varchar(64) NOT NULL COMMENT 'Telegram username',
  `fb` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Facebook id',
  `fname` tinytext NOT NULL COMMENT 'Full name',
  `langs` tinyint(3) UNSIGNED NOT NULL COMMENT 'Spoken languages bitmask',
  `lang_id` tinyint(3) UNSIGNED NOT NULL COMMENT 'Telegram chatbot interface language id from cfg-file',
  `city_def` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Default city of the user where he''ll spend most of the time',
  `src` varchar(20) DEFAULT NULL COMMENT 'HTTP referer',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the row'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tg_users`
--

INSERT INTO `tg_users` (`id`, `uname`, `fb`, `fname`, `langs`, `lang_id`, `city_def`, `src`, `ts`) VALUES
(1000, 'guymtg451', 0, 'Guy Montag', 9, 1, 5, NULL, '2018-05-29 12:52:01'),
(1001, 'clarisse452', 0, 'Clarisse McClellan', 11, 2, 6, NULL, '2018-06-06 19:27:45'),
(1002, 'cptbeatty453', 0, 'Captain Beatty', 0, 0, 1, NULL, '2018-06-06 19:32:30'),
(1010, 'faber454', 0, 'Faber', 0, 2, 1, NULL, '2018-06-16 22:49:26'),
(233737364, 'granger455', 0, 'Granger', 18, 2, 1, NULL, '2018-06-12 15:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Id of the ticket to the event',
  `event_id` int(10) UNSIGNED NOT NULL COMMENT 'Id of the event',
  `tg_id` bigint(10) UNSIGNED NOT NULL COMMENT 'Id if telegram user',
  `t_buy` varchar(32) CHARACTER SET ascii DEFAULT NULL COMMENT 'Transaction/Buy',
  `t_refund` varchar(32) CHARACTER SET ascii DEFAULT NULL COMMENT 'Transaction/Refund',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Ticket status. 0: normal state; 1: check in state; -1: refund/cancel',
  `t_code` varchar(20) CHARACTER SET ascii DEFAULT NULL COMMENT 'Ticket code written on the image',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `event_id`, `tg_id`, `t_buy`, `t_refund`, `status`, `t_code`, `ts`) VALUES
(17, 23, 1000, '8218-0830', NULL, 0, '912312', '2018-05-31 10:03:44'),
(22, 23, 1000, '912312', '912312111', -1, '95351-96579', '2018-05-31 15:12:48'),
(26, 23, 1001, '101010202020', '9999101010202020', -1, '72562-16516', '2018-06-06 21:44:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_id` (`org_id`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `auth_key` (`auth_key`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_id` (`org_id`),
  ADD KEY `lang_id` (`lang_id`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `status` (`status`),
  ADD KEY `dt` (`dt`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD KEY `city_id` (`city_id`),
  ADD KEY `d` (`dt`),
  ADD KEY `tg_id` (`tg_id`);

--
-- Indexes for table `tg_admins`
--
ALTER TABLE `tg_admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_id` (`org_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `tg_users`
--
ALTER TABLE `tg_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fb` (`fb`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `t_buy` (`t_buy`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`tg_id`),
  ADD KEY `code` (`t_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `tg_admins`
--
ALTER TABLE `tg_admins`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=341;
--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Id of the ticket to the event', AUTO_INCREMENT=27;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
