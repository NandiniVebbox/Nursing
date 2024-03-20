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


// Create an instance of the Put class
$obj = new Put();

// Call the A_updateStaticInfo method
$result = $obj->A_updateAchievement($data->adminId,$data->id,$data->content);

// Handle errors
if ($result === false) {
    handleResponse(500, 'Internal server error');
}

// Send the result
echo json_encode($result);
?>
