-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 01, 2023 at 02:34 PM
-- Server version: 5.7.36
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fromscratchluxaro`
--

-- --------------------------------------------------------

--
-- Table structure for table `charters`
--

DROP TABLE IF EXISTS `charters`;
CREATE TABLE IF NOT EXISTS `charters` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `thumbnail_img` int(255) NOT NULL,
  `rate` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(123) NOT NULL,
  `description` longtext NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) NOT NULL,
  `tags` varchar(500) NOT NULL,
  `charter_agreement` int(255) NOT NULL,
  `delivery_id` int(255) NOT NULL,
  `max_guests` int(255) NOT NULL,
  `charter_agreement_img` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `charters`
--

INSERT INTO `charters` (`id`, `thumbnail_img`, `rate`, `name`, `type`, `description`, `start_time`, `end_time`, `tags`, `charter_agreement`, `delivery_id`, `max_guests`, `charter_agreement_img`) VALUES
(17, 1025, 34, 'motor Bike service', 3, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchangedLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged', '23:58', '20:44', 'Quia eum omnis simil,132', 0, 2, 2, 1024),
(16, 0, 12, 'Charter Service', 2, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchangedLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged', '07:51', '10:00', 'Assumenda duis ullam,ss,slal', 0, 2, 3, 1023),
(15, 1022, 10, 'Van Service', 3, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchangedLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchangedLorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged', '', '', 'Odio quod ut archite', 0, 3, 3, 1021);

-- --------------------------------------------------------

--
-- Table structure for table `charter_categories`
--

DROP TABLE IF EXISTS `charter_categories`;
CREATE TABLE IF NOT EXISTS `charter_categories` (
  `id` int(123) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `charter_categories`
--

INSERT INTO `charter_categories` (`id`, `name`) VALUES
(1, 'Luxaurolicious'),
(2, 'Luxauro Fresh'),
(3, 'Antiques'),
(4, 'Vintage'),
(5, 'Street Market'),
(6, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `state_id`, `created_at`, `updated_at`) VALUES
(1, 'Miami', 1, '2023-02-27 14:31:03', '2023-02-27 14:31:03'),
(2, 'Tampa', 1, '2023-02-27 14:31:03', '2023-02-27 14:31:03'),
(3, 'Rajkot', 2, '2023-02-27 14:31:03', '2023-02-27 14:31:03'),
(4, 'Surat', 2, '2023-02-27 14:31:03', '2023-02-27 14:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'United State', '2023-02-27 14:31:03', '2023-02-27 14:31:03'),
(2, 'India', '2023-02-27 14:31:03', '2023-02-27 14:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_options`
--

