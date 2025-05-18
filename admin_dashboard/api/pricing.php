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
                $stmt = $pdo->prepare("SELECT pricing_id, category, item_name, price, description, is_active FROM pricing WHERE pricing_id = ?");
                $stmt->execute([$id]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse($data ?: ['error' => 'Pricing not found'], $data ? 200 : 404);
            } else {
                $stmt = $pdo->prepare("SELECT pricing_id, category, item_name, price, description, is_active FROM pricing");
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                sendResponse($data ?: []);
            }
            break;

        case 'POST':
            $errors = validatePricingDataMain($input);
            if (!empty($errors)) {
                sendResponse(['errors' => $errors], 400);
            }

            $stmt = $pdo->prepare("INSERT INTO pricing (category, item_name, price, description, is_active) VALUES (:category, :item_name, :price, :description, :is_active)");
            $success = $stmt->execute([
                ':category' => $input['category'],
                ':item_name' => $input['item_name'],
                ':price' => $input['price'],
                ':description' => $input['description'] ?? '',
                ':is_active' => isset($input['is_active']) ? $input['is_active'] : 1
            ]);

            if ($success) {
                $pricingId = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT pricing_id, category, item_name, price, description, is_active FROM pricing WHERE pricing_id = ?");
                $stmt->execute([$pricingId]);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                sendResponse(['success' => true, 'data' => $data], 201);
            } else {
                sendResponse(['error' => 'Failed to create pricing'], 500);
            }
            break;

        case 'PUT':
            if (!$id) {
                sendResponse(['error' => 'pricing_id is required'], 400);
            }

            $errors = validatePricingData($input, true);
            if (!empty($errors)) {
                sendResponse(['errors' => $errors], 400);
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pricing WHERE pricing_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Pricing not found'], 404);
            }

            $stmt = $pdo->prepare("UPDATE pricing SET category = COALESCE(:category, category), item_name = COALESCE(:item_name, item_name), price = COALESCE(:price, price), description = COALESCE(:description, description), is_active = COALESCE(:is_active, is_active) WHERE pricing_id = :pricing_id");
            $success = $stmt->execute([
                ':category' => $input['category'] ?? null,
                ':item_name' => $input['item_name'] ?? null,
                ':price' => $input['price'] ?? null,
                ':description' => $input['description'] ?? null,
                ':is_active' => isset($input['is_active']) ? $input['is_active'] : null,
                ':pricing_id' => $id
            ]);

            sendResponse(['success' => $success], $success ? 200 : 500);
            break;

        case 'DELETE':
            if (!$id) {
                sendResponse(['error' => 'pricing_id is required'], 400);
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM pricing WHERE pricing_id = ?");
            $stmt->execute([$id]);
            if ($stmt->fetchColumn() == 0) {
                sendResponse(['error' => 'Pricing not found'], 404);
            }

            $stmt = $pdo->prepare("DELETE FROM pricing WHERE pricing_id = ?");
            $success = $stmt->execute([$id]);
            sendResponse(['success' => $success], $success ? 200 : 500);
            break;

        default:
            sendResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    sendResponse(['error' => 'Server error: ' . $e->getMessage()], 500);
}