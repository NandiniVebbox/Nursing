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

// Decode incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Validate incoming data
if (!isset($data->adminId) || !isset($data->id) || !isset($data->content)) {
    handleResponse(400, 'Invalid input data');
}

// Create an instance of the Put class
$obj = new Put();

// Call the A_updateAchievement method
$result = $obj->A_updateAchievement($data->adminId, $data->id, $data->content);

// Check for errors
if ($result === false) {
    handleResponse(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

// Function to handle errors and send response
function handleResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}
?>
