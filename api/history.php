<?php
/**
 * Recent processing history API
 * Copyright (c) 2026 Remove Background System. All rights reserved.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

try {
    $db = getDbConnection();
    $result = $db->query(
        "SELECT id, original_name, original_path, processed_path, status, created_at
         FROM processed_images
         WHERE status = 'completed' AND processed_path IS NOT NULL
         ORDER BY created_at DESC
         LIMIT 12"
    );

    $items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }

    echo json_encode(['success' => true, 'data' => $items]);
} catch (Throwable $e) {
    echo json_encode(['success' => true, 'data' => [], 'message' => 'History unavailable until the database is set up.']);
}
