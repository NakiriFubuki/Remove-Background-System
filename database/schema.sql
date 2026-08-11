-- Remove Background System database schema
CREATE DATABASE IF NOT EXISTS remove_bg_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE remove_bg_system;

CREATE TABLE IF NOT EXISTS processed_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  original_name VARCHAR(255) NOT NULL,
  original_path VARCHAR(500) NOT NULL,
  processed_path VARCHAR(500) DEFAULT NULL,
  file_size INT UNSIGNED DEFAULT 0,
  status ENUM('uploaded', 'processing', 'completed', 'failed') NOT NULL DEFAULT 'uploaded',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
