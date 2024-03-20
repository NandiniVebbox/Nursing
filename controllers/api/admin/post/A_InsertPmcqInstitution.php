<?php

$modelsPath= '../../../../models/post.php';
$headersPath= '../../../../config/header.php';

if (file_exists($modelsPath) && file_exists($headersPath)) {
    require_once $modelsPath;
    require_once $headersPath;
} else {
    // Handle the case where one or both files are missing
    http_response_code(500);
    echo json_encode(['error' => 'required files are missing']);
    exit();
}

$data = json_decode(file_get_contents('php://input'));

// Function to handle errors and send response
function handleResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}

// Check if required data is provided
if (empty($data->adminId)&& empty($data->content)) {
    handleResponse(400, 'invalid data. All fields are required.');
}
if (empty($data->adminId)) {
    handleResponse(400, 'invalid data.admin id is required.');
}

if(empty($data->name)){
    handleResponse(400, 'invalid data.content is required.');
}
if(empty($data->desc)){
    handleResponse(400, 'invalid data.content is required.');
}
if(empty($data->instruction)){
    handleResponse(400, 'invalid data.content is required.');
}
// Check if the Gmail ID is a valid email address and contains "@gmail.com"
if (!filter_var($data->adminId, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data->adminId)) {
    handleResponse(400, 'invalid gmail id format. it should be a valid email address and contain "@gmail.com".');
}

$obj = new Post();
$result = $obj->A_InsertPmcqInstitution($data->adminId,$data->name,$data->desc,$data->instruction);

// Handle errors
if ($result === false) {
    handleResponse(500, 'internal server error');
}

// Send the result
echo json_encode($result);

?>
