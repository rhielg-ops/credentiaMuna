-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Mar 01, 2026 at 10:16 AM
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
  `lock_id` int(11) NOT NULL,
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
  `log_id` int(11) NOT NULL,
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

INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
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
(20, NULL, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:08:58'),
(21, NULL, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:09:40'),
(22, NULL, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-01-28 10:10:27'),
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
(188, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:32:36'),
(189, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-14 14:54:48'),
(190, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-14 14:55:07'),
(191, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 08:15:46'),
(192, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 08:16:01'),
(193, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:07:01'),
(194, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:07:02'),
(195, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:08:21'),
(196, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:10:19'),
(197, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:10:41'),
(198, 5, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:10:55'),
(199, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 06:25:19'),
(200, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 06:26:10'),
(201, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 08:41:32'),
(202, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 08:41:33'),
(203, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 12:06:39'),
(204, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 12:13:14'),
(205, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 12:13:29'),
(206, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 15:25:47'),
(207, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 15:26:11'),
(208, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 02:35:19'),
(209, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 02:35:32'),
(210, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:22:30'),
(211, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:22:48'),
(212, 1, 'user_created', 'Created new User (Former Admin): caldona.jayancarlo15@gmail.com (username: jayan1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:48:09'),
(213, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:48:38'),
(214, 6, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:48:51'),
(215, 6, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:49:05'),
(216, 6, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:49:34'),
(217, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:49:48'),
(218, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:50:24'),
(219, 1, 'privileges_updated', 'Updated privileges for user: caldona.jayancarlo15@gmail.com (username: jayan1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:50:49'),
(220, 1, 'user_updated', 'Updated user: caldona.jayancarlo15@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:50:50'),
(221, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:50:52'),
(222, 6, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:51:18'),
(223, 6, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:51:48'),
(224, 6, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:53:25'),
(225, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:53:45'),
(226, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:53:54'),
(227, 1, 'privileges_updated', 'Updated privileges for user: caldona.jayancarlo15@gmail.com (username: jayan1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:49:45'),
(228, 1, 'user_updated', 'Updated user: caldona.jayancarlo15@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:49:46'),
(229, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:49:50'),
(230, 6, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:50:04'),
(231, 6, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:50:18'),
(232, 6, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:52:56'),
(233, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:53:16'),
(234, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:53:34'),
(235, 1, 'privileges_updated', 'Updated privileges for user: caldona.jayancarlo15@gmail.com (username: jayan1)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:28:58'),
(236, 1, 'user_updated', 'Updated user: caldona.jayancarlo15@gmail.com - Updated: username: jayan1 → jayan12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:28:59'),
(237, 1, 'privileges_updated', 'Updated privileges for user: caldona.jayancarlo15@gmail.com (username: jayan12)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:29:18'),
(238, 1, 'user_updated', 'Updated user: caldona.jayancarlo15@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:29:18'),
(239, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:29:25'),
(240, 6, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:30:03'),
(241, 6, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:30:29');
INSERT INTO `activity_logs` (`log_id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(242, 6, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:31:52'),
(243, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:32:11'),
(244, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:32:43'),
(245, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 14:50:02'),
(246, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 14:51:29'),
(247, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 23:06:37'),
(248, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 23:07:04'),
(249, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 01:13:05'),
(250, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 01:13:37'),
(251, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 04:38:00'),
(252, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 04:39:08'),
(253, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:09:50'),
(254, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:10:03'),
(255, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:18:59'),
(256, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:19:34'),
(257, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:19:49'),
(258, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:38:14'),
(259, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:38:50'),
(260, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:51:22'),
(261, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:52:12'),
(262, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:16:39'),
(263, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:16:55'),
(264, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 06:30:26'),
(265, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 06:30:46'),
(266, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:51:38'),
(267, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:51:40'),
(268, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:51:47'),
(269, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:52:14'),
(270, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:52:38'),
(271, 5, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:54:05'),
(272, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:54:20'),
(273, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:54:34'),
(274, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:07:33'),
(275, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:07:56'),
(276, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:08:40'),
(277, 5, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:09:49'),
(278, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:10:04'),
(279, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:10:16'),
(280, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:10:43'),
(281, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:10:44'),
(282, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:10:48'),
(283, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:11:07'),
(284, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:11:36'),
(285, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:27:56'),
(286, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:30:05'),
(287, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:31:09'),
(288, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:31:09'),
(289, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:31:13'),
(290, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:31:40'),
(291, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:32:12'),
(292, 5, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:56:36'),
(293, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:56:51'),
(294, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:57:34'),
(295, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:57:50'),
(296, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:57:50'),
(297, 1, 'user_rejected', 'Rejected and removed user: reinabala0224@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 11:13:42'),
(298, 1, 'user_rejected', 'Rejected and removed user: mltripoli@tau.edu.phl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 11:13:48'),
(299, 1, 'user_deactivated', 'Deactivated user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:40:49'),
(300, 1, 'user_approved', 'Approved user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:41:08'),
(301, 1, 'user_deactivated', 'Deactivated user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:41:14'),
(302, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:41:17'),
(303, 5, 'reactivation_requested', 'User submitted reactivation request', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:41:50'),
(304, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:42:38'),
(305, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:44:04'),
(306, 1, 'user_approved', 'Approved user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:44:55'),
(307, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 09:14:44'),
(308, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 09:14:57'),
(309, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 15:00:47'),
(310, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 15:01:29'),
(311, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:43:20'),
(312, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:43:20'),
(313, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:43:27'),
(314, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:44:01'),
(315, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:44:18'),
(316, 5, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:49:03'),
(317, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:49:18'),
(318, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:49:40'),
(319, 1, 'privileges_updated', 'Updated privileges for user: teamorjj@gmail.com (username: orrrr123)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:50:19'),
(320, 1, 'user_updated', 'Updated user: teamorjj@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:50:20'),
(321, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:51:36'),
(322, 5, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:52:00'),
(323, 5, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:52:20'),
(324, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:05:34'),
(325, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:06:13'),
(326, 1, 'user_created', 'Created new User (Former Admin): rhielg1@gmail.com (username: rhiel)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:08:17'),
(327, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:08:48'),
(328, 7, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:09:23'),
(329, 7, 'code_resent', 'Verification code resent', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:12:46'),
(330, 7, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:13:26'),
(331, 7, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:13:58'),
(332, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:14:12'),
(333, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:15:26'),
(334, 1, 'privileges_updated', 'Updated privileges for user: rhielg1@gmail.com (username: rhiel)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:17:24'),
(335, 1, 'user_updated', 'Updated user: rhielg1@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:17:24'),
(336, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:17:30'),
(337, 7, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:17:54'),
(338, 7, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:18:16'),
(339, 7, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:21:44'),
(340, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:22:05'),
(341, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:22:22'),
(342, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 03:53:01'),
(343, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 03:53:14'),
(344, 1, 'privileges_updated', 'Updated privileges for user: abalaryxel@gmail.com (username: abalaryxel00)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 04:04:03'),
(345, 1, 'privileges_updated', 'Updated privileges for user: abalaryxel@gmail.com (username: abalaryxel00)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 04:08:52'),
(346, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 04:15:37'),
(347, 1, 'privileges_updated', 'Updated privileges for user: abalaryxel@gmail.com (username: abalaryxel00)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 04:17:07'),
(348, 1, 'user_updated', 'Updated user: abalaryxel@gmail.com - Updated user details', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 04:17:08'),
(349, 1, 'profile_updated', 'Profile updated: artryry6@gmail.com', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 04:18:52'),
(350, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 06:46:02'),
(351, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 06:46:27'),
(352, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 06:59:49'),
(353, 7, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 07:00:24'),
(354, 7, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 07:00:45'),
(355, 7, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 08:30:44'),
(356, 1, 'password_verified', 'Password verified, awaiting 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 08:31:02'),
(357, 1, 'login_success', 'Successfully logged in with 2FA', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 08:31:19');

-- --------------------------------------------------------

--
-- Table structure for table `approval_requests`
--

CREATE TABLE `approval_requests` (
  `approval_id` int(11) NOT NULL,
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

INSERT INTO `approval_requests` (`approval_id`, `user_id`, `email`, `full_name`, `request_type`, `status`, `message`, `ip_address`, `user_agent`, `requested_at`, `reviewed_at`, `reviewed_by`) VALUES
(2, 5, 'teamorjj@gmail.com', 'Orjj', 'reactivation', 'approved', 'User requesting account reactivation', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:41:40', '2026-02-25 04:44:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `file_metadata`
--

CREATE TABLE `file_metadata` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `nextcloud_path` varchar(500) NOT NULL,
  `folder_path` varchar(500) NOT NULL DEFAULT '/',
  `file_size` bigint(20) DEFAULT 0,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `attempt_id` int(11) NOT NULL,
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

INSERT INTO `login_attempts` (`attempt_id`, `email`, `attempt_type`, `is_successful`, `ip_address`, `user_agent`, `attempted_at`) VALUES
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
(126, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-09 14:32:36'),
(127, 'artryry6@username.login', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-14 14:54:08'),
(128, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-14 14:54:41'),
(129, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-14 14:55:07'),
(130, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 08:15:40'),
(131, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 08:16:01'),
(132, 'orrr123@username.login', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:09:25'),
(133, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:10:15'),
(134, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 09:10:41'),
(135, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 06:25:11'),
(136, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 06:26:10'),
(137, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 12:13:08'),
(138, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 12:13:29'),
(139, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 15:25:42'),
(140, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 15:26:11'),
(141, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 02:35:13'),
(142, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-18 02:35:32'),
(143, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:22:23'),
(144, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:22:48'),
(145, 'caldona.jayancarlo15@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:48:46'),
(146, 'caldona.jayancarlo15@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:49:05'),
(147, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:49:44'),
(148, 'artryry6@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:50:12'),
(149, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:50:24'),
(150, 'caldona.jayancarlo15@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:51:08'),
(151, 'caldona.jayancarlo15@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:51:29'),
(152, 'caldona.jayancarlo15@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:51:48'),
(153, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:53:36'),
(154, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 06:53:54'),
(155, 'caldona.jayancarlo15@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:49:59'),
(156, 'caldona.jayancarlo15@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:50:18'),
(157, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:53:08'),
(158, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 07:53:34'),
(159, 'caldona.jayancarlo15@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:29:56'),
(160, 'caldona.jayancarlo15@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:30:15'),
(161, 'caldona.jayancarlo15@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:30:29'),
(162, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:32:03'),
(163, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 09:32:43'),
(164, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 14:49:57'),
(165, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 14:51:29'),
(166, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 23:06:30'),
(167, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-19 23:07:04'),
(168, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 01:12:57'),
(169, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 01:13:37'),
(170, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 04:37:55'),
(171, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 04:39:08'),
(172, 'artryry6@gmail.com', 'password', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:09:30'),
(173, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:09:45'),
(174, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:10:03'),
(175, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:19:30'),
(176, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 13:19:49'),
(177, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:38:08'),
(178, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-22 12:38:50'),
(179, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:51:18'),
(180, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-23 09:52:12'),
(181, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:16:34'),
(182, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-24 14:16:55'),
(183, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 06:30:21'),
(184, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 06:30:46'),
(185, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:52:08'),
(186, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:52:38'),
(187, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:54:15'),
(188, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 07:54:34'),
(189, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:07:52'),
(190, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:08:40'),
(191, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:09:59'),
(192, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:10:16'),
(193, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:11:03'),
(194, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 08:11:36'),
(195, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:27:51'),
(196, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:30:05'),
(197, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:31:35'),
(198, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:32:12'),
(199, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:56:46'),
(200, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 10:57:34'),
(201, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:42:33'),
(202, 'artryry6@gmail.com', 'verification_code', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:43:38'),
(203, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-25 12:44:04'),
(204, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 09:14:38'),
(205, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 09:14:57'),
(206, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 15:00:41'),
(207, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 15:01:29'),
(208, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:43:56'),
(209, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:44:18'),
(210, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:49:13'),
(211, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:49:40'),
(212, 'teamorjj@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:51:56'),
(213, 'teamorjj@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-26 16:52:20'),
(214, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:05:24'),
(215, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:06:13'),
(216, 'rhielg1@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:09:00'),
(217, 'rhielg1@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:13:26'),
(218, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:14:07'),
(219, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:15:26'),
(220, 'rhielg1@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:17:47'),
(221, 'rhielg1@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:18:16'),
(222, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:21:57'),
(223, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-27 08:22:22'),
(224, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 03:52:56'),
(225, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 03:53:14'),
(226, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 06:45:58'),
(227, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 06:46:27'),
(228, 'rhielg1@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 07:00:19'),
(229, 'rhielg1@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 07:00:45'),
(230, 'artryry6@gmail.com', 'password', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 08:30:56'),
(231, 'artryry6@gmail.com', 'verification_code', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 08:31:19');

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
  `user_id` int(11) NOT NULL,
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

INSERT INTO `users` (`user_id`, `full_name`, `email`, `username`, `password`, `role`, `access_level`, `status`, `initial_password_changed`, `created_at`, `updated_at`, `created_by`, `last_login`) VALUES
(1, 'Rhiel Guillermo', 'artryry6@gmail.com', 'artryry60', '$2y$10$RyGBR7m.NjnnzzlJbRPUi.VIQQ9ob.5X0cN3VW6LvrKN4druJ1cLq', 'admin', 'full', 'active', 1, '2026-01-24 06:36:49', '2026-03-01 00:31:19', NULL, '2026-03-01 00:31:19'),
(2, 'Monay bread', 'abalaryxel@gmail.com', 'abalaryxel00', '$2y$10$VjCEEBKmXFOHkBE4bKPUf.5RIji5BhASBn3V5qyW3SGhDobm2pUnu', 'admin', 'full', 'active', 0, '2026-01-27 07:14:19', '2026-02-28 20:17:08', 1, '2026-02-08 08:29:52'),
(5, 'Orjj', 'teamorjj@gmail.com', 'orrrr123', '$2y$10$ZWFXL4oKpvQnivjf1OId3.klZo8Dy2LOgGAFRO4TVeotFGaDIePj6', 'user', 'limited', 'active', 0, '2026-02-09 06:25:50', '2026-02-26 08:52:20', 1, '2026-02-26 08:52:20'),
(6, 'Jayan', 'caldona.jayancarlo15@gmail.com', 'jayan12', '$2y$10$31g4gfDIYnjAjtK6BRltROjuZ0LrXENIlnHwviYkcErkOQrkygDBq', 'user', 'full', 'active', 0, '2026-02-18 22:48:09', '2026-02-19 01:30:29', 1, '2026-02-19 01:30:29'),
(7, 'Rhiel', 'rhielg1@gmail.com', 'rhiel', '$2y$10$ss4XH4XWvaAWM/bQXqXaxORdjgiFd1ME8ppBE4iWfLokZKKrI8OTW', 'user', 'full', 'active', 0, '2026-02-27 00:08:16', '2026-02-28 23:00:45', 1, '2026-02-28 23:00:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_folder_access`
--

CREATE TABLE `user_folder_access` (
  `access_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `folder_path` varchar(500) NOT NULL COMMENT 'e.g. "2021" or "2021/SEM1"',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_folder_access`
--

INSERT INTO `user_folder_access` (`access_id`, `user_id`, `folder_path`, `created_by`, `created_at`) VALUES
(2, 5, '2021', 1, '2026-02-26 16:50:20'),
(3, 5, 'example', 1, '2026-02-26 16:50:20'),
(4, 7, '2021', 1, '2026-02-27 08:17:24'),
(5, 2, '2021', 1, '2026-03-01 04:17:08');

-- --------------------------------------------------------

--
-- Table structure for table `user_privileges`
--

CREATE TABLE `user_privileges` (
  `privilege_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `privilege_key` varchar(50) NOT NULL,
  `privilege_value` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_privileges`
--

INSERT INTO `user_privileges` (`privilege_id`, `user_id`, `privilege_key`, `privilege_value`, `created_at`, `updated_at`) VALUES
(38, 1, 'records_delete', 1, '2026-02-07 00:42:03', '2026-02-07 00:42:03'),
(42, 2, 'records_delete', 1, '2026-02-07 00:42:56', '2026-02-28 20:17:07'),
(43, 2, 'profile_edit', 0, '2026-02-07 00:42:56', '2026-02-28 20:17:07'),
(44, 2, 'user_management', 0, '2026-02-07 00:42:56', '2026-02-28 20:17:07'),
(45, 2, 'system_backup', 0, '2026-02-07 00:42:56', '2026-02-28 20:17:07'),
(46, 2, 'audit_logs', 0, '2026-02-07 00:42:56', '2026-02-28 20:17:07'),
(47, 2, 'full_admin', 0, '2026-02-07 00:42:56', '2026-02-28 20:17:07'),
(55, 5, 'records_delete', 1, '2026-02-09 06:25:50', '2026-02-26 08:50:19'),
(56, 5, 'profile_edit', 0, '2026-02-15 01:07:01', '2026-02-26 08:50:19'),
(57, 5, 'user_management', 1, '2026-02-15 01:07:01', '2026-02-26 08:50:19'),
(58, 5, 'system_backup', 0, '2026-02-15 01:07:01', '2026-02-26 08:50:19'),
(59, 5, 'audit_logs', 0, '2026-02-15 01:07:01', '2026-02-26 08:50:19'),
(60, 5, 'full_admin', 0, '2026-02-15 01:07:01', '2026-02-26 08:50:19'),
(61, 1, 'records_upload', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(62, 2, 'records_upload', 1, '2026-02-17 08:38:10', '2026-02-28 20:17:07'),
(65, 5, 'records_upload', 1, '2026-02-17 08:38:10', '2026-02-26 08:50:19'),
(68, 1, 'files_view', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(69, 2, 'files_view', 1, '2026-02-17 08:38:10', '2026-02-28 20:17:07'),
(72, 5, 'files_view', 1, '2026-02-17 08:38:10', '2026-02-26 08:50:19'),
(82, 1, 'records_organize', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(83, 2, 'records_organize', 1, '2026-02-17 08:38:10', '2026-02-28 20:17:07'),
(86, 5, 'records_organize', 1, '2026-02-17 08:38:10', '2026-02-26 08:50:19'),
(89, 1, 'folders_add', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(90, 2, 'folders_add', 1, '2026-02-17 08:38:10', '2026-02-28 20:17:07'),
(93, 5, 'folders_add', 1, '2026-02-17 08:38:10', '2026-02-26 08:50:19'),
(96, 1, 'folders_delete', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(97, 2, 'folders_delete', 1, '2026-02-17 08:38:10', '2026-02-28 20:17:07'),
(100, 5, 'folders_delete', 1, '2026-02-17 08:38:10', '2026-02-26 08:50:19'),
(103, 1, 'profile_edit', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(106, 1, 'user_management', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(109, 1, 'system_backup', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(112, 1, 'audit_logs', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(115, 1, 'full_admin', 1, '2026-02-17 08:38:10', '2026-02-17 08:38:10'),
(118, 6, 'records_upload', 1, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(119, 6, 'files_view', 1, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(121, 6, 'records_organize', 0, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(122, 6, 'folders_add', 1, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(123, 6, 'records_delete', 1, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(124, 6, 'folders_delete', 0, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(125, 6, 'profile_edit', 1, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(126, 6, 'user_management', 0, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(127, 6, 'system_backup', 0, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(128, 6, 'audit_logs', 0, '2026-02-18 22:48:09', '2026-02-19 01:29:17'),
(129, 6, 'full_admin', 0, '2026-02-18 22:48:09', '2026-02-19 01:29:18'),
(130, 7, 'records_upload', 1, '2026-02-27 00:08:16', '2026-02-27 00:17:24'),
(131, 7, 'files_view', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(132, 7, 'records_organize', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(133, 7, 'folders_add', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(134, 7, 'records_delete', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(135, 7, 'folders_delete', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(136, 7, 'profile_edit', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(137, 7, 'user_management', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(138, 7, 'system_backup', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(139, 7, 'audit_logs', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24'),
(140, 7, 'full_admin', 1, '2026-02-27 00:08:17', '2026-02-27 00:17:24');

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `code_id` int(11) NOT NULL,
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

INSERT INTO `verification_codes` (`code_id`, `user_id`, `email`, `code`, `expires_at`, `is_used`, `used_at`, `created_at`) VALUES
(1, 1, 'artryry6@gmail.com', '155459', '2026-01-27 01:04:32', 1, NULL, '2026-01-27 08:54:32'),
(2, 1, 'artryry6@gmail.com', '011396', '2026-01-27 01:05:57', 1, '2026-01-27 00:57:28', '2026-01-27 08:55:57'),
(3, 1, 'artryry6@gmail.com', '864764', '2026-01-27 03:16:57', 1, '2026-01-27 03:07:26', '2026-01-27 11:06:57'),
(4, 1, 'artryry6@gmail.com', '684550', '2026-01-27 03:33:08', 1, '2026-01-27 03:23:30', '2026-01-27 11:23:08'),
(5, 1, 'artryry6@gmail.com', '719682', '2026-01-27 07:22:41', 1, '2026-01-27 07:13:05', '2026-01-27 15:12:41'),
(6, 2, 'abalaryxel@gmail.com', '354420', '2026-01-27 07:25:50', 1, '2026-01-27 07:16:31', '2026-01-27 15:15:50'),
(7, 1, 'artryry6@gmail.com', '837495', '2026-01-28 02:15:03', 1, '2026-01-28 02:05:45', '2026-01-28 10:05:03'),
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
(59, 1, 'artryry6@gmail.com', '862027', '2026-02-09 06:42:18', 1, '2026-02-09 06:32:36', '2026-02-09 14:32:18'),
(60, 1, 'artryry6@gmail.com', '192070', '2026-02-14 07:04:41', 1, '2026-02-14 06:55:07', '2026-02-14 14:54:41'),
(61, 1, 'artryry6@gmail.com', '527506', '2026-02-15 00:25:40', 1, '2026-02-15 00:16:01', '2026-02-15 08:15:40'),
(62, 5, 'teamorjj@gmail.com', '217853', '2026-02-15 01:20:15', 1, '2026-02-15 01:10:41', '2026-02-15 09:10:15'),
(63, 1, 'artryry6@gmail.com', '627849', '2026-02-16 22:35:11', 1, '2026-02-16 22:26:10', '2026-02-17 06:25:11'),
(64, 1, 'artryry6@gmail.com', '932680', '2026-02-17 04:23:08', 1, '2026-02-17 04:13:29', '2026-02-17 12:13:08'),
(65, 1, 'artryry6@gmail.com', '096758', '2026-02-17 07:35:42', 1, '2026-02-17 07:26:11', '2026-02-17 15:25:42'),
(66, 1, 'artryry6@gmail.com', '968209', '2026-02-17 18:45:13', 1, '2026-02-17 18:35:32', '2026-02-18 02:35:13'),
(67, 1, 'artryry6@gmail.com', '276901', '2026-02-18 22:32:23', 1, '2026-02-18 22:22:48', '2026-02-19 06:22:23'),
(68, 6, 'caldona.jayancarlo15@gmail.com', '067504', '2026-02-18 22:58:46', 1, '2026-02-18 22:49:05', '2026-02-19 06:48:46'),
(69, 1, 'artryry6@gmail.com', '413876', '2026-02-18 22:59:44', 1, '2026-02-18 22:50:24', '2026-02-19 06:49:44'),
(70, 6, 'caldona.jayancarlo15@gmail.com', '897892', '2026-02-18 23:01:08', 1, '2026-02-18 22:51:48', '2026-02-19 06:51:08'),
(71, 1, 'artryry6@gmail.com', '166278', '2026-02-18 23:03:36', 1, '2026-02-18 22:53:54', '2026-02-19 06:53:36'),
(72, 6, 'caldona.jayancarlo15@gmail.com', '237928', '2026-02-18 23:59:59', 1, '2026-02-18 23:50:18', '2026-02-19 07:49:59'),
(73, 1, 'artryry6@gmail.com', '515370', '2026-02-19 00:03:08', 1, '2026-02-18 23:53:34', '2026-02-19 07:53:08'),
(74, 6, 'caldona.jayancarlo15@gmail.com', '766820', '2026-02-19 01:39:56', 1, '2026-02-19 01:30:29', '2026-02-19 09:29:56'),
(75, 1, 'artryry6@gmail.com', '930629', '2026-02-19 01:42:03', 1, '2026-02-19 01:32:43', '2026-02-19 09:32:03'),
(76, 1, 'artryry6@gmail.com', '040344', '2026-02-19 06:59:57', 1, '2026-02-19 06:51:29', '2026-02-19 14:49:57'),
(77, 1, 'artryry6@gmail.com', '979543', '2026-02-19 15:16:30', 1, '2026-02-19 15:07:04', '2026-02-19 23:06:30'),
(78, 1, 'artryry6@gmail.com', '758858', '2026-02-19 17:22:58', 1, '2026-02-19 17:13:37', '2026-02-20 01:12:58'),
(79, 1, 'artryry6@gmail.com', '979069', '2026-02-19 20:47:55', 1, '2026-02-19 20:39:08', '2026-02-20 04:37:55'),
(80, 1, 'artryry6@gmail.com', '304204', '2026-02-20 05:19:45', 1, '2026-02-20 05:10:03', '2026-02-20 13:09:45'),
(81, 5, 'teamorjj@gmail.com', '653943', '2026-02-20 05:29:30', 1, '2026-02-20 05:19:49', '2026-02-20 13:19:30'),
(82, 1, 'artryry6@gmail.com', '058783', '2026-02-22 04:48:08', 1, '2026-02-22 04:38:50', '2026-02-22 12:38:08'),
(83, 1, 'artryry6@gmail.com', '352882', '2026-02-23 02:01:18', 1, '2026-02-23 01:52:12', '2026-02-23 09:51:18'),
(84, 1, 'artryry6@gmail.com', '237045', '2026-02-24 06:26:34', 1, '2026-02-24 06:16:55', '2026-02-24 14:16:34'),
(85, 1, 'artryry6@gmail.com', '948830', '2026-02-24 22:40:21', 1, '2026-02-24 22:30:46', '2026-02-25 06:30:21'),
(86, 5, 'teamorjj@gmail.com', '743534', '2026-02-25 00:02:08', 1, '2026-02-24 23:52:38', '2026-02-25 07:52:08'),
(87, 1, 'artryry6@gmail.com', '445107', '2026-02-25 00:04:15', 1, '2026-02-24 23:54:34', '2026-02-25 07:54:15'),
(88, 5, 'teamorjj@gmail.com', '793542', '2026-02-25 00:17:52', 1, '2026-02-25 00:08:40', '2026-02-25 08:07:52'),
(89, 1, 'artryry6@gmail.com', '898284', '2026-02-25 00:19:59', 1, '2026-02-25 00:10:16', '2026-02-25 08:09:59'),
(90, 5, 'teamorjj@gmail.com', '753801', '2026-02-25 00:21:03', 1, '2026-02-25 00:11:36', '2026-02-25 08:11:03'),
(91, 1, 'artryry6@gmail.com', '849433', '2026-02-25 02:37:51', 1, '2026-02-25 02:30:05', '2026-02-25 10:27:51'),
(92, 5, 'teamorjj@gmail.com', '193525', '2026-02-25 02:41:35', 1, '2026-02-25 02:32:12', '2026-02-25 10:31:35'),
(93, 1, 'artryry6@gmail.com', '670742', '2026-02-25 03:06:46', 1, '2026-02-25 02:57:34', '2026-02-25 10:56:46'),
(94, 1, 'artryry6@gmail.com', '061171', '2026-02-25 04:52:33', 1, '2026-02-25 04:44:04', '2026-02-25 12:42:33'),
(95, 1, 'artryry6@gmail.com', '082621', '2026-02-26 01:24:38', 1, '2026-02-26 01:14:57', '2026-02-26 09:14:38'),
(96, 1, 'artryry6@gmail.com', '066223', '2026-02-26 07:10:41', 1, '2026-02-26 07:01:29', '2026-02-26 15:00:41'),
(97, 5, 'teamorjj@gmail.com', '005585', '2026-02-26 08:53:56', 1, '2026-02-26 08:44:18', '2026-02-26 16:43:56'),
(98, 1, 'artryry6@gmail.com', '739062', '2026-02-26 08:59:13', 1, '2026-02-26 08:49:40', '2026-02-26 16:49:13'),
(99, 5, 'teamorjj@gmail.com', '281120', '2026-02-26 09:01:56', 1, '2026-02-26 08:52:20', '2026-02-26 16:51:56'),
(100, 1, 'artryry6@gmail.com', '208515', '2026-02-27 00:15:24', 1, '2026-02-27 00:06:13', '2026-02-27 08:05:24'),
(101, 7, 'rhielg1@gmail.com', '753134', '2026-02-27 00:19:00', 1, NULL, '2026-02-27 08:09:00'),
(102, 7, 'rhielg1@gmail.com', '878111', '2026-02-27 00:22:36', 1, '2026-02-27 00:13:26', '2026-02-27 08:12:36'),
(103, 1, 'artryry6@gmail.com', '003703', '2026-02-27 00:24:07', 1, '2026-02-27 00:15:26', '2026-02-27 08:14:07'),
(104, 7, 'rhielg1@gmail.com', '443892', '2026-02-27 00:27:47', 1, '2026-02-27 00:18:16', '2026-02-27 08:17:47'),
(105, 1, 'artryry6@gmail.com', '030009', '2026-02-27 00:31:57', 1, '2026-02-27 00:22:22', '2026-02-27 08:21:57'),
(106, 1, 'artryry6@gmail.com', '327295', '2026-02-28 20:02:56', 1, '2026-02-28 19:53:14', '2026-03-01 03:52:56'),
(107, 1, 'artryry6@gmail.com', '249664', '2026-02-28 22:55:58', 1, '2026-02-28 22:46:27', '2026-03-01 06:45:58'),
(108, 7, 'rhielg1@gmail.com', '967930', '2026-02-28 23:10:19', 1, '2026-02-28 23:00:45', '2026-03-01 07:00:19'),
(109, 1, 'artryry6@gmail.com', '903400', '2026-03-01 00:40:56', 1, '2026-03-01 00:31:19', '2026-03-01 08:30:56');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_active_users`
-- (See below for the actual view)
--
CREATE TABLE `view_active_users` (
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_pending_admins`
-- (See below for the actual view)
--
CREATE TABLE `view_pending_admins` (
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_recent_activity`
-- (See below for the actual view)
--
CREATE TABLE `view_recent_activity` (
`log_id` int(11)
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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_recent_activity`  AS SELECT `al`.`log_id` AS `log_id`, `al`.`action` AS `action`, `al`.`description` AS `description`, `u`.`full_name` AS `user_name`, `u`.`email` AS `email`, `al`.`created_at` AS `created_at` FROM (`activity_logs` `al` left join `users` `u` on(`al`.`user_id` = `u`.`user_id`)) ORDER BY `al`.`created_at` DESC LIMIT 0, 50 ;

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
  ADD KEY `academic_records_ibfk_2` (`uploaded_by`);

--
-- Indexes for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `access_requests_ibfk_2` (`reviewed_by`);

--
-- Indexes for table `account_locks`
--
ALTER TABLE `account_locks`
  ADD PRIMARY KEY (`lock_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_unlock_at` (`unlock_at`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requested_at` (`requested_at`),
  ADD KEY `approval_requests_ibfk_2` (`reviewed_by`);

--
-- Indexes for table `file_metadata`
--
ALTER TABLE `file_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_metadata_ibfk_1` (`uploaded_by`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`attempt_id`),
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
  ADD KEY `student_folders_ibfk_1` (`created_by`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `system_settings_ibfk_1` (`updated_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `fk_users_created_by` (`created_by`);

--
-- Indexes for table `user_folder_access`
--
ALTER TABLE `user_folder_access`
  ADD PRIMARY KEY (`access_id`),
  ADD UNIQUE KEY `uq_user_folder` (`user_id`,`folder_path`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `ufa_creator_fk` (`created_by`);

--
-- Indexes for table `user_privileges`
--
ALTER TABLE `user_privileges`
  ADD PRIMARY KEY (`privilege_id`),
  ADD UNIQUE KEY `user_privilege_unique` (`user_id`,`privilege_key`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`code_id`),
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
  MODIFY `lock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;

--
-- AUTO_INCREMENT for table `approval_requests`
--
ALTER TABLE `approval_requests`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `file_metadata`
--
ALTER TABLE `file_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_folder_access`
--
ALTER TABLE `user_folder_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_privileges`
--
ALTER TABLE `user_privileges`
  MODIFY `privilege_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=141;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_records`
--
ALTER TABLE `academic_records`
  ADD CONSTRAINT `academic_records_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `student_folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_records_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD CONSTRAINT `access_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `access_requests_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `approval_requests`
--
ALTER TABLE `approval_requests`
  ADD CONSTRAINT `approval_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_requests_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `file_metadata`
--
ALTER TABLE `file_metadata`
  ADD CONSTRAINT `file_metadata_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_folders`
--
ALTER TABLE `student_folders`
  ADD CONSTRAINT `student_folders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_folder_access`
--
ALTER TABLE `user_folder_access`
  ADD CONSTRAINT `ufa_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ufa_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_privileges`
--
ALTER TABLE `user_privileges`
  ADD CONSTRAINT `user_privileges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD CONSTRAINT `verification_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
