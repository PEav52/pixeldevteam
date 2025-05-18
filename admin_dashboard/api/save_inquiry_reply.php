<?php
require_once 'functions.php';

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];

    if ($method !== 'POST') {
        sendResponse(['error' => 'Method not allowed'], 405);
    }

    // Validate input data
    $required_fields = ['inquiry_id', 'subject', 'message', 'recipient_email'];
    $errors = [];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            $errors[] = "$field is required";
        }
    }

    if (!is_numeric($input['inquiry_id'])) {
        $errors[] = "Invalid inquiry ID";
    }

    if (!filter_var($input['recipient_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid recipient email";
    }

    if (!empty($errors)) {
        sendResponse(['errors' => $errors], 400);
    }

    // Validate inquiry exists
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_inquiries WHERE inquiry_id = ?");
    $stmt->execute([$input['inquiry_id']]);
    if ($stmt->fetchColumn() == 0) {
        sendResponse(['error' => 'Invalid inquiry ID'], 404);
    }

    // Handle PDF attachment
    $pdf_path = null;
    $uploadResult = handlePdfUpload('quote_pdf');
    if (!empty($uploadResult['error'])) {
        sendResponse(['error' => $uploadResult['error']], 400);
    }
    if ($uploadResult['success']) {
        $pdf_path = 'api/'.$uploadResult['path'];
    }

    // Insert reply into database
    $stmt = $pdo->prepare("
        INSERT INTO inquiry_replies (
            inquiry_id, subject, message, pdf_attachment, sender_name, sender_email, sent_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $currentDateTime = date('Y-m-d H:i:s');
    $success = $stmt->execute([
        $input['inquiry_id'],
        $input['subject'],
        $input['message'],
        $pdf_path,
        'Pixel IT Solution',
        'peavppppkh19@gmail.com',
        $currentDateTime
    ]);

    if ($success) {
        sendResponse(['success' => true, 'message' => 'Reply saved successfully'], 200);
    } else {
        sendResponse(['error' => 'Failed to save reply'], 500);
    }
} catch (Exception $e) {
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}