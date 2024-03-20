<?php

// Define paths to required files
$modelsPath = '../../../../models/get.php';
$headersPath = '../../../../config/header.php';

// Check if required files exist
if (!file_exists($modelsPath) || !file_exists($headersPath)) {
    respondWithError(500, 'Required files are missing');
}

// Require the necessary files
require_once $modelsPath;
require_once $headersPath;

// Decode the incoming JSON data
$data = json_decode(file_get_contents('php://input'));

// Validate incoming data
if (!isValidData($data)) {
    respondWithError(400, 'Invalid data. Gmail ID is required and should be in the correct format.');
}

// Create an instance of the Get class
$obj = new Get();

// Fetch achievements for the given admin ID
$result = $obj->A_viewAchievement($data->adminId);

// Handle errors
if ($result === false) {
    respondWithError(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

/**
 * Function to validate incoming data.
 *
 * @param object $data Incoming data object
 *
 * @return bool True if data is valid, otherwise false
 */
function isValidData($data) {
    if (empty($data->adminId)) {
        return false;
    }
    
    // Validate the Gmail ID format
    return filter_var($data->adminId, FILTER_VALIDATE_EMAIL) && strpos($data->adminId, '@gmail.com');
}

/**
 * Function to handle errors and send response.
 *
 * @param int    $statusCode HTTP status code
 * @param string $message    Error message
 */
function respondWithError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

?>
