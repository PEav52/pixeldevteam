<?php
require_once 'functions.php';

$request = getRequestData();
$method = $request['method'];
$input = $request['input'];
$id = $request['id'];
$projectId = $_GET['project_id'] ?? null;

switch ($method) {
    case 'GET':
        if ($id) {
            // Get single feature
            $stmt = $pdo->prepare("SELECT * FROM project_features WHERE p_feature_id = ?");
            $stmt->execute([$id]);
            $feature = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($feature) {
                sendResponse($feature);
            } else {
                sendResponse(['error' => 'Feature not found'], 404);
            }
        } else if ($projectId) {
            // Get all features for a project
            $stmt = $pdo->prepare("SELECT * FROM project_features WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($features);
        } else {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        break;
        
    case 'POST':
        if (!$projectId) {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        
        if (empty($input['p_feature_name']) || empty($input['p_feature_description'])) {
            sendResponse(['error' => 'Feature name and description are required'], 400);
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO project_features 
                (project_id, p_feature_name, p_feature_description) 
                VALUES (?, ?, ?)");
                
            $stmt->execute([
                $projectId,
                $input['p_feature_name'],
                $input['p_feature_description']
            ]);
            
            sendResponse([
                'message' => 'Feature added successfully',
                'p_feature_id' => $pdo->lastInsertId()
            ], 201);
            
        } catch (PDOException $e) {
            sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        if (!$id) {
            sendResponse(['error' => 'Feature ID is required'], 400);
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE project_features SET 
                p_feature_name = ?, 
                p_feature_description = ? 
                WHERE p_feature_id = ?");
                
            $stmt->execute([
                $input['p_feature_name'],
                $input['p_feature_description'],
                $id
            ]);
            
            if ($stmt->rowCount() > 0) {
                sendResponse(['message' => 'Feature updated successfully']);
            } else {
                sendResponse(['error' => 'Feature not found'], 404);
            }
            
        } catch (PDOException $e) {
            sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'DELETE':
        if (!$id) {
            sendResponse(['error' => 'Feature ID is required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM project_features WHERE p_feature_id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            sendResponse(['message' => 'Feature deleted successfully']);
        } else {
            sendResponse(['error' => 'Feature not found'], 404);
        }
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
        break;
}
?>