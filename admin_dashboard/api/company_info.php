<?php
// Include DB connection
require 'config.php'; // your connection file

$method = $_SERVER['REQUEST_METHOD'];

// Read raw JSON input
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
        // Fetch company info
        $stmt = $pdo->prepare("SELECT * FROM company_info LIMIT 1");
        $stmt->execute();
        $info = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($info ?: []);
        break;

    case 'PUT':
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE company_info SET 
            company_name = :company_name,
            phone = :phone,
            email = :email,
            address = :address,
            facebook_url = :facebook_url,
            instagram_url = :instagram_url,
            linkedin_url = :linkedin_url,
            twitter_url = :twitter_url,
            whatsapp_number = :whatsapp_number
            WHERE info_id = :info_id
        ");

        $success = $stmt->execute([
            ':company_name' => $input['company_name'] ?? '',
            ':phone' => $input['phone'] ?? '',
            ':email' => $input['email'] ?? '',
            ':address' => $input['address'] ?? '',
            ':facebook_url' => $input['facebook_url'] ?? '',
            ':instagram_url' => $input['instagram_url'] ?? '',
            ':linkedin_url' => $input['linkedin_url'] ?? '',
            ':twitter_url' => $input['twitter_url'] ?? '',
            ':whatsapp_number' => $input['whatsapp_number'] ?? '',
            ':info_id' => $input['info_id']
        ]);

        echo json_encode(['success' => $success]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
?>
