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

// Decode the incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Function to handle errors and send response
function respondWithError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Validate admin ID presence
if (empty($data->adminId)) {
    respondWithError(400, 'Invalid data. Gmail ID is required.');
}

// Validate ID presence
if (empty($data->id)) {
    respondWithError(400, 'Invalid data. ID is required.');
}

// Validate Gmail ID format
if (!filter_var($data->adminId, FILTER_VALIDATE_EMAIL) || strpos($data->adminId, '@gmail.com') === false) {
    respondWithError(400, 'Invalid Gmail ID format. It should be a valid email address and contain "@gmail.com".');
}

// Create an instance of the Get class
$obj = new Get();

// Retrieve PMCQ paper data based on admin ID and ID
$result = $obj->A_viewPmcqPaper($data->adminId, $data->id);

// Handle errors
if ($result === false) {
    respondWithError(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
