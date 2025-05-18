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
            $stmt = $pdo->prepare("SELECT * FROM results WHERE result_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                sendResponse($result);
            } else {
                sendResponse(['error' => 'Result not found'], 404);
            }
        } else if ($projectId) {
            $stmt = $pdo->prepare("SELECT * FROM results WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($results);
        } else {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        break;
        
    case 'POST':
        if (!$projectId) {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        
        if (empty($input['metric_value']) || empty($input['metric_description'])) {
            sendResponse(['error' => 'Metric value and description are required'], 400);
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO results 
                (project_id, metric_value, metric_description) 
                VALUES (?, ?, ?)");
                
            $stmt->execute([
                $projectId,
                $input['metric_value'],
                $input['metric_description']
            ]);
            
            sendResponse([
                'message' => 'Result added successfully',
                'result_id' => $pdo->lastInsertId()
            ], 201);
            
        } catch (PDOException $e) {
            sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        if (!$id) {
            sendResponse(['error' => 'Result ID is required'], 400);
        }
        
        $stmt = $pdo->prepare("UPDATE results SET 
            metric_value = ?, 
            metric_description = ? 
            WHERE result_id = ?");
            
        $stmt->execute([
            $input['metric_value'],
            $input['metric_description'],
            $id
        ]);
        
        if ($stmt->rowCount() > 0) {
            sendResponse(['message' => 'Result updated successfully']);
        } else {
            sendResponse(['error' => 'Result not found'], 404);
        }
        break;
        
    case 'DELETE':
        if (!$id) {
            sendResponse(['error' => 'Result ID is required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM results WHERE result_id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            sendResponse(['message' => 'Result deleted successfully']);
        } else {
            sendResponse(['error' => 'Result not found'], 404);
        }
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
        break;
}
?>