<?php
require_once 'functions.php';

// Start output buffering to capture any unintended output
ob_start();

$request = getRequestData();
$method = $request['method'];
$input = $request['input'];
$id = $request['id'];

// Enable error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log'); // Ensure this path is writable

try {
    switch ($method) {
        case 'GET':
            // Get all projects, projects by service_id, or a single project
            $serviceId = isset($_GET['service_id']) ? $_GET['service_id'] : null;
        
            if ($id) {
                // Fetch single project by project_id
                $stmt = $pdo->prepare("SELECT * FROM projects WHERE project_id = ?");
                $stmt->execute([$id]);
                $project = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($project) {
                    // Get related data
                    $featuresStmt = $pdo->prepare("SELECT * FROM project_features WHERE project_id = ?");
                    $featuresStmt->execute([$id]);
                    $features = $featuresStmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $techsStmt = $pdo->prepare("SELECT * FROM technologies WHERE project_id = ?");
                    $techsStmt->execute([$id]);
                    $techs = $techsStmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $assetsStmt = $pdo->prepare("SELECT * FROM visualassets WHERE project_id = ?");
                    $assetsStmt->execute([$id]);
                    $assets = $assetsStmt->fetchAll(PDO::FETCH_ASSOC);
        
                    $resultsStmt = $pdo->prepare("SELECT * FROM results WHERE project_id = ?");
                    $resultsStmt->execute([$id]);
                    $results = $resultsStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $project['features'] = $features;
                    $project['technologies'] = $techs;
                    $project['assets'] = $assets;
                    $project['results'] = $results;
                    
                    ob_end_clean();
                    sendResponse($project);
                } else {
                    ob_end_clean();
                    sendResponse(['error' => 'Project not found'], 404);
                }
            } elseif ($serviceId) {
                // Fetch projects by service_id
                $stmt = $pdo->prepare("SELECT * FROM projects WHERE service_id = ?");
                $stmt->execute([$serviceId]);
                $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ob_end_clean();
                sendResponse($projects);
            } else {
                // Fetch all projects
                $stmt = $pdo->query("SELECT * FROM projects");
                $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ob_end_clean();
                sendResponse($projects);
            }
            break;
            
        case 'POST':
            // Create new project
            $errors = validateProjectData($input);
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
                
                $stmt = $pdo->prepare("INSERT INTO projects 
                    (service_id, title, description, overview, image_url, development_process, client_quote, is_visible) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    
                $stmt->execute([
                    $input['service_id'],
                    $input['title'],
                    $input['description'],
                    $input['overview'],
                    $imageUrl,
                    $input['development_process'] ?? null,
                    $input['client_quote'] ?? null,
                    isset($input['is_visible']) ? (int)$input['is_visible'] : 1
                ]);
                
                $projectId = $pdo->lastInsertId();
                $pdo->commit();
                
                ob_end_clean();
                sendResponse([
                    'message' => 'Project created successfully',
                    'project_id' => $projectId
                ], 201);
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("POST Database error: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;
            
        case 'PUT':
            // Update project
            if (!$id) {
                error_log("PUT: Project ID is required");
                ob_end_clean();
                sendResponse(['error' => 'Project ID is required'], 400);
            }
            
            // Check if project exists
            $check = $pdo->prepare("SELECT * FROM projects WHERE project_id = ?");
            $check->execute([$id]);
            $existingProject = $check->fetch(PDO::FETCH_ASSOC);
            if (!$existingProject) {
                error_log("PUT: Project not found for ID $id");
                ob_end_clean();
                sendResponse(['error' => 'Project not found'], 404);
            }
            
            // Get input data
            $input = $_POST;
            
            // Ensure is_visible is set properly (default to 0 if not checked)
            $isVisible = isset($input['is_visible']) && $input['is_visible'] == 1 ? 1 : 0;
            
            // Handle file upload if present
            $imageUrl = $existingProject['image_url'];
            $oldImagePath = null;
            $uploadResult = null;
            if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
                // Determine the old image path for deletion
                if (!empty($existingProject['image_url'])) {
                    // Convert database image_url (e.g., 'api/uploads/filename.jpg') to actual file path (e.g., 'Uploads/filename.jpg')
                    $oldImagePath = str_replace('api/uploads/', 'Uploads/', $existingProject['image_url']);
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
                } else {
                    $errorMessage = $uploadResult['error'] ?? 'Unknown upload error';
                    error_log("PUT Image upload error: $errorMessage");
                    ob_end_clean();
                    sendResponse(['error' => $errorMessage], 400);
                }
            }
            
            try {
                $pdo->beginTransaction();
                
                $stmt = $pdo->prepare("UPDATE projects SET 
                    service_id = :service_id, 
                    title = :title, 
                    description = :description, 
                    overview = :overview, 
                    development_process = :development_process, 
                    client_quote = :client_quote, 
                    is_visible = :is_visible,
                    image_url = :image_url
                    WHERE project_id = :project_id");
                    
                $stmt->execute([
                    ':service_id' => $input['service_id'],
                    ':title' => $input['title'],
                    ':description' => $input['description'],
                    ':overview' => $input['overview'],
                    ':development_process' => $input['development_process'] ?? null,
                    ':client_quote' => $input['client_quote'] ?? null,
                    ':is_visible' => $isVisible,
                    ':image_url' => $imageUrl,
                    ':project_id' => $id
                ]);
                
                $pdo->commit();
                ob_end_clean();
                sendResponse(['message' => 'Project updated successfully']);
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                // If the transaction fails and a new image was uploaded, delete the new image
                if ($imageUrl !== $existingProject['image_url'] && isset($uploadResult['path']) && file_exists($uploadResult['path'])) {
                    if (!unlink($uploadResult['path'])) {
                        error_log("PUT: Failed to delete new image at {$uploadResult['path']} after database error");
                    } else {
                        error_log("PUT: Successfully deleted new image at {$uploadResult['path']} after database error");
                    }
                }
                error_log("PUT Database error: " . $e->getMessage());
                ob_end_clean();
                sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
            }
            break;
            
        case 'DELETE':
            // Delete project and all related data
            if (!$id) {
                error_log("DELETE: Project ID is required");
                ob_end_clean();
                sendResponse(['error' => 'Project ID is required'], 400);
            }
            
            try {
                $pdo->beginTransaction();
                
                // Delete related records first
                $pdo->prepare("DELETE FROM project_features WHERE project_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM technologies WHERE project_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM visualassets WHERE project_id = ?")->execute([$id]);
                $pdo->prepare("DELETE FROM results WHERE project_id = ?")->execute([$id]);
                
                // Then delete the project
                $stmt = $pdo->prepare("DELETE FROM projects WHERE project_id = ?");
                $stmt->execute([$id]);
                
                if ($stmt->rowCount() > 0) {
                    $pdo->commit();
                    ob_end_clean();
                    sendResponse(['message' => 'Project and all related data deleted successfully']);
                } else {
                    $pdo->rollBack();
                    error_log("DELETE: Project not found for ID $id");
                    ob_end_clean();
                    sendResponse(['error' => 'Project not found'], 404);
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