-- --------------------------------------------------------
-- Aetheris Core v2.2 Database Schema
-- System: Portal-OS
-- Architect: Shiro_Onigami
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:30";

--
-- 1. THE USER NODES (Students)
--
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT 'New Student',
  `profile_pic` varchar(500) DEFAULT 'assets/profile.png',
  `branch` varchar(50) DEFAULT 'CSE',
  `batch_year` int(4) DEFAULT 2026,
  `role` enum('student','admin') DEFAULT 'student',
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 2. THE VAULT (Encrypted Storage Indices)
--
CREATE TABLE `vault_items` (
  `vault_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `doc_title` varchar(255) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `google_drive_link` text NOT NULL, -- Stores the GitHub Path
  `uploaded_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`vault_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 3. NEURAL LINK (Direct Messages)
--
CREATE TABLE `direct_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_seen` tinyint(1) DEFAULT 0,
  `sent_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 4. BOUNTY BOARD (Crowdsourced Intelligence)
--
CREATE TABLE `resource_bounties` (
  `bounty_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `request_title` varchar(255) NOT NULL,
  `request_description` text NOT NULL,
  `status` enum('open','fulfilled') DEFAULT 'open',
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`bounty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 5. SIGNALING SERVER (P2P Call Handshakes)
--
CREATE TABLE `call_signals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  `payload` text NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 6. INFILTRATION LOGS (Profile Visits)
--
CREATE TABLE `profile_visits` (
  `visit_id` int(11) NOT NULL AUTO_INCREMENT,
  `visitor_id` int(11) NOT NULL,
  `profile_owner_id` int(11) NOT NULL,
  `visit_time` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`visit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 7. PROFESSIONAL IDENTITY (Resume Data)
--
CREATE TABLE `student_resume_data` (
  `resume_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `professional_summary` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`resume_id`),
  UNIQUE KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 8. CAMPUS BROADCASTS (Posts)
--
CREATE TABLE `campus_posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `media_url` varchar(500) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

--- Import this file in mysql --- 
