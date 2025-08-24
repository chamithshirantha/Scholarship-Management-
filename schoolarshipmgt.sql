-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 08:34 PM
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
-- Database: `schoolarshipmgt`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `scholarship_id` bigint(20) UNSIGNED NOT NULL,
  `personal_statement` text NOT NULL,
  `financial_information` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`financial_information`)),
  `academic_records` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`academic_records`)),
  `references` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`references`)),
  `status` enum('pending','under_review','approved','rejected') NOT NULL DEFAULT 'pending',
  `review_notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `user_id`, `scholarship_id`, `personal_statement`, `financial_information`, `academic_records`, `references`, `status`, `review_notes`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'I am a dedicated student with excellent academic records...', '{\"income\":20000,\"expenses\":18000}', '{\"gpa\":3.8,\"transcript\":\"excellent\"}', '[{\"name\":\"Dr. Smith\",\"email\":\"smith@example.com\",\"relationship\":\"Professor\"},{\"name\":\"Dr. john\",\"email\":\"john@example.com\",\"relationship\":\"Professor\"}]', 'approved', 'Excellent academic records and strong recommendation', 1, '2025-08-24 10:22:16', '2025-08-24 09:56:15', '2025-08-24 10:22:16'),
(2, 2, 3, 'I am a dedicated student with excellent academic records...', '{\"income\":20000,\"expenses\":18000}', '{\"gpa\":3.8,\"transcript\":\"excellent\"}', '[{\"name\":\"Dr. Smith\",\"email\":\"smith@example.com\",\"relationship\":\"Professor\"},{\"name\":\"Dr. john\",\"email\":\"john@example.com\",\"relationship\":\"Professor\"}]', 'pending', NULL, NULL, NULL, '2025-08-24 09:57:00', '2025-08-24 09:57:00');

-- --------------------------------------------------------

--
-- Table structure for table `application_logs`
--

CREATE TABLE `application_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `performed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `scholarship_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `disbursed_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('active','completed','cancelled') NOT NULL DEFAULT 'active',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `terms_and_conditions` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scholarship_id` bigint(20) UNSIGNED NOT NULL,
  `cost_category_id` bigint(20) UNSIGNED NOT NULL,
  `allocated_amount` decimal(10,2) NOT NULL,
  `utilized_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `scholarship_id`, `cost_category_id`, `allocated_amount`, `utilized_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3000.00, 0.00, '2025-08-24 11:56:47', '2025-08-24 11:56:47'),
(2, 1, 2, 1000.00, 0.00, '2025-08-24 11:56:47', '2025-08-24 11:56:47'),
(3, 1, 3, 1000.00, 0.00, '2025-08-24 11:56:47', '2025-08-24 11:56:47');

-- --------------------------------------------------------

--
-- Table structure for table `cost_categories`
--

CREATE TABLE `cost_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cost_categories`
--

