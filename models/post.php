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
            // $insertResult = $this->insertToken($gmail);
            // return $insertResult;
            return ["message" => "Success"];
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
    
    // Module: Admin
    // SubModule: Notification -> Single Notification
    public function A_singleNotify($adminId, $title, $content)
    {     
        // Set the timezone to Asia/Kolkata
        $timezone = new DateTimeZone('Asia/Kolkata');
        $datetime = new DateTime('now', $timezone);
        $created_at = $datetime->format('Y-m-d H:i:s');
    
        $insert = "INSERT INTO notification (admin_id, title, content, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insert);
    
        if (!$stmt) {
            return ["message" => "Query preparation error: " . $this->conn->error];
        }
    
        $stmt->bind_param("ssss", $adminId, $title, $content, $created_at);
        $result = $stmt->execute();
    
        if ($result) {
            return ["message" => "Notification insertion successful"];
        } else {
            return ["message" => "Notification insertion failed: " . $stmt->error];
        }
    }
    
    // Module: Admin
    // SubModule: Achievement->Insert
    public function A_InsertAchievement($adminId, $content)
    {         
        $insert = "INSERT INTO achievement (admin_id, content) VALUES (?, ?)";
        $stmt = $this->conn->prepare($insert);
    
        if (!$stmt) {
            return ["message" => "Query preparation error: " . $this->conn->error];
        }
    
        $stmt->bind_param("ss", $adminId, $content);
        $result = $stmt->execute();
    
        if ($result) {
            return ["message" => "Achievement insertion successful"];
        } else {
            return ["message" => "Achievement insertion failed: " . $stmt->error];
        }
    }
    
    // Module: Admin
    // SubModule: Course->Insert
    public function A_InsertCourse($adminId, $name, $about, $description)
    {         
        $insert = "INSERT INTO achievement (admin_id, name, about, description) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insert);
    
        if (!$stmt) {
            return ["message" => "Query preparation error: " . $this->conn->error];
        }
    
        $stmt->bind_param("ss", $adminId, $name, $about, $description);
        $result = $stmt->execute();
    
        if ($result) {
            return ["message" => "Course insertion successful"];
        } else {
            return ["message" => "Course insertion failed: " . $stmt->error];
        }
    }

}
?>