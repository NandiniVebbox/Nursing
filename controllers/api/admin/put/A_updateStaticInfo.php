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

// Check if required fields are provided
if (!isset($data->mobNo) || !isset($data->address) || !isset($data->whatsappLink) || !isset($data->gmailLink)) {
    handleResponse(400, 'Required fields are missing');
}

// Extract data from JSON
$mobNo = $data->mobNo;
$address = $data->address;
$whatsappLink = $data->whatsappLink;
$gmailLink = $data->gmailLink;

// Validate mobile number
if (!is_numeric($mobNo)) {
    handleResponse(400, 'Invalid mobile number');
}

// Create an instance of the Put class
$obj = new Put();

// Call the A_updateStaticInfo method
$result = $obj->A_updateStaticInfo($mobNo, $address, $whatsappLink, $gmailLink);

// Handle errors
if ($result === false) {
    handleResponse(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
