CREATE DATABASE IF NOT EXISTS vnt_aura_db;
USE vnt_aura_db;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `needs_password_change` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed default admin user (password123 - bcrypt hash)
-- Administrator must change this on first login
INSERT IGNORE INTO `users` (`username`, `password_hash`, `needs_password_change`)
VALUES ('vnt_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('hero_video_type', 'url'),
('hero_video_url', 'https://www.w3schools.com/html/mov_bbb.mp4'),
('hero_video_upload', ''),
('hero_poster', 'assets/images/placeholder-poster.jpg'),
('seo_title', 'VNT Aura Skin & Wellness'),
('seo_description', 'Personalised skin consultations and evidence-based skin treatments designed to improve skin health.');

CREATE TABLE IF NOT EXISTS `treatments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255),
  `sort_order` INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `sort_order` INT DEFAULT 0
);
