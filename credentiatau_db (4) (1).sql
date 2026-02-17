-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Feb 09, 2026 at 05:06 PM
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
-- Database: `credentiatau_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_records`
--

CREATE TABLE `academic_records` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `record_type` enum('Transcript','Diploma','Certificate','Grades') NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','archived','deleted') DEFAULT 'active',
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_requests`
--

CREATE TABLE `access_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` datetime DEFAULT current_timestamp(),
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_locks`
--

CREATE TABLE `account_locks` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lock_type` enum('password_attempts','code_attempts') NOT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `unlock_at` timestamp NULL DEFAULT NULL,
  `is_unlocked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 08:54:39'),
(2, 1, 'code_resent', 'Verification code resent', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 08:56:03'),
(3, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 08:57:28'),
(4, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:07:03'),
(5, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:07:26'),
(6, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:21:45'),
(7, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:23:13'),
(8, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:23:30'),
(9, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:12:48'),
(10, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:13:05'),
(11, 1, 'user_created', 'Created new admin: abalaryxel@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:14:19'),
(12, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:15:33'),
(13, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:15:56'),
(14, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:16:31'),
(15, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:17:20'),
(16, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:05:09'),
(17, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:05:45'),
(18, 1, 'user_created', 'Created new admin: mltripoli@tau.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:08:11'),
(19, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:08:37'),
(20, 3, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:08:58'),
(21, 3, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:09:40'),
(22, 3, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:10:27'),
(23, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:11:18'),
(24, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:11:37'),
(25, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:12:22'),
(26, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 13:37:25'),
(27, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 13:37:39'),
(28, 1, 'user_deactivated', 'User deactivated: mltripoli@tau.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 13:45:39'),
(29, 1, 'user_reactivated', 'User reactivated: mltripoli@tau.edu.ph', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 13:46:21'),
(30, 1, 'user_updated', 'Updated user: mltripoli@tau.edu.phl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:03:08'),
(31, 1, 'user_updated', 'Updated user: mltripoli@tau.edu.phl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:41:29'),
(32, 1, 'user_deactivated', 'User deactivated: mltripoli@tau.edu.phl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:43:12'),
(33, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:43:19'),
(34, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:44:26'),
(35, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:44:47'),
(36, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:51:18'),
(37, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:51:54'),
(38, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:52:11'),
(39, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:52:41'),
(40, 1, 'user_deactivated', 'User deactivated: abalaryxel@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:52:55'),
(41, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:53:29'),
(42, 2, 'approval_request_sent', 'User sent reactivation request', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:53:53'),
(43, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:56:08'),
(44, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:56:19'),
(45, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 16:01:05'),
(46, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:07:31'),
(47, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:07:58'),
(48, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:12:45'),
(49, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:13:15'),
(50, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:13:36'),
(51, 1, 'user_reactivated', 'User reactivated: abalaryxel@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:13:48'),
(52, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:14:02'),
(53, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:14:21'),
(54, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:14:39'),
(55, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 04:48:29'),
(56, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 04:51:58'),
(57, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 04:52:21'),
(58, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 04:52:27'),
(59, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:18:23'),
(60, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:18:59'),
(61, 1, 'user_updated', 'Updated user: mltripoli@tau.edu.phl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:20:39'),
(62, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:20:48'),
(63, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:21:25'),
(64, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:22:26'),
(65, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:27:55'),
(66, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:28:32'),
(67, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:28:53'),
(68, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 15:30:32'),
(69, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 15:30:43'),
(70, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 09:44:51'),
(71, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 09:45:06'),
(72, 1, 'user_reactivated', 'User reactivated: mltripoli@tau.edu.phl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 11:36:10'),
(73, 1, 'user_deactivated', 'User deactivated: mltripoli@tau.edu.phl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 11:59:30'),
(74, 1, 'user_created', 'Created new admin: reinabala0224@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 13:55:29'),
(75, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 13:56:28'),
(76, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 05:06:17'),
(77, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 05:06:38'),
(78, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 09:41:53'),
(79, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 09:42:11'),
(80, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 09:42:15'),
(81, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:07:36'),
(82, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:08:09'),
(83, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:19:52'),
(84, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:20:09'),
(85, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:20:32'),
(86, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:24:24'),
(87, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:24:39'),
(88, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:25:05'),
(89, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:47:27'),
(90, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:47:55'),
(91, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:49:08'),
(92, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 14:40:28'),
(93, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 14:40:46'),
(94, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 14:41:10'),
(95, 1, 'profile_updated', 'Profile updated: artryry6@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 15:41:39'),
(96, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:06:31'),
(97, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:06:51'),
(98, 1, 'user_updated', 'Updated user: reinabala0224@gmail.com - Updated: username: reinabala0224 → reinrein, staff unit:  → None', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:35:18'),
(99, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com - Updated: username: abalaryxel → monay, staff unit:  → Admission Staff', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:36:34'),
(100, 1, 'user_updated', 'Updated user: reinabala0224@gmail.com - Updated: username: reinabala0224 → reina, staff unit:  → None', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:42:48'),
(101, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:45:48'),
(102, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:46:05'),
(103, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:04:19'),
(104, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:04:41'),
(105, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:14:54'),
(106, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:15:14'),
(107, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:15:39'),
(108, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:15:43'),
(109, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:10:05'),
(110, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:10:29'),
(111, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:47:19'),
(112, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:47:43'),
(113, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:48:07'),
(114, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:30:06'),
(115, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:30:35'),
(116, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:34:32'),
(117, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:34:55'),
(118, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:35:10'),
(119, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 01:52:43'),
(120, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 01:53:00'),
(121, 1, 'user_updated', 'Updated user: reinabala0224@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 02:08:22'),
(122, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 07:19:03'),
(123, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 07:19:22'),
(124, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com - Updated: username: abalaryxel → abalaryxel00, role: User → User (Admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 08:41:03'),
(125, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com - Updated: role: User (Admin) → Admin (Super Admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 08:41:20'),
(126, 1, 'user_updated', 'Updated user: artryry6@gmail.com - Updated: username: artryry6 → artryry60, role:  → User (Admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 08:42:03'),
(127, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com - Updated: role:  → Admin (Super Admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 08:42:56'),
(128, 1, 'user_updated', 'Updated user: reinabala0224@gmail.com - Updated: username: reinabala0224 → reinabala022477, role: User → User (Admin)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 10:17:23'),
(129, 1, 'user_updated', 'Updated user: reinabala0224@gmail.com - Updated: username: reinabala022477 → reinabala022477kjksd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 10:17:37'),
(130, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:00:54'),
(131, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:01:12'),
(132, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:01:45'),
(133, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:03:02'),
(134, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:03:17'),
(135, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:04:58'),
(136, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:05:13'),
(137, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:05:27'),
(138, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:18:45'),
(139, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:19:15'),
(140, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:19:33'),
(141, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 18:02:27'),
(142, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 18:02:43'),
(143, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 18:02:56'),
(144, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 18:12:48'),
(145, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 02:12:58'),
(146, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 02:13:18'),
(147, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:17:17'),
(148, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:18:08'),
(149, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:18:44'),
(150, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:20:10'),
(151, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:20:29'),
(152, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:20:43'),
(153, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:43:47'),
(154, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:44:16'),
(155, 2, 'code_resent', 'Verification code resent', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:45:26'),
(156, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:46:10'),
(157, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:48:00'),
(158, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:48:19'),
(159, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:48:38'),
(160, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:29'),
(161, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:34'),
(162, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:53'),
(163, 1, 'super_admin_dashboard_access', 'Accessed super admin dashboard', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:53'),
(164, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:28:54'),
(165, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:29:09'),
(166, 2, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:29:30'),
(167, 2, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:29:52'),
(168, 2, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:37:25'),
(169, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 04:09:49'),
(170, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 04:10:14'),
(171, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 13:58:50'),
(172, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 13:59:24'),
(173, 1, 'user_created', 'Created new User (Former Admin): teamorjj@gmail.com (username: orrrr)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:25:50'),
(174, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:26:33'),
(175, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:27:12'),
(176, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:27:27'),
(177, 5, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:27:45'),
(178, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:27:59'),
(179, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:28:18'),
(180, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated: username: orrrr → orrrr123', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:29:07'),
(181, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:30:20'),
(182, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:30:34'),
(183, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:30:36'),
(184, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:30:52'),
(185, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:31:16'),
(186, 5, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:32:09'),
(187, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:32:23'),
(188, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `approval_requests`
--

CREATE TABLE `approval_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `request_type` enum('reactivation','initial_access') DEFAULT 'reactivation',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approval_requests`
--

INSERT INTO `approval_requests` (`id`, `user_id`, `email`, `full_name`, `request_type`, `status`, `message`, `ip_address`, `user_agent`, `requested_at`, `reviewed_at`, `reviewed_by`) VALUES
(1, 2, 'abalaryxel@gmail.com', 'Monay third Dela Cruz ', 'reactivation', 'pending', 'User requesting account reactivation', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:53:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `attempt_type` enum('password','verification_code') NOT NULL,
  `is_successful` tinyint(1) DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `attempt_type`, `is_successful`, `ip_address`, `user_agent`, `attempted_at`) VALUES
(1, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 03:21:58'),
(2, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 03:22:18'),
(3, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 03:23:53'),
(4, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 03:40:58'),
(5, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 08:54:32'),
(6, 'artryry6@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 08:56:56'),
(7, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 08:57:28'),
(8, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:06:57'),
(9, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:07:26'),
(10, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:23:08'),
(11, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 11:23:30'),
(12, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:12:41'),
(13, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:13:05'),
(14, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:15:50'),
(15, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-27 15:16:31'),
(16, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:05:03'),
(17, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:05:45'),
(18, 'mltripoli@tau.edu.ph', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:08:54'),
(19, 'mltripoli@tau.edu.ph', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:09:40'),
(20, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:11:13'),
(21, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:11:37'),
(22, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 13:37:18'),
(23, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 13:37:39'),
(24, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:44:20'),
(25, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 14:44:47'),
(26, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:51:47'),
(27, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:52:11'),
(28, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:56:02'),
(29, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 15:56:19'),
(30, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:07:27'),
(31, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:07:58'),
(32, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:13:11'),
(33, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:13:36'),
(34, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:14:17'),
(35, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 03:14:39'),
(36, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 04:51:52'),
(37, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 04:52:21'),
(38, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:18:18'),
(39, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:18:59'),
(40, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:21:21'),
(41, 'abalaryxel@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:22:04'),
(42, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:22:26'),
(43, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:28:04'),
(44, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:28:27'),
(45, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-29 06:28:53'),
(46, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 15:30:27'),
(47, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-30 15:30:43'),
(48, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 09:44:46'),
(49, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-31 09:45:06'),
(50, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 05:06:11'),
(51, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 05:06:38'),
(52, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 09:41:48'),
(53, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-01 09:42:11'),
(54, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:07:05'),
(55, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:07:27'),
(56, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:08:09'),
(57, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:20:04'),
(58, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:20:32'),
(59, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:24:34'),
(60, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:25:05'),
(61, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:47:48'),
(62, 'abalaryxel@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:48:52'),
(63, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 10:49:08'),
(64, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 14:40:40'),
(65, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-02 14:41:10'),
(66, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:06:24'),
(67, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:06:51'),
(68, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 03:46:00'),
(69, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:04:13'),
(70, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:04:41'),
(71, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:15:07'),
(72, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 08:15:39'),
(73, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:09:58'),
(74, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:10:29'),
(75, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:47:36'),
(76, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-03 09:48:07'),
(77, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:30:01'),
(78, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:30:35'),
(79, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:34:51'),
(80, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-05 15:35:10'),
(81, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 01:52:38'),
(82, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 01:53:00'),
(83, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 07:18:58'),
(84, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 07:19:22'),
(85, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:00:28'),
(86, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:00:43'),
(87, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:01:12'),
(88, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:02:57'),
(89, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:03:17'),
(90, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:05:09'),
(91, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:05:27'),
(92, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:19:10'),
(93, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 17:19:33'),
(94, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 18:02:38'),
(95, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-07 18:02:56'),
(96, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 02:12:53'),
(97, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 02:13:18'),
(98, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:18:04'),
(99, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:18:44'),
(100, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:20:24'),
(101, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:20:43'),
(102, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:44:11'),
(103, 'abalaryxel@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:45:06'),
(104, 'abalaryxel@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:45:15'),
(105, 'abalaryxel@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:45:41'),
(106, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:46:10'),
(107, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:48:13'),
(108, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 04:48:38'),
(109, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:15'),
(110, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:24'),
(111, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:29'),
(112, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:27:53'),
(113, 'abalaryxel@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:29:26'),
(114, 'abalaryxel@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-08 16:29:52'),
(115, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 04:09:43'),
(116, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 04:10:14'),
(117, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 13:58:46'),
(118, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 13:59:24'),
(119, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:27:07'),
(120, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:27:27'),
(121, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:27:54'),
(122, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:28:18'),
(123, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:30:48'),
(124, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:31:16'),
(125, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:32:18'),
(126, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:32:36');

-- --------------------------------------------------------

--
-- Table structure for table `student_folders`
--

CREATE TABLE `student_folders` (
  `id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'max_login_attempts', '5', 'Maximum number of failed login attempts before account lock', NULL, '2026-01-24 06:36:49'),
(2, 'account_lock_duration', '5', 'Account lock duration in minutes', NULL, '2026-01-24 06:36:49'),
(3, 'verification_code_expiry', '10', 'Verification code expiry time in minutes', NULL, '2026-01-24 06:36:49'),
(4, 'max_code_attempts', '5', 'Maximum number of failed verification code attempts', NULL, '2026-01-24 06:36:49'),
(5, 'smtp_host', 'smtp.gmail.com', 'SMTP server host', NULL, '2026-01-24 06:36:49'),
(6, 'smtp_port', '587', 'SMTP server port', NULL, '2026-01-24 06:36:49'),
(7, 'smtp_username', 'artryry6@gmail.com', 'SMTP username', NULL, '2026-01-24 06:36:49'),
(8, 'smtp_encryption', 'tls', 'SMTP encryption type', NULL, '2026-01-24 06:36:49'),
(9, 'system_email', 'artryry6@gmail.com', 'System email address for notifications', NULL, '2026-01-24 06:36:49'),
(10, 'max_file_size', '10485760', 'Maximum file upload size in bytes (10MB)', NULL, '2026-01-24 06:36:49'),
(11, 'allowed_file_types', 'pdf,doc,docx', 'Allowed file types for upload', NULL, '2026-01-24 06:36:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `access_level` enum('full','limited') NOT NULL DEFAULT 'full',
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `initial_password_changed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `username`, `password`, `role`, `access_level`, `status`, `initial_password_changed`, `created_at`, `updated_at`, `created_by`, `last_login`) VALUES
(1, 'Rhiel', 'artryry6@gmail.com', 'artryry60', '$2y$10$RyGBR7m.NjnnzzlJbRPUi.VIQQ9ob.5X0cN3VW6LvrKN4druJ1cLq', 'admin', 'full', 'active', 1, '2026-01-24 06:36:49', '2026-02-09 06:32:36', NULL, '2026-02-09 06:32:36'),
(2, 'Monay third Dela Cruz hhh', 'abalaryxel@gmail.com', 'abalaryxel00', '$2y$10$VjCEEBKmXFOHkBE4bKPUf.5RIji5BhASBn3V5qyW3SGhDobm2pUnu', 'admin', 'full', 'active', 0, '2026-01-27 07:14:19', '2026-02-09 12:16:19', 1, '2026-02-08 08:29:52'),
(3, 'Vince Tripoli Aray ko po aray aray', 'mltripoli@tau.edu.phl', 'mltripoli', '$2y$10$jpJI5TPvIEJeQB1.AsL0eOpmIuVP.30aOJoptqC643UhNQPeX8hMe', 'user', 'full', 'inactive', 0, '2026-01-28 02:08:10', '2026-02-07 07:44:44', 1, '2026-01-28 02:09:40'),
(4, 'Rein Abala', 'reinabala0224@gmail.com', 'reinabala022477kjksd', '$2y$10$SXQ7qDcQCSl.aW.a2qBaWO5/sL2AU6BdSiCo4FGCPku7ynfX0qyJu', 'user', 'full', 'inactive', 0, '2026-01-31 05:55:28', '2026-02-09 12:16:19', 1, NULL),
(5, 'Orjj', 'teamorjj@gmail.com', 'orrrr123', '$2y$10$ZWFXL4oKpvQnivjf1OId3.klZo8Dy2LOgGAFRO4TVeotFGaDIePj6', 'user', 'limited', 'active', 0, '2026-02-09 06:25:50', '2026-02-09 06:31:16', 1, '2026-02-09 06:31:16');

-- --------------------------------------------------------

--
-- Table structure for table `user_privileges`
--

CREATE TABLE `user_privileges` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `privilege_key` varchar(50) NOT NULL,
  `privilege_value` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_privileges`
--

INSERT INTO `user_privileges` (`id`, `user_id`, `privilege_key`, `privilege_value`, `created_at`, `updated_at`) VALUES
(11, 3, 'records_view', 1, '2026-02-07 02:26:28', '2026-02-07 02:26:28'),
(14, 3, 'records_create', 1, '2026-02-07 02:26:28', '2026-02-07 02:26:28'),
(17, 3, 'records_edit', 1, '2026-02-07 02:26:28', '2026-02-07 02:26:28'),
(20, 3, 'records_delete', 1, '2026-02-07 02:26:28', '2026-02-07 02:26:28'),
(35, 1, 'records_view', 1, '2026-02-07 00:42:03', '2026-02-07 00:42:03'),
(36, 1, 'records_create', 1, '2026-02-07 00:42:03', '2026-02-07 00:42:03'),
(37, 1, 'records_edit', 1, '2026-02-07 00:42:03', '2026-02-07 00:42:03'),
(38, 1, 'records_delete', 1, '2026-02-07 00:42:03', '2026-02-07 00:42:03'),
(39, 2, 'records_view', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(40, 2, 'records_create', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(41, 2, 'records_edit', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(42, 2, 'records_delete', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(43, 2, 'profile_edit', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(44, 2, 'user_management', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(45, 2, 'system_backup', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(46, 2, 'audit_logs', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(47, 2, 'full_admin', 1, '2026-02-07 00:42:56', '2026-02-07 00:42:56'),
(48, 4, 'records_view', 1, '2026-02-07 02:17:23', '2026-02-07 02:17:23'),
(49, 4, 'records_create', 1, '2026-02-07 02:17:23', '2026-02-07 02:17:23'),
(50, 4, 'records_edit', 1, '2026-02-07 02:17:23', '2026-02-07 02:17:23'),
(51, 4, 'records_delete', 1, '2026-02-07 02:17:23', '2026-02-07 02:17:23'),
(52, 5, 'records_view', 1, '2026-02-09 06:25:50', '2026-02-09 06:25:50'),
(53, 5, 'records_create', 1, '2026-02-09 06:25:50', '2026-02-09 06:25:50'),
(54, 5, 'records_edit', 1, '2026-02-09 06:25:50', '2026-02-09 06:25:50'),
(55, 5, 'records_delete', 1, '2026-02-09 06:25:50', '2026-02-09 06:25:50');

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification_codes`
--

INSERT INTO `verification_codes` (`id`, `user_id`, `email`, `code`, `expires_at`, `is_used`, `used_at`, `created_at`) VALUES
(1, 1, 'artryry6@gmail.com', '155459', '2026-01-27 01:04:32', 1, NULL, '2026-01-27 08:54:32'),
(2, 1, 'artryry6@gmail.com', '011396', '2026-01-27 01:05:57', 1, '2026-01-27 00:57:28', '2026-01-27 08:55:57'),
(3, 1, 'artryry6@gmail.com', '864764', '2026-01-27 03:16:57', 1, '2026-01-27 03:07:26', '2026-01-27 11:06:57'),
(4, 1, 'artryry6@gmail.com', '684550', '2026-01-27 03:33:08', 1, '2026-01-27 03:23:30', '2026-01-27 11:23:08'),
(5, 1, 'artryry6@gmail.com', '719682', '2026-01-27 07:22:41', 1, '2026-01-27 07:13:05', '2026-01-27 15:12:41'),
(6, 2, 'abalaryxel@gmail.com', '354420', '2026-01-27 07:25:50', 1, '2026-01-27 07:16:31', '2026-01-27 15:15:50'),
(7, 1, 'artryry6@gmail.com', '837495', '2026-01-28 02:15:03', 1, '2026-01-28 02:05:45', '2026-01-28 10:05:03'),
(8, 3, 'mltripoli@tau.edu.ph', '283167', '2026-01-28 02:18:54', 1, '2026-01-28 02:09:40', '2026-01-28 10:08:54'),
(9, 1, 'artryry6@gmail.com', '435424', '2026-01-28 02:21:13', 1, '2026-01-28 02:11:37', '2026-01-28 10:11:13'),
(10, 1, 'artryry6@gmail.com', '319608', '2026-01-28 05:47:18', 1, '2026-01-28 05:37:39', '2026-01-28 13:37:18'),
(11, 1, 'artryry6@gmail.com', '564555', '2026-01-28 06:54:20', 1, '2026-01-28 06:44:47', '2026-01-28 14:44:20'),
(12, 1, 'artryry6@gmail.com', '541465', '2026-01-28 08:01:47', 1, '2026-01-28 07:52:11', '2026-01-28 15:51:47'),
(13, 1, 'artryry6@gmail.com', '564170', '2026-01-28 08:06:02', 1, '2026-01-28 07:56:19', '2026-01-28 15:56:02'),
(14, 1, 'artryry6@gmail.com', '754790', '2026-01-28 19:17:27', 1, '2026-01-28 19:07:58', '2026-01-29 03:07:27'),
(15, 1, 'artryry6@gmail.com', '329791', '2026-01-28 19:23:11', 1, '2026-01-28 19:13:36', '2026-01-29 03:13:11'),
(16, 2, 'abalaryxel@gmail.com', '740590', '2026-01-28 19:24:17', 1, '2026-01-28 19:14:39', '2026-01-29 03:14:17'),
(17, 1, 'artryry6@gmail.com', '626900', '2026-01-28 21:01:52', 1, '2026-01-28 20:52:21', '2026-01-29 04:51:52'),
(18, 1, 'artryry6@gmail.com', '882996', '2026-01-28 22:28:18', 1, '2026-01-28 22:18:59', '2026-01-29 06:18:18'),
(19, 2, 'abalaryxel@gmail.com', '724427', '2026-01-28 22:31:21', 1, '2026-01-28 22:22:26', '2026-01-29 06:21:21'),
(20, 1, 'artryry6@gmail.com', '870253', '2026-01-28 22:38:27', 1, '2026-01-28 22:28:53', '2026-01-29 06:28:27'),
(21, 1, 'artryry6@gmail.com', '160704', '2026-01-30 07:40:27', 1, '2026-01-30 07:30:43', '2026-01-30 15:30:27'),
(22, 1, 'artryry6@gmail.com', '181273', '2026-01-31 01:54:46', 1, '2026-01-31 01:45:06', '2026-01-31 09:44:46'),
(23, 2, 'abalaryxel@gmail.com', '158363', '2026-01-31 21:16:11', 1, '2026-01-31 21:06:38', '2026-02-01 05:06:11'),
(24, 1, 'artryry6@gmail.com', '915761', '2026-02-01 01:51:48', 1, '2026-02-01 01:42:11', '2026-02-01 09:41:48'),
(25, 1, 'artryry6@gmail.com', '389501', '2026-02-02 02:17:27', 1, '2026-02-02 02:08:09', '2026-02-02 10:07:27'),
(26, 2, 'abalaryxel@gmail.com', '623667', '2026-02-02 02:30:04', 1, '2026-02-02 02:20:32', '2026-02-02 10:20:04'),
(27, 1, 'artryry6@gmail.com', '928141', '2026-02-02 02:34:35', 1, '2026-02-02 02:25:05', '2026-02-02 10:24:35'),
(28, 2, 'abalaryxel@gmail.com', '815876', '2026-02-02 02:57:48', 1, '2026-02-02 02:49:08', '2026-02-02 10:47:48'),
(29, 1, 'artryry6@gmail.com', '494108', '2026-02-02 06:50:41', 1, '2026-02-02 06:41:10', '2026-02-02 14:40:41'),
(30, 1, 'artryry6@gmail.com', '323840', '2026-02-02 19:16:24', 1, '2026-02-02 19:06:51', '2026-02-03 03:06:24'),
(31, 1, 'artryry6@gmail.com', '153521', '2026-02-02 19:56:00', 1, NULL, '2026-02-03 03:46:00'),
(32, 2, 'abalaryxel@gmail.com', '437920', '2026-02-03 00:14:13', 1, '2026-02-03 00:04:41', '2026-02-03 08:04:13'),
(33, 1, 'artryry6@gmail.com', '828140', '2026-02-03 00:25:07', 1, '2026-02-03 00:15:39', '2026-02-03 08:15:07'),
(34, 1, 'artryry6@gmail.com', '914795', '2026-02-03 01:19:58', 1, '2026-02-03 01:10:29', '2026-02-03 09:09:58'),
(35, 2, 'abalaryxel@gmail.com', '915746', '2026-02-03 01:57:36', 1, '2026-02-03 01:48:07', '2026-02-03 09:47:36'),
(36, 1, 'artryry6@gmail.com', '072967', '2026-02-05 07:40:01', 1, '2026-02-05 07:30:35', '2026-02-05 15:30:01'),
(37, 2, 'abalaryxel@gmail.com', '063735', '2026-02-05 07:44:51', 1, '2026-02-05 07:35:10', '2026-02-05 15:34:51'),
(38, 1, 'artryry6@gmail.com', '815448', '2026-02-06 18:02:38', 1, '2026-02-06 17:53:00', '2026-02-07 01:52:38'),
(39, 1, 'artryry6@gmail.com', '845840', '2026-02-06 23:28:58', 1, '2026-02-06 23:19:22', '2026-02-07 07:18:58'),
(40, 1, 'artryry6@gmail.com', '403269', '2026-02-07 09:10:44', 1, '2026-02-07 09:01:12', '2026-02-07 17:00:44'),
(41, 2, 'abalaryxel@gmail.com', '850344', '2026-02-07 09:12:57', 1, '2026-02-07 09:03:17', '2026-02-07 17:02:57'),
(42, 1, 'artryry6@gmail.com', '000793', '2026-02-07 09:15:09', 1, '2026-02-07 09:05:27', '2026-02-07 17:05:09'),
(43, 1, 'artryry6@gmail.com', '685810', '2026-02-07 09:29:10', 1, '2026-02-07 09:19:33', '2026-02-07 17:19:10'),
(44, 1, 'artryry6@gmail.com', '859386', '2026-02-07 10:12:38', 1, '2026-02-07 10:02:56', '2026-02-07 18:02:38'),
(45, 1, 'artryry6@gmail.com', '513102', '2026-02-07 18:22:53', 1, '2026-02-07 18:13:18', '2026-02-08 02:12:53'),
(46, 2, 'abalaryxel@gmail.com', '724011', '2026-02-07 20:28:04', 1, '2026-02-07 20:18:44', '2026-02-08 04:18:04'),
(47, 1, 'artryry6@gmail.com', '209669', '2026-02-07 20:30:24', 1, '2026-02-07 20:20:43', '2026-02-08 04:20:24'),
(48, 2, 'abalaryxel@gmail.com', '950714', '2026-02-07 20:54:11', 1, NULL, '2026-02-08 04:44:11'),
(49, 2, 'abalaryxel@gmail.com', '456707', '2026-02-07 20:55:22', 1, '2026-02-07 20:46:10', '2026-02-08 04:45:22'),
(50, 1, 'artryry6@gmail.com', '369806', '2026-02-07 20:58:13', 1, '2026-02-07 20:48:38', '2026-02-08 04:48:13'),
(51, 1, 'artryry6@gmail.com', '617217', '2026-02-08 08:37:24', 1, NULL, '2026-02-08 16:27:24'),
(52, 1, 'artryry6@gmail.com', '533653', '2026-02-08 08:37:29', 1, '2026-02-08 08:27:53', '2026-02-08 16:27:29'),
(53, 2, 'abalaryxel@gmail.com', '193907', '2026-02-08 08:39:26', 1, '2026-02-08 08:29:52', '2026-02-08 16:29:26'),
(54, 1, 'artryry6@gmail.com', '821697', '2026-02-08 20:19:43', 1, '2026-02-08 20:10:14', '2026-02-09 04:09:43'),
(55, 1, 'artryry6@gmail.com', '247593', '2026-02-09 06:08:46', 1, '2026-02-09 05:59:24', '2026-02-09 13:58:46'),
(56, 5, 'teamorjj@gmail.com', '915755', '2026-02-09 06:37:07', 1, '2026-02-09 06:27:27', '2026-02-09 14:27:07'),
(57, 1, 'artryry6@gmail.com', '748415', '2026-02-09 06:37:54', 1, '2026-02-09 06:28:18', '2026-02-09 14:27:54'),
(58, 5, 'teamorjj@gmail.com', '980633', '2026-02-09 06:40:48', 1, '2026-02-09 06:31:16', '2026-02-09 14:30:48'),
(59, 1, 'artryry6@gmail.com', '862027', '2026-02-09 06:42:18', 1, '2026-02-09 06:32:36', '2026-02-09 14:32:18');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_active_users`
-- (See below for the actual view)
--
CREATE TABLE `view_active_users` (
`id` int(11)
,`full_name` varchar(255)
,`email` varchar(255)
,`role` enum('admin','user')
,`access_level` enum('full','limited')
,`status` enum('active','inactive')
,`last_login` timestamp
,`total_records` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pending_admins`
-- (See below for the actual view)
--
CREATE TABLE `view_pending_admins` (
`id` int(11)
,`full_name` varchar(255)
,`email` varchar(255)
,`role` enum('admin','user')
,`requested_date` timestamp
,`created_by_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_recent_activity`
-- (See below for the actual view)
--
CREATE TABLE `view_recent_activity` (
`id` int(11)
,`action` varchar(100)
,`description` text
,`user_name` varchar(255)
,`email` varchar(255)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `view_active_users`
--
DROP TABLE IF EXISTS `view_active_users`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_active_users`  AS SELECT `u`.`id` AS `id`, `u`.`full_name` AS `full_name`, `u`.`email` AS `email`, `u`.`role` AS `role`, `u`.`access_level` AS `access_level`, `u`.`status` AS `status`, `u`.`last_login` AS `last_login`, count(`ar`.`id`) AS `total_records` FROM (`users` `u` left join `academic_records` `ar` on(`u`.`id` = `ar`.`uploaded_by`)) WHERE `u`.`status` = 'active' GROUP BY `u`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `view_pending_admins`
--
DROP TABLE IF EXISTS `view_pending_admins`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_pending_admins`  AS SELECT `u`.`id` AS `id`, `u`.`full_name` AS `full_name`, `u`.`email` AS `email`, `u`.`role` AS `role`, `u`.`created_at` AS `requested_date`, `creator`.`full_name` AS `created_by_name` FROM (`users` `u` left join `users` `creator` on(`u`.`created_by` = `creator`.`id`)) WHERE `u`.`status` = 'inactive' ORDER BY `u`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `view_recent_activity`
--
DROP TABLE IF EXISTS `view_recent_activity`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_recent_activity`  AS SELECT `al`.`id` AS `id`, `al`.`action` AS `action`, `al`.`description` AS `description`, `u`.`full_name` AS `user_name`, `u`.`email` AS `email`, `al`.`created_at` AS `created_at` FROM (`activity_logs` `al` left join `users` `u` on(`al`.`user_id` = `u`.`id`)) ORDER BY `al`.`created_at` DESC LIMIT 0, 50 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_records`
--
ALTER TABLE `academic_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_folder_id` (`folder_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_record_type` (`record_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `account_locks`
--
ALTER TABLE `account_locks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_unlock_at` (`unlock_at`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_attempted_at` (`attempted_at`);

--
-- Indexes for table `student_folders`
--
ALTER TABLE `student_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_student_name` (`student_name`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_users_created_by` (`created_by`);

--
-- Indexes for table `user_privileges`
--
ALTER TABLE `user_privileges`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_privilege_unique` (`user_id`,`privilege_key`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_records`
--
ALTER TABLE `academic_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_requests`
--
ALTER TABLE `access_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_locks`
--
ALTER TABLE `account_locks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;

--
-- AUTO_INCREMENT for table `approval_requests`
--
ALTER TABLE `approval_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `student_folders`
--
ALTER TABLE `student_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_privileges`
--
ALTER TABLE `user_privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_records`
--
ALTER TABLE `academic_records`
  ADD CONSTRAINT `academic_records_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `student_folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_records_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD CONSTRAINT `access_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `access_requests_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD CONSTRAINT `approval_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_requests_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_folders`
--
ALTER TABLE `student_folders`
  ADD CONSTRAINT `student_folders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_privileges`
--
ALTER TABLE `user_privileges`
  ADD CONSTRAINT `user_privileges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD CONSTRAINT `verification_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
