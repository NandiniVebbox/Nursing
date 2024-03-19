<?php
include_once '../../../../config/database.php';

class Post
{
    public $conn;
    public $response;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }
    
    // Module: Admin
    // SubModule: Login -> Validate OTP
    public function A_ValidateOtp_Token($gmail, $otp)
    {
        // Check if email exists
        $queryEmail = "SELECT * FROM admin WHERE gmail = ?";
        $stmtEmail = $this->conn->prepare($queryEmail);

        if (!$stmtEmail) {
            return ["message" => "Query preparation error: " . $this->conn->error];
        }

        $stmtEmail->bind_param("s", $gmail);
        $stmtEmail->execute();

        if ($stmtEmail->error) {
            return ["message" => "Query execution error: " . $stmtEmail->error];
        }

        $stmtEmail->store_result();
        if ($stmtEmail->num_rows === 0) {
            return ["message" => "Email not found"];
        }

        // Check OTP
        $queryOtp = "SELECT * FROM admin WHERE gmail = ? AND otp = ? AND expiration_time > NOW()";
        $stmtOtp = $this->conn->prepare($queryOtp);

        if (!$stmtOtp) {
            return ["message" => "Query preparation error: " . $this->conn->error];
        }

        $stmtOtp->bind_param("ss", $gmail, $otp);
        $stmtOtp->execute();

        if ($stmtOtp->error) {
            return ["message" => "Query execution error: " . $stmtOtp->error];
        }

        $stmtOtp->store_result();
        if ($stmtOtp->num_rows > 0) {
            // OTP is valid, insert token
            $insertResult = $this->insertToken($gmail);
            return $insertResult;
        } else {
            return ["message" => "Invalid OTP"];
        }
    }

    // Module: Admin
    // SubModule: Login -> Validate OTP -> Insert Token
    public function insertToken($gmail)
    {
        $token = bin2hex(random_bytes(32));
        $expirationTime = date('Y-m-d H:i:s', strtotime('+1 day'));

        $insert = "INSERT INTO admin_token (gmail, token, expiration_time) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($insert);

        if (!$stmt) {
            return ["message" => "Query preparation error: " . $this->conn->error];
        }

        $stmt->bind_param("sss", $gmail, $token, $expirationTime);
        $result = $stmt->execute();

        if ($result) {
            return ["message" => "Token insertion successful", "token" => $token];
        } else {
            return ["message" => "Token insertion failed: " . $stmt->error];
        }
    }


}
?>