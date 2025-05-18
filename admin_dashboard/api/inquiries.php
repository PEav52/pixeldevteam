<?php
require_once 'functions.php';

try {
    // Log raw request body for debugging
    $rawInput = file_get_contents('php://input');
    error_log("Raw request body: " . $rawInput);

    $request = getRequestData();
    error_log("Parsed request: " . json_encode($request));

    $method = $request['method'] ?? '';
    $input = $request['input'] ?? [];
    $id = $request['id'] ?? null;

    // Log the method and input for debugging
    error_log("Received method: $method, Input: " . json_encode($input));

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT inquiry_id, name, email, service_id, project_type, message, created_at, status, responded_at FROM contact_inquiries WHERE inquiry_id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse($data ?: ['error' => 'Inquiry not found'], $data ? 200 : 404);
            } else {
                $stmt = $pdo->prepare("SELECT inquiry_id, name, email, service_id, project_type, message, created_at, status, responded_at FROM contact_inquiries ORDER BY created_at DESC");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendResponse($data, 200);
            }
            break;

        case 'POST':
            if ($id) {
                // Update existing inquiry status
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_inquiries WHERE inquiry_id = ?");
                $stmt->execute([$id]);
                if ($stmt->fetchColumn() == 0) {
                    sendResponse(['error' => 'Invalid inquiry ID'], 404);
                }

                $status = $input['status'] ?? '';
                $errors = validateInquiryStatus($status);
                if (!empty($errors)) {
                    sendResponse(['errors' => $errors], 400);
                }

                $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ? WHERE inquiry_id = ?");
                $success = $stmt->execute([$status, $id]);

                sendResponse(['success' => $success], $success ? 200 : 500);
            } else {
                // Insert new inquiry
                $name = $input['name'] ?? '';
                $email = $input['email'] ?? '';
                $project_type = $input['project_type'] ?? '';
                $message = $input['message'] ?? '';
                $service_id = $input['service_id'] ?? null;

                // Log input values for debugging
                error_log("Input values - Name: $name, Email: $email, Project Type: $project_type, Message: $message, Service ID: $service_id");

                // Validate inputs
                $errors = [];
                if (empty($name)) {
                    $errors[] = 'Name is required';
                }
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Valid email is required';
                }
                if (empty($project_type)) {
                    $errors[] = 'Project type is required';
                }
                if (empty($message)) {
                    $errors[] = 'Message is required';
                }

                if (!empty($errors)) {
                    error_log("Validation errors: " . json_encode($errors));
                    sendResponse(['errors' => $errors], 400);
                }

                // Insert into database
                $stmt = $pdo->prepare("
                    INSERT INTO contact_inquiries (name, email, service_id, project_type, message, created_at, status)
                    VALUES (?, ?, ?, ?, ?, NOW(), 'pending')
                ");
                $success = $stmt->execute([$name, $email, $service_id, $project_type, $message]);

                if ($success) {
                    $inquiry_id = $pdo->lastInsertId();
                    sendResponse(['success' => true, 'inquiry_id' => $inquiry_id], 201);
                } else {
                    sendResponse(['error' => 'Failed to insert inquiry'], 500);
                }
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