<?php

// Define paths to required files
$modelsPath = '../../../../models/post.php';
$headersPath = '../../../../config/header.php';

// Check if required files exist and include them
if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    handleError(500, 'Required files are missing');
}

// Require the necessary files
require_once $modelsPath;
require_once $headersPath;

// Decode the incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Function to handle errors and send response
function handleError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Check if required data is provided
if (empty($data->adminId) || empty($data->name) || empty($data->desc) || empty($data->instruction)) {
    handleError(400, 'Invalid data. All fields are required.');
}

// Validate admin ID format
if (!filter_var($data->adminId, FILTER_VALIDATE_EMAIL) || strpos($data->adminId, '@gmail.com') === false) {
    handleError(400, 'Invalid admin ID format. It should be a valid email address and contain "@gmail.com".');
}

// Create an instance of the Post class
$obj = new Post();

// Insert PMCQ institution data
$result = $obj->A_InsertNonNurisngCategory($data->adminId, $data->name, $data->desc, $data->instruction);

// Handle errors
if ($result === false) {
    handleError(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
