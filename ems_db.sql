-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 02:01 PM
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
-- Database: `ems_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendees`
--

CREATE TABLE `attendees` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(10) UNSIGNED NOT NULL,
  `attendee_name` varchar(255) NOT NULL,
  `attendee_email` varchar(255) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `registration_type` enum('self','other') NOT NULL DEFAULT 'self',
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendees`
--

INSERT INTO `attendees` (`id`, `event_id`, `attendee_name`, `attendee_email`, `registered_at`, `registration_type`, `user_id`) VALUES
(1, 1, 'Admin User', 'admin@example.com', '2025-02-01 02:02:41', 'self', 1),
(3, 2, 'Alice Brown', 'alice.brown@example.com', '2025-02-01 02:02:41', 'self', 3),
(4, 3, 'Bayan Doe', 'doe@example.com', '2025-02-01 02:02:41', 'self', 2),
(5, 3, 'Sarah Jones', 'sarah.jones@example.com', '2025-02-01 02:02:41', 'self', 3),
(6, 7, 'Admin User', 'admin@example.com', '2025-02-01 16:21:17', 'self', 1);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `max_capacity` int(10) UNSIGNED NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `slug`, `description`, `max_capacity`, `created_by`, `start_datetime`, `end_datetime`, `created_at`, `updated_at`) VALUES
(1, 'Company Picnic', 'company-picnic', 'Annual company picnic at the park.', 15, 1, '2024-07-04 10:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(2, 'Team Building Workshop', 'team-building-workshop', 'Team building workshop for the marketing department.', 10, 2, '2024-07-12 14:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(3, 'Product Launch', 'product-launch', 'Launch of the new product line.', 5, 1, '2024-08-01 09:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(4, 'Tech Conference 2024', 'tech-conference-2024', 'Annual tech conference featuring industry leaders.', 20, 1, '2024-09-10 09:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 09:54:35'),
(5, 'Employee Appreciation Day', 'employee-appreciation-day', 'Celebration of employee achievements and contributions.', 3, 3, '2024-09-15 12:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(6, 'Sales Training', 'sales-training', 'Sales training for new hires and team members.', 28, 2, '2024-10-05 10:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(7, 'Holiday Party', 'holiday-party', 'End of year holiday celebration for all employees.', 50, 1, '2024-12-20 18:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(8, 'Marketing Seminar', 'marketing-seminar', 'Seminar on the latest marketing strategies and trends.', 10, 2, '2024-11-01 09:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(9, 'Networking Event', 'networking-event', 'Networking event for professionals in the tech industry.', 15, 3, '2024-08-18 16:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(10, 'Leadership Summit', 'leadership-summit', 'Leadership summit for senior executives and managers.', 50, 1, '2024-09-25 08:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29'),
(11, 'Innovation Workshop', 'innovation-workshop', 'Workshop to foster innovation within the company.', 5, 2, '2024-10-20 09:00:00', NULL, '2025-02-01 01:58:29', '2025-02-01 01:58:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@example.com', '$2y$10$uKLZrwVQ6dYAl1IyRwzSG.8vhypRo6DGvbfJtbnFWlZwMdJiSA9Fa', 'admin', '2025-01-31 19:24:29', '2025-01-31 19:27:30'),
(2, 'Bayan Doe', 'doe@example.com', '$2y$10$PdyE1OTo64cHf0C9WCY3wuS7AfXkYyPleLNAkiXvIH/6zXsC3DvSC', 'user', '2025-01-31 19:24:29', '2025-01-31 19:36:56'),
(3, 'David Lee', 'david@example.com', '$2y$10$umDB77/5uWyfdW7v7daSBOeKLJDfGVU1uqqJEgBm5AybZQwK7G0Tm', 'user', '2025-01-31 19:24:29', '2025-01-31 19:38:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendees`
--
ALTER TABLE `attendees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `unique_event_slug_datetime` (`slug`,`start_datetime`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendees`
--
ALTER TABLE `attendees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendees`
--
ALTER TABLE `attendees`
  ADD CONSTRAINT `attendees_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
