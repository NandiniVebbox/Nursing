<?php

$modelsPath = '../../../../models/post.php';
$headersPath = '../../../../config/header.php';

// Check if required files exist
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
if (empty($data->adminId) || empty($data->institutionId) || empty($data->year) || empty($data->month) || empty($data->questions)) {
    handleResponse(400, 'Invalid data. All fields are required.');
}

// Check if the Gmail ID is a valid email address and contains "@gmail.com"
if (!filter_var($data->adminId, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data->adminId)) {
    handleResponse(400, 'Invalid Gmail ID format. It should be a valid email address and contain "@gmail.com".');
}

// Create an instance of the Post class
$obj = new Post();

// Validate the questions' answers
foreach ($data->questions as $question) {
    $isValidAnswer = false;
    foreach (['option1', 'option2', 'option3', 'option4'] as $option) {
        if ($question->answer === $question->$option) {
            $isValidAnswer = true;
            break;
        }
    }
    if (!$isValidAnswer) {
        handleResponse(400, 'Invalid question answer. Answer should match any of the options.');
    }
}

// Insert PMCQ questions
$result = $obj->A_InsertPmcqQuestion($data->adminId, $data->institutionId, $data->year, $data->month, $data->questions);

// Handle errors
if ($result === false) {
    handleResponse(500, 'Internal server error');
}

// Send the result
echo json_encode($result);

?>
