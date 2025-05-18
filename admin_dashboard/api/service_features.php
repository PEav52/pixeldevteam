<?php
require_once 'functions.php';

// Start output buffering
ob_start();

$request = getRequestData();
$method = $request['method'];
$input = $request['input'];
$feature_id = isset($_GET['id']) ? $_GET['id'] : null;

// Log input for debugging
error_log("service_feature.php: Input for method $method: " . print_r($input, true));

try {
    switch ($method) {
        case 'POST':
            // Create new feature
            $errors = validateFeatureData($input);
            if (!empty($errors)) {
                error_log("service_feature.php: Validation errors: " . print_r($errors, true));
                ob_end_clean();
                sendResponse(['errors' => $errors], 400);
            }

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO feature (service_id, name, description, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $input['service_id'],
                    $input['name'],
                    $input['description'] ?? null,
                    isset($input['is_active']) ? (int)$input['is_active'] : 1
                ]);
                $featureId = $pdo->lastInsertId();
                $pdo->commit();

                ob_end_clean();
                sendResponse(['message' => 'Feature created successfully', 'feature_id' => $featureId], 201);
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("service_feature.php: Database error on POST: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        case 'PUT':
            if (!$feature_id) {
                error_log("service_feature.php: Missing feature ID for PUT");
                ob_end_clean();
                sendResponse(['error' => 'Feature ID is required'], 400);
            }

            $errors = validateFeatureData($input, true);
            if (!empty($errors)) {
                error_log("service_feature.php: Validation errors on PUT: " . print_r($errors, true));
                ob_end_clean();
                sendResponse(['errors' => $errors], 400);
            }

            try {
                $pdo->beginTransaction();
                $updateFields = [];
                $params = [];
                if (isset($input['name']) && $input['name'] !== '') {
                    $updateFields[] = 'name = ?';
                    $params[] = $input['name'];
                }
                if (array_key_exists('description', $input)) {
                    $updateFields[] = 'description = ?';
                    $params[] = $input['description'] === '' ? null : $input['description'];
                }
                if (isset($input['is_active']) && in_array($input['is_active'], ['0', '1'])) {
                    $updateFields[] = 'is_active = ?';
                    $params[] = (int)$input['is_active'];
                }
                if (empty($updateFields)) {
                    error_log("service_feature.php: No valid fields provided for update");
                    $pdo->rollBack();
                    ob_end_clean();
                    sendResponse(['error' => 'No valid fields provided for update'], 400);
                }
                $params[] = $feature_id;
                $query = "UPDATE feature SET " . implode(', ', $updateFields) . " WHERE id = ?";
                error_log("service_feature.php: Executing query: $query with params: " . print_r($params, true));
                $stmt = $pdo->prepare($query);
                $stmt->execute($params);

                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    ob_end_clean();
                    sendResponse(['message' => 'Feature updated successfully']);
                } else {
                    $pdo->rollBack();
                    error_log("service_feature.php: Feature not found or no changes made for ID: $feature_id");
                    ob_end_clean();
                    sendResponse(['error' => 'Feature not found or no changes made'], 404);
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("service_feature.php: Database error on PUT: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        case 'DELETE':
            if (!$feature_id) {
                error_log("service_feature.php: Missing feature ID for DELETE");
                ob_end_clean();
                sendResponse(['error' => 'Feature ID is required'], 400);
            }

            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("DELETE FROM feature WHERE id = ?");
                $stmt->execute([$feature_id]);

                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    ob_end_clean();
                    sendResponse(['message' => 'Feature deleted successfully']);
                } else {
                    $pdo->rollBack();
                    error_log("service_feature.php: Feature not found for DELETE ID: $feature_id");
                    ob_end_clean();
                    sendResponse(['error' => 'Feature not found'], 404);
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("service_feature.php: Database error on DELETE: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        default:
            error_log("service_feature.php: Method not allowed: $method");
            ob_end_clean();
            sendResponse(['error' => 'Method not allowed'], 405);
            break;
    }
} catch (Exception $e) {
    error_log('service_feature.php: Unexpected error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    ob_end_clean();
    sendResponse(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
}
?>