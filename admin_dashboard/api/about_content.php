<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];

    switch ($method) {
        case 'GET':
            $stmt = $pdo->prepare("SELECT * FROM about_content LIMIT 1");
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            sendResponse($data ?: ['error' => 'No content found'], $data ? 200 : 404);
            break;

        case 'POST':
        case 'PUT':
            $aboutId = $input['about_id'] ?? null;
            if (!$aboutId) {
                sendResponse(['error' => 'about_id is required'], 400);
            }

            // Validate about_id exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM about_content WHERE about_id = ?");
            $stmt->execute([$aboutId]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Invalid about_id'], 404);
            }

            // Validate description
            $description = $input['description'] ?? '';
            if (empty($description)) {
                sendResponse(['error' => 'Description is required'], 400);
            }

            // Get current image for deletion
            $stmt = $pdo->prepare("SELECT image_url FROM about_content WHERE about_id = ?");
            $stmt->execute([$aboutId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            $oldImagePath = isset($existing['image_url']) ? basename($existing['image_url']) : null;

            // Handle image upload
            $uploadResult = handleImageUpload('image');
            if (!empty($uploadResult['error'])) {
                sendResponse(['error' => $uploadResult['error']], 400);
            }

            $imageUrl = $uploadResult['success'] ? $uploadResult['url'] : ($existing['image_url'] ?? '');

            // Delete old image if new uploaded
            if ($uploadResult['success'] && $oldImagePath && file_exists("Uploads/$oldImagePath")) {
                unlink("Uploads/$oldImagePath");
            }

            // Update database
            $stmt = $pdo->prepare("UPDATE about_content SET description = :description, image_url = :image_url WHERE about_id = :about_id");
            $success = $stmt->execute([
                ':description' => $description,
                ':image_url' => $imageUrl,
                ':about_id' => $aboutId
            ]);

            sendResponse(['success' => $success], $success ? 200 : 500);
            break;

        default:
            sendResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}