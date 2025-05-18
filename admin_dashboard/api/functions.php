<?php
require_once 'config.php';

// Function to get request data and method
function getRequestData() {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = [];

    // Handle _method override for PUT/DELETE
    if ($method === 'POST' && isset($_POST['_method'])) {
        $method = strtoupper($_POST['_method']);
    }

    // Collect input data
    if ($method === 'POST' || $method === 'PUT') {
        // Handle FormData (multipart/form-data)
        if (!empty($_POST)) {
            $input = $_POST;
        }
        // Handle JSON payload
        $jsonInput = json_decode(file_get_contents('php://input'), true);
        if ($jsonInput !== null) {
            $input = array_merge($input, $jsonInput);
        }
        // Include uploaded files in input
        if (!empty($_FILES)) {
            $input['files'] = $_FILES;
        }
    }

    return [
        'method' => $method,
        'input' => $input,
        'id' => isset($_GET['id']) ? $_GET['id'] : null
    ];
}

// validate project data
function validateProjectData($data) {
    $errors = [];
    
    if (empty($data['service_id'])) {
        $errors[] = "Service ID is required";
    }
    if (empty($data['title'])) {
        $errors[] = "Title is required";
    }
    if (empty($data['description'])) {
        $errors[] = "Description is required";
    }
    
    return $errors;
}

// Handle file upload and return the file path
function handleImageUpload($fileInputName, $uploadDir = 'Uploads/') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'skipped' => true]; // Indicate no file was uploaded
    }
    
    $file = $_FILES[$fileInputName];
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error: ' . $file['error']];
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type'];
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    // Move the file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // Return relative path for database storage
        return ['success' => true, 'path' => $destination, 'url' => 'api/uploads/' . $filename];
    } else {
        return ['error' => 'Failed to move uploaded file'];
    }
}

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Validate Service Data
function validateServiceData($data, $isUpdate = false) {
    $errors = [];

    if (!$isUpdate || isset($data['title'])) {
        if (empty($data['title'])) {
            $errors[] = "Title is required";
        }
    }

    if (!$isUpdate || isset($data['description'])) {
        if (empty($data['description'])) {
            $errors[] = "Description is required";
        }
    }

    if (!$isUpdate || isset($data['min_price'])) {
        if (empty($data['min_price']) || !is_numeric($data['min_price']) || $data['min_price'] < 0) {
            $errors[] = "Valid minimum price is required";
        }
    }

    if (!$isUpdate || isset($data['max_price'])) {
        if (empty($data['max_price']) || !is_numeric($data['max_price']) || $data['max_price'] < 0) {
            $errors[] = "Valid maximum price is required";
        } elseif (isset($data['min_price']) && $data['max_price'] < $data['min_price']) {
            $errors[] = "Maximum price must be greater than or equal to minimum price";
        }
    }

    if (isset($data['is_visible']) && !in_array($data['is_visible'], ['0', '1'])) {
        $errors[] = "Invalid visibility value";
    }

    if (isset($data['display_order']) && (!is_numeric($data['display_order']) || $data['display_order'] < 0)) {
        $errors[] = "Valid display order is required";
    }

    return $errors;
}

// Validate Pricing Data of service
function validateFeatureData($data, $isUpdate = false) {
    $errors = [];

    if (!$isUpdate || isset($data['service_id'])) {
        if (empty($data['service_id'])) {
            $errors[] = "Service ID is required";
        } else {
            global $pdo;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE service_id = ?");
            $stmt->execute([$data['service_id']]);
            if ($stmt->fetchColumn() == 0) {
                $errors[] = "Invalid Service ID: Service does not exist";
            }
        }
    }

    // For updates, name is optional but must not be empty if provided
    if (isset($data['name']) && $data['name'] === '') {
        $errors[] = "Feature name cannot be empty if provided";
    }

    if (isset($data['is_active']) && !in_array($data['is_active'], ['0', '1'])) {
        $errors[] = "Invalid active status";
    }

    // Ensure at least one field is provided for updates
    if ($isUpdate && !isset($data['name']) && !isset($data['description']) && !isset($data['is_active'])) {
        $errors[] = "At least one field (name, description, or is_active) must be provided for update";
    }

    return $errors;
}

