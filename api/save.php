<?php
/**
 * Save processed (background-removed) image API
 * Copyright (c) 2026 Remove Background System. All rights reserved.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/db.php';

try {
    ensureUploadDirs();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit;
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $imageData = $_POST['image_data'] ?? '';

    if ($imageData === '' || !preg_match('/^data:image\/png;base64,/', $imageData)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid processed image data.']);
        exit;
    }

    $base64 = preg_replace('/^data:image\/png;base64,/', '', $imageData);
    $binary = base64_decode($base64, true);

    if ($binary === false) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Could not decode image data.']);
        exit;
    }

    $fileName = 'processed_' . bin2hex(random_bytes(8)) . '_' . time() . '.png';
    $destination = PROCESSED_DIR . $fileName;
    $relPath = 'processed/' . $fileName;

    if (file_put_contents($destination, $binary) === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save processed image.']);
        exit;
    }

    if ($id > 0) {
        try {
            $db = getDbConnection();
            $stmt = $db->prepare(
                'UPDATE processed_images SET processed_path = ?, status = ? WHERE id = ?'
            );
            $status = 'completed';
            $stmt->bind_param('ssi', $relPath, $status, $id);
            $stmt->execute();
            $stmt->close();
        } catch (Throwable $e) {
            // Ignore DB errors if schema not imported yet
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Processed image saved.',
        'data' => [
            'path' => $relPath,
            'url' => $relPath,
            'download_name' => 'no-background-' . date('Ymd-His') . '.png',
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Save failed. Please try again.']);
}
