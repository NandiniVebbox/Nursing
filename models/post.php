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
        $stmtEmail = mysqli_prepare($this->conn, $queryEmail);

        if (!$stmtEmail) {
            return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
        }

        mysqli_stmt_bind_param($stmtEmail, "s", $gmail);
        mysqli_stmt_execute($stmtEmail);

        $resultEmail = mysqli_stmt_get_result($stmtEmail);

        if (!$resultEmail) {
            return ["message" => "Query execution error: " . mysqli_error($this->conn)];
        }

        if (mysqli_num_rows($resultEmail) === 0) {
            return ["message" => "Email not found"];
        }

        // Check OTP
        $queryOtp = "SELECT * FROM admin WHERE gmail = ? AND otp = ? AND expiration_time > NOW()";
        $stmtOtp = mysqli_prepare($this->conn, $queryOtp);

        if (!$stmtOtp) {
            return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
        }

        mysqli_stmt_bind_param($stmtOtp, "ss", $gmail, $otp);
        mysqli_stmt_execute($stmtOtp);

        $resultOtp = mysqli_stmt_get_result($stmtOtp);

        if (!$resultOtp) {
            return ["message" => "Query execution error: " . mysqli_error($this->conn)];
        }

        if (mysqli_num_rows($resultOtp) > 0) {
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
        $stmt = mysqli_prepare($this->conn, $insert);

        if (!$stmt) {
            return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
        }

        mysqli_stmt_bind_param($stmt, "sss", $gmail, $token, $expirationTime);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            return ["message" => "Token insertion successful", "token" => $token];
        } else {
            return ["message" => "Token insertion failed: " . mysqli_error($this->conn)];
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
        $stmt = mysqli_prepare($this->conn, $insert);
    
        if (!$stmt) {
            return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
        }
    
        mysqli_stmt_bind_param($stmt, "ssss", $adminId, $title, $content, $created_at);
        $result = mysqli_stmt_execute($stmt);
    
        if ($result) {
            return ["message" => "Notification insertion successful"];
        } else {
            return ["message" => "Notification insertion failed: " . mysqli_error($this->conn)];
        }
    }
    
    // Module: Admin
    // SubModule: Achievement->Insert
    public function A_InsertAchievement($adminId, $content)
    {         
        $insert = "INSERT INTO achievement (admin_id, content) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->conn, $insert);
    
        if (!$stmt) {
            return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
        }
    
        mysqli_stmt_bind_param($stmt, "ss", $adminId, $content);
        $result = mysqli_stmt_execute($stmt);
    
        if ($result) {
            return ["message" => "Achievement insertion successful"];
        } else {
            return ["message" => "Achievement insertion failed: " . mysqli_error($this->conn)];
        }
    }
    
    // Module: Admin
    // SubModule: Course->Insert
    public function A_InsertCourse($adminId, $name, $about, $description)
    {         
        $insert = "INSERT INTO course (admin_id, course_name, course_about, course_description) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $insert);
    
        if (!$stmt) {
            return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
        }
    
        mysqli_stmt_bind_param($stmt, "ssss", $adminId, $name, $about, $description);
        $result = mysqli_stmt_execute($stmt);
    
        if ($result) {
            return ["message" => "Course insertion successful"];
        } else {
            return ["message" => "Course insertion failed: " . mysqli_error($this->conn)];
        }
    }

}
?> 
