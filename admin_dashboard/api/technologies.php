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
            $stmt = $pdo->prepare("SELECT * FROM technologies WHERE technology_id = ?");
            $stmt->execute([$id]);
            $tech = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($tech) {
                sendResponse($tech);
            } else {
                sendResponse(['error' => 'Technology not found'], 404);
            }
        } else if ($projectId) {
            $stmt = $pdo->prepare("SELECT * FROM technologies WHERE project_id = ?");
            $stmt->execute([$projectId]);
            $techs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($techs);
        } else {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        break;
        
    case 'POST':
        if (!$projectId) {
            sendResponse(['error' => 'Project ID is required'], 400);
        }
        
        if (empty($input['technology_name'])) {
            sendResponse(['error' => 'Technology name is required'], 400);
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO technologies 
                (project_id, technology_name) 
                VALUES (?, ?)");
                
            $stmt->execute([
                $projectId,
                $input['technology_name']
            ]);
            
            sendResponse([
                'message' => 'Technology added successfully',
                'technology_id' => $pdo->lastInsertId()
            ], 201);
            
        } catch (PDOException $e) {
            sendResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        if (!$id) {
            sendResponse(['error' => 'Technology ID is required'], 400);
        }
        
        $stmt = $pdo->prepare("UPDATE technologies SET 
            technology_name = ? 
            WHERE technology_id = ?");
            
        $stmt->execute([
            $input['technology_name'],
            $id
        ]);
        
        if ($stmt->rowCount() > 0) {
            sendResponse(['message' => 'Technology updated successfully']);
        } else {
            sendResponse(['error' => 'Technology not found'], 404);
        }
        break;
        
    case 'DELETE':
        if (!$id) {
            sendResponse(['error' => 'Technology ID is required'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM technologies WHERE technology_id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            sendResponse(['message' => 'Technology deleted successfully']);
        } else {
            sendResponse(['error' => 'Technology not found'], 404);
        }
        break;
        
    default:
        sendResponse(['error' => 'Method not allowed'], 405);
        break;
}
?>