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
if (empty($data->gmailId) && empty($data->otp)) {
    handleResponse(400, 'invalid data. both gmail_id and otp are required.');
}
if (empty($data->gmailId)) {
    handleResponse(400, 'invalid data.gmail_id is required.');
}
if(empty($data->otp))
{
    handleResponse(400, 'invalid data.otp is required.');
}
if (empty($data->otp) || !preg_match('/^\d{4}$/', $data->otp)) {
    handleResponse(400, 'invalid OTP. please provide a four-digit number for OTP.');
}
// Check if the Gmail ID is a valid email address and contains "@gmail.com"
if (!filter_var($data->gmailId, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data->gmailId)) {
    handleResponse(400, 'invalid gmail id format. it should be a valid email address and contain "@gmail.com".');
}

$obj = new Post();
$result = $obj->A_ValidateOtp_Token($data->gmailId, $data->otp);

// Handle errors
if ($result === false) {
    handleResponse(500, 'internal server error');
}

// Send the result
echo json_encode($result);

?>
