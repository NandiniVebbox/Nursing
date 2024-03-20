<?php

// Define paths to required files
$modelsPath = '../../../../models/post.php';
$headersPath = '../../../../config/header.php';

// Check if required files exist and include them
if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Required files are missing']);
    exit();
}

// Require the necessary files
require_once $modelsPath;
require_once $headersPath;

// Decode the incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Function to handle errors and send response
function handleResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Check if required data is provided
if (empty($data->adminId) || empty($data->title) || empty($data->content)) {
    handleResponse(400, 'Invalid data. All fields are required.');
}

// Validate admin ID format
if (!filter_var($data->adminId, FILTER_VALIDATE_EMAIL) || strpos($data->adminId, '@gmail.com') === false) {
    handleResponse(400, 'Invalid admin ID format. It should be a valid email address and contain "@gmail.com".');
}

$obj = new Post();
$result = $obj->A_singleNotify($data->adminId, $data->title, $data->content);

// Handle errors
if ($result === false) {
    handleResponse(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
