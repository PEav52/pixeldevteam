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
                $stmt = $pdo->prepare("SELECT reason_id, title, description, icon_class, is_visible, display_order FROM why_choose_us WHERE reason_id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse($data ?: ['error' => 'Reason not found'], $data ? 200 : 404);
            } else {
                $stmt = $pdo->prepare("SELECT reason_id, title, description, icon_class, is_visible, display_order FROM why_choose_us");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendResponse($data ?: []);
            }
            break;

        case 'POST':
            $errors = validateWhyChooseUsData($input);
            if (!empty($errors)) {
                sendResponse(['errors' => $errors], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO why_choose_us (title, description, icon_class, is_visible, display_order) VALUES (:title, :description, :icon_class, :is_visible, :display_order)");
            $success = $stmt->execute([
                ':title' => $input['title'],
                ':description' => $input['description'] ?? '',
                ':icon_class' => $input['icon_class'],
                ':is_visible' => isset($input['is_visible']) ? $input['is_visible'] : 1,
                ':display_order' => $input['display_order']
            ]);

            if ($success) {
                $reasonId = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT reason_id, title, description, icon_class, is_visible, display_order FROM why_choose_us WHERE reason_id = ?");
                $stmt->execute([$reasonId]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse(['success' => true, 'data' => $data], 201);
            } else {
                sendResponse(['error' => 'Failed to create reason'], 500);
            }
            break;

        case 'PUT':
            if (!$id) {
                sendResponse(['error' => 'reason_id is required'], 400);
            }

            $errors = validateWhyChooseUsData($input, true);
            if (!empty($errors)) {
                sendResponse(['errors' => $errors], 400);
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM why_choose_us WHERE reason_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Reason not found'], 404);
            }

            $stmt = $pdo->prepare("UPDATE why_choose_us SET title = COALESCE(:title, title), description = COALESCE(:description, description), icon_class = COALESCE(:icon_class, icon_class), is_visible = COALESCE(:is_visible, is_visible), display_order = COALESCE(:display_order, display_order) WHERE reason_id = :reason_id");
            $success = $stmt->execute([
                ':title' => $input['title'] ?? null,
                ':description' => $input['description'] ?? null,
                ':icon_class' => $input['icon_class'] ?? null,
                ':is_visible' => isset($input['is_visible']) ? $input['is_visible'] : null,
                ':display_order' => $input['display_order'] ?? null,
                ':reason_id' => $id
            ]);

            if ($success) {
                $stmt = $pdo->prepare("SELECT reason_id, title, description, icon_class, is_visible, display_order FROM why_choose_us WHERE reason_id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse(['success' => true, 'data' => $data], 200);
            } else {
                sendResponse(['error' => 'Failed to update reason'], 500);
            }
            break;

        case 'DELETE':
            if (!$id) {
                sendResponse(['error' => 'reason_id is required'], 400);
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM why_choose_us WHERE reason_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Reason not found'], 404);
            }

            $stmt = $pdo->prepare("DELETE FROM why_choose_us WHERE reason_id = ?");
            $success = $stmt->execute([$id]);
            sendResponse(['success' => $success], $success ? 200 : 500);
            break;

        default:
            sendResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}
?>