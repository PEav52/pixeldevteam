<?php
require_once 'functions.php';

// Set timezone to Phnom Penh, Cambodia
date_default_timezone_set('Asia/Phnom_Penh');

$request = getRequestData();
$method = $request['method'];
$input = $request['input'];
$id = $request['id'];
$projectId = $_GET['project_id'] ?? null;

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM visualassets WHERE asset_id = ?");
            $stmt->execute([$id]);
            $asset = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($asset) {
                sendResponse($asset);
            } else {
                sendResponse(['error' => 'Asset not found'], 404);
            }
        } else if ($projectId) {
            $stmt = $pdo->prepare("SELECT * FROM visualassets WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $assets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($assets);
        } else {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        break;
        
    case 'POST':
        if (!$projectId) {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        
        if (empty($input['alt_text']) || !isset($_FILES['image_url']) || $_FILES['image_url']['error'] === UPLOAD_ERR_NO_FILE) {
            sendResponse(['error' => 'Image file and alt text are required'], 400);
        }
        
        $uploadResult = handleImageUpload('image_url');
        if (!isset($uploadResult['url'])) {
            sendResponse(['error' => $uploadResult['error']], 400);
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO visualassets 
                (project_id, image_url, alt_text, updated_at) 
                VALUES (?, ?, ?, ?)");
                            
            $stmt->execute([
                $projectId,
                $uploadResult['url'],
                $input['alt_text'],
                date('Y-m-d H:i:s') // current PHP datetime
            ]);
            
            sendResponse([
                'message' => 'Asset added successfully',
                'asset_id' => $pdo->lastInsertId()
            ], 201);
            
        } catch (PDOException $e) {
            sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        if (!$id) {
            sendResponse(['error' => 'Asset ID is required'], 400);
        }
        
        // Check if asset exists
        $check = $pdo->prepare("SELECT image_url FROM visualassets WHERE asset_id = ?");
        $check->execute([$id]);
        $existingAsset = $check->fetch(PDO::FETCH_ASSOC);
        if (!$existingAsset) {
            sendResponse(['error' => 'Asset not found'], 404);
        }
        
        try {
            $pdo->beginTransaction(); // Start transaction for consistency
            
            $updateFields = [];
            $params = [];
            $uploadResult = null;
            
            if (!empty($input['alt_text'])) {
                $updateFields[] = 'alt_text = ?';
                $params[] = $input['alt_text'];
            }
            
            $imageUrl = $existingAsset['image_url'];
            $oldImagePath = null;
            if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Determine the old image path for deletion
                if (!empty($existingAsset['image_url'])) {
                    // Convert database image_url (e.g., 'api/uploads/filename.jpg') to actual file path (e.g., 'Uploads/filename.jpg')
                    $oldImagePath = str_replace('api/uploads/', 'Uploads/', $existingAsset['image_url']);
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
                            } else {
                                error_log("PUT: Successfully deleted old image at $oldImagePath");
                            }
                        }
                    }
                    
                    $updateFields[] = 'image_url = ?';
                    $params[] = $imageUrl;
                } else {
                    $errorMessage = $uploadResult['error'] ?? 'Unknown upload error';
                    sendResponse(['error' => $errorMessage], 400);
                }
            } else {
                $updateFields[] = 'image_url = ?';
                $params[] = $imageUrl;
            }
            
            if (empty($updateFields)) {
                sendResponse(['error' => 'No fields to update'], 400);
            }
            
            $params[] = date('Y-m-d H:i:s'); // Add updated_at value
            $params[] = $id;
            $sql = "UPDATE visualassets SET " . implode(', ', $updateFields) . ", updated_at = ? WHERE asset_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $pdo->commit(); // Commit transaction
            
            if ($stmt->rowCount() > 0) {
                sendResponse(['message' => 'Asset updated successfully']);
            } else {
                sendResponse(['message' => 'No changes made']);
            }
            
        } catch (PDOException $e) {
            $pdo->rollBack(); // Rollback transaction
            // If the transaction fails and a new image was uploaded, delete the new image
            if ($imageUrl !== $existingAsset['image_url'] && isset($uploadResult['path']) && file_exists($uploadResult['path'])) {
                if (!unlink($uploadResult['path'])) {
                    error_log("PUT: Failed to delete new image at {$uploadResult['path']} after database error");
                } else {
                    error_log("PUT: Successfully deleted new image at {$uploadResult['path']} after database error");
                }
            }
            error_log("PUT Database error: " . $e->getMessage());
            sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'DELETE':
        if (!$id) {
            sendResponse(['error' => 'Asset ID is required'], 400);
        }
        
        // First get the image URL to delete the file
        $stmt = $pdo->prepare("SELECT image_url FROM visualassets WHERE asset_id = ?");
        $stmt->execute([$id]);
        $asset = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($asset) {
            // Delete the file
            $filePath = str_replace('api/', '', $asset['image_url']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete the record
            $stmt = $pdo->prepare("DELETE FROM visualassets WHERE asset_id = ?");
            $stmt->execute([$id]);
            
            sendResponse(['message' => 'Asset deleted successfully']);
        } else {
            sendResponse(['error' => 'Asset not found'], 404);
        }
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
        break;
}
?>