// Validate Pricing Data of main page
function validatePricingData($data, $isUpdate = false) {
    $errors = [];

    if (!$isUpdate || isset($data['service_id'])) {
        if (empty($data['service_id'])) {
            $errors[] = "Service ID is required";
        } else {
            global $pdo;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM services WHERE service_id = ?");
            $stmt->execute([$data['service_id']]);
            if ($stmt->fetchColumn() == 0) {
                $errors[] = "Invalid Service ID: Service does not exist";
            }
        }
    }

    if (!$isUpdate || isset($data['title'])) {
        if (empty($data['title'])) {
            $errors[] = "Pricing package title is required";
        }
    }

    if (!$isUpdate || isset($data['price'])) {
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
            $errors[] = "Valid price is required";
        }
    }

    return $errors;
}

// Validate Company Info Data
function validatePricingDataMain($data, $isUpdate = false) {
    $errors = [];

    if (!$isUpdate || isset($data['category'])) {
        if (empty($data['category'])) {
            $errors[] = "Category is required";
        } elseif (!in_array($data['category'], ['development', 'hosting', 'domain'])) {
            $errors[] = "Category must be one of: development, hosting, domain";
        }
    }

    if (!$isUpdate || isset($data['item_name'])) {
        if (empty($data['item_name'])) {
            $errors[] = "Item name is required";
        }
    }

    if (!$isUpdate || isset($data['price'])) {
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
            $errors[] = "Valid price is required";
        }
    }

    if (isset($data['is_active']) && !in_array($data['is_active'], ['0', '1'])) {
        $errors[] = "Invalid active status";
    }

    return $errors;
}

// Validate About Content Data
function validateStepData($data, $isUpdate = false) {
    $errors = [];

    if (!$isUpdate || isset($data['title'])) {
        if (empty($data['title'])) {
            $errors[] = "Title is required";
        }
    }

    if (!$isUpdate || isset($data['step_number'])) {
        if (empty($data['step_number']) || !is_numeric($data['step_number']) || $data['step_number'] < 1) {
            $errors[] = "Valid step number (positive integer) is required";
        }
    }

    if (isset($data['is_visible']) && !in_array($data['is_visible'], ['0', '1'])) {
        $errors[] = "Invalid visibility status";
    }

    return $errors;
}

// Validate Why Choose Us Data
function validateWhyChooseUsData($data, $isUpdate = false) {
    $errors = [];

    if (!$isUpdate || isset($data['title'])) {
        if (empty($data['title'])) {
            $errors[] = "Title is required";
        }
    }

    if (!$isUpdate || isset($data['icon_class'])) {
        if (empty($data['icon_class'])) {
            $errors[] = "Icon class is required";
        }
    }

    if (!$isUpdate || isset($data['display_order'])) {
        if (empty($data['display_order']) || !is_numeric($data['display_order']) || $data['display_order'] < 1) {
            $errors[] = "Valid display order (positive integer) is required";
        }
    }

    if (isset($data['is_visible']) && !in_array($data['is_visible'], ['0', '1'])) {
        $errors[] = "Invalid visibility status";
    }

    return $errors;
}

// Handle file upload for PDFs and return the file path
function handlePdfUpload($fileInputName, $uploadDir = 'Uploads/') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'skipped' => true]; // Indicate no file was uploaded
    }
    
    $file = $_FILES[$fileInputName];
    
    // Check for errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error: ' . $file['error']];
    }
    
    // Validate file type
    if ($file['type'] !== 'application/pdf') {
        return ['error' => 'Invalid file type. Only PDF is allowed.'];
    }
    
    // Validate file size (e.g., max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'File size exceeds 5MB limit.'];
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    // Move the file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'path' => $destination, 'filename' => $filename];
    } else {
        return ['error' => 'Failed to move uploaded file'];
    }
}

// Validate inquiry reply data
function validateInquiryReplyData($data) {
    $errors = [];
    
    if (empty($data['inquiry_id'])) {
        $errors[] = "Inquiry ID is required";
    }
    if (empty($data['recipient_email'])) {
        $errors[] = "Recipient email is required";
    }
    if (empty($data['subject'])) {
        $errors[] = "Subject is required";
    }
    if (empty($data['message'])) {
        $errors[] = "Message is required";
    }
    
    return $errors;
}

// Validate inquiry status update
function validateInquiryStatus($status) {
    $validStatuses = ['pending', 'read', 'responded', 'closed'];
    if (!in_array($status, $validStatuses)) {
        return ["Invalid status. Must be one of: " . implode(', ', $validStatuses)];
    }
    return [];
}

?>