DROP TABLE IF EXISTS `delivery_options`;
CREATE TABLE IF NOT EXISTS `delivery_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `delivery_options`
--

INSERT INTO `delivery_options` (`id`, `name`) VALUES
(1, 'Global'),
(2, 'Limited International'),
(3, 'National'),
(4, 'Limited National'),
(5, 'Locak Delivery'),
(6, 'Picup');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

DROP TABLE IF EXISTS `merchants`;
CREATE TABLE IF NOT EXISTS `merchants` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` int(11) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `zip_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `business_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_website` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ein` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_card_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_header_logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_business_logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_detail` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_type_id` int(11) DEFAULT NULL,
  `delivery_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_media_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_upload_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `introduce_owner` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_owner_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `team_upload_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `history` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ethic` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `merchants`
--

INSERT INTO `merchants` (`id`, `business_address`, `business_name`, `city`, `state`, `zip_code`, `country`, `business_email`, `business_website`, `business_phone`, `ein`, `bank_account_number`, `credit_card_number`, `store_header_logo`, `upload_business_logo`, `business_detail`, `business_type_id`, `delivery_id`, `social_media_link`, `owner_name`, `owner_upload_image`, `introduce_owner`, `team_owner_name`, `team_upload_image`, `history`, `ethic`, `created_at`, `updated_at`) VALUES
(1, 'cyre@mailinator.com', 'kipavum@mailinator.com', 0, 1, 'gucypa@mailinator.com', 2, 'vybe@mailinator.com', 'kodudez@mailinator.com', 'zajazirul@mailinator.com', 'xusec@mailinator.com', 'koludu@mailinator.com', 'rybi@mailinator.com', '/merchants/store_header.jpg', '/merchants/business_logo.jpg', 'Est laborum dolor d', 1, '[\"2\",\"4\",\"6\"]', 'Larissa Rivas', 'Stone Bailey', '/merchants/owner_upload_image.jpg', 'Repellendus Ea nisi', 'Isabelle Clay', '/merchants/team_upload_image.jpg', 'Ullamco quis nisi id', 'Laborum voluptas Nam', '2023-02-02 09:48:21', '2023-02-02 09:48:21'),
(2, 'radyzyrep@mailinator.com', 'kameryzik@mailinator.com', 0, 1, 'borica@mailinator.com', 1, 'fivyjal@mailinator.com', 'zaryheluv@mailinator.com', 'ludi@mailinator.com', 'tiqoha@mailinator.com', 'rasyqypet@mailinator.com', 'naniluqa@mailinator.com', '/merchants/store_header.jpg', '/merchants/business_logo.jpg', 'Sint deleniti eum q', 2, '[\"1\",\"2\",\"3\",\"5\",\"6\"]', 'Ulysses Church', 'Urielle Bartlett', '/business_document/default_image', 'Omnis quis ducimus', 'Gillian Griffith', '/business_document/default_image', 'Praesentium voluptat', 'Est dolor sed quo t', '2023-02-02 10:18:34', '2023-02-02 10:18:34'),
(3, 'zuze@mailinator.com', 'vohizyze@mailinator.com', 0, 1, 'jawory@mailinator.com', 1, 'suwimucu@mailinator.com', 'viruxofuvy@mailinator.com', 'disydaqas@mailinator.com', 'nevela@mailinator.com', 'cicezyj@mailinator.com', 'zoziceloj@mailinator.com', '', '', '', 0, '', '', '', '', '', '', '', '', '', '2023-02-07 08:16:49', '2023-02-07 08:16:49'),
(4, 'zuze@mailinator.com', 'vohizyze@mailinator.com', 0, 1, 'jawory@mailinator.com', 1, 'suwimucu@mailinator.com', 'viruxofuvy@mailinator.com', 'disydaqas@mailinator.com', 'nevela@mailinator.com', 'cicezyj@mailinator.com', 'zoziceloj@mailinator.com', '999', '', '', 0, '', '', '', '', '', '', '', '', '', '2023-02-07 08:17:29', '2023-02-07 08:17:29'),
(5, 'zuze@mailinator.com', 'vohizyze@mailinator.com', 0, 1, 'jawory@mailinator.com', 1, 'suwimucu@mailinator.com', 'viruxofuvy@mailinator.com', 'disydaqas@mailinator.com', 'nevela@mailinator.com', 'cicezyj@mailinator.com', 'zoziceloj@mailinator.com', '1001', '1002', 'Exercitation in in m', 2, '3,5,6', 'Olivia Cline', 'Veronica Greene', '1003', 'Enim vero iusto veli', 'Phelan Acevedo', '1004', 'Harum natus voluptat', 'Enim officia illum', '2023-02-07 08:18:12', '2023-02-07 08:33:45'),
(6, 'magoja@mailinator.com', 'qaqyxynu@mailinator.com', 0, 2, 'kehu@mailinator.com', 2, 'linogy@mailinator.com', 'gynekibim@mailinator.com', 'fyzuwyliti@mailinator.com', 'vykif@mailinator.com', 'qalig@mailinator.com', 'loxod@mailinator.com', '', '', 'Aut velit voluptate', 2, '2,5', 'Eaton Hopper', 'Tana Fitzpatrick', '1026', 'Molestias eos et rep', 'Lacy Ayala', '1027', 'Reprehenderit ea lor', 'Eaque quas nulla hic', '2023-02-09 15:19:25', '2023-02-09 15:19:54'),
(7, 'ryloxi@mailinator.com', 'jomoca@mailinator.com', 3, 2, 'vyqakuvi@mailinator.com', 2, 'jibiru@mailinator.com', 'vyhezi@mailinator.com', 'sapulyry@mailinator.com', 'vigog@mailinator.com', 'qujoqemy@mailinator.com', 'mugox@mailinator.com', '1043', '1044', 'qswdewfg', 2, '1,2,3', 'Phelan Powers', 'Sylvester Cunningham', '1045', 'ASDEF', 'QWER', '1046', 'QWEDER', 'sdfrg', '2023-02-28 15:05:01', '2023-02-28 15:06:12');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '2014_10_12_000000_create_users_table', 1),
(5, '2014_10_12_100000_create_password_resets_table', 1),
(6, '2019_08_19_000000_create_failed_jobs_table', 1),
(7, '2023_02_27_191614_create_countries_table', 2),
(8, '2023_02_27_191631_create_states_table', 2),
(9, '2023_02_27_191732_create_cities_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `delivery_option` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `shipping_charge` int(123) NOT NULL,
  `added_by` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'admin',
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `photos` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail_img` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video_provider` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `video_link` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `unit_price` double(20,2) NOT NULL,
  `purchase_price` double(20,2) DEFAULT NULL,
  `variant_product` int(11) NOT NULL DEFAULT '0',
  `attributes` varchar(1000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '[]',
  `choice_options` mediumtext COLLATE utf8_unicode_ci,
  `colors` mediumtext COLLATE utf8_unicode_ci,
  `variations` text COLLATE utf8_unicode_ci,
  `todays_deal` int(11) NOT NULL DEFAULT '0',
  `published` int(11) NOT NULL DEFAULT '1',
  `approved` tinyint(1) NOT NULL DEFAULT '1',
  `stock_visibility_state` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'quantity',
  `cash_on_delivery` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = On, 0 = Off',
  `featured` int(11) NOT NULL DEFAULT '0',
  `seller_featured` int(11) NOT NULL DEFAULT '0',
  `current_stock` int(11) NOT NULL DEFAULT '0',
  `unit` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` double(8,2) NOT NULL DEFAULT '0.00',
  `min_qty` int(11) NOT NULL DEFAULT '1',
  `low_stock_quantity` int(11) DEFAULT NULL,
  `discount` double(20,2) DEFAULT NULL,
  `discount_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount_start_date` int(11) DEFAULT NULL,
  `discount_end_date` int(11) DEFAULT NULL,
  `tax` double(20,2) DEFAULT NULL,
  `tax_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `shipping_type` varchar(20) COLLATE utf8_unicode_ci DEFAULT 'flat_rate',
  `shipping_cost` double(20,2) NOT NULL DEFAULT '0.00',
  `is_quantity_multiplied` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = Mutiplied with shipping cost',
  `est_shipping_days` int(11) DEFAULT NULL,
  `num_of_sale` int(11) NOT NULL DEFAULT '0',
  `meta_title` mediumtext COLLATE utf8_unicode_ci,
  `meta_description` longtext COLLATE utf8_unicode_ci,
  `meta_img` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pdf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `refundable` int(11) NOT NULL DEFAULT '0',
  `earn_point` double(8,2) NOT NULL DEFAULT '0.00',
  `rating` double(8,2) NOT NULL DEFAULT '0.00',
  `barcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `digital` int(11) NOT NULL DEFAULT '0',
  `auction_product` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `external_link` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `external_link_btn` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'Buy Now',
  `wholesale_product` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `delivery_option`, `shipping_charge`, `added_by`, `user_id`, `category_id`, `brand_id`, `photos`, `thumbnail_img`, `video_provider`, `video_link`, `tags`, `description`, `unit_price`, `purchase_price`, `variant_product`, `attributes`, `choice_options`, `colors`, `variations`, `todays_deal`, `published`, `approved`, `stock_visibility_state`, `cash_on_delivery`, `featured`, `seller_featured`, `current_stock`, `unit`, `weight`, `min_qty`, `low_stock_quantity`, `discount`, `discount_type`, `discount_start_date`, `discount_end_date`, `tax`, `tax_type`, `shipping_type`, `shipping_cost`, `is_quantity_multiplied`, `est_shipping_days`, `num_of_sale`, `meta_title`, `meta_description`, `meta_img`, `pdf`, `slug`, `refundable`, `earn_point`, `rating`, `barcode`, `digital`, `auction_product`, `file_name`, `file_path`, `external_link`, `external_link_btn`, `wholesale_product`, `created_at`, `updated_at`) VALUES
