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

// Function to handle errors and send response
function respondWithError($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Validate incoming data
if (!isValidData($data)) {
    respondWithError(400, 'Invalid data. Gmail ID and course ID are required.');
}

// Create an instance of the Get class
$obj = new Get();

// Fetch course content for the given admin ID and course ID
$result = $obj->A_viewCourseContent($data->adminId, $data->id);

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
    if (empty($data->adminId) || empty($data->id)) {
        return false;
    }
    
    // Validate the Gmail ID format
    return filter_var($data->adminId, FILTER_VALIDATE_EMAIL) && preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data->adminId);
}

?>
