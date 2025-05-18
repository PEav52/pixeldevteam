<?php
require_once 'functions.php';

// Start output buffering to capture any unintended output
ob_start();

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log'); // Ensure this path is writable

$request = getRequestData();
$method = $request['method'];
$input = $request['input'];
$id = $request['id'];

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
                $stmt->execute([$id]);
                $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($service) {
                    $featuresStmt = $pdo->prepare("SELECT * FROM feature WHERE service_id = ?");
                    $featuresStmt->execute([$id]);
                    $features = $featuresStmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $pricingStmt = $pdo->prepare("SELECT * FROM pricing_package WHERE service_id = ?");
                    $pricingStmt->execute([$id]);
                    $pricing_packages = $pricingStmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $service['features'] = $features;
                    $service['pricing_packages'] = $pricing_packages;
        
                    ob_end_clean();
                    sendResponse($service);
                } else {
                    ob_end_clean();
                    sendResponse(['error' => 'Service not found'], 404);
                }
            } else {
                $stmt = $pdo->query("SELECT * FROM services");
                $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                // Fetch features and pricing packages for each service
                foreach ($services as &$service) {
                    $serviceId = $service['service_id'];
                    $featuresStmt = $pdo->prepare("SELECT * FROM feature WHERE service_id = ?");
                    $featuresStmt->execute([$serviceId]);
                    $service['features'] = $featuresStmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $pricingStmt = $pdo->prepare("SELECT * FROM pricing_package WHERE service_id = ?");
                    $pricingStmt->execute([$serviceId]);
                    $service['pricing_packages'] = $pricingStmt->fetchAll(PDO::FETCH_ASSOC);
                }
                unset($service); // Unset reference to avoid issues
        
                ob_end_clean();
                sendResponse($services);
            }
            break;

        case 'POST':
            // Create new service
            $errors = validateServiceData($input);
            if (!empty($errors)) {
                error_log("POST Validation errors: " . json_encode($errors));
                ob_end_clean();
                sendResponse(['errors' => $errors], 400);
            }

            // Handle image upload if present
            $imageUrl = null;
            if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = handleImageUpload('image_url');
                if (isset($uploadResult['url'])) {
                    $imageUrl = $uploadResult['url'];
                } else {
                    error_log("Image upload error: " . $uploadResult['error']);
                    ob_end_clean();
                    sendResponse(['error' => $uploadResult['error']], 400);
                }
            }

            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("INSERT INTO services 
                    (title, description, overview, min_price, max_price, image_url, is_visible, display_order) 
                    VALUES (:title, :description, :overview, :min_price, :max_price, :image_url, :is_visible, :display_order)");

                $stmt->execute([
                    ':title' => $input['title'],
                    ':description' => $input['description'],
                    ':overview' => $input['overview'] ?? null,
                    ':min_price' => $input['min_price'],
                    ':max_price' => $input['max_price'],
                    ':image_url' => $imageUrl,
                    ':is_visible' => isset($input['is_visible']) ? (int)$input['is_visible'] : 1,
                    ':display_order' => $input['display_order'] ?? null
                ]);

                $serviceId = $pdo->lastInsertId();
                $pdo->commit();

                ob_end_clean();
                sendResponse([
                    'message' => 'Service created successfully',
                    'service_id' => $serviceId
                ], 201);
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("POST Database error: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        case 'PUT':
            // Update service
            if (!$id) {
                error_log("PUT: Service ID is required");
                ob_end_clean();
                sendResponse(['error' => 'Service ID is required'], 400);
            }

            // Check if service exists
            $check = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
            $check->execute([$id]);
            $existingService = $check->fetch(PDO::FETCH_ASSOC);
            if (!$existingService) {
                error_log("PUT: Service not found for ID $id");
                ob_end_clean();
                sendResponse(['error' => 'Service not found'], 404);
            }

            // Get input data
            $input = $_POST;

            // Validate input data
            $errors = validateServiceData($input, true);
            if (!empty($errors)) {
                error_log("PUT Validation errors: " . json_encode($errors));
                ob_end_clean();
                sendResponse(['errors' => $errors], 400);
            }

            // Ensure is_visible is set properly (default to 0 if not provided)
            $isVisible = isset($input['is_visible']) && $input['is_visible'] == '1' ? 1 : 0;

            // Handle file upload if present
            $imageUrl = $existingService['image_url'];
            $oldImagePath = null;
            if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
                // Determine the old image path for deletion
                if (!empty($existingService['image_url'])) {
                    // Convert database image_url (e.g., 'api/uploads/filename.jpg') to actual file path (e.g., 'Uploads/filename.jpg')
                    $oldImagePath = str_replace('api/uploads/', 'Uploads/', $existingService['image_url']);
                    $oldImagePath = __DIR__ . '/' . $oldImagePath; // Full server path
                }

                // Handle new image upload
                $uploadResult = handleImageUpload('image_url');
                if (isset($uploadResult['success']) && $uploadResult['success']) {
                    $imageUrl = $uploadResult['url']; // e.g., 'api/uploads/newfilename.jpg'

                    // Delete the old image file if it exists and is not the same as the new image
                    if ($oldImagePath && file_exists($oldImagePath)) {
                        $newImagePath = __DIR__ . '/' . str_replace('api/uploads/', 'Uploads/', $imageUrl);
                        if ($oldImagePath !== $newImagePath) {
                            if (!unlink($oldImagePath)) {
                                error_log("PUT: Failed to delete old image at $oldImagePath");
                                // Continue with update even if deletion fails
                            }
                        }
                    }
                } else {
                    $errorMessage = $uploadResult['error'] ?? 'Unknown upload error';
                    error_log("PUT Image upload error: $errorMessage");
                    ob_end_clean();
                    sendResponse(['error' => $errorMessage], 400);
                }
            }

            try {
                $pdo->beginTransaction();

                $stmt = $pdo->prepare("UPDATE services SET 
                    title = :title, 
                    description = :description, 
                    overview = :overview, 
                    min_price = :min_price, 
                    max_price = :max_price, 
                    is_visible = :is_visible, 
                    display_order = :display_order, 
                    image_url = :image_url 
                    WHERE service_id = :service_id");

                $stmt->execute([
                    ':title' => $input['title'],
                    ':description' => $input['description'],
                    ':overview' => $input['overview'] ?? null,
                    ':min_price' => $input['min_price'],
                    ':max_price' => $input['max_price'],
                    ':is_visible' => $isVisible,
                    ':display_order' => $input['display_order'] ?? null,
                    ':image_url' => $imageUrl,
                    ':service_id' => $id
                ]);

                $pdo->commit();
                ob_end_clean();
                sendResponse(['message' => 'Service updated successfully']);
            } catch (PDOException $e) {
                $pdo->rollBack();
                // If the transaction fails and a new image was uploaded, delete the new image
                if ($imageUrl !== $existingService['image_url'] && isset($uploadResult['path']) && file_exists($uploadResult['path'])) {
                    if (!unlink($uploadResult['path'])) {
                        error_log("PUT: Failed to delete new image at {$uploadResult['path']} after database error");
                    }
                }
                error_log("PUT Database error: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        case 'DELETE':
            // Delete service and all related data
            if (!$id) {
                error_log("DELETE: Service ID is required");
                ob_end_clean();
                sendResponse(['error' => 'Service ID is required'], 400);
            }

            try {
                $pdo->beginTransaction();

                // Delete related records first
                $pdo->prepare("DELETE FROM feature WHERE service_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM pricing_package WHERE service_id = ?")->execute([$id]);

                // Then delete the service
                $stmt = $pdo->prepare("DELETE FROM services WHERE service_id = ?");
                $stmt->execute([$id]);

                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    ob_end_clean();
                    sendResponse(['message' => 'Service and all related data deleted successfully']);
                } else {
                    $pdo->rollBack();
                    error_log("DELETE: Service not found for ID $id");
                    ob_end_clean();
                    sendResponse(['error' => 'Service not found'], 404);
                }
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("DELETE Database error: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;

        default:
            error_log("Invalid method: $method");
            ob_end_clean();
            sendResponse(['error' => 'Method not allowed'], 405);
            break;
    }
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    ob_end_clean();
    sendResponse(['error' => 'Unexpected error: ' . $e->getMessage()], 500);
}
?>