INSERT INTO `cost_categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Tuition Fee', 'Covers tuition expenses', 1, '2025-08-24 10:42:09', '2025-08-24 10:42:09', NULL),
(2, 'Tuition', 'Tuition fees and academic charges', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(3, 'Books', 'Textbooks and study materials', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(4, 'Living Expenses', 'Accommodation and daily living costs', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(5, 'Transportation', 'Travel and transportation expenses', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(6, 'Equipment', 'Study equipment and tools', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(7, 'Research Materials', 'Research-related materials and resources', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(8, 'Thesis Expenses', 'Thesis or dissertation preparation costs', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(9, 'Health Insurance', 'Student health insurance coverage', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(10, 'Fees', 'Miscellaneous academic fees', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL),
(11, 'Other', 'Other educational expenses', 1, '2025-08-24 11:55:17', '2025-08-24 11:55:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `application_id`, `document_type`, `file_path`, `file_name`, `mime_type`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 1, 'transcript', 'documents/1/1756049571_sample-local-pdf.pdf', 'sample-local-pdf.pdf', 'application/pdf', 49672, '2025-08-24 10:02:51', '2025-08-24 10:02:51'),
(2, 2, 'transcript', 'documents/2/1756049831_sample.pdf', 'sample.pdf', 'application/pdf', 18810, '2025-08-24 10:07:11', '2025-08-24 10:07:11');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_08_24_074855_create_roles_table', 1),
(6, '2025_08_24_074936_add_role_id_to_users_table', 1),
(7, '2025_08_24_104804_create_scholarships_table', 1),
(9, '2025_08_24_104813_create_applications_table', 1),
(10, '2025_08_24_104813_create_documents_table', 1),
(11, '2025_08_24_104814_create_awards_table', 1),
(12, '2025_08_24_104813_create_application_logs_table', 2),
(13, '2025_08_24_160840_create_cost_categories_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '4030f8bdc2ff273b186daaefe5a5d0bf894e0930d6866ad40319a8b370bf58a0', '[\"*\"]', NULL, NULL, '2025-08-24 07:47:18', '2025-08-24 07:47:18'),
(2, 'App\\Models\\User', 2, 'auth_token', '80545136c56518dae98d6f72760e643e1528adbb98cc2f26bff6f2057f8d99c9', '[\"*\"]', NULL, NULL, '2025-08-24 07:47:30', '2025-08-24 07:47:30'),
(3, 'App\\Models\\User', 1, 'auth_token', '01be73d08657191741e5bfbe9e794e64577056633174c8c320c686e3e6a596f9', '[\"*\"]', NULL, NULL, '2025-08-24 07:47:42', '2025-08-24 07:47:42'),
(4, 'App\\Models\\User', 1, 'auth_token', '3371581b8c6b3f0daeab73f5f15041b26ec67c69c77ba0dbd815691ddf4709f1', '[\"*\"]', '2025-08-24 08:14:06', NULL, '2025-08-24 07:52:52', '2025-08-24 08:14:06'),
(5, 'App\\Models\\User', 1, 'auth_token', '15d8406af91328ee7aff11e141ba6dfc11ce4e1ce73d8cdf3fe7f2af6dbce987', '[\"*\"]', '2025-08-24 12:32:26', NULL, '2025-08-24 07:58:02', '2025-08-24 12:32:26'),
(6, 'App\\Models\\User', 2, 'auth_token', '930ea3eead9093c1d4c3e03971ba72d7b9afa0e63eb48f3f851334d6c3c37128', '[\"*\"]', '2025-08-24 08:31:40', NULL, '2025-08-24 08:31:21', '2025-08-24 08:31:40'),
(7, 'App\\Models\\User', 2, 'auth_token', '8894d12c196359e5c5309b7db3284377d1527e8958b7b24e9a56dccd2f941e1e', '[\"*\"]', '2025-08-24 12:36:30', NULL, '2025-08-24 08:36:42', '2025-08-24 12:36:30'),
(8, 'App\\Models\\User', 1, 'auth_token', '4fb6539bf4e12806d2aa8b03bf3033c605a39a5e72218e89d6b2987a2b38bf18', '[\"*\"]', '2025-08-24 12:30:13', NULL, '2025-08-24 10:15:33', '2025-08-24 12:30:13');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2025-08-24 07:46:48', '2025-08-24 07:46:48'),
(2, 'Student', '2025-08-24 07:46:48', '2025-08-24 07:46:48');

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `deadline` date NOT NULL,
  `eligibility_criteria` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `available_slots` int(11) NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`id`, `title`, `description`, `amount`, `deadline`, `eligibility_criteria`, `status`, `available_slots`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Robotic Scholarship 2025', 'Scholarship for students with excellent academic performance', 5000.00, '2025-11-15', 'Minimum GPA 3.8, demonstrated leadership skills', 'active', 10, 1, '2025-08-24 08:09:36', '2025-08-24 08:21:46', NULL),
(2, 'Engineering Scholarship 2024', 'Scholarship for students with excellent academic performance', 5000.00, '2025-11-15', 'Minimum GPA 3.8, demonstrated leadership skills', 'active', 10, 1, '2025-08-24 08:11:54', '2025-08-24 08:25:12', '2025-08-24 08:25:12'),
(3, 'Engineering Scholarship 2025', 'Scholarship for students with excellent academic performance', 5000.00, '2025-11-15', 'Minimum GPA 3.8, demonstrated leadership skills', 'active', 10, 1, '2025-08-24 08:26:18', '2025-08-24 08:26:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'Admin User', 'admin@example.com', NULL, '$2y$12$hJ0a5MEUq8SU7u2kOeO3RexXun3AW2uo2a4CvenB8ujzYdTyC5Js.', NULL, '2025-08-24 07:47:18', '2025-08-24 07:47:18', 1),
(2, 'John Student', 'student@example.com', NULL, '$2y$12$RgvPCFI./kPV376PFOZ7mOCmN9eWVwdIFyv94fhcqZ32jNg9qOPQS', NULL, '2025-08-24 07:47:30', '2025-08-24 07:47:30', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `applications_user_id_scholarship_id_unique` (`user_id`,`scholarship_id`),
  ADD KEY `applications_scholarship_id_foreign` (`scholarship_id`),
  ADD KEY `applications_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `application_logs`
--
ALTER TABLE `application_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `awards_application_id_foreign` (`application_id`),
  ADD KEY `awards_user_id_foreign` (`user_id`),
  ADD KEY `awards_scholarship_id_foreign` (`scholarship_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budgets_scholarship_id_foreign` (`scholarship_id`);

--
-- Indexes for table `cost_categories`
--
ALTER TABLE `cost_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cost_categories_name_unique` (`name`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_application_id_foreign` (`application_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scholarships_created_by_foreign` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `application_logs`
--
ALTER TABLE `application_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cost_categories`
--
ALTER TABLE `cost_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `applications_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`),
  ADD CONSTRAINT `applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `awards`
--
ALTER TABLE `awards`
  ADD CONSTRAINT `awards_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `awards_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`),
  ADD CONSTRAINT `awards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_scholarship_id_foreign` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`);

--
-- Constraints for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD CONSTRAINT `scholarships_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
