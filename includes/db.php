<?php
/**
 * Database connection helper
 * Copyright (c) 2026 Remove Background System. All rights reserved.
 */

require_once __DIR__ . '/config.php';

function getDbConnection(): mysqli
{
    static $conn = null;

    if ($conn instanceof mysqli) {
        return $conn;
    }

    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        throw new RuntimeException('Database connection failed: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

function ensureUploadDirs(): void
{
    foreach ([UPLOAD_DIR, PROCESSED_DIR] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
