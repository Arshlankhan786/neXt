-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 04, 2026 at 08:13 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u946810828_Next`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('Super Admin','Admin','Administrator') DEFAULT 'Admin',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `email`, `role`, `created_at`, `last_login`) VALUES
(3, 'Next', '$2y$10$XrsVVtBJbxipHSxw/3X7jO/qnNwU95JP21eqEsxdkrSmzoslOK1NC', 'Next Admin', 'next@academy.com', 'Super Admin', '2026-01-17 04:39:08', '2026-02-04 07:27:47'),
(5, 'Arshlan', '$2y$10$lYIF/fMj0.alATKsvdrpxeQiLP5nYgPiCTH7wfVpTYbVG6c11UUGy', 'Pathan Arshlan', 'Arshlanmk11@gmail.com', 'Admin', '2026-01-17 04:59:59', NULL),
(7, 'NextA', '$2y$10$EhyS/oug0CeRaxVTBVhLXONcyVslpPoLJ3O4xOV6j5qV11nNXO1sW', 'Checking', 'Thakorjigar927@gmail.com', 'Administrator', '2026-01-26 04:57:02', '2026-01-26 04:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `admin_tasks`
--

CREATE TABLE `admin_tasks` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_repeating` tinyint(1) DEFAULT 0,
  `repeat_interval_hours` int(11) DEFAULT NULL,
  `max_repeats` int(11) DEFAULT NULL,
  `repeat_count` int(11) DEFAULT 0,
  `status` enum('Pending','Completed','Incomplete') DEFAULT 'Pending',
  `next_due_date` date DEFAULT curdate(),
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_tasks`
--

INSERT INTO `admin_tasks` (`id`, `admin_id`, `title`, `description`, `is_repeating`, `repeat_interval_hours`, `max_repeats`, `repeat_count`, `status`, `next_due_date`, `completed_at`, `created_at`) VALUES
(57, 3, 'Checking 1', '', 0, NULL, NULL, 0, 'Completed', '2026-02-03', '2026-02-03 11:12:20', '2026-02-03 11:11:58'),
(59, 3, 'Checking 2', '', 1, 1, NULL, 1, 'Completed', '2026-02-03', '2026-02-03 11:13:01', '2026-02-03 11:12:41'),
(60, 3, 'Checking 2', '', 1, 1, NULL, 2, 'Completed', '2026-02-03', '2026-02-03 11:57:01', '2026-02-03 11:13:01'),
(64, 3, 'GRAPHIC MORNING BATCH', '', 1, 24, NULL, 0, 'Pending', '2026-02-04', NULL, '2026-02-03 12:20:01'),
(65, 3, 'GRAPHIC EVENING', '', 1, 24, NULL, 0, 'Pending', '2026-02-04', NULL, '2026-02-03 12:20:39'),
(66, 3, 'DIGITAL MARKETING POST', '', 1, 24, NULL, 0, 'Pending', '2026-02-04', NULL, '2026-02-03 12:20:55'),
(67, 3, 'FINAL YEAR COURSE', '', 1, 24, NULL, 0, 'Pending', '2026-02-04', NULL, '2026-02-03 12:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `admin_task_history`
--

CREATE TABLE `admin_task_history` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` enum('created','updated','completed','reopened','deleted') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_task_history`
--

INSERT INTO `admin_task_history` (`id`, `task_id`, `admin_id`, `action`, `notes`, `created_at`) VALUES
(1, 1, 3, 'created', NULL, '2026-02-02 20:23:28'),
(2, 2, 3, 'created', NULL, '2026-02-02 20:23:39'),
(3, 3, 3, 'created', NULL, '2026-02-02 20:24:48'),
(4, 4, 3, 'created', NULL, '2026-02-02 20:31:12'),
(5, 5, 3, 'created', NULL, '2026-02-03 10:26:36'),
(6, 6, 3, 'created', NULL, '2026-02-03 10:26:56'),
(7, 6, 3, 'completed', NULL, '2026-02-03 10:27:15'),
(8, 6, 3, 'completed', NULL, '2026-02-03 10:27:22'),
(9, 7, 3, 'created', NULL, '2026-02-03 10:28:15'),
(10, 8, 3, 'created', NULL, '2026-02-03 10:28:19'),
(11, 9, 3, 'created', NULL, '2026-02-03 10:28:58'),
(12, 10, 3, 'created', NULL, '2026-02-03 10:32:18'),
(13, 10, 3, 'completed', 'Repeat #1 - Next due: 2026-02-03', '2026-02-03 10:32:26'),
(14, 10, 3, 'completed', 'Repeat #2 - Next due: 2026-02-03', '2026-02-03 10:32:29'),
(15, 10, 3, 'completed', 'Repeat #3 - Next due: 2026-02-03', '2026-02-03 10:32:33'),
(16, 10, 3, 'completed', 'Repeat #4 - Next due: 2026-02-03', '2026-02-03 10:32:37'),
(17, 10, 3, 'completed', 'Repeat #5 - Next due: 2026-02-03', '2026-02-03 10:32:43'),
(18, 11, 3, 'created', 'Created scheduled (repeating) task - Due: 2026-02-03', '2026-02-03 10:52:02'),
(19, 11, 3, 'completed', 'Task completed manually', '2026-02-03 10:52:04'),
(20, 12, 3, 'created', 'Auto-created scheduled task (occurrence #1) from task #11', '2026-02-03 10:52:04'),
(21, 11, 3, 'completed', 'Completed - spawned new scheduled task #12', '2026-02-03 10:52:04'),
(22, 9, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:57:18'),
(23, 13, 3, 'created', 'Auto-created scheduled task (occurrence #1) from task #9', '2026-02-03 10:57:18'),
(24, 9, 3, 'updated', 'New scheduled task #13 created after marking #9 incomplete', '2026-02-03 10:57:18'),
(25, 12, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:57:18'),
(26, 14, 3, 'created', 'Auto-created scheduled task (occurrence #2) from task #12', '2026-02-03 10:57:18'),
(27, 12, 3, 'updated', 'New scheduled task #14 created after marking #12 incomplete', '2026-02-03 10:57:18'),
(28, 13, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:57:24'),
(29, 15, 3, 'created', 'Auto-created scheduled task (occurrence #2) from task #13', '2026-02-03 10:57:24'),
(30, 13, 3, 'updated', 'New scheduled task #15 created after marking #13 incomplete', '2026-02-03 10:57:24'),
(31, 14, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:57:24'),
(32, 16, 3, 'created', 'Auto-created scheduled task (occurrence #3) from task #14', '2026-02-03 10:57:24'),
(33, 14, 3, 'updated', 'New scheduled task #16 created after marking #14 incomplete', '2026-02-03 10:57:24'),
(34, 15, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:57:54'),
(35, 17, 3, 'created', 'Auto-created scheduled task (occurrence #3) from task #15', '2026-02-03 10:57:54'),
(36, 15, 3, 'updated', 'New scheduled task #17 created after marking #15 incomplete', '2026-02-03 10:57:54'),
(37, 16, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:57:54'),
(38, 18, 3, 'created', 'Auto-created scheduled task (occurrence #4) from task #16', '2026-02-03 10:57:54'),
(39, 16, 3, 'updated', 'New scheduled task #18 created after marking #16 incomplete', '2026-02-03 10:57:54'),
(40, 17, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:24'),
(41, 19, 3, 'created', 'Auto-created scheduled task (occurrence #4) from task #17', '2026-02-03 10:58:24'),
(42, 17, 3, 'updated', 'New scheduled task #19 created after marking #17 incomplete', '2026-02-03 10:58:24'),
(43, 18, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:24'),
(44, 20, 3, 'created', 'Auto-created scheduled task (occurrence #5) from task #18', '2026-02-03 10:58:24'),
(45, 18, 3, 'updated', 'New scheduled task #20 created after marking #18 incomplete', '2026-02-03 10:58:24'),
(46, 19, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:28'),
(47, 21, 3, 'created', 'Auto-created scheduled task (occurrence #5) from task #19', '2026-02-03 10:58:28'),
(48, 19, 3, 'updated', 'New scheduled task #21 created after marking #19 incomplete', '2026-02-03 10:58:28'),
(49, 20, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:28'),
(50, 22, 3, 'created', 'Auto-created scheduled task (occurrence #6) from task #20', '2026-02-03 10:58:28'),
(51, 20, 3, 'updated', 'New scheduled task #22 created after marking #20 incomplete', '2026-02-03 10:58:28'),
(52, 21, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:29'),
(53, 23, 3, 'created', 'Auto-created scheduled task (occurrence #6) from task #21', '2026-02-03 10:58:29'),
(54, 21, 3, 'updated', 'New scheduled task #23 created after marking #21 incomplete', '2026-02-03 10:58:29'),
(55, 22, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:29'),
(56, 24, 3, 'created', 'Auto-created scheduled task (occurrence #7) from task #22', '2026-02-03 10:58:29'),
(57, 22, 3, 'updated', 'New scheduled task #24 created after marking #22 incomplete', '2026-02-03 10:58:29'),
(58, 23, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:29'),
(59, 25, 3, 'created', 'Auto-created scheduled task (occurrence #7) from task #23', '2026-02-03 10:58:29'),
(60, 23, 3, 'updated', 'New scheduled task #25 created after marking #23 incomplete', '2026-02-03 10:58:29'),
(61, 24, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:29'),
(62, 26, 3, 'created', 'Auto-created scheduled task (occurrence #8) from task #24', '2026-02-03 10:58:29'),
(63, 24, 3, 'updated', 'New scheduled task #26 created after marking #24 incomplete', '2026-02-03 10:58:29'),
(64, 25, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:31'),
(65, 27, 3, 'created', 'Auto-created scheduled task (occurrence #8) from task #25', '2026-02-03 10:58:31'),
(66, 25, 3, 'updated', 'New scheduled task #27 created after marking #25 incomplete', '2026-02-03 10:58:31'),
(67, 26, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:31'),
(68, 28, 3, 'created', 'Auto-created scheduled task (occurrence #9) from task #26', '2026-02-03 10:58:31'),
(69, 26, 3, 'updated', 'New scheduled task #28 created after marking #26 incomplete', '2026-02-03 10:58:31'),
(70, 27, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:34'),
(71, 29, 3, 'created', 'Auto-created scheduled task (occurrence #9) from task #27', '2026-02-03 10:58:34'),
(72, 27, 3, 'updated', 'New scheduled task #29 created after marking #27 incomplete', '2026-02-03 10:58:34'),
(73, 28, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:34'),
(74, 30, 3, 'created', 'Auto-created scheduled task (occurrence #10) from task #28', '2026-02-03 10:58:34'),
(75, 28, 3, 'updated', 'New scheduled task #30 created after marking #28 incomplete', '2026-02-03 10:58:34'),
(76, 29, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:37'),
(77, 31, 3, 'created', 'Auto-created scheduled task (occurrence #10) from task #29', '2026-02-03 10:58:37'),
(78, 29, 3, 'updated', 'New scheduled task #31 created after marking #29 incomplete', '2026-02-03 10:58:37'),
(79, 30, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:37'),
(80, 32, 3, 'created', 'Auto-created scheduled task (occurrence #11) from task #30', '2026-02-03 10:58:37'),
(81, 30, 3, 'updated', 'New scheduled task #32 created after marking #30 incomplete', '2026-02-03 10:58:37'),
(82, 29, 3, 'deleted', 'Task deleted by user', '2026-02-03 10:58:37'),
(83, 31, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:37'),
(84, 33, 3, 'created', 'Auto-created scheduled task (occurrence #11) from task #31', '2026-02-03 10:58:37'),
(85, 31, 3, 'updated', 'New scheduled task #33 created after marking #31 incomplete', '2026-02-03 10:58:37'),
(86, 32, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:37'),
(87, 34, 3, 'created', 'Auto-created scheduled task (occurrence #12) from task #32', '2026-02-03 10:58:37'),
(88, 32, 3, 'updated', 'New scheduled task #34 created after marking #32 incomplete', '2026-02-03 10:58:37'),
(89, 33, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:40'),
(90, 35, 3, 'created', 'Auto-created scheduled task (occurrence #12) from task #33', '2026-02-03 10:58:40'),
(91, 33, 3, 'updated', 'New scheduled task #35 created after marking #33 incomplete', '2026-02-03 10:58:40'),
(92, 34, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:40'),
(93, 36, 3, 'created', 'Auto-created scheduled task (occurrence #13) from task #34', '2026-02-03 10:58:40'),
(94, 34, 3, 'updated', 'New scheduled task #36 created after marking #34 incomplete', '2026-02-03 10:58:40'),
(95, 33, 3, 'deleted', 'Task deleted by user', '2026-02-03 10:58:40'),
(96, 35, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:40'),
(97, 37, 3, 'created', 'Auto-created scheduled task (occurrence #13) from task #35', '2026-02-03 10:58:40'),
(98, 35, 3, 'updated', 'New scheduled task #37 created after marking #35 incomplete', '2026-02-03 10:58:40'),
(99, 36, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:40'),
(100, 38, 3, 'created', 'Auto-created scheduled task (occurrence #14) from task #36', '2026-02-03 10:58:40'),
(101, 36, 3, 'updated', 'New scheduled task #38 created after marking #36 incomplete', '2026-02-03 10:58:40'),
(102, 37, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:44'),
(103, 39, 3, 'created', 'Auto-created scheduled task (occurrence #14) from task #37', '2026-02-03 10:58:44'),
(104, 37, 3, 'updated', 'New scheduled task #39 created after marking #37 incomplete', '2026-02-03 10:58:44'),
(105, 38, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:44'),
(106, 40, 3, 'created', 'Auto-created scheduled task (occurrence #15) from task #38', '2026-02-03 10:58:44'),
(107, 38, 3, 'updated', 'New scheduled task #40 created after marking #38 incomplete', '2026-02-03 10:58:44'),
(108, 37, 3, 'deleted', 'Task deleted by user', '2026-02-03 10:58:44'),
(109, 39, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:44'),
(110, 41, 3, 'created', 'Auto-created scheduled task (occurrence #15) from task #39', '2026-02-03 10:58:44'),
(111, 39, 3, 'updated', 'New scheduled task #41 created after marking #39 incomplete', '2026-02-03 10:58:44'),
(112, 40, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:44'),
(113, 42, 3, 'created', 'Auto-created scheduled task (occurrence #16) from task #40', '2026-02-03 10:58:44'),
(114, 40, 3, 'updated', 'New scheduled task #42 created after marking #40 incomplete', '2026-02-03 10:58:44'),
(115, 41, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:50'),
(116, 43, 3, 'created', 'Auto-created scheduled task (occurrence #16) from task #41', '2026-02-03 10:58:50'),
(117, 41, 3, 'updated', 'New scheduled task #43 created after marking #41 incomplete', '2026-02-03 10:58:50'),
(118, 42, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:58:50'),
(119, 44, 3, 'created', 'Auto-created scheduled task (occurrence #17) from task #42', '2026-02-03 10:58:50'),
(120, 42, 3, 'updated', 'New scheduled task #44 created after marking #42 incomplete', '2026-02-03 10:58:50'),
(121, 43, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:59:21'),
(122, 45, 3, 'created', 'Auto-created scheduled task (occurrence #17) from task #43', '2026-02-03 10:59:21'),
(123, 43, 3, 'updated', 'New scheduled task #45 created after marking #43 incomplete', '2026-02-03 10:59:21'),
(124, 44, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:59:21'),
(125, 46, 3, 'created', 'Auto-created scheduled task (occurrence #18) from task #44', '2026-02-03 10:59:21'),
(126, 44, 3, 'updated', 'New scheduled task #46 created after marking #44 incomplete', '2026-02-03 10:59:21'),
(127, 45, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:59:51'),
(128, 47, 3, 'created', 'Auto-created scheduled task (occurrence #18) from task #45', '2026-02-03 10:59:51'),
(129, 45, 3, 'updated', 'New scheduled task #47 created after marking #45 incomplete', '2026-02-03 10:59:51'),
(130, 46, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 10:59:51'),
(131, 48, 3, 'created', 'Auto-created scheduled task (occurrence #19) from task #46', '2026-02-03 10:59:51'),
(132, 46, 3, 'updated', 'New scheduled task #48 created after marking #46 incomplete', '2026-02-03 10:59:51'),
(133, 47, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:00:21'),
(134, 49, 3, 'created', 'Auto-created scheduled task (occurrence #19) from task #47', '2026-02-03 11:00:21'),
(135, 47, 3, 'updated', 'New scheduled task #49 created after marking #47 incomplete', '2026-02-03 11:00:21'),
(136, 48, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:00:21'),
(137, 50, 3, 'created', 'Auto-created scheduled task (occurrence #20) from task #48', '2026-02-03 11:00:21'),
(138, 48, 3, 'updated', 'New scheduled task #50 created after marking #48 incomplete', '2026-02-03 11:00:21'),
(139, 49, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:00:51'),
(140, 51, 3, 'created', 'Auto-created scheduled task (occurrence #20) from task #49', '2026-02-03 11:00:51'),
(141, 49, 3, 'updated', 'New scheduled task #51 created after marking #49 incomplete', '2026-02-03 11:00:51'),
(142, 50, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:00:51'),
(143, 52, 3, 'created', 'Auto-created scheduled task (occurrence #21) from task #50', '2026-02-03 11:00:51'),
(144, 50, 3, 'updated', 'New scheduled task #52 created after marking #50 incomplete', '2026-02-03 11:00:51'),
(145, 51, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:01:21'),
(146, 53, 3, 'created', 'Auto-created scheduled task (occurrence #21) from task #51', '2026-02-03 11:01:21'),
(147, 51, 3, 'updated', 'New scheduled task #53 created after marking #51 incomplete', '2026-02-03 11:01:21'),
(148, 52, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:01:21'),
(149, 54, 3, 'created', 'Auto-created scheduled task (occurrence #22) from task #52', '2026-02-03 11:01:21'),
(150, 52, 3, 'updated', 'New scheduled task #54 created after marking #52 incomplete', '2026-02-03 11:01:21'),
(151, 53, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:01:51'),
(152, 55, 3, 'created', 'Auto-created scheduled task (occurrence #22) from task #53', '2026-02-03 11:01:51'),
(153, 53, 3, 'updated', 'New scheduled task #55 created after marking #53 incomplete', '2026-02-03 11:01:51'),
(154, 54, 3, 'updated', 'Automatically marked incomplete - missed due date: 2026-02-03', '2026-02-03 11:01:51'),
(155, 56, 3, 'created', 'Auto-created scheduled task (occurrence #23) from task #54', '2026-02-03 11:01:51'),
(156, 54, 3, 'updated', 'New scheduled task #56 created after marking #54 incomplete', '2026-02-03 11:01:51'),
(157, 55, 3, 'deleted', 'Task deleted by user', '2026-02-03 11:02:37'),
(158, 56, 3, 'deleted', 'Task deleted by user', '2026-02-03 11:02:39'),
(159, 11, 3, 'deleted', 'Task deleted by user', '2026-02-03 11:02:44'),
(160, 57, 3, 'created', 'Created normal task - Due: 2026-02-03', '2026-02-03 11:11:58'),
(161, 58, 3, 'created', 'Created scheduled (repeating) task - Due: 2026-02-03', '2026-02-03 11:12:13'),
(162, 57, 3, 'completed', 'Task completed manually', '2026-02-03 11:12:20'),
(163, 58, 3, 'completed', 'Task completed manually', '2026-02-03 11:12:41'),
(164, 59, 3, 'created', 'Auto-created scheduled task (occurrence #1) from task #58', '2026-02-03 11:12:41'),
(165, 58, 3, 'completed', 'Completed - spawned new scheduled task #59', '2026-02-03 11:12:41'),
(166, 58, 3, 'deleted', 'Task deleted by user', '2026-02-03 11:12:48'),
(167, 59, 3, 'completed', 'Task completed manually', '2026-02-03 11:13:01'),
(168, 60, 3, 'created', 'Auto-created scheduled task (occurrence #2) from task #59', '2026-02-03 11:13:01'),
(169, 59, 3, 'completed', 'Completed - spawned new scheduled task #60', '2026-02-03 11:13:01'),
(170, 61, 3, 'created', 'Created normal task - Due: 2026-02-03', '2026-02-03 11:15:59'),
(171, 62, 3, 'created', 'Created normal task - Due: 2026-02-03', '2026-02-03 11:16:04'),
(172, 62, 3, 'deleted', 'Task deleted by user', '2026-02-03 11:16:07'),
(173, 60, 3, 'completed', 'Task completed manually', '2026-02-03 11:57:01'),
(174, 63, 3, 'created', 'Auto-created scheduled task (occurrence #3) from task #60', '2026-02-03 11:57:01'),
(175, 60, 3, 'completed', 'Completed - spawned new scheduled task #63', '2026-02-03 11:57:01'),
(176, 63, 3, 'deleted', 'Task deleted by user', '2026-02-03 12:19:30'),
(177, 61, 3, 'deleted', 'Task deleted by user', '2026-02-03 12:19:33'),
(178, 64, 3, 'created', 'Created scheduled (repeating) task - Due: 2026-02-04', '2026-02-03 12:20:01'),
(179, 65, 3, 'created', 'Created scheduled (repeating) task - Due: 2026-02-04', '2026-02-03 12:20:39'),
(180, 66, 3, 'created', 'Created scheduled (repeating) task - Due: 2026-02-04', '2026-02-03 12:20:55'),
(181, 67, 3, 'created', 'Created scheduled (repeating) task - Due: 2026-02-04', '2026-02-03 12:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Web Development', 'Courses related to web development and programming', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(2, 'Graphic Design', 'Courses related to graphic design and visual arts', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(3, 'Digital Marketing', 'Courses related to digital marketing and SEO', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(4, 'Mobile Development', 'iOS and Android app development courses', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(5, 'Data Science', 'Data analysis, machine learning, and AI courses', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `full_name`, `email`, `phone`, `subject`, `message`, `created_at`) VALUES
(1, 'Dontae seo', 'dontae.lucas2@gmail.com', '2102102101', 'Permission to Share SEO Report and Improvement Plan', 'Hello,\r\n\r\nWith your permission I would like to send you a SEO Report with prices showing you a few things to greatly improve these search results for you.\r\n\r\nThese things are not difficult, and my report will be very specific. It will show you exactly what needs to be done to move you up in the rankings dramatically.\r\n\r\nIf interested. May I send you a quote/package/proposal.?\r\n\r\nThanks & Regards,', '2026-01-18 08:41:01'),
(2, 'Sonam Prajapati', 'sonam.websolution12@gmail.com', '3207529555', 'Improve Your Google Rankings Strategically', 'Hi http://nextacademyindia.com, \r\n\r\nI had a quick look at your website and noticed that it’s not getting the Google visibility it deserves.\r\n\r\nIn most cases, traffic is lost due to:\r\n\r\n1.Keywords not aligned with search intent\r\n\r\n2.Technical SEO issues\r\n\r\n3.Pages not being indexed or structured properly\r\n\r\nI work with businesses to fix these problems and improve organic reach.\r\n\r\nWould you be open to a SEO check or a short 10-minute call to discuss this?\r\n\r\nWarm regards, \r\n\r\nSonam', '2026-01-19 11:04:22'),
(3, 'Anaya Prajapati', 'anaya.dgtlsolution@gmail.com', '9266141479', 'Missing out on traffic from Google?', 'Hi http://nextacademyindia.com,\r\n \r\nI would like to discuss a business SEO.\r\n \r\nLet me know if you are interested, then I can send you our Full SEO Packages with plan, activities, and Price list.\r\n\r\nBest Regards,\r\nAnaya', '2026-01-19 12:47:29'),
(4, 'GeorgeTar', 'zekisuquc419@gmail.com', '88278385279', 'Hallo  i write about your   price for reseller', 'Salam, qiymətinizi bilmək istədim.', '2026-01-19 16:43:01'),
(5, 'Seth Nunes', 'indexing@searches-nextacademyindia.com', '269192624', 'nextacademyindia.com', 'Hello,\r\n\r\nadd nextacademyindia.com to Google Search Index to be displayed in websearch results!\r\n\r\nSubmit nextacademyindia.com now at https://searchregister.net', '2026-01-19 18:08:00'),
(6, 'Deepak Parcha', 'parchad78@gmail.com', '314032526', 'Let’s redesign your website for better leads and conversions', 'Hello http://nextacademyindia.com,\r\n\r\nI am a professional designer, and I design great looking websites for small business owners with their cost-effective services. I\'m contacting you to see if there are changes or enhancements that you\'d like to make to your site. \r\n\r\nHave you been thinking about upgrading your site to a more cutting edge look and feel or adding a few elements to the site that will help automate some of your business? If so, I\'d really love to speak with you.\r\n\r\nI can make a website according to your requirement in your budget and according to your time period.\r\n\r\nIf you\'re interested in creating a new website or rebuilding the site completely, please share your requirements & reference website (if you have) and WhatsApp number. \r\n\r\nThanks regards,\r\nDeepak Parcha', '2026-01-19 18:40:09'),
(7, 'Nikita Joshi', 'joshinikita.rocketdigitaltech@gmail.com', '7532833829', 'Your Website Has Potential – Let’s Improve Its Google Ranking', 'Hi http://nextacademyindia.com,\r\n\r\nJust had a look at your site – it’s well-designed, but not performing well in search engines.\r\n\r\nWould you be interested in improving your SEO and getting more traffic?\r\n\r\nI can send over a detailed proposal with affordable packages.\r\n\r\nWarm regards,\r\nNikita', '2026-01-19 19:57:56'),
(8, 'RobertTar', 'zekisuquc419@gmail.com', '82595667238', 'Hi, i write about your the price', 'Здравейте, исках да знам цената ви.', '2026-01-20 02:35:06'),
(9, 'Maik Andres', 'indexing@searches-nextacademyindia.com', '494208755', 'nextacademyindia.com', 'Hi,\r\n\r\nlist nextacademyindia.com in GoogleSearchIndex and have it displayed in web search results!\r\n\r\nAdd nextacademyindia.com now at https://searchregister.org', '2026-01-20 18:25:49'),
(10, 'Jeffery Rubino', 'better@ai-nextacademyindia.com', '89215246', 'nextacademyindia.com and A.I.', 'Users search using AI more & more.\r\n\r\nAdd nextacademyindia.com to our AI-optimized directory now to increase your chances of being recommended / mentioned.\r\n\r\nList it here:  https://AIREG.pro', '2026-01-20 22:05:34'),
(11, 'Sonam Prajapati', 'sonam.websolution12@gmail.com', '4839125', 'Elevate Your Website’s Google Rankings', 'Hello http://nextacademyindia.com, \r\n\r\nI checked your website and noticed that your pages are not ranking consistently on Google, even though your content/service is good.\r\n\r\nMost websites lose 60–70% potential traffic due to:\r\n\r\n1.Missing keyword optimization\r\n\r\n2.Technical SEO issues\r\n\r\n3.Pages not properly indexed\r\n\r\nI help businesses fix these issues and bring relevant organic traffic (not ads).\r\n\r\n������ Would you like a SEO check + growth suggestions for your website?\r\n\r\nIf yes, share a number for a quick 10-minute call.\r\n\r\nBest Regards, \r\nSonam', '2026-01-21 08:37:43'),
(12, 'Kathleen Noyes', 'indexing@searches-nextacademyindia.com', '', 'nextacademyindia.com', 'Enlist nextacademyindia.com in GoogleSearchIndex to have it displayed in search results!\r\n\r\nSubmit nextacademyindia.com now at https://searchregister.info', '2026-01-21 17:57:30'),
(13, 'Louella Head', 'better@ai-nextacademyindia.com', '3347372789', 'nextacademyindia.com and A.I.', 'Users search using AI more & more.\r\n\r\nAdd nextacademyindia.com to our AI-optimized directory now to increase your chances of being recommended / mentioned.\r\n\r\nList it here:  https://AIREG.pro', '2026-01-21 20:18:01'),
(14, 'Sonam Prajapati', 'sonam.websolution12@gmail.com', '7073053998', 'Let\'s Get Your Website to Google\'s 1st Page', 'Dear Web Owner http://nextacademyindia.com, \r\n\r\nI came across your website and wanted to ask a quick question.\r\n\r\nWe help businesses improve their visibility on Google and other search engines through practical SEO improvements that attract more relevant visitors and inquiries.\r\n\r\nIf you’re open to it, I’d be happy to share a brief overview or suggestions tailored to your website.\r\n\r\nYou can reply here or share a WhatsApp/contact number if a quick discussion is easier.\r\n\r\nBest regards,\r\nSonam Prajapati\r\nWebsite & SEO Support', '2026-01-22 07:02:39'),
(15, 'Deepak Parcha', 'parchad78@gmail.com', '392633289', 'Enhancing Your Website Design to Attract More Clients', 'Hello http://nextacademyindia.com,\r\n\r\nI design modern, user-friendly websites for small businesses and help improve their online presence.\r\n\r\nI wanted to check if you’re considering any updates to your current website—such as improving the design, usability, or adding features to better support your business.\r\n\r\nIf you’re planning a new website or a redesign, feel free to share your requirements and  reference website link. \r\n\r\nI’d be happy to discuss.\r\n\r\nKind regards,\r\nDeepak Parcha', '2026-01-22 16:50:47'),
(16, 'Candelaria Sadleir', 'better@ai-nextacademyindia.com', '672821729', 'nextacademyindia.com and A.I.', 'Users search using AI more & more.\r\n\r\nAdd nextacademyindia.com to our AI-optimized directory now to increase your chances of being recommended / mentioned.\r\n\r\nList it here:  https://AIREG.pro', '2026-01-22 22:06:26'),
(17, 'Sharma', 'ashwanisharma.rocketdigitaltech@gmail.com', '7532833829', 'Re Improve traffic to your website', 'Hi nextacademyindia.com,\r\n\r\nYour website is not ranking well on Google.\r\n\r\nI can help you in putting your website on the Google\'s top-3 Rank and getting more customers Guaranteed.\r\n\r\nWould you like to me seo proposal your business site\r\n\r\nI can send over a detailed proposal with affordable packages.\r\n\r\nWarm regards,\r\nAshwani', '2026-01-22 23:39:07'),
(18, 'dhiraj', 'dhirajjangid190@gmail.com', '08824482059', '', 'mjhgf', '2026-01-23 12:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `category_id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Full Stack Web Development', 'Complete web development course covering frontend and backend', 'Active', '2026-01-17 04:39:08', '2026-01-19 05:26:52'),
(2, 1, 'Frontend Development', 'HTML, CSS, JavaScript, React, and modern frontend frameworks', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(3, 1, 'Backend Development', 'Node.js, PHP, Python, and database management', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(4, 2, 'Graphic Design', 'Professional photo editing and graphic design', 'Active', '2026-01-17 04:39:08', '2026-01-19 05:27:17'),
(5, 2, 'UI/UX Design', 'User interface and experience design principles', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(6, 2, 'Adobe Illustrator', 'Vector graphics and logo design', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(7, 3, 'Social Media Marketing', 'Master social media platforms for business growth', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(9, 3, 'Google Ads & PPC', 'Pay-per-click advertising and campaign management', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(10, 4, 'Android Development', 'Native Android app development with Kotlin', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(11, 4, 'iOS Development', 'Native iOS app development with Swift', 'Active', '2026-01-17 04:39:08', '2026-01-17 04:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `course_fees`
--

CREATE TABLE `course_fees` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `duration_months` int(11) NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_fees`
--

INSERT INTO `course_fees` (`id`, `course_id`, `duration_months`, `fee_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 15500.00, '2026-01-17 04:39:08', '2026-01-19 05:25:24'),
(2, 1, 6, 28500.00, '2026-01-17 04:39:08', '2026-01-19 05:25:24'),
(3, 1, 9, 40000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(4, 1, 12, 57000.00, '2026-01-17 04:39:08', '2026-01-19 05:25:24'),
(5, 1, 18, 0.00, '2026-01-17 04:39:08', '2026-01-19 05:25:24'),
(6, 1, 24, 85000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(7, 2, 3, 15000.00, '2026-01-17 04:39:08', '2026-01-19 13:25:18'),
(8, 2, 6, 28500.00, '2026-01-17 04:39:08', '2026-01-19 13:25:18'),
(9, 2, 9, 32000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(10, 2, 12, 57000.00, '2026-01-17 04:39:08', '2026-01-19 13:25:18'),
(11, 2, 18, 85500.00, '2026-01-17 04:39:08', '2026-01-19 13:25:18'),
(12, 2, 24, 114000.00, '2026-01-17 04:39:08', '2026-01-19 13:25:18'),
(13, 3, 3, 12000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(14, 3, 6, 22000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(15, 3, 9, 32000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(16, 3, 12, 40000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(17, 4, 3, 12500.00, '2026-01-17 04:39:08', '2026-01-19 04:27:09'),
(18, 4, 6, 28500.00, '2026-01-17 04:39:08', '2026-01-19 04:27:09'),
(19, 4, 9, 21000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(20, 4, 12, 57000.00, '2026-01-17 04:39:08', '2026-01-19 04:27:09'),
(21, 4, 18, 36000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(22, 5, 3, 10000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(23, 5, 6, 18000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(24, 5, 9, 26000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(25, 5, 12, 32000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(26, 5, 18, 45000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(27, 5, 24, 55000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(28, 6, 3, 8000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(29, 6, 6, 15000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(30, 6, 9, 21000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(31, 7, 3, 9000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(32, 7, 6, 16000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(33, 7, 9, 23000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(34, 7, 12, 28000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(35, 7, 18, 38000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(41, 9, 3, 8500.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(42, 9, 6, 15500.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(43, 9, 9, 22000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(44, 10, 6, 25000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(45, 10, 9, 35000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(46, 10, 12, 45000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(47, 10, 18, 60000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(48, 11, 6, 28000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(49, 11, 9, 38000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(50, 11, 12, 48000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08'),
(51, 11, 18, 65000.00, '2026-01-17 04:39:08', '2026-01-17 04:39:08');

-- --------------------------------------------------------

--
-- Table structure for table `course_topics`
--

CREATE TABLE `course_topics` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `topic_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `completed_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `type` enum('image','video') DEFAULT 'image',
  `video_url` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `course_interested` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `source` enum('website','manual','phone','walkin','referral') DEFAULT 'website',
  `status` enum('new','contacted','followup','converted','closed') DEFAULT 'new',
  `assigned_to` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `name`, `mobile`, `email`, `course_interested`, `message`, `source`, `status`, `assigned_to`, `created_by`, `created_at`, `updated_at`) VALUES
(3, 'Online Work', '1234567890', 'online902317@gmail.com', 'web', 'tuu', 'walkin', 'converted', NULL, NULL, '2026-01-24 06:16:56', '2026-01-26 04:45:32'),
(4, 'Arshlan', '1234567890', '', 'Frontend Development', 'noijoih', 'website', 'new', NULL, NULL, '2026-01-26 04:47:43', '2026-01-27 05:27:00'),
(5, 'Farzan Hussain', '7477479488', '', 'Other', 'Hi,\\r\\n\\r\\nHave you ever considered turning training into a stronger brand asset for your business?\\r\\n\\r\\nOur white-label partnership models lets you offer 2,500+ internationally recognised courses as your own. Certificates are auto-generated instantly with your branding, no manual work.\\r\\n\\r\\nIt’s a simple way to enhance your brand and retain clients.\\r\\n\\r\\nOpen to a quick call to see how it works?\\r\\n\\r\\nBest regards,\\r\\nFarzan Hussain', 'website', 'new', NULL, NULL, '2026-02-02 10:58:55', '2026-02-02 10:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_notes`
--

CREATE TABLE `inquiry_notes` (
  `id` int(11) NOT NULL,
  `inquiry_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiry_notes`
--

INSERT INTO `inquiry_notes` (`id`, `inquiry_id`, `note`, `created_by`, `created_at`) VALUES
(2, 4, 'Lead converted to student. Student Code: STU202601262701', 3, '2026-01-26 04:54:55'),
(3, 4, 'Lead converted to student. Student Code: STU202601262386', 7, '2026-01-26 04:58:16');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('Cash','Bank Transfer','Card','UPI','Other') DEFAULT 'Cash',
  `receipt_number` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `student_id`, `amount_paid`, `payment_date`, `payment_method`, `receipt_number`, `notes`, `created_by`, `created_at`) VALUES
(2, 2, 5000.00, '2025-12-06', 'UPI', 'RCP202512063227', '', 3, '2026-01-17 05:59:59'),
(3, 2, 4500.00, '2026-01-15', 'UPI', 'RCP202601156048', '', 3, '2026-01-17 06:02:23'),
(4, 3, 4000.00, '2025-11-06', 'Cash', 'RCP2025110691117', '', 3, '2026-01-19 04:45:00'),
(5, 3, 4000.00, '2025-12-11', 'Cash', 'RCP202511127802', '', 3, '2026-01-19 04:46:02'),
(6, 3, 4500.00, '2026-01-05', 'Cash', 'RCP202601054492', '', 3, '2026-01-19 04:47:11'),
(7, 4, 5000.00, '2025-12-05', 'UPI', 'RCP202512057301', '', 3, '2026-01-19 04:52:46'),
(8, 4, 5500.00, '2026-01-05', 'UPI', 'RCP202601054763', '', 3, '2026-01-19 04:53:44'),
(11, 7, 20000.00, '2025-09-01', 'Cash', 'RCP202509017382', '', 3, '2026-01-19 05:31:27'),
(12, 8, 6000.00, '2025-12-10', 'Cash', 'RCP202512105488', '', 3, '2026-01-19 05:32:42'),
(13, 8, 4500.00, '2026-01-08', 'Cash', 'RCP202601081472', '', 3, '2026-01-19 05:34:06'),
(14, 9, 1000.00, '2025-10-13', 'Cash', 'RCP202510133754', '', 3, '2026-01-19 05:44:30'),
(15, 9, 7000.00, '2025-12-03', 'Cash', 'RCP202512033824', '', 3, '2026-01-19 05:53:14'),
(16, 9, 2500.00, '2026-01-02', 'Cash', 'RCP202601028382', '', 3, '2026-01-19 06:00:56'),
(17, 9, 2000.00, '2026-01-17', 'Cash', 'RCP202601172317', '', 3, '2026-01-19 06:01:30'),
(20, 12, 7000.00, '2025-06-11', 'Cash', 'RCP202506116632', '', 3, '2026-01-19 11:10:33'),
(21, 12, 5000.00, '2025-09-11', 'Cash', 'RCP202609112697', '', 3, '2026-01-19 11:11:21'),
(22, 12, 5000.00, '2025-10-13', 'Cash', 'RCP202510135276', '', 3, '2026-01-19 11:12:46'),
(23, 12, 10000.00, '2025-12-10', 'Cash', 'RCP202512104371', '', 3, '2026-01-19 11:13:35'),
(24, 10, 5000.00, '2025-04-14', 'UPI', 'RCP202504143356', '', 3, '2026-01-19 11:18:51'),
(25, 10, 4200.00, '2025-05-29', 'UPI', 'RCP202505292047', '', 3, '2026-01-19 11:19:43'),
(26, 10, 3700.00, '2025-06-30', 'Cash', 'RCP202506305617', '', 3, '2026-01-19 11:20:58'),
(27, 10, 3700.00, '2025-07-31', 'UPI', 'RCP202507313602', '', 3, '2026-01-19 11:22:48'),
(28, 10, 3700.00, '2025-09-03', 'UPI', 'RCP202509033397', '', 3, '2026-01-19 11:26:50'),
(29, 10, 3750.00, '2025-10-13', 'UPI', 'RCP202510138832', '', 3, '2026-01-19 11:27:55'),
(30, 10, 5000.00, '2025-11-10', 'UPI', 'RCP202511105733', '', 3, '2026-01-19 11:30:31'),
(31, 10, 3700.00, '2025-12-25', 'UPI', 'RCP202512256657', '', 3, '2026-01-19 11:31:28'),
(32, 13, 6000.00, '2025-12-02', 'UPI', 'RCP202512026801', '', 3, '2026-01-19 11:39:38'),
(33, 13, 4000.00, '2026-01-07', 'Cash', 'RCP202601074641', '', 3, '2026-01-19 11:40:22'),
(34, 14, 24500.00, '2025-06-17', 'Cash', 'RCP202506177186', '', 3, '2026-01-19 11:47:46'),
(35, 15, 24000.00, '2025-07-14', 'Cash', 'RCP202507149039', '', 3, '2026-01-19 11:53:27'),
(36, 16, 5000.00, '2025-06-03', 'UPI', 'RCP202506033704', '', 3, '2026-01-19 12:00:13'),
(37, 16, 5000.00, '2025-07-15', 'UPI', 'RCP202507156976', '\r\n', 3, '2026-01-19 12:01:10'),
(38, 16, 5000.00, '2025-08-21', 'UPI', 'RCP202508218390', '', 3, '2026-01-19 12:01:52'),
(39, 16, 4000.00, '2025-09-16', 'UPI', 'RCP202509161531', '', 3, '2026-01-19 12:02:42'),
(40, 16, 5000.00, '2025-12-02', 'UPI', 'RCP202512022201', '', 3, '2026-01-19 12:03:23'),
(41, 14, 24500.00, '2026-01-19', 'Cash', 'RCP202601195962', '', 3, '2026-01-19 13:10:31'),
(42, 16, 4500.00, '2026-01-19', 'Cash', 'RCP202601195922', '', 3, '2026-01-19 13:15:14'),
(43, 17, 28500.00, '2025-10-09', 'Cash', 'RCP202510098203', '', 3, '2026-01-20 04:26:30'),
(44, 18, 4000.00, '2025-09-23', 'Cash', 'RCP202509239324', '', 3, '2026-01-20 04:33:00'),
(45, 18, 5000.00, '2025-12-01', 'Cash', 'RCP202512019306', '', 3, '2026-01-20 04:34:35'),
(46, 18, 5000.00, '2025-12-08', 'Cash', 'RCP202512084025', '', 3, '2026-01-20 04:35:18'),
(47, 19, 4000.00, '2025-09-23', 'Cash', 'RCP202609232993', '', 3, '2026-01-20 04:39:50'),
(48, 19, 5000.00, '2025-12-01', 'Cash', 'RCP202512016919', '', 3, '2026-01-20 04:40:57'),
(49, 19, 5000.00, '2025-12-08', 'Cash', 'RCP202512086422', '', 3, '2026-01-20 04:41:38'),
(50, 20, 4000.00, '2025-09-10', 'Cash', 'RCP202509107425', '', 3, '2026-01-20 04:47:48'),
(51, 20, 4000.00, '2025-10-14', 'Cash', 'RCP202510145965', '', 3, '2026-01-20 04:48:30'),
(52, 20, 4000.00, '2025-11-12', 'Cash', 'RCP202511125951', '', 3, '2026-01-20 04:49:01'),
(53, 20, 4000.00, '2025-12-11', 'Cash', 'RCP202512112481', '', 3, '2026-01-20 04:50:05'),
(54, 20, 4000.00, '2026-01-13', 'Cash', 'RCP202601137893', '', 3, '2026-01-20 04:51:00'),
(55, 21, 2000.00, '2025-06-16', 'Cash', 'RCP202506165421', '', 3, '2026-01-20 05:01:15'),
(56, 21, 2000.00, '2025-07-16', 'Cash', 'RCP202507165401', '', 3, '2026-01-20 05:02:56'),
(57, 21, 2000.00, '2025-08-23', 'Cash', 'RCP202508232078', '', 3, '2026-01-20 05:03:51'),
(58, 21, 2000.00, '2025-09-25', 'Cash', 'RCP202509257372', '', 3, '2026-01-20 05:05:57'),
(59, 21, 2000.00, '2025-10-28', 'Cash', 'RCP202510288789', '', 3, '2026-01-20 05:06:32'),
(60, 21, 2000.00, '2025-11-25', 'Cash', 'RCP202511251100', '', 3, '2026-01-20 05:07:30'),
(61, 21, 2000.00, '2026-01-02', 'Cash', 'RCP202601024856', '', 3, '2026-01-20 05:10:07'),
(62, 22, 5000.00, '2025-05-22', 'Cash', 'RCP202505225429', '', 3, '2026-01-20 05:25:51'),
(63, 22, 5000.00, '2025-06-23', 'Cash', 'RCP202505235785', '', 3, '2026-01-20 05:26:36'),
(64, 22, 5000.00, '2025-07-24', 'Cash', 'RCP202507244944', '', 3, '2026-01-20 05:27:36'),
(65, 22, 5000.00, '2025-09-01', 'Cash', 'RCP202509012117', '', 3, '2026-01-20 05:33:11'),
(66, 22, 3500.00, '2025-12-13', 'Cash', 'RCP202512133975', '', 3, '2026-01-20 05:33:53'),
(67, 21, 2500.00, '2026-01-20', 'Cash', 'RCP202601209658', '', 3, '2026-01-20 06:12:32'),
(68, 23, 5000.00, '2025-05-22', 'Cash', 'RCP202505221587', '', 3, '2026-01-20 07:20:17'),
(69, 23, 5000.00, '2025-06-23', 'Cash', 'RCP202505234148', '', 3, '2026-01-20 07:21:01'),
(70, 23, 5000.00, '2025-07-24', 'Cash', 'RCP202507248438', '', 3, '2026-01-20 07:21:40'),
(71, 23, 5000.00, '2025-09-01', 'Cash', 'RCP202509017831', '', 3, '2026-01-20 07:22:25'),
(72, 23, 3500.00, '2025-12-13', 'Cash', 'RCP202512139533', '', 3, '2026-01-20 07:23:05'),
(73, 24, 24000.00, '2025-01-20', 'Cash', 'RCP202501202877', '', 3, '2026-01-20 10:41:01'),
(74, 24, 24000.00, '2025-06-20', 'Cash', 'RCP202506206425', '', 3, '2026-01-20 10:41:46'),
(75, 25, 3500.00, '2024-05-21', 'Cash', 'RCP202405212468', '', 3, '2026-01-20 11:48:53'),
(76, 25, 2000.00, '2024-06-21', 'Cash', 'RCP202601204842', '', 3, '2026-01-20 11:49:50'),
(77, 25, 4000.00, '2024-08-12', 'Cash', 'RCP202601209215', '', 3, '2026-01-20 11:50:29'),
(78, 25, 2000.00, '2024-09-23', 'Cash', 'RCP202601209419', '', 3, '2026-01-20 11:51:03'),
(79, 25, 2000.00, '2024-10-28', 'Cash', 'RCP202601204880', '', 3, '2026-01-20 11:51:29'),
(80, 25, 2000.00, '2024-12-10', 'Cash', 'RCP202601205976', '', 3, '2026-01-20 11:53:56'),
(81, 25, 2000.00, '2025-01-11', 'Cash', 'RCP202601208860', '', 3, '2026-01-20 11:54:48'),
(82, 25, 3000.00, '2025-02-10', 'Cash', 'RCP202601207899', '', 3, '2026-01-20 11:55:31'),
(83, 25, 6000.00, '2025-04-10', 'Cash', 'RCP202601202335', '', 3, '2026-01-20 11:56:04'),
(84, 25, 3000.00, '2025-05-19', 'Cash', 'RCP202601207439', '', 3, '2026-01-20 11:56:38'),
(85, 25, 3000.00, '2025-06-17', 'Cash', 'RCP202601203003', '', 3, '2026-01-20 11:57:08'),
(86, 25, 3000.00, '2025-07-10', 'Cash', 'RCP202601201730', '', 3, '2026-01-20 11:57:37'),
(87, 25, 3000.00, '2025-08-07', 'Cash', 'RCP202601201251', '', 3, '2026-01-20 11:57:54'),
(88, 25, 3000.00, '2025-09-22', 'Cash', 'RCP202601201525', '', 3, '2026-01-20 11:58:23'),
(89, 25, 1000.00, '2025-10-28', 'Cash', 'RCP202601201996', '', 3, '2026-01-20 11:58:56'),
(90, 25, 2000.00, '2025-11-19', 'Cash', 'RCP202601209835', '', 3, '2026-01-20 11:59:39'),
(91, 26, 4000.00, '2024-07-10', 'Cash', 'RCP202410078049', '', 3, '2026-01-21 04:38:41'),
(92, 26, 8000.00, '2024-09-15', 'Cash', 'RCP202601215014', '', 3, '2026-01-21 04:39:23'),
(93, 26, 8000.00, '2024-11-11', 'Cash', 'RCP202601217956', '', 3, '2026-01-21 04:39:45'),
(94, 26, 4000.00, '2024-12-24', 'Cash', 'RCP202601217982', '', 3, '2026-01-21 04:42:52'),
(95, 26, 8000.00, '2025-02-11', 'Cash', 'RCP202601213946', '', 3, '2026-01-21 04:44:06'),
(96, 26, 5000.00, '2025-05-10', 'Cash', 'RCP202601212068', '', 3, '2026-01-21 04:44:38'),
(97, 26, 1500.00, '2025-06-25', 'Cash', 'RCP202601213741', '', 3, '2026-01-21 04:45:16'),
(98, 26, 4000.00, '2025-09-01', 'Cash', 'RCP202601213765', '', 3, '2026-01-21 04:45:50'),
(99, 26, 4000.00, '2025-11-11', 'Cash', 'RCP202601214290', '', 3, '2026-01-21 04:46:32'),
(100, 27, 2000.00, '2024-04-22', 'Cash', 'RCP202601211448', '', 3, '2026-01-21 04:49:31'),
(101, 27, 2000.00, '2024-06-06', 'Cash', 'RCP202601212651', '', 3, '2026-01-21 04:49:54'),
(102, 27, 2000.00, '2024-07-12', 'Cash', 'RCP202601217597', '', 3, '2026-01-21 04:51:34'),
(103, 27, 2000.00, '2024-08-17', 'Cash', 'RCP202601217560', '', 3, '2026-01-21 04:52:04'),
(104, 27, 2000.00, '2024-09-18', 'Cash', 'RCP202601214180', '', 3, '2026-01-21 04:52:26'),
(105, 27, 2000.00, '2024-10-10', 'Cash', 'RCP202601216713', '', 3, '2026-01-21 04:52:41'),
(106, 27, 2000.00, '2024-11-13', 'Cash', 'RCP202601212019', '', 3, '2026-01-21 04:53:07'),
(107, 27, 2000.00, '2024-12-10', 'Cash', 'RCP202601216392', '', 3, '2026-01-21 04:53:30'),
(109, 27, 3000.00, '2025-01-09', 'Cash', 'RCP202601212141', '', 3, '2026-01-21 04:54:26'),
(110, 27, 3000.00, '2025-02-11', 'Cash', 'RCP202601211902', '', 3, '2026-01-21 04:54:49'),
(111, 27, 2500.00, '2025-03-17', 'Cash', 'RCP202601214159', '', 3, '2026-01-21 04:55:03'),
(112, 27, 3000.00, '2025-04-09', 'Cash', 'RCP202601214284', '', 3, '2026-01-21 04:58:46'),
(113, 27, 1000.00, '2025-05-13', 'Cash', 'RCP202601219229', '', 3, '2026-01-21 04:59:09'),
(114, 27, 2000.00, '2025-07-25', 'Cash', 'RCP202601215923', '', 3, '2026-01-21 04:59:35'),
(115, 27, 2000.00, '2025-08-19', 'Cash', 'RCP202601212367', '', 3, '2026-01-21 05:00:07'),
(116, 27, 2000.00, '2025-09-17', 'Cash', 'RCP202601218438', '', 3, '2026-01-21 05:00:46'),
(117, 27, 2000.00, '2025-10-16', 'Cash', 'RCP202601216037', '', 3, '2026-01-21 05:01:10'),
(118, 27, 2000.00, '2025-12-09', 'Cash', 'RCP202601216509', '', 3, '2026-01-21 05:01:26'),
(119, 27, 2500.00, '2026-01-07', 'Cash', 'RCP202601219441', '', 3, '2026-01-21 05:01:53'),
(120, 28, 3000.00, '2024-05-04', 'Cash', 'RCP202601213885', '', 3, '2026-01-21 05:05:06'),
(121, 28, 3000.00, '2024-02-07', 'Cash', 'RCP202601218582', '', 3, '2026-01-21 05:06:29'),
(122, 28, 3000.00, '2024-08-22', 'Cash', 'RCP202601217192', '', 3, '2026-01-21 05:06:47'),
(123, 28, 3000.00, '2024-10-08', 'Cash', 'RCP202601215576', '', 3, '2026-01-21 05:07:38'),
(124, 28, 9000.00, '2025-05-07', 'Cash', 'RCP202601213159', '', 3, '2026-01-21 05:07:58'),
(125, 29, 2500.00, '2024-05-02', 'Cash', 'RCP202601216373', '', 3, '2026-01-21 05:14:12'),
(126, 29, 2000.00, '2024-06-01', 'Cash', 'RCP202601212172', '', 3, '2026-01-21 05:14:42'),
(127, 29, 2000.00, '2024-07-23', 'Cash', 'RCP202601217278', '', 3, '2026-01-21 05:15:10'),
(128, 29, 2000.00, '2024-08-21', 'Cash', 'RCP202601218485', '', 3, '2026-01-21 05:15:27'),
(129, 29, 2000.00, '2024-09-20', 'Cash', 'RCP202601218870', '', 3, '2026-01-21 05:15:54'),
(130, 29, 2000.00, '2024-10-12', 'Cash', 'RCP202601214922', '', 3, '2026-01-21 05:16:17'),
(131, 29, 2000.00, '2024-11-08', 'Cash', 'RCP202601215560', '', 3, '2026-01-21 05:18:04'),
(132, 29, 3000.00, '2025-01-13', 'Cash', 'RCP202601212963', '', 3, '2026-01-21 05:18:26'),
(134, 29, 3000.00, '2025-02-13', 'Cash', 'RCP202601216833', '', 3, '2026-01-21 05:18:53'),
(135, 29, 3000.00, '2025-04-10', 'Cash', 'RCP202601215541', '', 3, '2026-01-21 05:20:27'),
(136, 29, 3000.00, '2025-05-15', 'Cash', 'RCP202601218343', '', 3, '2026-01-21 05:20:50'),
(137, 29, 2000.00, '2025-07-22', 'Cash', 'RCP202601215730', '', 3, '2026-01-21 05:21:07'),
(138, 20, 10000.00, '2025-07-22', 'Cash', 'RCP202601234342', '', 3, '2026-01-23 04:14:12'),
(139, 19, 7000.00, '2025-08-15', 'Cash', 'RCP202601235339', '', 3, '2026-01-23 04:36:43'),
(140, 18, 7000.00, '2025-08-15', 'Cash', 'RCP202601231769', '', 3, '2026-01-23 04:37:26'),
(141, 32, 27000.00, '2025-12-23', 'Cash', 'RCP202601233740', '', 3, '2026-01-23 04:50:29'),
(143, 31, 13000.00, '2026-01-06', 'Cash', 'RCP202601231652', '', 3, '2026-01-23 04:55:49'),
(144, 34, 2000.00, '2023-06-27', 'Cash', 'RCP202601234176', '', 3, '2026-01-23 05:03:04'),
(145, 34, 3000.00, '2023-11-30', 'Cash', 'RCP202601234415', '', 3, '2026-01-23 05:03:24'),
(146, 34, 5000.00, '2024-01-10', 'Cash', 'RCP202601239377', '', 3, '2026-01-23 05:03:45'),
(147, 34, 5000.00, '2024-12-21', 'Cash', 'RCP202601235187', '', 3, '2026-01-23 05:04:19'),
(148, 34, 5000.00, '2025-03-15', 'Cash', 'RCP202601237149', '', 3, '2026-01-23 05:05:17'),
(150, 34, 5000.00, '2025-11-11', 'Cash', 'RCP202601234124', '', 3, '2026-01-23 05:05:43'),
(151, 36, 5000.00, '2025-04-22', 'Cash', 'RCP202601233665', '', 3, '2026-01-23 10:22:30'),
(152, 36, 5000.00, '2025-05-29', 'Cash', 'RCP202601239838', '', 3, '2026-01-23 10:22:47'),
(153, 36, 5000.00, '2025-06-24', 'Cash', 'RCP202601232605', '', 3, '2026-01-23 10:23:10'),
(154, 36, 5000.00, '2025-07-26', 'Cash', 'RCP202601238725', '', 3, '2026-01-23 10:23:30'),
(155, 36, 5000.00, '2025-09-06', 'Cash', 'RCP202601236005', '', 3, '2026-01-23 10:23:47'),
(156, 36, 5000.00, '2025-10-13', 'Cash', 'RCP202601234813', '', 3, '2026-01-23 10:24:03'),
(157, 36, 5000.00, '2025-11-10', 'Cash', 'RCP202601234431', '', 3, '2026-01-23 10:24:18'),
(158, 36, 5000.00, '2025-12-25', 'Cash', 'RCP202601234916', '', 3, '2026-01-23 10:24:33'),
(159, 37, 5000.00, '2025-06-10', 'Cash', 'RCP202601232175', '', 3, '2026-01-23 12:06:12'),
(160, 37, 5000.00, '2025-07-16', 'Cash', 'RCP202601233009', '', 3, '2026-01-23 12:06:27'),
(161, 37, 5000.00, '2025-09-09', 'Cash', 'RCP202601238852', '', 3, '2026-01-23 12:06:44'),
(162, 36, 5000.00, '2026-01-27', 'UPI', 'RCP202601277504', 'sakm', 3, '2026-01-27 14:09:04'),
(163, 10, 3700.00, '2026-01-28', 'UPI', 'RCP202601288992', 'by manoj bhai FO Rishikesh\r\n- sakm', 3, '2026-01-28 07:17:18'),
(164, 38, 2000.00, '2026-01-05', 'UPI', 'RCP202601055236', '1800 + 200\r\nsakm', 3, '2026-01-29 08:41:38'),
(165, 41, 20000.00, '2025-03-15', 'Cash', 'RCP202601292840', '', 3, '2026-01-29 11:59:41'),
(166, 38, 3000.00, '2026-01-10', 'UPI', 'RCP202601294205', '', 3, '2026-01-29 14:13:33'),
(167, 12, 5000.00, '2026-01-31', 'Cash', 'RCP202601313624', '', 3, '2026-01-31 12:23:37'),
(168, 12, 5000.00, '2026-02-01', 'Cash', 'RCP202602017903', '', 3, '2026-01-31 12:24:10');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `inquiry_id` int(11) DEFAULT NULL,
  `student_code` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `duration_months` int(11) NOT NULL,
  `total_fees` decimal(10,2) NOT NULL,
  `enrollment_date` date NOT NULL,
  `monthly_due_day` tinyint(4) DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `status` enum('Active','Completed','Dropped','Deleted') DEFAULT 'Active',
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `login_enabled` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `portal_last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `inquiry_id`, `student_code`, `full_name`, `email`, `phone`, `address`, `birthdate`, `photo`, `category_id`, `course_id`, `duration_months`, `total_fees`, `enrollment_date`, `monthly_due_day`, `completion_date`, `status`, `username`, `password`, `login_enabled`, `last_login`, `portal_last_login`, `created_at`, `updated_at`) VALUES
(2, NULL, 'STU202601173562', 'Malla Pradeep Chitragupta', 'Pradeepmalla35@gmail.com', '7041379134', 'Galaxy Apartment N-207 Chhatral', '2003-05-13', 'uploads/students/student_2_1769141509.jpeg', 2, 4, 3, 12500.00, '2025-12-04', NULL, NULL, 'Active', 'Pradeep', '$2y$10$bH6SNVLe5fXfIXXJe61sxOZGxAtA2FkiSMGUotjNFBtbvxHODK3ny', 1, '2026-02-02 07:30:43', NULL, '2026-01-17 05:59:27', '2026-02-02 07:30:43'),
(3, NULL, 'STU202601197362', 'Thakor Jigar Balvant Sinh', 'Thakorjigar927@gmail.com', '9265052738', 'Mansa Ghandhinagar Gujarat', '2001-09-27', NULL, 2, 4, 3, 12500.00, '2025-11-06', NULL, NULL, 'Active', 'Jigar', '$2y$10$z3bJEaiRi1JU1wr5y3CCYehm4IM3tNqieEVjQIk6ZABubwr/dNWSi', 1, '2026-02-03 06:34:14', NULL, '2026-01-19 04:44:12', '2026-02-03 06:34:14'),
(4, NULL, 'STU202601194061', 'Vaghela Nilamben Rajdeep', 'null@gmail.com', '7698719987', 'Janta Nagar society Kalol', '1999-09-29', NULL, 2, 4, 3, 15500.00, '2025-12-05', NULL, NULL, 'Active', 'Nilamben', '$2y$10$zQRf5eON4gO9Jb/8jTEmV.HGESUjRGJjmPePWT8gYjWHF7OUuSaye', 1, '2026-01-28 14:52:58', NULL, '2026-01-19 04:51:42', '2026-01-28 14:52:58'),
(7, NULL, 'STU202601196096', 'Malek Alfez Jafarbhai', 'malekalfez01@gmail.com', '9898326959', 'Moti Vas Matva Kuva Kalol', '2009-12-05', 'uploads/students/student_7_1769151091.jpeg', 2, 4, 6, 20000.00, '2025-08-21', NULL, NULL, 'Active', 'Alfez', '$2y$10$Ua1.TjFrMRARqS5yvHH01eZHmTxBQlTwF.fLZ6WtVr03InJegNTIC', 1, '2026-02-02 18:09:29', NULL, '2026-01-19 05:09:20', '2026-02-02 18:09:29'),
(8, NULL, 'STU202601196034', 'Bariya Shreddha Rajeshbhai', 'null@gmail.com', '7990662688', 'Kalol', '2004-12-16', NULL, 3, 7, 6, 28500.00, '2025-12-01', NULL, NULL, 'Active', 'Shraddha', '$2y$10$IEcovtWMlfA8/HDY/bsjROynLTkJqDgfBnqJ2ngMHFK739RHNFBqK', 1, '2026-02-04 06:24:26', NULL, '2026-01-19 05:16:47', '2026-02-04 06:24:26'),
(9, NULL, 'STU202601197721', 'Sipai Aliashgar Salimbhai', 'alimalek21636@gmail.com', '6355188720', 'Ahmedi Park Society Pansar Road Kalol', '2005-05-15', NULL, 2, 4, 3, 15500.00, '2025-10-13', NULL, NULL, 'Active', 'Ali', '$2y$10$kh3Xwxj9IYEftD9J0BOYy.nx6b0GUT3.lX8RVCzBOvGVG.Q/w3sp6', 1, '2026-02-03 07:24:38', NULL, '2026-01-19 05:38:30', '2026-02-03 07:24:38'),
(10, NULL, 'STU202601197997', 'Badgujar Rushikesh Manojbhai', 'null@gmail.com', '9426341022', 'C/303, Shiv Residency, Kalyanpura Kalol', '2005-09-08', 'uploads/students/student_10_1769163965.jpeg', 2, 4, 12, 53000.00, '2025-04-14', NULL, NULL, 'Active', 'Rushikesh', '$2y$10$OdNQc2AH8SxjQZKYHubpPuexxvYyKlnc/TMWoOX3lJN/bxPCF/Pz.', 1, '2026-02-04 04:10:29', NULL, '2026-01-19 10:29:03', '2026-02-04 04:10:29'),
(12, NULL, 'STU202601195626', 'Joshi Mansi Anandbhai Joshi', 'null@gmail.com', '9016518642', 'Kalyanpura Somnath Nagar Society D-5 Kalol', '2007-11-04', NULL, 2, 4, 12, 53000.00, '2025-06-09', NULL, NULL, 'Active', 'Mansi', '$2y$10$6okB3r7OLlo1E3r75JrXEu4F6vkUNmU2K5jOfPmx205soe/h19UnS', 1, '2026-02-04 04:50:58', NULL, '2026-01-19 11:09:44', '2026-02-04 04:50:58'),
(13, NULL, 'STU202601194270', 'Pathan Shahid Imran Khan', 'null@gmail.com', '9723801962', 'Kazba Vaas Itadara, Mansa', '2006-10-28', NULL, 2, 4, 6, 28500.00, '2025-12-01', NULL, NULL, 'Active', 'Shahid', '$2y$10$5se0PA78q0ygNj7waN.ft.3rzgazgQzMCa4r3czDVVrPE651yu0Mq', 1, '2026-02-03 08:05:14', NULL, '2026-01-19 11:38:26', '2026-02-03 08:05:14'),
(14, NULL, 'STU202601193040', 'Barot Dev Bhavin Kumar', 'barotdev800@gmail.com', '6354001217', 'Kalol', '2007-02-22', NULL, 2, 4, 18, 73500.00, '2025-06-13', NULL, NULL, 'Active', 'Dev', '$2y$10$UEDG8NyTUE0Wm8.AAYap9uV9bV205ooAegxFIYtz5ylg2wcAzll7.', 1, '2026-02-04 06:23:04', NULL, '2026-01-19 11:47:03', '2026-02-04 06:23:04'),
(15, NULL, 'STU202601198339', 'Prajapati Manish Bharatbhai', 'manishprajapati8002@gmail.com', '6352229657', 'Mahesana, Vadasna, Shankarpura, 382705', '2008-08-29', NULL, 3, 7, 6, 24000.00, '2025-07-08', NULL, '2026-01-23', 'Completed', NULL, NULL, 0, NULL, NULL, '2026-01-19 11:52:40', '2026-01-23 11:34:14'),
(16, NULL, 'STU202601195056', 'Zhala Rajdeep Gajendrasinh', 'null@gmail.com', '9216902530', 'Udaipur Rajasthan', '2005-02-01', NULL, 3, 7, 6, 28500.00, '2026-01-19', NULL, '2026-01-23', 'Completed', NULL, NULL, 0, NULL, NULL, '2026-01-19 11:58:40', '2026-01-23 11:30:46'),
(17, NULL, 'STU202601207081', 'Ansari Sahani Mukhtar Bhai', 'null@gmail.com', '94294782', 'Kalol', NULL, NULL, 1, 2, 6, 28500.00, '2025-10-09', NULL, NULL, 'Active', 'Sahani', '$2y$10$3HD5cm4l7Z9PyAzDCPYiYeW3bq.FUz1q10xQCJjFHrx78Jam6sHre', 1, NULL, NULL, '2026-01-20 04:22:19', '2026-01-23 10:36:10'),
(18, NULL, 'STU202601203439', 'Patel Kartik Rajendra Kumar', 'kartikpatel23112@gmail.com', '9712710723', 'Nardipur Kalol', NULL, NULL, 1, 2, 6, 28000.00, '2025-08-11', NULL, NULL, 'Active', 'Kartik', '$2y$10$8krvydM6q6fpHJcq4quU7eyen3Z0il80Shv/ftOE2fhjeaHsfX0f2', 1, '2026-02-04 06:08:26', NULL, '2026-01-20 04:30:46', '2026-02-04 06:08:26'),
(19, NULL, 'STU202601206067', 'Prajapati Dhrumil Kumar Prawinbhai', 'dhrumilprajapati8950@gmail.com', '9327934416', 'Ghandhinagar', NULL, NULL, 1, 2, 6, 28000.00, '2025-08-11', NULL, NULL, 'Active', 'Dhrumil', '$2y$10$BVo0ikdKx/XnJFCQ2YMoS.ons91iqUkxfVycjPoD/KTEimnBUQws2', 1, '2026-02-04 04:53:46', NULL, '2026-01-20 04:39:13', '2026-02-04 04:53:46'),
(20, NULL, 'STU202601209408', 'Jani Tinkal Yogeshbhai', 'twinklejani35@gmail.com', '8160393164', 'Kalol', NULL, NULL, 1, 2, 18, 85500.00, '2025-07-18', NULL, NULL, 'Active', 'Tinkal', '$2y$10$CIhSWZrOADmJrZUIZsTixOORtjS0LTw6OTv7ixnzV3wrKzCMggv1q', 1, '2026-02-04 06:23:30', NULL, '2026-01-20 04:47:00', '2026-02-04 06:23:30'),
(21, NULL, 'STU202601205550', 'Chauhan Bushra M. Hanif', 'null@gmail.com', '8160179746', 'Kalol', NULL, NULL, 1, 2, 6, 28500.00, '2025-05-20', NULL, NULL, 'Active', 'ChauhanBushra', '$2y$10$9A9B6pZF8HWETwQmxkrkpeZ2aPdWBTfIibL3mD.lEmKypudTEZqQ2', 1, NULL, NULL, '2026-01-20 04:53:54', '2026-01-20 06:13:21'),
(22, NULL, 'STU202601203753', 'Vaghela Pruthvirajsinh Harisinh', 'vaghelapruthvirajsinhv31@gmail.com', '9313619711', 'V1, Vekra, Kadi', NULL, NULL, 1, 2, 6, 28500.00, '2025-05-19', NULL, NULL, 'Active', 'Pruthviraj', '$2y$10$C5hUkd2I2Z6s8QNFU/Y1sOCL4cWrBnClhNZnBj25/5qdGZ24scYsy', 1, '2026-01-31 05:51:58', NULL, '2026-01-20 05:21:47', '2026-01-31 05:51:58'),
(23, NULL, 'STU202601207264', 'Mehta Vedansh Nitinkumar', 'vedanshmehta1008@gmail.com', '6354053479', 'Kadi', NULL, NULL, 1, 2, 6, 28500.00, '2025-05-19', NULL, NULL, 'Active', 'Vedansh', '$2y$10$w8w/r/ZJX6CbhEWWzGU2N.IoMY1KCR/JZ3.H0EKouZ7m0AD5UxUs2', 1, '2026-01-31 05:48:47', NULL, '2026-01-20 07:11:33', '2026-01-31 05:48:47'),
(24, NULL, 'STU202601208409', 'Saiyad Sarjumiya Sikandarmiya', 'null@gmail.com', '6355998335`', 'nani malek vass tower chok Kalol', NULL, NULL, 1, 2, 12, 48000.00, '2025-01-20', NULL, NULL, 'Active', 'Sarju', '$2y$10$xTIZwNVVOdBDCrxLnfLp6uvqBYOg0SNIoisMBlKL.cV1mQQx3B9kW', 1, '2026-02-04 05:47:24', NULL, '2026-01-20 10:39:45', '2026-02-04 05:47:24'),
(25, NULL, 'STU202601206723', 'Shaikh Tehrim Nasirbhai', 'null@gmail.com', '7433895330', 'Kalol', NULL, NULL, 1, 2, 24, 52500.00, '2024-05-21', NULL, NULL, 'Active', NULL, NULL, 0, NULL, NULL, '2026-01-20 11:48:01', '2026-01-20 11:48:01'),
(26, NULL, 'STU202601218107', 'Parasrampuria Kunal Sanjaybhai', 'parasrampuriakunal003@gmail.com', '7226069124', 'Kalol Borisana Road, E block 304', NULL, NULL, 1, 2, 24, 73000.00, '2024-07-10', NULL, NULL, 'Active', 'Kunal', '$2y$10$2KdOUYOQ0rJ.HZoIOAaHaO.LGAipDE4233r5wQEwFjpdGXpavXHwq', 1, '2026-02-02 08:11:25', NULL, '2026-01-21 04:37:43', '2026-02-02 08:11:25'),
(27, NULL, 'STU202601219800', 'Shaikh Ayan Sikandarbhai', 'null@gmail.com', '7778068491', 'Kalol', NULL, NULL, 1, 2, 24, 53500.00, '2024-04-22', NULL, NULL, 'Active', 'Ayan', '$2y$10$4Xg7W.lti0gVm9kghVDQaOZNDVjoA5CCPxIhhTyziVdozsSoY.OI2', 1, '2026-02-02 13:51:39', NULL, '2026-01-21 04:48:47', '2026-02-02 13:51:39'),
(28, NULL, 'STU202601213539', 'Khokhar Arkan Altaf husen', 'thomasadward2527@gmail.com', '9510952230', '16, Al-Samad Residency Near Mohammadi Hospital Kalol', '0000-00-00', NULL, 1, 2, 24, 64500.00, '2024-05-04', NULL, NULL, 'Active', 'Arkan', '$2y$10$GmyjEhdZncN0l4GFeoy47OPNqLbxhTd/XZq.LWyRg/nR7QFB6aLni', 1, '2026-02-02 13:50:00', NULL, '2026-01-21 05:04:50', '2026-02-02 13:50:00'),
(29, NULL, 'STU202601213775', 'Parmar Sahid Salimbhai', 'parmarsahid9@gmail.com', '7861800317', 'Kalol', NULL, NULL, 1, 2, 24, 57000.00, '2024-05-01', NULL, NULL, 'Active', 'Sahid', '$2y$10$p6ZPw32fnR5sP6A2IJOlCO2YYJIq4HkByl1Vgv8exkMP5KRkMehdW', 1, '2026-02-02 13:52:20', NULL, '2026-01-21 05:13:34', '2026-02-02 13:52:20'),
(31, NULL, 'STU202601228729', 'Payal Vikas Pawale', 'pawalepayal1@gmail.com', '9322905638', '508- Fortune Empire, Borisana Road, Oppo- Kashiram hall, Kalol, Gujarat- 382721.', NULL, NULL, 2, 4, 3, 13000.00, '2026-01-06', NULL, NULL, 'Active', 'Payal', '$2y$10$Nf95rD1r8qpci0JGMtVpiOZIhcbTLZ22eLJhKr0IsY3jocY283Rw6', 1, '2026-02-04 05:43:13', NULL, '2026-01-22 11:39:34', '2026-02-04 05:43:13'),
(32, NULL, 'STU202601236532', 'Patel Krupal Jitendrakumar', 'krupalpatel703@gmail.com', '6351292060', '6, shyamvihar-1 Vijapur Mehsana 382870', NULL, NULL, 1, 2, 6, 27000.00, '2025-12-23', NULL, NULL, 'Active', 'Krupal', '$2y$10$7X9quQ1NLWulWjjW4ovrNuz4BnExlVL2EsiBnrWFBQabB6zIVQtFi', 1, NULL, NULL, '2026-01-23 04:48:01', '2026-01-23 10:27:33'),
(34, NULL, 'STU202601239371', 'Chauhan Aamir', 'null@gmail.com', '8140535570', 'Kalol', '0000-00-00', NULL, 1, 2, 24, 48500.00, '2023-06-27', NULL, NULL, 'Active', 'Aamir', '$2y$10$KcA.vn8KnR51vdh/fUdftu9p.16/FuCD5XsC37AW160H953L0P9Ym', 1, '2026-02-02 13:50:00', NULL, '2026-01-23 05:02:32', '2026-02-02 13:50:00'),
(35, NULL, 'STU202601238121', 'Malek Mo. Hussain', 'null@gmail.com', '9054593873', 'Kalol', NULL, NULL, 1, 2, 24, 57000.00, '2024-07-05', NULL, NULL, 'Active', 'Hussain', '$2y$10$fzylVdK7Rr71jHSH0pbXVOc.ko109GfgWX/A/MVOCA90km7Cb6ozm', 1, '2026-01-31 07:02:55', NULL, '2026-01-23 05:13:43', '2026-01-31 07:02:55'),
(36, NULL, 'STU202601235647', 'Sudeep Goswami', 'null@gmail.com', '9327191488', '', '0000-00-00', NULL, 2, 4, 12, 57000.00, '2025-04-22', NULL, NULL, 'Active', 'Sudeep', '$2y$10$gtDIVtN005NzghmF0BWLYu6nRcPHwE7vYKPHV4FB/vNEJhK7n3VS2', 1, '2026-02-03 13:48:50', NULL, '2026-01-23 10:22:06', '2026-02-03 13:48:50'),
(37, NULL, 'STU202601234531', 'Jangid Dhiraj Manoj Kumar', 'null@gmail.com', '8824482059', 'kalol', NULL, NULL, 3, 7, 6, 28500.00, '2025-06-06', NULL, NULL, 'Active', 'Dhiraj', '$2y$10$KRSL.c5lpGo1YN6oBzudCOoOaXZOsCHAL9k8iYcEVl6zXF.DIUejO', 1, '2026-01-30 13:39:05', NULL, '2026-01-23 12:05:52', '2026-01-30 13:39:05'),
(38, NULL, 'STU202601236984', 'Manoj ramawat', 'ramavat554@gmail.com', '9313403564', 'Kalol', NULL, NULL, 3, 7, 3, 15500.00, '2026-01-05', NULL, NULL, 'Active', 'Manoj', '$2y$10$kDw1TI42UhSla5Ke9mkKiud4g8/FizOI7g5ynxGfFsFtnrEZRtIEG', 1, '2026-01-30 12:59:20', NULL, '2026-01-23 12:53:49', '2026-01-30 12:59:20'),
(41, NULL, 'STU202601275320', 'Ghanchi Mansha', 'ghanchimansha28@gmail.com', '7600669245', 'Kalol', '0000-00-00', NULL, 1, 2, 18, 57000.00, '2024-05-01', NULL, NULL, 'Active', NULL, NULL, 0, NULL, NULL, '2026-01-27 12:21:51', '2026-01-29 11:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent') NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `total_hours` decimal(4,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `student_attendance`
--

INSERT INTO `student_attendance` (`id`, `student_id`, `attendance_date`, `status`, `check_in_time`, `check_out_time`, `total_hours`, `created_at`) VALUES
(4, 3, '2026-01-22', 'Present', '09:56:54', '12:34:42', 2.62, '2026-01-22 04:26:54'),
(5, 10, '2026-01-22', 'Present', '10:10:44', '12:31:51', 2.35, '2026-01-22 04:40:44'),
(6, 9, '2026-01-22', 'Present', '10:10:54', '12:45:45', 2.57, '2026-01-22 04:40:54'),
(7, 4, '2026-01-22', 'Present', '10:19:01', '12:48:39', 2.48, '2026-01-22 04:49:01'),
(8, 8, '2026-01-22', 'Present', '10:22:31', '12:01:28', 1.63, '2026-01-22 04:52:31'),
(9, 20, '2026-01-22', 'Present', '10:24:21', '12:01:51', 1.62, '2026-01-22 04:54:21'),
(10, 19, '2026-01-22', 'Present', '10:39:03', NULL, NULL, '2026-01-22 05:09:03'),
(11, 14, '2026-01-22', 'Present', '10:43:23', '12:12:30', 1.48, '2026-01-22 05:13:23'),
(12, 7, '2026-01-22', 'Present', '11:04:36', '12:05:02', 1.00, '2026-01-22 05:34:36'),
(13, 24, '2026-01-22', 'Present', '11:30:04', '12:54:05', 1.40, '2026-01-22 06:00:04'),
(14, 26, '2026-01-22', 'Present', '11:30:34', '12:54:56', 1.40, '2026-01-22 06:00:34'),
(15, 31, '2026-01-22', 'Present', '17:13:24', NULL, NULL, '2026-01-22 11:43:24'),
(16, 2, '2026-01-22', 'Present', '18:37:44', NULL, NULL, '2026-01-22 13:07:44'),
(17, 10, '2026-01-23', 'Present', '09:21:44', '12:28:12', 3.10, '2026-01-23 03:51:44'),
(18, 8, '2026-01-23', 'Present', '10:09:39', '12:43:04', 2.55, '2026-01-23 04:39:39'),
(19, 4, '2026-01-23', 'Present', '10:12:13', '12:55:48', 2.72, '2026-01-23 04:42:13'),
(20, 3, '2026-01-23', 'Present', '10:18:14', '12:34:37', 2.27, '2026-01-23 04:48:14'),
(21, 14, '2026-01-23', 'Present', '10:22:21', '12:34:04', 2.18, '2026-01-23 04:52:21'),
(22, 9, '2026-01-23', 'Present', '11:02:40', '12:32:48', 1.50, '2026-01-23 05:32:40'),
(23, 7, '2026-01-23', 'Present', '11:15:16', '12:24:09', 1.13, '2026-01-23 05:45:16'),
(24, 35, '2026-01-23', 'Present', '11:22:37', '12:30:41', 1.10, '2026-01-23 05:52:37'),
(25, 24, '2026-01-23', 'Present', '11:23:15', '12:31:51', 1.13, '2026-01-23 05:53:15'),
(26, 13, '2026-01-23', 'Present', '17:26:18', '18:27:44', 1.02, '2026-01-23 11:56:18'),
(27, 27, '2026-01-23', 'Present', '17:52:31', '19:31:23', 1.63, '2026-01-23 12:22:31'),
(28, 28, '2026-01-23', 'Present', '17:53:29', '19:32:00', 1.63, '2026-01-23 12:23:29'),
(29, 36, '2026-01-23', 'Present', '18:03:02', '19:10:37', 1.12, '2026-01-23 12:33:02'),
(30, 34, '2026-01-23', 'Present', '18:15:38', '19:30:53', 1.25, '2026-01-23 12:45:38'),
(31, 37, '2026-01-23', 'Present', '18:17:54', '18:18:06', 0.00, '2026-01-23 12:47:54'),
(32, 31, '2026-01-23', 'Present', '18:21:52', NULL, NULL, '2026-01-23 12:51:52'),
(33, 38, '2026-01-23', 'Present', '18:28:52', '20:35:41', 2.10, '2026-01-23 12:58:52'),
(34, 2, '2026-01-23', 'Present', '18:40:04', '20:08:37', 1.47, '2026-01-23 13:10:04'),
(35, 10, '2026-01-24', 'Present', '09:42:06', '12:46:51', 3.07, '2026-01-24 04:12:06'),
(36, 9, '2026-01-24', 'Present', '09:43:55', '12:20:41', 2.60, '2026-01-24 04:13:55'),
(37, 8, '2026-01-24', 'Present', '10:17:01', '11:53:42', 1.60, '2026-01-24 04:47:01'),
(38, 20, '2026-01-24', 'Present', '10:18:24', '11:54:13', 1.58, '2026-01-24 04:48:24'),
(39, 3, '2026-01-24', 'Present', '10:25:24', '12:18:13', 1.87, '2026-01-24 04:55:24'),
(40, 4, '2026-01-24', 'Present', '10:25:37', '12:28:59', 2.05, '2026-01-24 04:55:37'),
(41, 14, '2026-01-24', 'Present', '11:36:43', '12:14:43', 0.63, '2026-01-24 06:06:43'),
(42, 13, '2026-01-24', 'Present', '11:38:39', '13:34:19', 1.92, '2026-01-24 06:08:39'),
(43, 31, '2026-01-26', 'Present', '11:03:33', NULL, NULL, '2026-01-26 05:33:33'),
(44, 9, '2026-01-26', 'Present', '17:05:36', '19:32:18', 2.43, '2026-01-26 11:35:36'),
(45, 27, '2026-01-26', 'Present', '17:33:58', '18:54:58', 1.35, '2026-01-26 12:03:58'),
(46, 36, '2026-01-26', 'Present', '17:43:00', '19:42:45', 1.98, '2026-01-26 12:13:00'),
(47, 10, '2026-01-27', 'Present', '09:50:48', '12:33:38', 2.70, '2026-01-27 04:20:48'),
(48, 8, '2026-01-27', 'Present', '10:10:32', '11:58:36', 1.80, '2026-01-27 04:40:32'),
(49, 20, '2026-01-27', 'Present', '10:12:32', '11:59:03', 1.77, '2026-01-27 04:42:32'),
(50, 4, '2026-01-27', 'Present', '10:18:33', '13:07:38', 2.82, '2026-01-27 04:48:33'),
(51, 7, '2026-01-27', 'Present', '10:44:39', '12:05:16', 1.33, '2026-01-27 05:14:39'),
(52, 18, '2026-01-27', 'Present', '10:55:45', NULL, NULL, '2026-01-27 05:25:45'),
(53, 19, '2026-01-27', 'Present', '10:56:37', NULL, NULL, '2026-01-27 05:26:37'),
(54, 24, '2026-01-27', 'Present', '11:10:41', '12:51:24', 1.67, '2026-01-27 05:40:41'),
(55, 31, '2026-01-27', 'Present', '11:57:40', NULL, NULL, '2026-01-27 06:27:40'),
(56, 27, '2026-01-27', 'Present', '17:43:50', '19:01:37', 1.28, '2026-01-27 12:13:50'),
(57, 28, '2026-01-27', 'Present', '17:45:51', '19:10:31', 1.40, '2026-01-27 12:15:51'),
(58, 34, '2026-01-27', 'Present', '17:51:18', '19:08:32', 1.28, '2026-01-27 12:21:18'),
(59, 2, '2026-01-27', 'Present', '18:58:21', NULL, NULL, '2026-01-27 13:28:21'),
(60, 36, '2026-01-27', 'Present', '18:59:25', '20:08:46', 1.15, '2026-01-27 13:29:25'),
(61, 10, '2026-01-28', 'Present', '09:47:36', '12:22:39', 2.58, '2026-01-28 04:17:36'),
(62, 14, '2026-01-28', 'Present', '10:00:46', '12:04:36', 2.05, '2026-01-28 04:30:46'),
(63, 3, '2026-01-28', 'Present', '10:08:43', '12:02:43', 1.90, '2026-01-28 04:38:43'),
(64, 8, '2026-01-28', 'Present', '10:12:23', '11:54:27', 1.70, '2026-01-28 04:42:23'),
(65, 20, '2026-01-28', 'Present', '10:12:50', '11:54:01', 1.68, '2026-01-28 04:42:50'),
(66, 18, '2026-01-28', 'Present', '11:11:42', NULL, NULL, '2026-01-28 05:41:42'),
(67, 26, '2026-01-28', 'Present', '11:11:51', '13:58:54', 2.78, '2026-01-28 05:41:51'),
(68, 19, '2026-01-28', 'Present', '11:12:53', NULL, NULL, '2026-01-28 05:42:53'),
(69, 24, '2026-01-28', 'Present', '11:23:17', NULL, NULL, '2026-01-28 05:53:17'),
(70, 31, '2026-01-28', 'Present', '11:54:38', NULL, NULL, '2026-01-28 06:24:38'),
(71, 4, '2026-01-28', 'Present', '17:02:48', '20:23:20', 3.33, '2026-01-28 11:32:48'),
(72, 7, '2026-01-28', 'Present', '17:10:52', '17:46:51', 0.58, '2026-01-28 11:40:52'),
(73, 28, '2026-01-28', 'Present', '17:43:21', '19:06:30', 1.38, '2026-01-28 12:13:21'),
(74, 36, '2026-01-28', 'Present', '17:48:55', '20:19:32', 2.50, '2026-01-28 12:18:55'),
(75, 34, '2026-01-28', 'Present', '17:52:07', '19:06:23', 1.23, '2026-01-28 12:22:07'),
(76, 38, '2026-01-28', 'Present', '18:32:44', '20:19:08', 1.77, '2026-01-28 13:02:44'),
(77, 13, '2026-01-28', 'Present', '18:43:15', '20:18:20', 1.58, '2026-01-28 13:13:15'),
(78, 2, '2026-01-28', 'Present', '20:17:08', '20:17:11', 0.00, '2026-01-28 14:47:08'),
(79, 10, '2026-01-29', 'Present', '09:58:31', '12:21:43', 2.38, '2026-01-29 04:28:31'),
(80, 18, '2026-01-29', 'Present', '10:01:31', '12:07:01', 2.08, '2026-01-29 04:31:31'),
(81, 14, '2026-01-29', 'Present', '10:04:25', '12:03:05', 1.97, '2026-01-29 04:34:25'),
(82, 19, '2026-01-29', 'Present', '10:04:27', '12:07:45', 2.05, '2026-01-29 04:34:27'),
(83, 8, '2026-01-29', 'Present', '10:14:23', '11:32:47', 1.30, '2026-01-29 04:44:23'),
(84, 20, '2026-01-29', 'Present', '10:15:54', '11:32:47', 1.27, '2026-01-29 04:45:54'),
(85, 31, '2026-01-29', 'Present', '11:08:40', '13:27:18', 2.30, '2026-01-29 05:38:40'),
(86, 24, '2026-01-29', 'Present', '11:16:32', '13:16:28', 1.98, '2026-01-29 05:46:32'),
(87, 35, '2026-01-29', 'Present', '11:22:13', NULL, NULL, '2026-01-29 05:52:13'),
(88, 7, '2026-01-29', 'Present', '11:27:07', '12:25:16', 0.97, '2026-01-29 05:57:07'),
(89, 26, '2026-01-29', 'Present', '12:04:34', '13:39:10', 1.57, '2026-01-29 06:34:34'),
(90, 28, '2026-01-29', 'Present', '17:45:04', '19:11:08', 1.43, '2026-01-29 12:15:04'),
(91, 27, '2026-01-29', 'Present', '17:45:36', '19:00:11', 1.23, '2026-01-29 12:15:36'),
(92, 34, '2026-01-29', 'Present', '18:11:32', '19:11:04', 0.98, '2026-01-29 12:41:32'),
(93, 36, '2026-01-29', 'Present', '18:30:45', '19:36:57', 1.10, '2026-01-29 13:00:45'),
(94, 2, '2026-01-29', 'Present', '18:51:39', '20:41:55', 1.83, '2026-01-29 13:21:39'),
(95, 38, '2026-01-29', 'Present', '18:51:50', '20:53:10', 2.02, '2026-01-29 13:21:50'),
(96, 10, '2026-01-30', 'Present', '09:43:48', '13:06:59', 3.38, '2026-01-30 04:13:48'),
(97, 19, '2026-01-30', 'Present', '10:28:17', NULL, NULL, '2026-01-30 04:58:17'),
(98, 18, '2026-01-30', 'Present', '10:29:04', NULL, NULL, '2026-01-30 04:59:04'),
(99, 14, '2026-01-30', 'Present', '10:31:48', '13:08:36', 2.60, '2026-01-30 05:01:48'),
(100, 26, '2026-01-30', 'Present', '11:57:00', NULL, NULL, '2026-01-30 06:27:00'),
(101, 31, '2026-01-30', 'Present', '12:02:15', NULL, NULL, '2026-01-30 06:32:15'),
(102, 3, '2026-01-30', 'Present', '13:07:36', '13:07:45', 0.00, '2026-01-30 07:37:36'),
(103, 28, '2026-01-30', 'Present', '17:36:46', '20:26:10', 2.82, '2026-01-30 12:06:46'),
(104, 9, '2026-01-30', 'Present', '17:39:47', '19:37:44', 1.95, '2026-01-30 12:09:47'),
(105, 34, '2026-01-30', 'Present', '17:43:53', '20:26:45', 2.70, '2026-01-30 12:13:53'),
(106, 36, '2026-01-30', 'Present', '18:11:48', '19:18:25', 1.10, '2026-01-30 12:41:48'),
(107, 38, '2026-01-30', 'Present', '18:30:07', '19:57:15', 1.45, '2026-01-30 13:00:07'),
(108, 2, '2026-01-30', 'Present', '18:43:10', '19:55:06', 1.18, '2026-01-30 13:13:10'),
(109, 37, '2026-01-30', 'Present', '19:09:12', NULL, NULL, '2026-01-30 13:39:12'),
(110, 27, '2026-01-30', 'Present', '20:27:32', '20:27:36', 0.00, '2026-01-30 14:57:32'),
(111, 10, '2026-01-31', 'Present', '09:42:50', '12:25:20', 2.70, '2026-01-31 04:12:50'),
(112, 34, '2026-01-31', 'Present', '10:55:53', '12:39:29', 1.72, '2026-01-31 05:25:53'),
(113, 14, '2026-01-31', 'Present', '10:55:58', '12:21:59', 1.43, '2026-01-31 05:25:58'),
(114, 3, '2026-01-31', 'Present', '10:57:30', '12:15:28', 1.28, '2026-01-31 05:27:30'),
(115, 23, '2026-01-31', 'Present', '10:58:51', '11:18:53', 0.33, '2026-01-31 05:28:51'),
(116, 22, '2026-01-31', 'Present', '10:58:51', '11:22:06', 0.38, '2026-01-31 05:28:51'),
(117, 28, '2026-01-31', 'Present', '11:35:41', '12:39:06', 1.05, '2026-01-31 06:05:41'),
(118, 31, '2026-01-31', 'Present', '12:18:07', NULL, NULL, '2026-01-31 06:48:07'),
(119, 35, '2026-01-31', 'Present', '12:33:08', NULL, NULL, '2026-01-31 07:03:08'),
(120, 36, '2026-01-31', 'Present', '17:58:26', '19:12:12', 1.22, '2026-01-31 12:28:26'),
(121, 10, '2026-02-02', 'Present', '09:46:39', '12:09:55', 2.38, '2026-02-02 04:16:39'),
(122, 14, '2026-02-02', 'Present', '10:32:50', '12:03:09', 1.50, '2026-02-02 05:02:50'),
(123, 3, '2026-02-02', 'Present', '10:42:40', '12:03:01', 1.33, '2026-02-02 05:12:40'),
(124, 31, '2026-02-02', 'Present', '10:47:41', '13:41:43', 2.90, '2026-02-02 05:17:41'),
(125, 24, '2026-02-02', 'Present', '11:19:14', '13:05:59', 1.77, '2026-02-02 05:49:14'),
(126, 26, '2026-02-02', 'Present', '12:06:13', '13:41:37', 1.58, '2026-02-02 06:36:13'),
(127, 2, '2026-02-02', 'Present', '13:00:46', NULL, NULL, '2026-02-02 07:30:46'),
(128, 28, '2026-02-02', 'Present', '17:31:43', '19:20:35', 1.80, '2026-02-02 12:01:43'),
(129, 7, '2026-02-02', 'Present', '17:46:49', '19:17:38', 1.50, '2026-02-02 12:16:49'),
(130, 27, '2026-02-02', 'Present', '17:52:51', '19:21:43', 1.47, '2026-02-02 12:22:51'),
(131, 36, '2026-02-02', 'Present', '17:56:56', '19:04:38', 1.12, '2026-02-02 12:26:56'),
(132, 34, '2026-02-02', 'Present', '18:09:56', '19:20:37', 1.17, '2026-02-02 12:39:56'),
(133, 29, '2026-02-02', 'Present', '18:12:35', '19:22:38', 1.17, '2026-02-02 12:42:35'),
(134, 10, '2026-02-03', 'Present', '09:58:36', '12:10:04', 2.18, '2026-02-03 04:28:36'),
(135, 8, '2026-02-03', 'Present', '10:13:46', '12:04:02', 1.83, '2026-02-03 04:43:46'),
(136, 12, '2026-02-03', 'Present', '10:16:55', '10:17:17', 0.00, '2026-02-03 04:46:55'),
(137, 3, '2026-02-03', 'Present', '10:38:03', '12:04:19', 1.43, '2026-02-03 05:08:03'),
(138, 24, '2026-02-03', 'Present', '11:02:42', '12:42:46', 1.67, '2026-02-03 05:32:42'),
(139, 31, '2026-02-03', 'Present', '11:17:53', '13:39:59', 2.37, '2026-02-03 05:47:53'),
(140, 9, '2026-02-03', 'Present', '11:36:41', '12:54:48', 1.30, '2026-02-03 06:06:41'),
(141, 13, '2026-02-03', 'Present', '12:42:42', '13:36:59', 0.90, '2026-02-03 07:12:42'),
(142, 36, '2026-02-03', 'Present', '17:50:25', '19:52:53', 2.03, '2026-02-03 12:20:25'),
(143, 10, '2026-02-04', 'Present', '09:40:36', '12:03:26', 2.37, '2026-02-04 04:10:36'),
(144, 8, '2026-02-04', 'Present', '10:08:40', '11:55:36', 1.77, '2026-02-04 04:38:40'),
(145, 20, '2026-02-04', 'Present', '10:10:25', '11:55:18', 1.73, '2026-02-04 04:40:25'),
(146, 14, '2026-02-04', 'Present', '10:16:47', '11:53:06', 1.60, '2026-02-04 04:46:47'),
(147, 12, '2026-02-04', 'Present', '10:21:07', '12:08:34', 1.78, '2026-02-04 04:51:07'),
(148, 19, '2026-02-04', 'Present', '10:23:53', '11:35:16', 1.18, '2026-02-04 04:53:53'),
(149, 18, '2026-02-04', 'Present', '10:24:04', '11:38:32', 1.23, '2026-02-04 04:54:04'),
(150, 31, '2026-02-04', 'Present', '11:13:19', NULL, NULL, '2026-02-04 05:43:19'),
(151, 24, '2026-02-04', 'Present', '11:17:26', NULL, NULL, '2026-02-04 05:47:26');

-- --------------------------------------------------------

--
-- Table structure for table `student_notifications`
--

CREATE TABLE `student_notifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','success','payment') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_notifications`
--

INSERT INTO `student_notifications` (`id`, `student_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(3, 2, 'Payment Received - Receipt #RCP202512063227', 'Dear Malla Pradeep Chitragupta,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 06 Dec 2025\n• Payment Method: UPI\n• Receipt Number: RCP202512063227\n\nFee Summary:\n• Total Course Fees: ₹12,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹7,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-17 05:59:59'),
(4, 2, 'Payment Received - Receipt #RCP202601156048', 'Dear Malla Pradeep Chitragupta,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,500.00\n• Payment Date: 15 Jan 2026\n• Payment Method: UPI\n• Receipt Number: RCP202601156048\n\nFee Summary:\n• Total Course Fees: ₹12,500.00\n• Total Paid: ₹9,500.00\n• Remaining Balance: ₹3,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-17 06:02:23'),
(5, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-17 06:09:01'),
(6, 3, 'Payment Received - Receipt #RCP2025110691117', 'Dear Thakor Jigar Balvant Sinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 06 Nov 2025\n• Payment Method: Cash\n• Receipt Number: RCP2025110691117\n\nFee Summary:\n• Total Course Fees: ₹12,500.00\n• Total Paid: ₹4,000.00\n• Remaining Balance: ₹8,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 04:45:00'),
(7, 3, 'Payment Received - Receipt #RCP202511127802', 'Dear Thakor Jigar Balvant Sinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 11 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202511127802\n\nFee Summary:\n• Total Course Fees: ₹12,500.00\n• Total Paid: ₹8,000.00\n• Remaining Balance: ₹4,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 04:46:02'),
(8, 3, 'Payment Received - Receipt #RCP202601054492', 'Dear Thakor Jigar Balvant Sinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,500.00\n• Payment Date: 05 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601054492\n\nFee Summary:\n• Total Course Fees: ₹12,500.00\n• Total Paid: ₹12,500.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-19 04:47:11'),
(9, 4, 'Payment Received - Receipt #RCP202512057301', 'Dear Vaghela Nilamben Rajdeep,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 05 Dec 2025\n• Payment Method: UPI\n• Receipt Number: RCP202512057301\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹10,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 04:52:46'),
(10, 4, 'Payment Received - Receipt #RCP202601054763', 'Dear Vaghela Nilamben Rajdeep,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,500.00\n• Payment Date: 05 Jan 2026\n• Payment Method: UPI\n• Receipt Number: RCP202601054763\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹10,500.00\n• Remaining Balance: ₹5,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 04:53:44'),
(13, 7, 'Payment Received - Receipt #RCP202509017382', 'Dear Malek Alfez Jafarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹20,000.00\n• Payment Date: 01 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202509017382\n\nFee Summary:\n• Total Course Fees: ₹20,000.00\n• Total Paid: ₹20,000.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-19 05:31:27'),
(14, 8, 'Payment Received - Receipt #RCP202512105488', 'Dear Bariya Shreddha Rajeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹6,000.00\n• Payment Date: 10 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512105488\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹6,000.00\n• Remaining Balance: ₹22,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 05:32:42'),
(15, 8, 'Payment Received - Receipt #RCP202601081472', 'Dear Bariya Shreddha Rajeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,500.00\n• Payment Date: 08 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601081472\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹10,500.00\n• Remaining Balance: ₹18,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 05:34:06'),
(16, 9, 'Payment Received - Receipt #RCP202510133754', 'Dear Sipai Aliashgar Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹1,000.00\n• Payment Date: 13 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202510133754\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹1,000.00\n• Remaining Balance: ₹14,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-19 05:44:30'),
(17, 9, 'Payment Received - Receipt #RCP202512033824', 'Dear Sipai Aliashgar Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹7,000.00\n• Payment Date: 03 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512033824\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹8,000.00\n• Remaining Balance: ₹7,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-19 05:53:14'),
(18, 9, 'Payment Received - Receipt #RCP202601028382', 'Dear Sipai Aliashgar Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,500.00\n• Payment Date: 02 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601028382\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹10,500.00\n• Remaining Balance: ₹5,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-19 06:00:56'),
(19, 9, 'Payment Received - Receipt #RCP202601172317', 'Dear Sipai Aliashgar Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 17 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601172317\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹12,500.00\n• Remaining Balance: ₹3,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-19 06:01:30'),
(22, 12, 'Payment Received - Receipt #RCP202506116632', 'Dear Joshi Mansi Anandbhai Joshi,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹7,000.00\n• Payment Date: 11 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202506116632\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹7,000.00\n• Remaining Balance: ₹21,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:10:33'),
(23, 12, 'Payment Received - Receipt #RCP202609112697', 'Dear Joshi Mansi Anandbhai Joshi,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 11 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202609112697\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹12,000.00\n• Remaining Balance: ₹16,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:11:21'),
(24, 12, 'Payment Received - Receipt #RCP202510135276', 'Dear Joshi Mansi Anandbhai Joshi,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 13 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202510135276\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹17,000.00\n• Remaining Balance: ₹11,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:12:46'),
(25, 12, 'Payment Received - Receipt #RCP202512104371', 'Dear Joshi Mansi Anandbhai Joshi,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹10,000.00\n• Payment Date: 10 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512104371\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹27,000.00\n• Remaining Balance: ₹1,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:13:35'),
(26, 10, 'Payment Received - Receipt #RCP202504143356', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 14 Apr 2025\n• Payment Method: UPI\n• Receipt Number: RCP202504143356\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹23,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:18:51'),
(27, 10, 'Payment Received - Receipt #RCP202505292047', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,200.00\n• Payment Date: 29 May 2025\n• Payment Method: UPI\n• Receipt Number: RCP202505292047\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹9,200.00\n• Remaining Balance: ₹19,300.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:19:43'),
(28, 10, 'Payment Received - Receipt #RCP202506305617', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,700.00\n• Payment Date: 30 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202506305617\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹12,900.00\n• Remaining Balance: ₹15,600.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:20:58'),
(29, 10, 'Payment Received - Receipt #RCP202507313602', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,700.00\n• Payment Date: 31 Jul 2025\n• Payment Method: UPI\n• Receipt Number: RCP202507313602\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹16,600.00\n• Remaining Balance: ₹11,900.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:22:48'),
(30, 10, 'Payment Received - Receipt #RCP202509033397', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,700.00\n• Payment Date: 03 Sep 2025\n• Payment Method: UPI\n• Receipt Number: RCP202509033397\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹20,300.00\n• Remaining Balance: ₹8,200.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:26:50'),
(31, 10, 'Payment Received - Receipt #RCP202510138832', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,750.00\n• Payment Date: 13 Oct 2025\n• Payment Method: UPI\n• Receipt Number: RCP202510138832\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹24,050.00\n• Remaining Balance: ₹4,450.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:27:55'),
(32, 10, 'Payment Received - Receipt #RCP202511105733', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 10 Nov 2025\n• Payment Method: UPI\n• Receipt Number: RCP202511105733\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹29,050.00\n• Remaining Balance: ₹27,950.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:30:31'),
(33, 10, 'Payment Received - Receipt #RCP202512256657', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,700.00\n• Payment Date: 25 Dec 2025\n• Payment Method: UPI\n• Receipt Number: RCP202512256657\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹32,750.00\n• Remaining Balance: ₹24,250.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:31:28'),
(34, 13, 'Payment Received - Receipt #RCP202512026801', 'Dear Pathan Shahid Imran Khan,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹6,000.00\n• Payment Date: 02 Dec 2025\n• Payment Method: UPI\n• Receipt Number: RCP202512026801\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹6,000.00\n• Remaining Balance: ₹22,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:39:38'),
(35, 13, 'Payment Received - Receipt #RCP202601074641', 'Dear Pathan Shahid Imran Khan,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 07 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601074641\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹18,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 11:40:22'),
(36, 14, 'Payment Received - Receipt #RCP202506177186', 'Dear Barot Dev Bhavin Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹24,500.00\n• Payment Date: 17 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202506177186\n\nFee Summary:\n• Total Course Fees: ₹73,500.00\n• Total Paid: ₹24,500.00\n• Remaining Balance: ₹49,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-19 11:47:46'),
(37, 15, 'Payment Received - Receipt #RCP202507149039', 'Dear Prajapati Manish Bharatbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹24,000.00\n• Payment Date: 14 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202507149039\n\nFee Summary:\n• Total Course Fees: ₹24,000.00\n• Total Paid: ₹24,000.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-19 11:53:27'),
(38, 16, 'Payment Received - Receipt #RCP202506033704', 'Dear Zhala Rajdeep Gajendrasinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 03 Jun 2025\n• Payment Method: UPI\n• Receipt Number: RCP202506033704\n\nFee Summary:\n• Total Course Fees: ₹30,000.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹25,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 12:00:13'),
(39, 16, 'Payment Received - Receipt #RCP202507156976', 'Dear Zhala Rajdeep Gajendrasinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 15 Jul 2025\n• Payment Method: UPI\n• Receipt Number: RCP202507156976\n\nFee Summary:\n• Total Course Fees: ₹30,000.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹20,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 12:01:10'),
(40, 16, 'Payment Received - Receipt #RCP202508218390', 'Dear Zhala Rajdeep Gajendrasinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 21 Aug 2025\n• Payment Method: UPI\n• Receipt Number: RCP202508218390\n\nFee Summary:\n• Total Course Fees: ₹30,000.00\n• Total Paid: ₹15,000.00\n• Remaining Balance: ₹15,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 12:01:52'),
(41, 16, 'Payment Received - Receipt #RCP202509161531', 'Dear Zhala Rajdeep Gajendrasinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 16 Sep 2025\n• Payment Method: UPI\n• Receipt Number: RCP202509161531\n\nFee Summary:\n• Total Course Fees: ₹30,000.00\n• Total Paid: ₹19,000.00\n• Remaining Balance: ₹11,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 12:02:42'),
(42, 16, 'Payment Received - Receipt #RCP202512022201', 'Dear Zhala Rajdeep Gajendrasinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 02 Dec 2025\n• Payment Method: UPI\n• Receipt Number: RCP202512022201\n\nFee Summary:\n• Total Course Fees: ₹30,000.00\n• Total Paid: ₹24,000.00\n• Remaining Balance: ₹6,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-19 12:03:23'),
(43, 14, 'Payment Received - Receipt #RCP202601195962', 'Dear Barot Dev Bhavin Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹24,500.00\n• Payment Date: 19 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601195962\n\nFee Summary:\n• Total Course Fees: ₹73,500.00\n• Total Paid: ₹49,000.00\n• Remaining Balance: ₹24,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-19 13:10:31'),
(44, 16, 'Payment Received - Receipt #RCP202601195922', 'Dear Zhala Rajdeep Gajendrasinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,500.00\n• Payment Date: 19 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601195922\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹28,500.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-19 13:15:14'),
(45, 17, 'Payment Received - Receipt #RCP202510098203', 'Dear Ansari Sahani Mukhtar Bhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹28,500.00\n• Payment Date: 09 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202510098203\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹28,500.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-20 04:26:30'),
(46, 18, 'Payment Received - Receipt #RCP202509239324', 'Dear Patel Kartik Rajendra Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 23 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202509239324\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹4,000.00\n• Remaining Balance: ₹24,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:33:00'),
(47, 18, 'Payment Received - Receipt #RCP202512019306', 'Dear Patel Kartik Rajendra Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 01 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512019306\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹9,000.00\n• Remaining Balance: ₹19,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:34:35'),
(48, 18, 'Payment Received - Receipt #RCP202512084025', 'Dear Patel Kartik Rajendra Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 08 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512084025\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹14,000.00\n• Remaining Balance: ₹14,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:35:18'),
(49, 19, 'Payment Received - Receipt #RCP202609232993', 'Dear Prajapati Dhrumil Kumar Prawinbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 23 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202609232993\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹4,000.00\n• Remaining Balance: ₹24,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:39:50'),
(50, 19, 'Payment Received - Receipt #RCP202512016919', 'Dear Prajapati Dhrumil Kumar Prawinbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 01 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512016919\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹9,000.00\n• Remaining Balance: ₹19,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:40:57'),
(51, 19, 'Payment Received - Receipt #RCP202512086422', 'Dear Prajapati Dhrumil Kumar Prawinbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 08 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512086422\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹14,000.00\n• Remaining Balance: ₹14,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:41:38'),
(52, 20, 'Payment Received - Receipt #RCP202509107425', 'Dear Jani Tinkal Yogeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 10 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202509107425\n\nFee Summary:\n• Total Course Fees: ₹85,500.00\n• Total Paid: ₹4,000.00\n• Remaining Balance: ₹81,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:47:48'),
(53, 20, 'Payment Received - Receipt #RCP202510145965', 'Dear Jani Tinkal Yogeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 14 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202510145965\n\nFee Summary:\n• Total Course Fees: ₹85,500.00\n• Total Paid: ₹8,000.00\n• Remaining Balance: ₹77,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:48:30'),
(54, 20, 'Payment Received - Receipt #RCP202511125951', 'Dear Jani Tinkal Yogeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 12 Nov 2025\n• Payment Method: Cash\n• Receipt Number: RCP202511125951\n\nFee Summary:\n• Total Course Fees: ₹85,500.00\n• Total Paid: ₹12,000.00\n• Remaining Balance: ₹73,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:49:01'),
(55, 20, 'Payment Received - Receipt #RCP202512112481', 'Dear Jani Tinkal Yogeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 11 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512112481\n\nFee Summary:\n• Total Course Fees: ₹85,500.00\n• Total Paid: ₹16,000.00\n• Remaining Balance: ₹69,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:50:05'),
(56, 20, 'Payment Received - Receipt #RCP202601137893', 'Dear Jani Tinkal Yogeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 13 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601137893\n\nFee Summary:\n• Total Course Fees: ₹85,500.00\n• Total Paid: ₹20,000.00\n• Remaining Balance: ₹65,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 04:51:00'),
(57, 21, 'Payment Received - Receipt #RCP202506165421', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 16 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202506165421\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹2,000.00\n• Remaining Balance: ₹26,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:01:15'),
(58, 21, 'Payment Received - Receipt #RCP202507165401', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 16 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202507165401\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹4,000.00\n• Remaining Balance: ₹24,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:02:56'),
(59, 21, 'Payment Received - Receipt #RCP202508232078', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 23 Aug 2025\n• Payment Method: Cash\n• Receipt Number: RCP202508232078\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹6,000.00\n• Remaining Balance: ₹22,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:03:51'),
(60, 21, 'Payment Received - Receipt #RCP202509257372', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 25 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202509257372\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹8,000.00\n• Remaining Balance: ₹20,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:05:57'),
(61, 21, 'Payment Received - Receipt #RCP202510288789', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 28 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202510288789\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹18,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:06:32'),
(62, 21, 'Payment Received - Receipt #RCP202511251100', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 25 Nov 2025\n• Payment Method: Cash\n• Receipt Number: RCP202511251100\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹12,000.00\n• Remaining Balance: ₹16,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:07:30'),
(63, 21, 'Payment Received - Receipt #RCP202601024856', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 02 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601024856\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹14,000.00\n• Remaining Balance: ₹14,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:10:07'),
(64, 22, 'Payment Received - Receipt #RCP202505225429', 'Dear Vaghela Pruthvirajsinh Harisinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 22 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202505225429\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹23,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:25:51'),
(65, 22, 'Payment Received - Receipt #RCP202505235785', 'Dear Vaghela Pruthvirajsinh Harisinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 23 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202505235785\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹18,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:26:36'),
(66, 22, 'Payment Received - Receipt #RCP202507244944', 'Dear Vaghela Pruthvirajsinh Harisinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 24 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202507244944\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹15,000.00\n• Remaining Balance: ₹13,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:27:36'),
(67, 22, 'Payment Received - Receipt #RCP202509012117', 'Dear Vaghela Pruthvirajsinh Harisinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 01 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202509012117\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹20,000.00\n• Remaining Balance: ₹8,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:33:11'),
(68, 22, 'Payment Received - Receipt #RCP202512133975', 'Dear Vaghela Pruthvirajsinh Harisinh,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 13 Dec 2026\n• Payment Method: Cash\n• Receipt Number: RCP202512133975\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹25,000.00\n• Remaining Balance: ₹3,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 05:33:53'),
(69, 21, 'Payment Received - Receipt #RCP202601209658', 'Dear Chauhan Bushra M. Hanif,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,500.00\n• Payment Date: 20 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601209658\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹16,500.00\n• Remaining Balance: ₹12,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 06:12:32'),
(70, 21, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-20 06:13:21'),
(71, 23, 'Payment Received - Receipt #RCP202505221587', 'Dear Mehta Vedansh Nitinkumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 22 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202505221587\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹23,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 07:20:17'),
(72, 23, 'Payment Received - Receipt #RCP202505234148', 'Dear Mehta Vedansh Nitinkumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 23 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202505234148\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹18,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 07:21:01'),
(73, 23, 'Payment Received - Receipt #RCP202507248438', 'Dear Mehta Vedansh Nitinkumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 24 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202507248438\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹15,000.00\n• Remaining Balance: ₹13,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 07:21:40'),
(74, 23, 'Payment Received - Receipt #RCP202509017831', 'Dear Mehta Vedansh Nitinkumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 01 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202509017831\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹20,000.00\n• Remaining Balance: ₹8,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 07:22:25'),
(75, 23, 'Payment Received - Receipt #RCP202512139533', 'Dear Mehta Vedansh Nitinkumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,500.00\n• Payment Date: 13 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202512139533\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹23,500.00\n• Remaining Balance: ₹5,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 07:23:05'),
(76, 24, 'Payment Received - Receipt #RCP202501202877', 'Dear Saiyad Sarjumiya Sikandarmiya,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹24,000.00\n• Payment Date: 20 Jan 2025\n• Payment Method: Cash\n• Receipt Number: RCP202501202877\n\nFee Summary:\n• Total Course Fees: ₹48,000.00\n• Total Paid: ₹24,000.00\n• Remaining Balance: ₹24,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-20 10:41:01'),
(77, 24, 'Payment Received - Receipt #RCP202506206425', 'Dear Saiyad Sarjumiya Sikandarmiya,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹24,000.00\n• Payment Date: 20 Jun 2026\n• Payment Method: Cash\n• Receipt Number: RCP202506206425\n\nFee Summary:\n• Total Course Fees: ₹48,000.00\n• Total Paid: ₹48,000.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 1, '2026-01-20 10:41:46'),
(78, 25, 'Payment Received - Receipt #RCP202405212468', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,500.00\n• Payment Date: 21 May 2024\n• Payment Method: Cash\n• Receipt Number: RCP202405212468\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹3,500.00\n• Remaining Balance: ₹49,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:48:53'),
(79, 25, 'Payment Received - Receipt #RCP202601204842', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 21 Jun 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601204842\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹5,500.00\n• Remaining Balance: ₹47,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:49:50'),
(80, 25, 'Payment Received - Receipt #RCP202601209215', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 12 Aug 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601209215\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹9,500.00\n• Remaining Balance: ₹43,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:50:29'),
(81, 25, 'Payment Received - Receipt #RCP202601209419', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 23 Sep 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601209419\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹11,500.00\n• Remaining Balance: ₹41,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:51:03'),
(82, 25, 'Payment Received - Receipt #RCP202601204880', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 28 Oct 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601204880\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹13,500.00\n• Remaining Balance: ₹39,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:51:29'),
(83, 25, 'Payment Received - Receipt #RCP202601205976', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 10 Dec 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601205976\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹15,500.00\n• Remaining Balance: ₹37,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:53:56'),
(84, 25, 'Payment Received - Receipt #RCP202601208860', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 11 Jan 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601208860\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹17,500.00\n• Remaining Balance: ₹35,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:54:48'),
(85, 25, 'Payment Received - Receipt #RCP202601207899', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 10 Feb 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601207899\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹20,500.00\n• Remaining Balance: ₹32,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:55:31'),
(86, 25, 'Payment Received - Receipt #RCP202601202335', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹6,000.00\n• Payment Date: 10 Apr 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601202335\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹26,500.00\n• Remaining Balance: ₹26,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:56:04'),
(87, 25, 'Payment Received - Receipt #RCP202601207439', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 19 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601207439\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹29,500.00\n• Remaining Balance: ₹23,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:56:38'),
(88, 25, 'Payment Received - Receipt #RCP202601203003', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 17 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601203003\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹32,500.00\n• Remaining Balance: ₹20,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:57:08'),
(89, 25, 'Payment Received - Receipt #RCP202601201730', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 10 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601201730\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹35,500.00\n• Remaining Balance: ₹17,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:57:37'),
(90, 25, 'Payment Received - Receipt #RCP202601201251', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 07 Aug 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601201251\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹38,500.00\n• Remaining Balance: ₹14,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:57:54'),
(91, 25, 'Payment Received - Receipt #RCP202601201525', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 22 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601201525\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹41,500.00\n• Remaining Balance: ₹11,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:58:23'),
(92, 25, 'Payment Received - Receipt #RCP202601201996', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹1,000.00\n• Payment Date: 28 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601201996\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹42,500.00\n• Remaining Balance: ₹10,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:58:56'),
(93, 25, 'Payment Received - Receipt #RCP202601209835', 'Dear Shaikh Tehrim Nasirbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 19 Nov 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601209835\n\nFee Summary:\n• Total Course Fees: ₹52,500.00\n• Total Paid: ₹44,500.00\n• Remaining Balance: ₹8,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-20 11:59:39'),
(94, 26, 'Payment Received - Receipt #RCP202410078049', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 07 Oct 2024\n• Payment Method: Cash\n• Receipt Number: RCP202410078049\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹4,000.00\n• Remaining Balance: ₹69,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:38:41');
INSERT INTO `student_notifications` (`id`, `student_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(95, 26, 'Payment Received - Receipt #RCP202601215014', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹8,000.00\n• Payment Date: 15 Sep 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601215014\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹12,000.00\n• Remaining Balance: ₹61,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:39:23'),
(96, 26, 'Payment Received - Receipt #RCP202601217956', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 11 Nov 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601217956\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹16,000.00\n• Remaining Balance: ₹57,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:39:45'),
(97, 26, 'Payment Received - Receipt #RCP202601217982', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 24 Dec 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601217982\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹24,000.00\n• Remaining Balance: ₹49,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:42:52'),
(98, 26, 'Payment Received - Receipt #RCP202601213946', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹8,000.00\n• Payment Date: 11 Feb 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601213946\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹32,000.00\n• Remaining Balance: ₹41,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:44:06'),
(99, 26, 'Payment Received - Receipt #RCP202601212068', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 10 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601212068\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹37,000.00\n• Remaining Balance: ₹36,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:44:38'),
(100, 26, 'Payment Received - Receipt #RCP202601213741', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹1,500.00\n• Payment Date: 25 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601213741\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹38,500.00\n• Remaining Balance: ₹34,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:45:16'),
(101, 26, 'Payment Received - Receipt #RCP202601213765', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 01 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601213765\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹42,500.00\n• Remaining Balance: ₹30,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:45:50'),
(102, 26, 'Payment Received - Receipt #RCP202601214290', 'Dear Parasrampuria Kunal Sanjaybhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹4,000.00\n• Payment Date: 11 Nov 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601214290\n\nFee Summary:\n• Total Course Fees: ₹73,000.00\n• Total Paid: ₹46,500.00\n• Remaining Balance: ₹26,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:46:32'),
(103, 27, 'Payment Received - Receipt #RCP202601211448', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 22 Apr 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601211448\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹2,000.00\n• Remaining Balance: ₹51,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:49:31'),
(104, 27, 'Payment Received - Receipt #RCP202601212651', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 06 Jun 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601212651\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹4,000.00\n• Remaining Balance: ₹49,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:49:54'),
(105, 27, 'Payment Received - Receipt #RCP202601217597', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 12 Jul 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601217597\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹6,000.00\n• Remaining Balance: ₹47,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:51:34'),
(106, 27, 'Payment Received - Receipt #RCP202601217560', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 17 Aug 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601217560\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹8,000.00\n• Remaining Balance: ₹45,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:52:04'),
(107, 27, 'Payment Received - Receipt #RCP202601214180', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 18 Sep 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601214180\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹43,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:52:26'),
(108, 27, 'Payment Received - Receipt #RCP202601216713', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 10 Oct 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601216713\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹12,000.00\n• Remaining Balance: ₹41,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:52:41'),
(109, 27, 'Payment Received - Receipt #RCP202601212019', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 13 Nov 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601212019\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹14,000.00\n• Remaining Balance: ₹39,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:53:07'),
(110, 27, 'Payment Received - Receipt #RCP202601216392', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 10 Dec 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601216392\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹16,000.00\n• Remaining Balance: ₹37,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:53:30'),
(111, 27, 'Payment Received - Receipt #RCP202601216173', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 12 Jan 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601216173\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹18,000.00\n• Remaining Balance: ₹35,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:53:53'),
(112, 27, 'Payment Received - Receipt #RCP202601212141', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 09 Jan 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601212141\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹21,000.00\n• Remaining Balance: ₹32,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:54:26'),
(113, 27, 'Payment Received - Receipt #RCP202601211902', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 11 Feb 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601211902\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹24,000.00\n• Remaining Balance: ₹29,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:54:49'),
(114, 27, 'Payment Received - Receipt #RCP202601214159', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 17 Mar 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601214159\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹27,000.00\n• Remaining Balance: ₹26,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:55:03'),
(115, 27, 'Payment Received - Receipt #RCP202601214284', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 09 Apr 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601214284\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹27,500.00\n• Remaining Balance: ₹26,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:58:46'),
(116, 27, 'Payment Received - Receipt #RCP202601219229', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹1,000.00\n• Payment Date: 13 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601219229\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹28,500.00\n• Remaining Balance: ₹25,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:59:09'),
(117, 27, 'Payment Received - Receipt #RCP202601215923', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 25 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601215923\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹30,500.00\n• Remaining Balance: ₹23,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 04:59:35'),
(118, 27, 'Payment Received - Receipt #RCP202601212367', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 19 Aug 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601212367\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹32,500.00\n• Remaining Balance: ₹21,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:00:07'),
(119, 27, 'Payment Received - Receipt #RCP202601218438', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 17 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601218438\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹34,500.00\n• Remaining Balance: ₹19,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:00:46'),
(120, 27, 'Payment Received - Receipt #RCP202601216037', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 16 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601216037\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹36,500.00\n• Remaining Balance: ₹17,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:01:10'),
(121, 27, 'Payment Received - Receipt #RCP202601216509', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 09 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601216509\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹38,500.00\n• Remaining Balance: ₹15,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:01:26'),
(122, 27, 'Payment Received - Receipt #RCP202601219441', 'Dear Shaikh Ayan Sikandarbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,500.00\n• Payment Date: 07 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601219441\n\nFee Summary:\n• Total Course Fees: ₹53,500.00\n• Total Paid: ₹41,000.00\n• Remaining Balance: ₹12,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:01:53'),
(123, 28, 'Payment Received - Receipt #RCP202601213885', 'Dear Khokhar Arkan Altaf husen,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 04 May 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601213885\n\nFee Summary:\n• Total Course Fees: ₹32,500.00\n• Total Paid: ₹3,000.00\n• Remaining Balance: ₹29,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-21 05:05:06'),
(124, 28, 'Payment Received - Receipt #RCP202601218582', 'Dear Khokhar Arkan Altaf husen,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 07 Feb 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601218582\n\nFee Summary:\n• Total Course Fees: ₹32,500.00\n• Total Paid: ₹6,000.00\n• Remaining Balance: ₹26,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-21 05:06:29'),
(125, 28, 'Payment Received - Receipt #RCP202601217192', 'Dear Khokhar Arkan Altaf husen,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 22 Aug 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601217192\n\nFee Summary:\n• Total Course Fees: ₹32,500.00\n• Total Paid: ₹9,000.00\n• Remaining Balance: ₹23,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-21 05:06:47'),
(126, 28, 'Payment Received - Receipt #RCP202601215576', 'Dear Khokhar Arkan Altaf husen,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 08 Oct 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601215576\n\nFee Summary:\n• Total Course Fees: ₹32,500.00\n• Total Paid: ₹12,000.00\n• Remaining Balance: ₹20,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-21 05:07:38'),
(127, 28, 'Payment Received - Receipt #RCP202601213159', 'Dear Khokhar Arkan Altaf husen,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹9,000.00\n• Payment Date: 07 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601213159\n\nFee Summary:\n• Total Course Fees: ₹32,500.00\n• Total Paid: ₹21,000.00\n• Remaining Balance: ₹11,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-21 05:07:58'),
(128, 29, 'Payment Received - Receipt #RCP202601216373', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,500.00\n• Payment Date: 02 May 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601216373\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹2,500.00\n• Remaining Balance: ₹54,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:14:12'),
(129, 29, 'Payment Received - Receipt #RCP202601212172', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 01 Jun 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601212172\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹4,500.00\n• Remaining Balance: ₹52,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:14:42'),
(130, 29, 'Payment Received - Receipt #RCP202601217278', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 23 Jul 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601217278\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹6,500.00\n• Remaining Balance: ₹50,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:15:10'),
(131, 29, 'Payment Received - Receipt #RCP202601218485', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 21 Aug 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601218485\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹8,500.00\n• Remaining Balance: ₹48,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:15:27'),
(132, 29, 'Payment Received - Receipt #RCP202601218870', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 20 Sep 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601218870\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹10,500.00\n• Remaining Balance: ₹46,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:15:54'),
(133, 29, 'Payment Received - Receipt #RCP202601214922', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 10 Dec 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601214922\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹12,500.00\n• Remaining Balance: ₹44,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:16:17'),
(134, 29, 'Payment Received - Receipt #RCP202601215560', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 08 Nov 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601215560\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹14,500.00\n• Remaining Balance: ₹42,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:18:04'),
(135, 29, 'Payment Received - Receipt #RCP202601212963', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 13 Jan 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601212963\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹17,500.00\n• Remaining Balance: ₹39,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:18:26'),
(136, 29, 'Payment Received - Receipt #RCP202601212963', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 13 Jan 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601212963\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹20,500.00\n• Remaining Balance: ₹36,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:18:26'),
(137, 29, 'Payment Received - Receipt #RCP202601216833', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 13 Feb 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601216833\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹23,500.00\n• Remaining Balance: ₹33,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:18:53'),
(138, 29, 'Payment Received - Receipt #RCP202601215541', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 10 Apr 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601215541\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹23,500.00\n• Remaining Balance: ₹33,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:20:27'),
(139, 29, 'Payment Received - Receipt #RCP202601218343', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 15 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601218343\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹26,500.00\n• Remaining Balance: ₹30,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:20:50'),
(140, 29, 'Payment Received - Receipt #RCP202601215730', 'Dear Parmar Sahid Salimbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 22 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601215730\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹28,500.00\n• Remaining Balance: ₹28,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-21 05:21:07'),
(141, 3, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 04:26:05'),
(142, 10, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 04:27:55'),
(143, 9, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 04:38:15'),
(144, 4, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 04:45:52'),
(145, 20, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 04:49:59'),
(146, 8, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 04:51:24'),
(147, 19, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 04:55:03'),
(148, 18, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 04:57:02'),
(149, 14, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 05:06:21'),
(150, 7, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 05:24:17'),
(154, 26, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 05:48:54'),
(155, 26, 'Payment Reminder', 'Your course fees payment is pending. Please make the payment at the earliest to avoid any inconvenience.', 'payment', 0, '2026-01-22 07:19:29'),
(156, 31, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 11:40:25'),
(157, 31, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 11:40:44'),
(158, 31, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 11:40:51'),
(159, 31, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 11:40:51'),
(160, 31, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 11:40:51'),
(161, 31, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-22 11:40:56'),
(162, 27, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 12:41:42'),
(163, 27, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 12:41:45'),
(164, 27, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 12:41:46'),
(165, 27, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 12:41:46'),
(166, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:04:10'),
(167, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:04:34'),
(168, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:04:34'),
(169, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:04:36'),
(170, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:04:36'),
(171, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:04:36'),
(172, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:04:36'),
(173, 2, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-22 13:05:38'),
(174, 20, 'Payment Received - Receipt #RCP202601234342', 'Dear Jani Tinkal Yogeshbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹10,000.00\n• Payment Date: 22 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601234342\n\nFee Summary:\n• Total Course Fees: ₹85,500.00\n• Total Paid: ₹30,000.00\n• Remaining Balance: ₹55,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 04:14:12'),
(175, 19, 'Payment Received - Receipt #RCP202601235339', 'Dear Prajapati Dhrumil Kumar Prawinbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹7,000.00\n• Payment Date: 15 Aug 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601235339\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹21,000.00\n• Remaining Balance: ₹7,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 04:36:43'),
(176, 18, 'Payment Received - Receipt #RCP202601231769', 'Dear Patel Kartik Rajendra Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹7,000.00\n• Payment Date: 15 Aug 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601231769\n\nFee Summary:\n• Total Course Fees: ₹28,000.00\n• Total Paid: ₹21,000.00\n• Remaining Balance: ₹7,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 04:37:26'),
(177, 32, 'Payment Received - Receipt #RCP202601233740', 'Dear Patel Krupal Jitendrakumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹27,000.00\n• Payment Date: 23 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601233740\n\nFee Summary:\n• Total Course Fees: ₹27,000.00\n• Total Paid: ₹27,000.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-23 04:50:29'),
(178, 32, 'Payment Received - Receipt #RCP202601233740', 'Dear Patel Krupal Jitendrakumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹27,000.00\n• Payment Date: 23 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601233740\n\nFee Summary:\n• Total Course Fees: ₹27,000.00\n• Total Paid: ₹54,000.00\n• Remaining Balance: ₹-27,000.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-23 04:50:45'),
(179, 31, 'Payment Received - Receipt #RCP202601231652', 'Dear Payal Vikas Pawale,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹13,000.00\n• Payment Date: 06 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601231652\n\nFee Summary:\n• Total Course Fees: ₹13,000.00\n• Total Paid: ₹13,000.00\n• Remaining Balance: ₹0.00\n\n🎉 Congratulations! Your fees are now fully paid.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'success', 0, '2026-01-23 04:55:49'),
(180, 34, 'Payment Received - Receipt #RCP202601234176', 'Dear Chauhan Aamir,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 27 Jun 2023\n• Payment Method: Cash\n• Receipt Number: RCP202601234176\n\nFee Summary:\n• Total Course Fees: ₹48,500.00\n• Total Paid: ₹2,000.00\n• Remaining Balance: ₹46,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 05:03:04'),
(181, 34, 'Payment Received - Receipt #RCP202601234415', 'Dear Chauhan Aamir,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 30 Nov 2023\n• Payment Method: Cash\n• Receipt Number: RCP202601234415\n\nFee Summary:\n• Total Course Fees: ₹48,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹43,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 05:03:24'),
(182, 34, 'Payment Received - Receipt #RCP202601239377', 'Dear Chauhan Aamir,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 10 Jan 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601239377\n\nFee Summary:\n• Total Course Fees: ₹48,500.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹38,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 05:03:45'),
(183, 34, 'Payment Received - Receipt #RCP202601235187', 'Dear Chauhan Aamir,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 21 Dec 2024\n• Payment Method: Cash\n• Receipt Number: RCP202601235187\n\nFee Summary:\n• Total Course Fees: ₹48,500.00\n• Total Paid: ₹15,000.00\n• Remaining Balance: ₹33,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 05:04:19'),
(184, 34, 'Payment Received - Receipt #RCP202601237149', 'Dear Chauhan Aamir,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 15 Mar 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601237149\n\nFee Summary:\n• Total Course Fees: ₹48,500.00\n• Total Paid: ₹20,000.00\n• Remaining Balance: ₹28,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 05:05:17'),
(185, 34, 'Payment Received - Receipt #RCP202601237149', 'Dear Chauhan Aamir,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 15 Mar 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601237149\n\nFee Summary:\n• Total Course Fees: ₹48,500.00\n• Total Paid: ₹25,000.00\n• Remaining Balance: ₹23,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 05:05:17'),
(186, 34, 'Payment Received - Receipt #RCP202601234124', 'Dear Chauhan Aamir,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 11 Nov 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601234124\n\nFee Summary:\n• Total Course Fees: ₹48,500.00\n• Total Paid: ₹30,000.00\n• Remaining Balance: ₹18,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 05:05:43'),
(187, 35, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 05:14:00'),
(188, 36, 'Payment Received - Receipt #RCP202601233665', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 22 Apr 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601233665\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹52,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:22:30'),
(189, 36, 'Payment Received - Receipt #RCP202601239838', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 29 May 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601239838\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹47,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:22:47'),
(190, 36, 'Payment Received - Receipt #RCP202601232605', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 24 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601232605\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹15,000.00\n• Remaining Balance: ₹42,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:23:10'),
(191, 36, 'Payment Received - Receipt #RCP202601238725', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 26 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601238725\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹20,000.00\n• Remaining Balance: ₹37,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:23:30'),
(192, 36, 'Payment Received - Receipt #RCP202601236005', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 06 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601236005\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹25,000.00\n• Remaining Balance: ₹32,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:23:47'),
(193, 36, 'Payment Received - Receipt #RCP202601234813', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 13 Oct 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601234813\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹30,000.00\n• Remaining Balance: ₹27,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:24:03'),
(194, 36, 'Payment Received - Receipt #RCP202601234431', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 10 Nov 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601234431\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹35,000.00\n• Remaining Balance: ₹22,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:24:18'),
(195, 36, 'Payment Received - Receipt #RCP202601234916', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 25 Dec 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601234916\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹40,000.00\n• Remaining Balance: ₹17,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-23 10:24:33'),
(196, 36, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 10:25:05'),
(197, 32, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 10:27:25'),
(198, 32, 'Password Reset', 'Your student portal password has been reset by the administrator. Please use your new credentials to login.', 'warning', 0, '2026-01-23 10:27:33'),
(199, 29, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 10:27:59'),
(200, 28, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-23 10:28:25'),
(201, 23, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 10:34:49'),
(203, 17, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 10:36:10'),
(204, 13, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 11:55:02'),
(205, 37, 'Payment Received - Receipt #RCP202601232175', 'Dear Jangid Dhiraj Manoj Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 10 Jun 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601232175\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹23,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-23 12:06:12'),
(206, 37, 'Payment Received - Receipt #RCP202601233009', 'Dear Jangid Dhiraj Manoj Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 16 Jul 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601233009\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹10,000.00\n• Remaining Balance: ₹18,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-23 12:06:27'),
(207, 37, 'Payment Received - Receipt #RCP202601238852', 'Dear Jangid Dhiraj Manoj Kumar,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 09 Sep 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601238852\n\nFee Summary:\n• Total Course Fees: ₹28,500.00\n• Total Paid: ₹15,000.00\n• Remaining Balance: ₹13,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-23 12:06:44'),
(208, 37, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-23 12:07:06'),
(209, 34, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-23 12:45:20'),
(210, 38, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 1, '2026-01-23 12:54:09'),
(211, 10, 'Password Reset', 'Your student portal password has been reset by the administrator. Please use your new credentials to login.', 'warning', 0, '2026-01-24 06:53:08'),
(212, 36, 'Payment Received - Receipt #RCP202601277504', 'Dear Sudeep Goswami,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 27 Jan 2026\n• Payment Method: UPI\n• Receipt Number: RCP202601277504\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹45,000.00\n• Remaining Balance: ₹12,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-27 14:09:04'),
(213, 10, 'Payment Received - Receipt #RCP202601288992', 'Dear Badgujar Rushikesh Manojbhai,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,700.00\n• Payment Date: 28 Jan 2026\n• Payment Method: UPI\n• Receipt Number: RCP202601288992\n\nFee Summary:\n• Total Course Fees: ₹53,000.00\n• Total Paid: ₹36,450.00\n• Remaining Balance: ₹16,550.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-28 07:17:18');
INSERT INTO `student_notifications` (`id`, `student_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(214, 38, 'Payment Received - Receipt #RCP202601055236', 'Dear Manoj ramawat,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹2,000.00\n• Payment Date: 05 Jan 2026\n• Payment Method: UPI\n• Receipt Number: RCP202601055236\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹2,000.00\n• Remaining Balance: ₹13,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-29 08:41:38'),
(215, 41, 'Payment Received - Receipt #RCP202601292840', 'Dear Ghanchi Mansha,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹20,000.00\n• Payment Date: 15 Mar 2025\n• Payment Method: Cash\n• Receipt Number: RCP202601292840\n\nFee Summary:\n• Total Course Fees: ₹57,000.00\n• Total Paid: ₹20,000.00\n• Remaining Balance: ₹37,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-29 11:59:41'),
(216, 38, 'Payment Received - Receipt #RCP202601294205', 'Dear Manoj ramawat,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹3,000.00\n• Payment Date: 10 Jan 2026\n• Payment Method: UPI\n• Receipt Number: RCP202601294205\n\nFee Summary:\n• Total Course Fees: ₹15,500.00\n• Total Paid: ₹5,000.00\n• Remaining Balance: ₹10,500.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 1, '2026-01-29 14:13:33'),
(217, 12, 'Payment Received - Receipt #RCP202601313624', 'Dear Joshi Mansi Anandbhai Joshi,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 31 Jan 2026\n• Payment Method: Cash\n• Receipt Number: RCP202601313624\n\nFee Summary:\n• Total Course Fees: ₹53,000.00\n• Total Paid: ₹32,000.00\n• Remaining Balance: ₹21,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-31 12:23:37'),
(218, 12, 'Payment Received - Receipt #RCP202602017903', 'Dear Joshi Mansi Anandbhai Joshi,\n\nYour payment has been successfully received!\n\nPayment Details:\n• Amount Paid: ₹5,000.00\n• Payment Date: 01 Feb 2026\n• Payment Method: Cash\n• Receipt Number: RCP202602017903\n\nFee Summary:\n• Total Course Fees: ₹53,000.00\n• Total Paid: ₹37,000.00\n• Remaining Balance: ₹16,000.00\n\nPlease pay the remaining balance at your earliest convenience.\n\nYou can view your payment receipt in the Student Portal.\n\nThank you for your payment!', 'payment', 0, '2026-01-31 12:24:10'),
(219, 12, 'Welcome to Student Portal!', 'Your portal access has been activated. You can now login and view your payment history, receipts, and profile information.', 'success', 0, '2026-01-31 12:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `student_projects`
--

CREATE TABLE `student_projects` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `project_link` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Not Started','In Progress','Completed','On Hold') DEFAULT 'Not Started',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `student_projects`
--

INSERT INTO `student_projects` (`id`, `student_id`, `project_name`, `description`, `project_link`, `start_date`, `end_date`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(5, 28, 'NextAcademy', 'First Project', 'https://www.google.com', '2026-01-31', '2026-01-31', 'Completed', NULL, '2026-01-31 13:12:23', '2026-01-31 13:12:23');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_monthly_revenue`
-- (See below for the actual view)
--
CREATE TABLE `vw_monthly_revenue` (
`month` varchar(7)
,`payment_count` bigint(21)
,`total_revenue` decimal(32,2)
,`unique_students` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_student_payment_summary`
-- (See below for the actual view)
--
CREATE TABLE `vw_student_payment_summary` (
`id` int(11)
,`student_code` varchar(20)
,`full_name` varchar(100)
,`phone` varchar(20)
,`email` varchar(100)
,`course_name` varchar(150)
,`category_name` varchar(100)
,`total_fees` decimal(10,2)
,`total_paid` decimal(32,2)
,`pending_fees` decimal(33,2)
,`status` enum('Active','Completed','Dropped','Deleted')
,`enrollment_date` date
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`);

--
-- Indexes for table `admin_tasks`
--
ALTER TABLE `admin_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `status` (`status`),
  ADD KEY `next_due_date` (`next_due_date`);

--
-- Indexes for table `admin_task_history`
--
ALTER TABLE `admin_task_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `course_fees`
--
ALTER TABLE `course_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course_duration` (`course_id`,`duration_months`),
  ADD KEY `idx_course` (`course_id`);

--
-- Indexes for table `course_topics`
--
ALTER TABLE `course_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_source` (`source`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_assigned_to` (`assigned_to`),
  ADD KEY `fk_inquiries_created_by` (`created_by`);

--
-- Indexes for table `inquiry_notes`
--
ALTER TABLE `inquiry_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inquiry_id` (`inquiry_id`),
  ADD KEY `fk_notes_admin` (`created_by`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_admin_token` (`admin_id`,`token`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `idx_receipt` (`receipt_number`),
  ADD KEY `idx_payment_student_date` (`student_id`,`payment_date`),
  ADD KEY `fk_payments_admin` (`created_by`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_code` (`student_code`),
  ADD KEY `idx_student_code` (`student_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_student_status_date` (`status`,`enrollment_date`),
  ADD KEY `idx_inquiry_id` (`inquiry_id`);

--
-- Indexes for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`attendance_date`),
  ADD KEY `idx_date` (`attendance_date`),
  ADD KEY `idx_student_date` (`student_id`,`attendance_date`);

--
-- Indexes for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_unread` (`student_id`,`is_read`);

--
-- Indexes for table `student_projects`
--
ALTER TABLE `student_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admin_tasks`
--
ALTER TABLE `admin_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `admin_task_history`
--
ALTER TABLE `admin_task_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `course_fees`
--
ALTER TABLE `course_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `course_topics`
--
ALTER TABLE `course_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inquiry_notes`
--
ALTER TABLE `inquiry_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `student_attendance`
--
ALTER TABLE `student_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `student_notifications`
--
ALTER TABLE `student_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `student_projects`
--
ALTER TABLE `student_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- --------------------------------------------------------

--
-- Structure for view `vw_monthly_revenue`
--
DROP TABLE IF EXISTS `vw_monthly_revenue`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u946810828_Next_academy`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_monthly_revenue`  AS SELECT date_format(`p`.`payment_date`,'%Y-%m') AS `month`, count(0) AS `payment_count`, sum(`p`.`amount_paid`) AS `total_revenue`, count(distinct `p`.`student_id`) AS `unique_students` FROM `payments` AS `p` GROUP BY date_format(`p`.`payment_date`,'%Y-%m') ORDER BY date_format(`p`.`payment_date`,'%Y-%m') DESC ;

-- --------------------------------------------------------

--
-- Structure for view `vw_student_payment_summary`
--
DROP TABLE IF EXISTS `vw_student_payment_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u946810828_Next_academy`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_student_payment_summary`  AS SELECT `s`.`id` AS `id`, `s`.`student_code` AS `student_code`, `s`.`full_name` AS `full_name`, `s`.`phone` AS `phone`, `s`.`email` AS `email`, `c`.`name` AS `course_name`, `cat`.`name` AS `category_name`, `s`.`total_fees` AS `total_fees`, coalesce(sum(`p`.`amount_paid`),0) AS `total_paid`, `s`.`total_fees`- coalesce(sum(`p`.`amount_paid`),0) AS `pending_fees`, `s`.`status` AS `status`, `s`.`enrollment_date` AS `enrollment_date` FROM (((`students` `s` join `courses` `c` on(`s`.`course_id` = `c`.`id`)) join `categories` `cat` on(`s`.`category_id` = `cat`.`id`)) left join `payments` `p` on(`s`.`id` = `p`.`student_id`)) GROUP BY `s`.`id` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_courses_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_fees`
--
ALTER TABLE `course_fees`
  ADD CONSTRAINT `fk_course_fees` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_topics`
--
ALTER TABLE `course_topics`
  ADD CONSTRAINT `course_topics_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `fk_inquiries_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inquiries_created_by` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inquiry_notes`
--
ALTER TABLE `inquiry_notes`
  ADD CONSTRAINT `fk_notes_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notes_inquiry` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_resets_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `fk_payments_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_students_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD CONSTRAINT `student_attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_notifications`
--
ALTER TABLE `student_notifications`
  ADD CONSTRAINT `fk_notifications_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_projects`
--
ALTER TABLE `student_projects`
  ADD CONSTRAINT `student_projects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
