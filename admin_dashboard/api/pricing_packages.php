<?php
require_once 'functions.php';

// Start output buffering
ob_start();

$request = getRequestData();
$method = $request['method'];
$input = $request['input'];
$pricing_package_id = isset($_GET['id']) ? $_GET['id'] : null;

try {
    switch ($method) {
        case 'POST':
            // Create new pricing package
            $errors = validatePricingData($input);
            if (!empty($errors)) {
                ob_end_clean();
                sendResponse(['errors' => $errors], 400);
            }

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO pricing_package (service_id, title, price) VALUES (?, ?, ?)");
                $stmt->execute([
                    $input['service_id'],
                    $input['title'],
                    $input['price']
                ]);
                $pricingId = $pdo->lastInsertId();
                $pdo->commit();

                ob_end_clean();
                sendResponse(['message' => 'Pricing package created successfully', 'pricing_package_id' => $pricingId], 201);
            } catch (PDOException $e) {
                $pdo->rollBack();
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        case 'PUT':
            if (!$pricing_package_id) {
                error_log("pricing_packages.php: Missing Pricing Package ID for PUT");
                ob_end_clean();
                sendResponse(['error' => 'Pricing Package ID is required'], 400);
            }

            $errors = validatePricingData($input, true);
            if (!empty($errors)) {
                error_log("pricing_packages.php: Validation errors on PUT: " . print_r($errors, true));
                ob_end_clean();
                sendResponse(['errors' => $errors], 400);
            }

            try {
                $pdo->beginTransaction();
                $updateFields = [];
                $params = [];
                if (isset($input['title']) && $input['title'] !== '') {
                    $updateFields[] = 'title = ?';
                    $params[] = $input['title'];
                }
                if (array_key_exists('price', $input)) {
                    $updateFields[] = 'price = ?';
                    $params[] = $input['price'] === '' ? null : $input['price'];
                }
                if (empty($updateFields)) {
                    error_log("pricing_packages.php: No valid fields provided for update");
                    $pdo->rollBack();
                    ob_end_clean();
                    sendResponse(['error' => 'No valid fields provided for update'], 400);
                }

                $params[] = $pricing_package_id;
                $query = "UPDATE pricing_package SET " . implode(', ', $updateFields) . " WHERE pricing_package_id = ?";
                error_log("pricing_packages.php: Executing query: $query with params: " . print_r($params, true));
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);

                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    ob_end_clean();
                    sendResponse(['message' => 'Pricing package updated successfully']);
                } else {
                    $pdo->rollBack();
                    error_log("pricing_packages.php: Pricing not found or no changes made for ID: $feature_id");
                    ob_end_clean();
                    sendResponse(['error' => 'Pricing package not found or no changes made'], 404);
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("pricing_packages.php: Database error on PUT: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        case 'DELETE':
            if (!$pricing_package_id) {
                ob_end_clean();
                sendResponse(['error' => 'Pricing package ID is required'], 400);
            }

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("DELETE FROM pricing_package WHERE pricing_package_id = ?");
                $stmt->execute([$pricing_package_id]);

                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    ob_end_clean();
                    sendResponse(['message' => 'Pricing package deleted successfully']);
                } else {
                    $pdo->rollBack();
                    ob_end_clean();
                    sendResponse(['error' => 'Pricing package not found'], 404);
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        default:
            ob_end_clean();
            sendResponse(['error' => 'Method not allowed'], 405);
            break;
    }
} catch (Exception $e) {
    error_log('Unexpected error in pricing_packages.php: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    ob_end_clean();
    sendResponse(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
}
?>