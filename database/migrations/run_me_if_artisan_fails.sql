-- Run this SQL via phpMyAdmin / HeidiSQL if `php artisan migrate` fails

CREATE TABLE IF NOT EXISTS `video_challenges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `video_challenges_created_by_foreign` (`created_by`),
  CONSTRAINT `video_challenges_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `video_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `challenge_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `link` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `video_submissions_challenge_id_user_id_unique` (`challenge_id`,`user_id`),
  KEY `video_submissions_challenge_id_foreign` (`challenge_id`),
  KEY `video_submissions_user_id_foreign` (`user_id`),
  CONSTRAINT `video_submissions_challenge_id_foreign` FOREIGN KEY (`challenge_id`) REFERENCES `video_challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `video_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
