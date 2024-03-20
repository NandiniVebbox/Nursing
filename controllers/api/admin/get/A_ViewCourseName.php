<?php

// Define paths to required files
$modelsPath = '../../../../models/get.php';
$headersPath = '../../../../config/header.php';

// Check if required files exist and include them
if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    respondWithError(500, 'Required files are missing');
}

// Require the necessary files
require_once $modelsPath;
require_once $headersPath;

// Decode JSON input data
$data = json_decode(file_get_contents('php://input'));

// Function to handle errors and send response
function respondWithError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Validate admin ID presence and format
if (empty($data->adminId) || !filter_var($data->adminId, FILTER_VALIDATE_EMAIL)) {
    respondWithError(400, 'Invalid data. Gmail ID is required and should be a valid email address.');
}

// Create an instance of the Get class
$obj = new Get();

// Retrieve course names based on admin ID
$result = $obj->A_viewCourseName($data->adminId);

// Handle errors
if ($result === false) {
    respondWithError(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
