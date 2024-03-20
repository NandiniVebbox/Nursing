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

// Check if required data is provided
if (empty($data->adminId)) {
    respondWithError(400, 'Invalid data. Gmail ID is required.');
}
if (empty($data->institutionId)) {
    respondWithError(400, 'Invalid data. Institution ID is required.');
}
if (empty($data->paperId)) {
    respondWithError(400, 'Invalid data. Paper ID is required.');
}

// Validate Gmail ID format
if (!filter_var($data->adminId, FILTER_VALIDATE_EMAIL) || strpos($data->adminId, '@gmail.com') === false) {
    respondWithError(400, 'Invalid Gmail ID format. It should be a valid email address and contain "@gmail.com".');
}

// Create an instance of the Get class
$obj = new Get();

// Retrieve PMCQ question count data based on admin ID, institution ID, and paper ID
$result = $obj->A_viewPmcqQuestionCount($data->adminId, $data->institutionId, $data->paperId);

// Handle errors
if ($result === false) {
    respondWithError(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
