<?php
require_once 'functions.php';

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];
    $id = $request['id'];

    // Log the method and input for debugging
    error_log("Received method: $method, Input: " . json_encode($input));

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT testimonial_id, client_name, client_title, content, rating, image_url, project_id, is_visible, display_order FROM testimonials WHERE testimonial_id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse($data ?: ['error' => 'Testimonial not found'], $data ? 200 : 404);
            } else {
                $stmt = $pdo->prepare("SELECT testimonial_id, client_name, client_title, content, rating, image_url, project_id, is_visible, display_order FROM testimonials ORDER BY display_order ASC");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendResponse($data, 200);
            }
            break;

        case 'POST':
            if (!$id) {
                sendResponse(['error' => 'Testimonial ID is required'], 400);
            }

            // Validate testimonial exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM testimonials WHERE testimonial_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Invalid testimonial ID'], 404);
            }

            // Validate is_visible
            $is_visible = isset($input['is_visible']) ? $input['is_visible'] : null;
            if (!in_array($is_visible, ['0', '1'], true)) {
                sendResponse(['error' => 'Invalid visibility value. Must be 0 or 1'], 400);
            }

            // Update visibility
            $stmt = $pdo->prepare("UPDATE testimonials SET is_visible = ? WHERE testimonial_id = ?");
            $success = $stmt->execute([$is_visible, $id]);

            if ($success) {
                sendResponse(['success' => true, 'message' => 'Visibility updated successfully'], 200);
            } else {
                sendResponse(['error' => 'Failed to update visibility'], 500);
            }
            break;

        default:
            error_log("Method not allowed: $method");
            sendResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    error_log("Server error: " . $e->getMessage());
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}
?>