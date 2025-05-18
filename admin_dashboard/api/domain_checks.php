<?php
require_once 'functions.php';

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];
    $id = $request['id'];

    switch ($method) {
        case 'GET':
            if ($id) {
                // Get specific domain check
                $stmt = $pdo->prepare("
                    SELECT check_id, domain, 
                    CASE 
                        WHEN available IS NULL THEN 'Null'
                        WHEN available = 1 THEN 'Available'
                        WHEN available = 0 THEN 'Unavailable'
                    END as availability_status,
                    checked_at, error_message 
                    FROM domain_checks 
                    WHERE check_id = ?
                ");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse($data ?: ['error' => 'Domain check not found'], $data ? 200 : 404);
            } else {
                // Get all domain checks with analytics
                $stmt = $pdo->prepare("
                    SELECT check_id, domain, 
                    CASE 
                        WHEN available IS NULL THEN 'Null'
                        WHEN available = 1 THEN 'Available'
                        WHEN available = 0 THEN 'Unavailable'
                    END as availability_status,
                    checked_at, error_message 
                    FROM domain_checks 
                    ORDER BY checked_at DESC
                ");
                $stmt->execute();
                $checks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Get analytics
                $analytics = [
                    'total_checks' => count($checks),
                    'available_domains' => 0,
                    'unavailable_domains' => 0,
                    'null_status_domains' => 0,
                    'most_checked_tld' => '',
                    'top_tlds' => []
                ];

                // Calculate analytics
                $tldCounts = [];
                foreach ($checks as $check) {
                    // Count domains by availability status
                    if ($check['availability_status'] === 'Available') {
                        $analytics['available_domains']++;
                    } elseif ($check['availability_status'] === 'Unavailable') {
                        $analytics['unavailable_domains']++;
                    } elseif ($check['availability_status'] === 'Null') {
                        $analytics['null_status_domains']++;
                    }

                    // Extract TLD from domain
                    $parts = explode('.', $check['domain']);
                    if (count($parts) > 1) {
                        $tld = end($parts);
                        $tldCounts[$tld] = ($tldCounts[$tld] ?? 0) + 1;
                    }
                }

                // Sort TLDs by count
                arsort($tldCounts);
                $analytics['most_checked_tld'] = key($tldCounts) ?? '-';
                
                // Get top 5 TLDs
                $analytics['top_tlds'] = array_map(
                    function($tld, $count) {
                        return ['tld' => $tld, 'count' => $count];
                    },
                    array_slice(array_keys($tldCounts), 0, 5),
                    array_slice(array_values($tldCounts), 0, 5)
                );

                sendResponse([
                    'checks' => $checks,
                    'analytics' => $analytics
                ], 200);
            }
            break;

        case 'POST':
            // Insert new domain check
            $stmt = $pdo->prepare("
                INSERT INTO domain_checks (domain, available, error_message, checked_at)
                VALUES (?, ?, ?, NOW())
            ");
            $success = $stmt->execute([
                $input['domain'] ?? null,
                $input['available'] ?? null,
                $input['error_message'] ?? null
            ]);
    
            if ($success) {
                sendResponse(['success' => true, 'message' => 'Domain check recorded successfully'], 201);
            } else {
                sendResponse(['error' => 'Failed to record domain check'], 500);
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