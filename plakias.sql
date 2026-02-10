-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2026 at 04:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `plakias`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slot_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `therapist_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendees_count` int(11) NOT NULL DEFAULT 1 COMMENT 'Αριθμός ατόμων',
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `appointment_type` enum('inPerson','online','mixed') DEFAULT NULL,
  `status` enum('booked','canceled','completed') NOT NULL DEFAULT 'booked',
  `payment_status` enum('unpaid','paid','partially_paid') DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `google_event_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `slot_id`, `client_id`, `therapist_id`, `package_id`, `attendees_count`, `start_datetime`, `end_datetime`, `appointment_type`, `status`, `payment_status`, `notes`, `admin_notes`, `created_at`, `google_event_id`) VALUES
(37, NULL, 11, 1, 6, 1, '2026-02-08 09:00:00', '2026-02-08 10:00:00', 'inPerson', 'booked', 'paid', '', NULL, '2026-02-09 17:55:49', NULL),
(38, NULL, 32, 2, 5, 1, '2026-03-10 09:00:00', '2026-03-10 10:30:00', 'inPerson', 'booked', 'paid', '', NULL, '2026-02-09 17:58:05', NULL),
(39, NULL, 33, 2, 5, 1, '2026-03-10 09:00:00', '2026-03-10 10:30:00', 'inPerson', 'booked', 'paid', '', NULL, '2026-02-09 18:35:06', NULL),
(40, NULL, 9, 1, 6, 1, '2026-03-08 09:00:00', '2026-03-08 10:00:00', 'inPerson', 'booked', 'unpaid', '', NULL, '2026-02-10 15:43:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL COMMENT 'URL friendly version e.g. trail-running',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`) VALUES
(1, 'Road Running', 'road-running', 'Διαδρομές σε άσφαλτο και αστικό περιβάλλον', 1, '2026-02-09 15:50:22'),
(2, 'Trail Running', 'trail-running', 'Τρέξιμο σε μονοπάτια και βουνό', 1, '2026-02-09 15:50:22'),
(3, 'Track Session', 'track-session', 'Προπονήσεις ταχύτητας σε ταρτάν', 1, '2026-02-09 15:50:22'),
(4, 'Fun Run', 'fun-run', 'Χαλαρό τρέξιμο για κοινωνικοποίηση', 1, '2026-02-09 15:50:22');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `client_note` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `last_name`, `email`, `phone`, `created_at`, `client_note`, `updated_at`, `is_active`) VALUES
(7, 'Γιώργος', 'Αλεξίου', 'george@cattus.dev', '6985878586', '2025-01-11 15:02:34', 'Αλεργια στα ψάρια', '2025-01-11 13:02:34', 1),
(8, 'Δημήτρης', 'Πάνου', 'jim@cattus.dev', '32323232323', '2025-01-11 15:05:13', 'Test sdol', '2025-01-11 13:05:13', 1),
(9, 'Άννα', 'Μαρία', 'anna@cattus.dev', '78558586569', '2025-01-11 15:06:44', 'Teste fs', '2025-01-11 13:06:44', 1),
(11, 'Μαρκος', 'Μποτσαρης', 'mark@cattus.dev', '827878787464', '2025-01-11 15:10:45', 'Sdotpwe', '2025-01-11 13:10:45', 1),
(32, 'Scarlett', 'Levy', 'mrkakoliris@gmail.com', '6985858582', '2025-01-17 11:33:21', '', '2025-01-17 09:33:21', 1),
(33, 'asddsa', 'dsaasd', 'sdaasd@dassda.com', '123231321', '2026-01-30 09:23:50', '', '2026-01-30 07:23:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `digital_products`
--

CREATE TABLE `digital_products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL COMMENT 'To path του PDF',
  `cover_image` varchar(255) DEFAULT NULL COMMENT 'To path της εικόνας εξωφύλλου',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `digital_products`
--

INSERT INTO `digital_products` (`id`, `title`, `description`, `price`, `file_path`, `cover_image`, `created_at`) VALUES
(1, 'Test Download', 'This is a digital product for download', 22.00, 'uploads/ebooks/d0b23bc95346825ef9550a1fa8ff58a1.pdf', 'uploads/ebooks/covers/e95463b53ba8fd07782d658604b150af.jpeg', '2025-12-12 08:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `ediet_packages`
--

CREATE TABLE `ediet_packages` (
  `id` int(11) NOT NULL,
  `includes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`includes`)),
  `description` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ediet_packages`
--

INSERT INTO `ediet_packages` (`id`, `includes`, `description`, `title`, `price`, `created_at`) VALUES
(1, '[\"Example 1\",\"Example 2\",\"Example 3\"]', 'Περιλαμβάνει', 'E-Diet Package 1', 40.00, '2025-01-16 10:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `image_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `email`, `token`, `created`, `updated_at`) VALUES
(4, 'nick@cattus.dev', '9a734c77783f78ae0044d643275b8a32efc1939f5664f0ffc43c80651d7efc7c', '2024-11-11 16:14:53', '2024-11-11 16:33:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `url` text DEFAULT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `option_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `option_extra` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `option_name`, `option_value`, `option_enabled`, `option_extra`) VALUES
(1, 'companySettings', '{\"companyName\":\"Alma Psychology\",\"companyUrl\":\"www.almapsychology.gr\",\"contactEmail\":\"info@almapsychology.gr\",\"adminEmail\":\"mrkakoliris@gmail.com\",\"contactPhoneNumber\":\"(+30) xxxxxx\",\"physicalAddress\":\"xxxxxxx\",\"mapUrl\":\"https://www.google.com/maps/place/\",\"businessHours\":\"Δευ. 8:30 π.μ. – Παρ. 1:00 μ.μ.\",\"socialFacebook\":\"https://facebook.com/\",\"socialTwitter\":\"https://www.tripadvisor.com/\",\"socialInstagram\":\"https://www.instagram.com/\"}', 1, ''),
(2, 'companyLogo', '{\"logoPath\":\"assets/uploads/images/2026/January/70ab69edd31bf1b44bf9ef85ee0a5821.png\",\"logoName\":\"Transparent Logo.png\"}', 1, ''),
(3, 'smtpSettings', '{\"smtpHost\":\"ams11.siteground.eu\",\"smtpPort\":\"465\",\"fromMail\":\"testclient2@cattus.dev\",\"smtpUser\":\"testclient2@cattus.dev\",\"smtpPassword\":\"@yvx(e@~#<51\"}', 1, ''),
(4, 'mailResponses', '{\"r_newsletterTitle\":\"Επιβεβαίωση Εγγραφής στο Newsletter\",\"r_newsletterMessage\":\"<p>Thank you for subscribing to our newsletter!</p><p>You will receive updates on our news and offers.</p><p>Sincerely,<br>Our team!</p>\",\"r_submitMessageTitle\":\"Επιβεβαίωση λήψης μηνύματος\",\"r_submitMessageMessage\":\"<p>Dear {fullName},</p><p>Thank you for contacting us. We have received your message and will get back to you as soon as possible.</p><p>Sincerely,<br>Our team</p>\",\"r_completeEdietBookingTitle\":\"E-Diet - Επιβεβαίωση λήψης μηνύματος\",\"r_completeEdietBookingMessage\":\"<p>Dear {fullName},</p><p>Thank you for contacting us. We have received your message and will get back to you as soon as possible.<br><br>Package: {packageTitle}<br>Price: {price}<br>Payment Reference: {paymentToken}</p><p>Sincerely,<br>Our team</p>\",\"r_completeBookingTitle\":\"Booking - Επιβεβαίωση λήψης μηνύματος\",\"r_completeBookingMessage\":\"<p>Dear {fullName},</p><p>Thank you for booking with us. We have received your message and will get back to you as soon as possible.<br><br>Package: {packageTitle}<br>Price: {price}<br>Payment Reference: {paymentToken}</p><p>Sincerely,<br>Our team</p>\"}', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL COMMENT 'Distance in Kilometers',
  `elevation_gain` int(11) DEFAULT NULL COMMENT 'Elevation gain in meters',
  `difficulty` enum('Easy','Moderate','Hard','Elite') DEFAULT 'Moderate',
  `terrain_type` enum('Road','Trail','Mixed','Track') DEFAULT 'Road',
  `meeting_point_url` varchar(255) DEFAULT NULL COMMENT 'Google Maps Link',
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `buffer_minutes` int(11) DEFAULT 0,
  `start_datetime` datetime DEFAULT NULL,
  `includes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`includes`)),
  `gear_requirements` longtext DEFAULT NULL COMMENT 'JSON: mandatory/optional lists',
  `type` enum('online','inPerson','mixed') NOT NULL,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `max_attendants` int(11) DEFAULT 1,
  `manual_bookings` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `title`, `category_id`, `description`, `price`, `distance_km`, `elevation_gain`, `difficulty`, `terrain_type`, `meeting_point_url`, `duration_minutes`, `buffer_minutes`, `start_datetime`, `includes`, `gear_requirements`, `type`, `is_group`, `max_attendants`, `manual_bookings`, `created_at`) VALUES
(3, 'Personal Running Coaching', 1, 'Ατομική προπόνηση τεχνικής και βελτίωσης φυσικής κατάστασης σε άσφαλτο. Ιδανικό για αρχάριους.', 40.00, 5.00, 50, 'Easy', 'Road', 'https://goo.gl/maps/example1', 60, 10, NULL, '[\"Ανάλυση Τεχνικής\",\"Πλάνο Προπόνησης\",\"Νερό\"]', '{\"mandatory\":[],\"optional\":[]}', 'inPerson', 0, 4, 0, '2025-01-09 09:57:17'),
(4, 'Parnitha Mountain Experience', 2, 'Μια απαιτητική αλλά πανέμορφη διαδρομή στα μονοπάτια της Πάρνηθας. Απευθύνεται σε έμπειρους δρομείς.', 60.00, 12.50, 700, 'Hard', 'Trail', 'https://goo.gl/maps/example2', 120, 15, NULL, '[\"Οδηγός Βουνού\",\"Energy Gel\",\"Μεταφορά\"]', '{\"mandatory\":[],\"optional\":[]}', 'inPerson', 0, 2, 0, '2025-01-09 16:12:29'),
(5, 'Κυριακάτικο Long Run (Group)', 1, 'Το κλασικό μας ομαδικό τρέξιμο κάθε Κυριακή. Τρέχουμε όλοι μαζί με σταθερό ρυθμό.', 10.00, 15.00, 100, 'Moderate', 'Road', 'https://goo.gl/maps/example3', 90, 0, '2026-03-10 09:00:00', '[\"Pacer\", \"Φωτογραφίες\", \"Snack Τερματισμού\"]', NULL, 'inPerson', 1, 20, 1, '2025-01-09 16:12:53'),
(6, 'Athens City Center Fun Run', 4, 'Χαλαρό τρέξιμο στο ιστορικό κέντρο της Αθήνας. Στάσεις για φωτογραφίες και καφέ στο τέλος.', 15.00, 5.00, 30, 'Easy', 'Mixed', 'https://goo.gl/maps/example4', 60, 20, NULL, '[\"Ξενάγηση\",\"Καφές\",\"Αναμνηστικό\"]', '{\"mandatory\":[],\"optional\":[]}', 'inPerson', 0, 15, 0, '2025-01-16 18:51:16'),
(7, 'Προπόνηση Στίβου (Intervals)', 3, 'Εντατική προπόνηση ταχύτητας σε ταρτάν. Βελτιώστε την ταχύτητα και την εκρηκτικότητά σας.', 20.00, 6.00, 0, 'Hard', 'Track', 'https://goo.gl/maps/example5', 75, 0, '2026-01-28 19:00:00', '[\"Χρονόμετρηση\", \"Ασκήσεις Ενδυνάμωσης\", \"Ισοτονικό\"]', NULL, 'inPerson', 1, 10, 7, '2026-01-26 16:58:08'),
(8, 'Night Urban Trail (Λυκαβηττός)', 2, 'Βραδινό τρέξιμο στα μονοπάτια του Λυκαβηττού με φακούς κεφαλής.', 25.00, 7.00, 250, 'Moderate', 'Trail', 'https://goo.gl/maps/example6', 60, 40, NULL, '[\"Φακός Κεφαλής (Δανεισμός)\",\"Μπίρα Τερματισμού\"]', '{\"mandatory\":[],\"optional\":[]}', 'inPerson', 0, 1, 0, '2026-01-26 18:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `package_therapists`
--

CREATE TABLE `package_therapists` (
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `therapist_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_therapists`
--

INSERT INTO `package_therapists` (`package_id`, `therapist_id`) VALUES
(3, 2),
(4, 1),
(4, 2),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `amount_total` varchar(255) NOT NULL,
  `fee` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `payed_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `token` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `card_type` varchar(50) NOT NULL,
  `card_holder_name` varchar(255) NOT NULL,
  `card_name` varchar(255) NOT NULL,
  `billing_country` varchar(255) NOT NULL,
  `billing_city` varchar(255) NOT NULL,
  `note` tinytext NOT NULL,
  `amount_paid` varchar(255) NOT NULL,
  `paymentRef` varchar(255) NOT NULL,
  `payer_email` varchar(255) NOT NULL,
  `billing_zip` varchar(255) NOT NULL,
  `billing_address` varchar(255) NOT NULL,
  `date_created` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `client_id`, `reservation_id`, `amount_total`, `fee`, `status`, `payment_method`, `payed_at`, `created_at`, `updated_at`, `token`, `description`, `currency`, `card_type`, `card_holder_name`, `card_name`, `billing_country`, `billing_city`, `note`, `amount_paid`, `paymentRef`, `payer_email`, `billing_zip`, `billing_address`, `date_created`) VALUES
(587, 32, 0, '22.00', '', 'Captured', 'EveryPay', '0000-00-00 00:00:00', '2026-01-22 08:53:15', NULL, 'pmt_doWW5sPFwTvMoSZExjZLnWa7', '', 'EUR', 'Visa', '', 'Visa •••• 9395 (05/2026)', 'GR', 'asd', '', '22.00', 'EBOOK-pmt_doWW', 'mrkakoliris@gmail.com', '231', 'das', '2026-01-22'),
(592, 32, 29, '50', '', 'Completed', 'Cash', '2026-01-29 12:00:00', '2026-01-29 15:22:31', '2026-01-29 16:14:24', '', '', '', '', '', '', '', '', '', '50', '', '', '', '', NULL),
(593, 8, 30, '50', '', 'Completed', 'Cash', '2026-01-29 12:00:00', '2026-01-29 16:18:02', NULL, '', '', '', '', '', '', '', '', '', '50', '', '', '', '', NULL),
(594, 0, 38, '60', '', 'Completed', 'Cash', '2026-02-09 18:34:35', '2026-02-09 16:04:01', '2026-02-09 16:34:35', '', '', '', '', '', '', '', '', '', '60', '', '', '', '', NULL),
(595, 11, 37, '60', '', 'Completed', 'Cash', '2026-02-10 12:57:18', '2026-02-10 10:57:18', NULL, '', '', '', '', '', '', '', '', '', '60', '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_orders`
--

CREATE TABLE `product_orders` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `payment_token` varchar(255) NOT NULL,
  `download_token` varchar(64) NOT NULL,
  `downloads_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_orders`
--

INSERT INTO `product_orders` (`id`, `client_id`, `product_id`, `payment_token`, `download_token`, `downloads_count`, `created_at`) VALUES
(1, 32, 1, 'pmt_NLBOdJ5vhjUyY43PaFHG5Ht3', '6bd99f85bb3187342fca4180a8506898cb91fbce833deba124d9f551fd28277d', 0, '2025-12-12 12:35:15'),
(2, 32, 1, 'pmt_kxPe7MWkwopz54lUfcFGeew1', '3da3ae2911612d0914fc00754efd78a2dac8e009690577aa3caee02a0d0bd553', 0, '2025-12-12 11:53:13'),
(3, 32, 1, 'pmt_hhIRBg93uTSObea3cQzwTf0o', 'c903c1b22b1c2af7428e9207202689a11ea7868dac5933fd6687bbde12962a70', 0, '2025-12-12 11:55:52'),
(4, 32, 1, 'pmt_pjfNcrotE4xcjtQfAUjEQ5JS', '43df000aa47d742111123eb5fe46775c6755d9ecb9ad314f5994077fc86764c9', 0, '2025-12-12 11:58:46'),
(5, 32, 1, 'pmt_MInjQcVhqLW6RaQT9vNv6xjZ', 'c1ee723733a0c1e685c7bc0348a9b68da5da86dc5e27072272c8bf82f6c2dc62', 0, '2025-12-12 12:02:19'),
(6, 32, 1, 'pmt_jg4IIUJfRoSDpvQLXTz2WF8X', '82c444bea3e7998eadad8c2e6c1d9f04907d1f223e496ae4228145e5a9b3cd4a', 0, '2025-12-12 12:07:36'),
(7, 32, 1, 'pmt_Tihhn533cS6PNHm2jaHgYRkc', '8850afe60c7af7f99bfeff7eec6007be5de3567b7b8e03d648e3da2e5dffcd75', 0, '2025-12-12 12:49:15'),
(8, 32, 1, 'pmt_dOIc0acdWexobkcGnUZqAHXj', '6a67b1516b6ff764dc85bde730166347853276a2b3a63e4a8bdb3758db08c9a2', 0, '2025-12-12 12:26:17'),
(9, 32, 1, 'pmt_fVq9kxxiHwPyi12mnF7n0Smw', 'ee19aa8de4d2794bca99e402fc97e5c86aac86378f36915d5478df0d989b6b8f', 1, '2026-01-22 07:59:45'),
(10, 32, 1, 'pmt_wv3IhXLNOj9lOafECgMIOpML', '88fd809485c4f00a4fbf28b75c44451f4195be12fd8108732ba01deed1c98bf5', 0, '2026-01-22 08:06:57'),
(11, 32, 1, 'pmt_LsrxDlPfNMjPfYsIJ6PHVOQZ', '420ac77d0ffff4e9222c8d50fcb2e955c58670e7df55ed037b35a68afb604151', 0, '2026-01-22 08:13:34'),
(12, 32, 1, 'pmt_doWW5sPFwTvMoSZExjZLnWa7', '8181cad5e929244d1c82275bdeed6a095dfd529a44d37c4802e5228383d600d2', 2, '2026-01-22 08:53:15');

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `therapist_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `status` enum('available','booked','other') NOT NULL DEFAULT 'available',
  `appointment_type` enum('inPerson','online','mixed') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `slot_packages`
--

CREATE TABLE `slot_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slot_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `therapists`
--

CREATE TABLE `therapists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `title` varchar(150) DEFAULT NULL COMMENT 'π.χ. Ψυχολόγος, Σύμβουλος',
  `bio` text DEFAULT NULL,
  `languages` varchar(255) DEFAULT NULL COMMENT 'e.g. English, Greek',
  `pace_range` varchar(100) DEFAULT NULL COMMENT 'e.g. 5:00-6:30 min/km',
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Path φωτογραφίας',
  `is_active` tinyint(1) DEFAULT 1,
  `booking_window_days` int(11) DEFAULT 60 COMMENT 'Πόσες μέρες μπροστά ανοίγει το πρόγραμμα',
  `min_notice_hours` int(11) DEFAULT 12 COMMENT 'Ελάχιστη προειδοποίηση σε ώρες',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `color` varchar(7) DEFAULT '#3788d8' COMMENT 'Hex Color για το Calendar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `therapists`
--

INSERT INTO `therapists` (`id`, `first_name`, `last_name`, `title`, `bio`, `languages`, `pace_range`, `email`, `phone`, `avatar`, `is_active`, `booking_window_days`, `min_notice_hours`, `created_at`, `color`) VALUES
(1, 'John', 'Souvito', 'Runner', 'ADSIJHASJDIh:AKOSDjaos;DJ', '', '', 'john@plakiasrunning.gr', '6985858585', 'uploads/therapists/c656ae2d548e9d264e70c7ba5cea26dc.jpg', 1, 60, 12, '2026-01-27 14:57:58', '#3788d8'),
(2, 'Sarra', 'Doe', 'Runner', 'dasads', '', '', 'sarra@plakiasrunning.gr', '6544654654', 'uploads/therapists/60884161a6410626e87f4161b8f3b2cc.jpg', 1, 60, 12, '2026-01-27 15:04:23', '#3788d8');

-- --------------------------------------------------------

--
-- Table structure for table `therapist_availability_rules`
--

CREATE TABLE `therapist_availability_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `therapist_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED DEFAULT NULL,
  `weekday` tinyint(3) UNSIGNED NOT NULL COMMENT '0=Sun ... 6=Sat',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `appointment_type` enum('inPerson','online','mixed') DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `therapist_availability_rules`
--

INSERT INTO `therapist_availability_rules` (`id`, `therapist_id`, `package_id`, `weekday`, `start_time`, `end_time`, `appointment_type`, `is_active`, `created_at`, `updated_at`) VALUES
(19, 1, 6, 0, '09:00:00', '10:00:00', NULL, 1, '2026-02-10 15:25:04', NULL),
(27, 2, 4, 1, '21:00:00', '23:00:00', NULL, 1, '2026-02-10 16:15:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `therapist_time_blocks`
--

CREATE TABLE `therapist_time_blocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `therapist_id` bigint(20) UNSIGNED NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `kind` enum('block','extra_open') NOT NULL DEFAULT 'block',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `therapist_time_blocks`
--

INSERT INTO `therapist_time_blocks` (`id`, `therapist_id`, `start_datetime`, `end_datetime`, `kind`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-02-09 11:00:00', '2026-02-09 12:00:00', 'block', 'Test book', '2026-01-28 17:48:40', NULL),
(2, 2, '2026-01-28 11:00:00', '2026-01-28 12:00:00', 'block', 'tedt', '2026-01-28 18:56:23', NULL),
(4, 1, '2026-01-27 11:00:00', '2026-01-27 12:30:00', 'block', 'Out for coffee', '2026-01-29 19:18:25', NULL),
(5, 2, '2026-03-11 09:00:00', '2026-03-11 10:30:00', 'block', 'dafs', '2026-02-09 18:35:35', NULL),
(6, 1, '2026-02-10 09:00:00', '2026-02-10 10:00:00', 'block', '', '2026-02-10 15:53:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `access` int(11) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attempts` int(11) NOT NULL,
  `twoFactorAuth` tinyint(1) NOT NULL,
  `last_login_attempt` timestamp NULL DEFAULT current_timestamp(),
  `total_attempts` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `firstName`, `lastName`, `access`, `locked`, `locked_until`, `created_at`, `updated_at`, `attempts`, `twoFactorAuth`, `last_login_attempt`, `total_attempts`) VALUES
(1, 'nick@cattus.dev', '$2y$10$Y6.N0c4BHcMg0Wk3NNjpSe3Drfa1dK8F4Dyw8k5zdAJoGDeqCCfxa', 'Nick', 'Kakoliris', 1, 0, NULL, '2023-09-05 07:59:34', '2026-02-10 08:59:27', 0, 0, '2026-02-10 08:59:27', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `permissions` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `name`, `permissions`) VALUES
(1, 'Administrator', '{\"admin\":1,\"editor\":1,\"user\":1}'),
(2, 'Editor', '{\"admin\":0,\"editor\":1,\"user\":0}'),
(3, 'User', '{\"admin\":0,\"editor\":0,\"user\":1}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_slot_id` (`slot_id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `package_id` (`package_id`),
  ADD KEY `idx_booking_therapist` (`therapist_id`),
  ADD KEY `idx_booking_therapist_start` (`therapist_id`,`start_datetime`),
  ADD KEY `idx_booking_therapist_end` (`therapist_id`,`end_datetime`),
  ADD KEY `idx_booking_package` (`package_id`),
  ADD KEY `idx_booking_dates` (`start_datetime`,`end_datetime`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_email` (`email`);

--
-- Indexes for table `digital_products`
--
ALTER TABLE `digital_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ediet_packages`
--
ALTER TABLE `ediet_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_package_category` (`category_id`);

--
-- Indexes for table `package_therapists`
--
ALTER TABLE `package_therapists`
  ADD PRIMARY KEY (`package_id`,`therapist_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_orders`
--
ALTER TABLE `product_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_start_end` (`start_datetime`,`end_datetime`),
  ADD KEY `idx_slot_therapist` (`therapist_id`);

--
-- Indexes for table `slot_packages`
--
ALTER TABLE `slot_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_id` (`slot_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `therapists`
--
ALTER TABLE `therapists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `therapist_availability_rules`
--
ALTER TABLE `therapist_availability_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rule_therapist_weekday` (`therapist_id`,`weekday`,`is_active`),
  ADD KEY `fk_rules_package` (`package_id`);

--
-- Indexes for table `therapist_time_blocks`
--
ALTER TABLE `therapist_time_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_block_therapist_start` (`therapist_id`,`start_datetime`),
  ADD KEY `idx_block_therapist_end` (`therapist_id`,`end_datetime`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `digital_products`
--
ALTER TABLE `digital_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ediet_packages`
--
ALTER TABLE `ediet_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=596;

--
-- AUTO_INCREMENT for table `product_orders`
--
ALTER TABLE `product_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1875;

--
-- AUTO_INCREMENT for table `slot_packages`
--
ALTER TABLE `slot_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3020;

--
-- AUTO_INCREMENT for table `therapists`
--
ALTER TABLE `therapists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `therapist_availability_rules`
--
ALTER TABLE `therapist_availability_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `therapist_time_blocks`
--
ALTER TABLE `therapist_time_blocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_client_id` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_slot_id` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `packages`
--
ALTER TABLE `packages`
  ADD CONSTRAINT `fk_package_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `slot_packages`
--
ALTER TABLE `slot_packages`
  ADD CONSTRAINT `slot_packages_ibfk_1` FOREIGN KEY (`slot_id`) REFERENCES `slots` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `slot_packages_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `therapist_availability_rules`
--
ALTER TABLE `therapist_availability_rules`
  ADD CONSTRAINT `fk_rules_package` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rules_therapist` FOREIGN KEY (`therapist_id`) REFERENCES `therapists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `therapist_time_blocks`
--
ALTER TABLE `therapist_time_blocks`
  ADD CONSTRAINT `fk_blocks_therapist` FOREIGN KEY (`therapist_id`) REFERENCES `therapists` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
