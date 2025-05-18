<?php
require_once 'functions.php';

// Function to validate URLs
function isValidUrl($url, $domain) {
    if (empty($url)) return true; // Allow empty URLs
    return preg_match("/^https?:\/\/(www\.)?$domain\.com\/.*/i", $url);
}

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];
    $id = $request['id'];

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse($data ?: ['error' => 'Team member not found'], $data ? 200 : 404);
            } else {
                $stmt = $pdo->query("SELECT * FROM team_members ORDER BY id DESC");
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendResponse($data, 200);
            }
            break;

        case 'POST':
            $full_name = !empty($input['full_name']) ? trim($input['full_name']) : '';
            $role = !empty($input['role']) ? trim($input['role']) : '';
            $description = !empty($input['description']) ? trim($input['description']) : '';
            $linkedin = !empty($input['linkedin']) ? trim($input['linkedin']) : null;
            $github = !empty($input['github']) ? trim($input['github']) : null;
            $image_url = null;

            // Validate required fields
            if (empty($full_name) || empty($role) || empty($description)) {
                sendResponse(['error' => 'Full name, role, and description are required'], 400);
            }

            // Validate URLs
            if ($linkedin && !isValidUrl($linkedin, 'linkedin')) {
                sendResponse(['error' => 'Invalid LinkedIn URL'], 400);
            }
            if ($github && !isValidUrl($github, 'github')) {
                sendResponse(['error' => 'Invalid GitHub URL'], 400);
            }

            // Handle image upload if present
            $uploadResult = handleImageUpload('profile_image');
            if (!empty($uploadResult['error'])) {
                sendResponse(['error' => $uploadResult['error']], 400);
            }
            if ($uploadResult['success']) {
                $image_url = $uploadResult['url'];
            }

            $stmt = $pdo->prepare("INSERT INTO team_members (full_name, role, description, linkedin, github, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            $success = $stmt->execute([$full_name, $role, $description, $linkedin, $github, $image_url]);
            sendResponse(['success' => $success, 'id' => $pdo->lastInsertId()], $success ? 201 : 500);
            break;

        case 'PUT':
            if (!$id) {
                sendResponse(['error' => 'ID is required'], 400);
            }

            // Get current member data
            $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$existing) {
                sendResponse(['error' => 'Team member not found'], 404);
            }

            // Handle FormData input
            $full_name = !empty($_POST['full_name']) ? trim($_POST['full_name']) : '';
            $role = !empty($_POST['role']) ? trim($_POST['role']) : '';
            $description = !empty($_POST['description']) ? trim($_POST['description']) : '';
            $linkedin = !empty($_POST['linkedin']) ? trim($_POST['linkedin']) : null;
            $github = !empty($_POST['github']) ? trim($_POST['github']) : null;
            $image_url = $existing['image_url'];

            // Validate required fields
            if (empty($full_name) || empty($role) || empty($description)) {
                sendResponse(['error' => 'Full name, role, and description are required'], 400);
            }

            // Validate URLs
            if ($linkedin && !isValidUrl($linkedin, 'linkedin')) {
                sendResponse(['error' => 'Invalid LinkedIn URL'], 400);
            }
            if ($github && !isValidUrl($github, 'github')) {
                sendResponse(['error' => 'Invalid GitHub URL'], 400);
            }

            // Handle image upload if present
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) {
                $uploadResult = handleImageUpload('profile_image');
                if (!empty($uploadResult['error'])) {
                    sendResponse(['error' => $uploadResult['error']], 400);
                }
                if ($uploadResult['success']) {
                    $image_url = $uploadResult['url'];
                    // Delete old image if exists
                    if ($existing['image_url'] && file_exists($existing['image_url'])) {
                        unlink($existing['image_url']);
                    }
                }
            }

            // Update the team member
            $stmt = $pdo->prepare("UPDATE team_members SET full_name = ?, role = ?, description = ?, linkedin = ?, github = ?, image_url = ? WHERE id = ?");
            $success = $stmt->execute([$full_name, $role, $description, $linkedin, $github, $image_url, $id]);

            if ($success) {
                $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
                $stmt->execute([$id]);
                $updatedMember = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse(['success' => true, 'data' => $updatedMember], 200);
            } else {
                sendResponse(['error' => 'Failed to update team member'], 500);
            }
            break;

        case 'DELETE':
            if (!$id) {
                sendResponse(['error' => 'ID is required'], 400);
            }

            // Delete image file
            $stmt = $pdo->prepare("SELECT image_url FROM team_members WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing && $existing['image_url'] && file_exists($existing['image_url'])) {
                unlink($existing['image_url']);
            }

            $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
            $success = $stmt->execute([$id]);
            sendResponse(['success' => $success], $success ? 200 : 500);
            break;

        default:
            sendResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    sendResponse(['error' => 'An unexpected error occurred'], 500);
}
?>