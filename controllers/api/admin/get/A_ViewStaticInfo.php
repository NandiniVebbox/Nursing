<?php

$modelsPath= '../../../../models/get.php';
$headersPath= '../../../../config/header.php';

if (file_exists($modelsPath) && file_exists($headersPath)) {
    require_once $modelsPath;
    require_once $headersPath;
} else {
    // Handle the case where one or both files are missing
    http_response_code(500);
    echo json_encode(['error' => 'Required files are missing']);
    exit();
}

$data = json_decode(file_get_contents('php://input'));

// Function to handle errors and send response
function handleResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Check if required data is provided
if (empty($data->adminId)) {
    handleResponse(400, 'invalid data. gmail_id is required.');
}

// Check if the Gmail ID is a valid email address and contains "@gmail.com"
if (!filter_var($data->adminId, FILTER_VALIDATE_EMAIL) || !strpos($data->adminId, '@gmail.com')) {
    handleResponse(400, 'invalid gmail id format. it should be a valid email address and contain "@gmail.com".');
}

$obj = new Get();
$result = $obj->A_viewStaticInfo($data->adminId);

// Handle errors
if ($result === false) {
    handleResponse(500, 'internal server error');
}

// Send the result
echo json_encode($result);

?>
