<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'functions.php';

try {
    $request = getRequestData();
    $method = $request['method'];
    $input = $request['input'];
    $id = $request['id'];

    switch ($method) {
        case 'GET':
            if ($id) {
                $stmt = $pdo->prepare("SELECT step_id, title, description, step_number, is_visible FROM process_steps WHERE step_id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse($data ?: ['error' => 'Step not found'], $data ? 200 : 404);
            } else {
                $stmt = $pdo->prepare("SELECT step_id, title, description, step_number, is_visible FROM process_steps");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendResponse($data ?: []);
            }
            break;

        case 'POST':
            $errors = validateStepData($input);
            if (!empty($errors)) {
                sendResponse(['errors' => $errors], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO process_steps (title, description, step_number, is_visible) VALUES (:title, :description, :step_number, :is_visible)");
            $success = $stmt->execute([
                ':title' => $input['title'],
                ':description' => $input['description'] ?? '',
                ':step_number' => $input['step_number'],
                ':is_visible' => isset($input['is_visible']) ? $input['is_visible'] : 1
            ]);

            if ($success) {
                $stepId = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT step_id, title, description, step_number, is_visible FROM process_steps WHERE step_id = ?");
                $stmt->execute([$stepId]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse(['success' => true, 'data' => $data], 201);
            } else {
                sendResponse(['error' => 'Failed to create step'], 500);
            }
            break;

        case 'PUT':
            if (!$id) {
                sendResponse(['error' => 'step_id is required'], 400);
            }

            $errors = validateStepData($input, true);
            if (!empty($errors)) {
                sendResponse(['errors' => $errors], 400);
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM process_steps WHERE step_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Step not found'], 404);
            }

            $stmt = $pdo->prepare("UPDATE process_steps SET title = COALESCE(:title, title), description = COALESCE(:description, description), step_number = COALESCE(:step_number, step_number), is_visible = COALESCE(:is_visible, is_visible) WHERE step_id = :step_id");
            $success = $stmt->execute([
                ':title' => $input['title'] ?? null,
                ':description' => $input['description'] ?? null,
                ':step_number' => $input['step_number'] ?? null,
                ':is_visible' => isset($input['is_visible']) ? $input['is_visible'] : null,
                ':step_id' => $id
            ]);

            sendResponse(['success' => $success], $success ? 200 : 500);
            break;

        case 'DELETE':
            if (!$id) {
                sendResponse(['error' => 'step_id is required'], 400);
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM process_steps WHERE step_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Step not found'], 404);
            }

            $stmt = $pdo->prepare("DELETE FROM process_steps WHERE step_id = ?");
            $success = $stmt->execute([$id]);
            sendResponse(['success' => $success], $success ? 200 : 500);
            break;

        default:
            sendResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}