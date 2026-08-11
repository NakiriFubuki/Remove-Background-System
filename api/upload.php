<?php
/**
 * Upload original image API
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

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please select a valid image file.']);
        exit;
    }

    $file = $_FILES['image'];

    if ($file['size'] > MAX_FILE_SIZE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'File is too large. Maximum size is 10 MB.']);
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($mime, ALLOWED_TYPES, true) || !in_array($ext, ALLOWED_EXTENSIONS, true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, and WEBP images are allowed.']);
        exit;
    }

    $safeName = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $destination = UPLOAD_DIR . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
        exit;
    }

    $recordId = null;
    try {
        $db = getDbConnection();
        $stmt = $db->prepare(
            'INSERT INTO processed_images (original_name, original_path, file_size, status) VALUES (?, ?, ?, ?)'
        );
        $status = 'uploaded';
        $relPath = 'uploads/' . $safeName;
        $size = (int) $file['size'];
        $originalName = basename($file['name']);
        $stmt->bind_param('ssis', $originalName, $relPath, $size, $status);
        $stmt->execute();
        $recordId = (int) $stmt->insert_id;
        $stmt->close();
    } catch (Throwable $e) {
        // Continue without DB if not set up yet
        $recordId = null;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully.',
        'data' => [
            'id' => $recordId,
            'original_name' => basename($file['name']),
            'path' => 'uploads/' . $safeName,
            'url' => 'uploads/' . $safeName,
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Upload failed. Please try again.']);
}
