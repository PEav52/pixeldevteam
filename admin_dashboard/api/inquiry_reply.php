<?php
require_once 'functions.php';
require 'vendor/autoload.php'; // Composer autoload for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];

    if ($method !== 'POST') {
        sendResponse(['error' => 'Method not allowed'], 405);
    }

    // Validate input data
    $errors = validateInquiryReplyData($input);
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

    // Brevo SMTP configuration
    $smtp_host = 'smtp-relay.brevo.com';
    $smtp_port = 587;
    $smtp_username = '8bdc87002@smtp-brevo.com'; // From Brevo dashboard
    $smtp_password = 'bPzwRxvGBsNUXIDt'; // From Brevo dashboard
    $from_email = 'peavppppkh19@gmail.com'; // Verified sender email in Brevo
    $from_name = 'Pixel IT Solution';

    // Initialize PHPMailer
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2; // Enable verbose debug output (set to 0 in production)
    $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer Debug level $level: $str");
    };
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp_port;

    // Set email details
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($input['recipient_email']);
    $mail->Subject = $input['subject'];
    $mail->Body = nl2br($input['message']);
    $mail->AltBody = strip_tags($input['message']);
    $mail->isHTML(true);

    // Handle PDF attachment
    $uploadResult = handlePdfUpload('quote_pdf');
    if (!empty($uploadResult['error'])) {
        sendResponse(['error' => $uploadResult['error']], 400);
    }

    if ($uploadResult['success']) {
        $mail->addAttachment($uploadResult['path'], $uploadResult['filename']);
    }

    // Current date and time
    $currentDateTime = date('Y-m-d H:i:s');
    // Send email
    if ($mail->send()) {
        // Update inquiry status to responded
        $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ?, responded_at = ? WHERE inquiry_id = ?");
        $success = $stmt->execute(['responded', $currentDateTime, $input['inquiry_id']]);

        // Delete uploaded PDF to save space
        if ($uploadResult['success'] && file_exists($uploadResult['path'])) {
            unlink($uploadResult['path']);
        }

        sendResponse(['success' => true, 'message' => 'Reply sent successfully'], 200);
    } else {
        sendResponse(['error' => 'Failed to send reply: ' . $mail->ErrorInfo], 500);
    }
} catch (Exception $e) {
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}