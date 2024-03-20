<?php

// Define file paths
$modelsPath = '../../../../models/put.php';
$headersPath = '../../../../config/header.php';

// Check if required files exist
if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    handleResponse(500, 'Required files are missing');
}

// Include necessary files
require_once $modelsPath;
require_once $headersPath;

// Function to handle errors and send response
function handleResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Decode incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Check if Gmail ID and password are provided
if (!isset($data->gmailId) || !isset($data->password)) {
    handleResponse(400, 'Both Gmail ID and password are required');
}

// Remove spaces from the Gmail ID
$cleanedGmail = str_replace(' ', '', $data->gmailId);

// Check if the Gmail ID is empty or invalid
if (empty($cleanedGmail) || !filter_var($cleanedGmail, FILTER_VALIDATE_EMAIL)) {
    handleResponse(400, 'Invalid Gmail ID format');
}

// Check if the password is empty
if (empty($data->password)) {
    handleResponse(400, 'Password is required');
}

// Create an instance of the Put class
$obj = new Put();

// Call the A_updateOTP method
$result = $obj->A_updateOTP($cleanedGmail, $data->password);

// Handle errors
if ($result === false) {
    handleResponse(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
