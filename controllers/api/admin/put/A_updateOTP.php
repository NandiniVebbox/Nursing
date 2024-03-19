<?php

// Include necessary files with error handling
$modelsPath = '../../../../models/put.php';
$headersPath = '../../../../config/header.php';

if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    // Handle the case where one or both files are missing
    http_response_code(500);
    echo json_encode(['error' => 'Required files are missing']);
    exit();
}

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
$gmail=$data->gmailId;
// Remove spaces from the Gmail ID
$cleanedGmail = str_replace(' ', '', $gmail);
// Check if required data is provided
if(empty($data->password)&&(empty($data->$cleanedGmail)))
{
    handleResponse(400, 'invalid data.both gmail and password is required.');

}
if (empty($cleanedGmail)) {
    handleResponse(400, 'invalid data.gmail_id is required.');
}
if(empty($data->password))
{
    handleResponse(400, 'invalid data.password is required.');
}

// Check if the email ID is a valid email address
if (!filter_var($cleanedGmail, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $cleanedGmail)) {
    handleResponse(400, 'invalid email id format. it should be a valid email address');
}

// Create an instance of the Put class
$obj = new Put();

// Call the SuperAdmin_otp_generation method
$result = $obj->A_updateOTP($cleanedGmail, $data->password);

// Handle errors
if ($result === false) {
    handleResponse(500, 'internal server error');
}

// Send the result
echo json_encode($result);
?>
