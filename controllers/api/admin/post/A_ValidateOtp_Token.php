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
if (empty($data->gmailId) || empty($data->otp)) {
    handleResponse(400, 'Invalid data. Both gmail_id and otp are required.');
}

// Validate gmailId format
if (!filter_var($data->gmailId, FILTER_VALIDATE_EMAIL) || strpos($data->gmailId, '@gmail.com') === false) {
    handleResponse(400, 'Invalid gmail id format. It should be a valid email address and contain "@gmail.com".');
}

// Validate OTP format
if (!preg_match('/^\d{4}$/', $data->otp)) {
    handleResponse(400, 'Invalid OTP. Please provide a four-digit number for OTP.');
}

$obj = new Post();
$result = $obj->A_ValidateOtp_Token($data->gmailId, $data->otp);

// Handle errors
if ($result === false) {
    handleResponse(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