(10, 'Jackets', '', 0, 'admin', 9, 32, NULL, '986', '986', NULL, NULL, 'Leather', '<p>In marketing, a product is an object, or system, or service made available for consumer use as of the consumer demand; it is anything that can be offered to a market to satisfy the desire or need of a customer.[1] In retailing, products are often referred to as merchandise, and in manufacturing, products are bought as raw materials and then sold as finished goods. A service is also regarded as a type of product.</p>', 12.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 12, NULL, 0.00, 1, NULL, 12.00, 'amount', 1673568000, 1674172740, NULL, NULL, 'flat_rate', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'jackets', 0, 12.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, '2023-01-13 12:39:15', '2023-01-13 13:37:44'),
(13, 'Cedric Barry', 'national,locak_delivery,picup', 0, 'admin', 9, 4, NULL, NULL, '986', NULL, NULL, '', '<p>In marketing, a product is an object, or system, or service made available for consumer use as of the consumer demand; it is anything that can be offered to a market to satisfy the desire or need of a customer.[1] In retailing, products are often referred to as merchandise, and in manufacturing, products are bought as raw materials and then sold as finished goods. A service is also regarded as a type of product.</p>', 329.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 213, NULL, 0.00, 1, NULL, 91.00, 'percent', 1674608400, 1674863940, NULL, NULL, 'within_usa', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'cedric-barry', 0, 65.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-01-16 08:22:10', '2023-01-16 14:20:55'),
(14, 'Maya Spence', 'global,limited_international', 123, 'admin', 9, 1, NULL, '988', '986', NULL, NULL, 'CCTV Cameras,digital Cameras', '<p>In marketing, a product is an object, or system, or service made available for consumer use as of the consumer demand; it is anything that can be offered to a market to satisfy the desire or need of a customer.[1] In retailing, products are often referred to as merchandise, and in manufacturing, products are bought as raw materials and then sold as finished goods. A service is also regarded as a type of product.</p>', 703.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 19, NULL, 0.00, 1, NULL, 22.00, 'amount', 1673337600, 1674086340, NULL, NULL, 'within_usa', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'cameras', 0, 68.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-01-16 08:50:33', '2023-01-16 10:13:40'),
(15, 'Shoes', '', 0, 'admin', 9, 32, NULL, '986', '986', NULL, NULL, 'Leather', '<p>weqwrqwqwe retewewr</p>', 12.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 12, NULL, 0.00, 1, NULL, 12.00, 'amount', 1673568000, 1674172740, NULL, NULL, 'flat_rate', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'jackets', 0, 12.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, '2023-01-13 12:39:15', '2023-01-13 13:37:44'),
(16, 'Cedric Barry', 'national,locak_delivery,picup', 0, 'admin', 9, 4, NULL, NULL, '986', NULL, NULL, '', '<p>Product Description</p>', 329.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 213, NULL, 0.00, 1, NULL, 91.00, 'percent', 1674608400, 1674863940, NULL, NULL, 'within_usa', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'cedric-barry', 0, 65.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-01-16 08:22:10', '2023-01-16 14:20:55'),
(17, 'Maya Spence', 'global,limited_international', 123, 'admin', 9, 1, NULL, '988', '986', NULL, NULL, 'CCTV Cameras,digital Cameras', '<p>awdrfs</p>', 703.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 19, NULL, 0.00, 1, NULL, 22.00, 'amount', 1673337600, 1674086340, NULL, NULL, 'within_usa', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'cameras', 0, 68.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-01-16 08:50:33', '2023-01-16 10:13:40'),
(18, 'wehybuz@mailinator.com', '1,2,3,4,5,6', 12, 'admin', 9, 0, NULL, NULL, NULL, NULL, NULL, '12,34,32', 'Sint velit eos minu', 0.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 0, NULL, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'wehybuz-at-mailinatorcom', 0, 0.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-02-06 14:10:35', '2023-02-06 14:10:35'),
(19, 'byfozav@mailinator.com', '4,6', 12, 'admin', 9, 0, NULL, NULL, NULL, NULL, NULL, 'Esse quod ratione o,ksks,sjsj', 'Officia maiores veli', 0.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 0, NULL, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'byfozav-at-mailinatorcom', 0, 0.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-02-06 14:15:29', '2023-02-06 14:15:29'),
(20, 'bahaneb@mailinator.com', '4,5,6', 0, 'admin', 9, 0, NULL, NULL, NULL, NULL, NULL, 'Saepe dolor laudanti', 'Adipisci aperiam dol', 0.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 0, NULL, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'bahaneb-at-mailinatorcom', 0, 0.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-02-06 14:34:43', '2023-02-06 14:34:43'),
(21, 'Yuri Armstrong', 'limited_international', 0, 'admin', 9, 2, NULL, NULL, NULL, NULL, NULL, '', NULL, 180.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 85, NULL, 0.00, 1, NULL, 96.00, 'percent', NULL, NULL, NULL, NULL, 'within_usa,outside_u', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'yuri-armstrong', 0, 12.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-02-06 14:46:52', '2023-02-06 14:46:52'),
(22, 'cudinukas@mailinator.com', '4', 0, 'admin', 9, 0, NULL, NULL, 'C:\\wamp64\\tmp\\php8892.tmp', NULL, NULL, 'Quia consectetur du', 'Consequatur Odio at', 0.00, NULL, 0, '[]', '[]', '[]', NULL, 0, 1, 1, 'quantity', 0, 0, 0, 0, NULL, 0.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1,2', 0.00, 0, NULL, 0, NULL, NULL, NULL, NULL, 'cudinukas-at-mailinatorcom', 0, 0.00, 0.00, NULL, 0, 0, NULL, NULL, NULL, 'Buy Now', 0, '2023-02-07 09:57:59', '2023-02-07 09:57:59');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `name`, `country_id`, `created_at`, `updated_at`) VALUES
(1, 'Florida', 1, '2023-02-27 14:31:03', '2023-02-27 14:31:03'),
(2, 'Gujarat', 2, '2023-02-27 14:31:03', '2023-02-27 14:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_original_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `extension` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `external_link` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `file_original_name`, `file_name`, `user_id`, `file_size`, `extension`, `type`, `external_link`, `created_at`, `updated_at`, `deleted_at`) VALUES
(13, 'person', 'public/uploads/all/KXivNMGJeKvn6uLm7W5gtMLOGJa3ZeNyfzUDvCaR.png', 1, 12210, 'png', 'image', NULL, '2023-03-01 08:56:44', '2023-03-01 08:56:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `referred_by` int(11) DEFAULT NULL,
  `business_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `about_me` longtext COLLATE utf8_unicode_ci NOT NULL,
  `course_certification_document` int(255) NOT NULL,
  `user_profile_image` int(255) NOT NULL,
  `portfolio_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_of_birth` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `college_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `portfolio_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `degree` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `course_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `book_from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `book_to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profile_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `profile_link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'customer',
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `verification_code` text COLLATE utf8_unicode_ci,
  `new_email_verificiation_code` text COLLATE utf8_unicode_ci,
  `password` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar_original` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `balance` double(20,2) NOT NULL DEFAULT '0.00',
  `banned` tinyint(4) NOT NULL DEFAULT '0',
  `referral_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_package_id` int(11) DEFAULT NULL,
  `remaining_uploads` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `referred_by`, `business_name`, `about_me`, `course_certification_document`, `user_profile_image`, `portfolio_link`, `date_of_birth`, `description`, `college_name`, `portfolio_name`, `degree`, `course_name`, `language`, `book_from`, `book_to`, `provider_id`, `profile_name`, `profile_link`, `user_type`, `name`, `email`, `email_verified_at`, `verification_code`, `new_email_verificiation_code`, `password`, `remember_token`, `device_token`, `avatar`, `avatar_original`, `address`, `country`, `state`, `city`, `postal_code`, `phone`, `balance`, `banned`, `referral_code`, `customer_package_id`, `remaining_uploads`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, NULL, 'Flowering', 'Non dignissimos ut n', 0, 13, 'Kyra Rutledge', NULL, 'Kirby Mcmillan', 'Craig Ramos', 'Kendall Medina', 'Declan Sharp', 'Axel Gibbs', 'spanish', '2004-01-12', '2016-12-28', NULL, '', '', 'admin', 'cagaru@mailinator.com', 'admin@luxauro.com', '2022-04-21 01:04:44', NULL, NULL, '$2y$10$e7Su9J28nFHut2NXefgS.OQeUdQvfInCbtK73mT3ShKwmZmkh7Zzi', 'Q5T78RXYR7qultxpHeA3lXrUeoousAtPai3k9umxqnkrxuFv0B4GdWvU7kzp', NULL, NULL, NULL, 'Ina Chang', '2', '2', NULL, '1212', '+1 (649) 765-3307', 0.00, 0, NULL, NULL, 0, '2022-04-21 01:10:44', '2023-03-01 08:56:44', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
