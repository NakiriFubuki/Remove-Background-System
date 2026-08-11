<?php
/**
 * Remove Background System - Configuration
 * Copyright (c) 2026 Remove Background System. All rights reserved.
 */

define('APP_NAME', 'Remove Background System');
define('APP_VERSION', '1.0.0');
define('COPYRIGHT_YEAR', '2026');
define('COPYRIGHT_HOLDER', 'Eng Choon Hao');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'remove_bg_system');

define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
define('PROCESSED_DIR', BASE_PATH . '/processed/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

date_default_timezone_set('Asia/Kuala_Lumpur');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
