<?php
require_once 'functions.php';

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];

    if ($method !== 'GET') {
        sendResponse(['error' => 'Method not allowed'], 405);
    }

    $inquiry_id = isset($_GET['inquiry_id']) ? (int)$_GET['inquiry_id'] : null;
    if (!$inquiry_id) {
        sendResponse(['error' => 'Inquiry ID is required'], 400);
    }

    global $pdo;
    $stmt = $pdo->prepare("
        SELECT reply_id, inquiry_id, subject, message, pdf_attachment, sent_at
        FROM inquiry_replies
        WHERE inquiry_id = ?
        ORDER BY sent_at DESC
    ");
    $stmt->execute([$inquiry_id]);
    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendResponse($replies, 200);
} catch (Exception $e) {
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}