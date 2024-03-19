<?php
include_once '../../../../config/database.php';

class Put
{
    public $conn;
    public $response;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    //Module:Admin
    //SubModule:Login->Update OTP
    public function A_updateOTP($gmail,$password)
    {
        $query = "SELECT * FROM admin WHERE gmail=? AND BINARY password=?";
        $stmt = mysqli_prepare($this->conn,$query);
        mysqli_stmt_bind_param($stmt,'ss',$gmail,$password);
        mysqli_stmt_execute($stmt);
        $result= mysqli_stmt_get_result($stmt);

        if($row= mysqli_fetch_assoc($result))
        {
            $fourDigitRandomNumber = rand(1111,9999);
            $userIdWithoutDomain = substr($gmail, 0, strpos($gmail, '@'));
            $subject = "Your One-Time Password (OTP) for Super Admin Login";
            
            // Create a DateTime object for the current time in the Asia/Kolkata timezone
            $timezone = new DateTimeZone('Asia/Kolkata');
            $datetime = new DateTime('now', $timezone);

            // Calculate the expiration time (1 minute and 30 seconds from now) and format it
            $expirationTime = $datetime->modify('+1 minute 30 seconds')->format('Y-m-d H:i:s');

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";                                            
            $headers .= 'From: <Nursing>' . "\r\n";
            $message = "
            <p style='color: black;'>Dear $userIdWithoutDomain,</p>
            <p style='color: black;'>Thank you for initiating the OTP verification process for accessing the  Admin panel. To proceed further, please find your OTP below:</p>
            <p style='color: black;'><strong>One-Time Password (OTP):</strong> $fourDigitRandomNumber</p>
            <p style='color: black;'>This OTP is valid for a single use and will expire in 1 minute 30 seconds. Please do not share this OTP with anyone for security reasons.</p>
            <p style='color: black;'>If you did not initiate this request, please disregard this email.</p>
            <p style='color: black;'>Thank you,<br>
            Team - Nursing<br>
            <a href='http://www.nursing.com' style='color: blue;' title='Nursing Website'>www.nursing.com</a><br>
            <a href='mailto:support@nursing.com' style='color: blue;' title='Support Email'>support@nursing.com</a></p>
            ";

            if (mail($gmail, $subject, "$message", $headers)) {
                $updateQuery = "UPDATE admin SET expiration_time=?, otp=? WHERE gmail=? ";
                $updateStmt = mysqli_prepare($this->conn, $updateQuery);
                mysqli_stmt_bind_param($updateStmt, 'sis', $expirationTime, $fourDigitRandomNumber, $gmail);
                $updateResult = mysqli_stmt_execute($updateStmt);
                if($updateResult)
                {
                    $this->response = ["message" => "success"];
                }
                else
                {
                    $this->response = ["message" => "not success"];

                }
            } else {
                $this->response = ["message" => "Failed to send email. Please try again."];
            }
        }
        else 
        {
            // Check if the email exists
            $checkEmailQuery = "SELECT * FROM admin WHERE gmail=?";
            $checkEmailStmt = mysqli_prepare($this->conn, $checkEmailQuery);
            mysqli_stmt_bind_param($checkEmailStmt, 's', $gmail);
            mysqli_stmt_execute($checkEmailStmt);
            $emailExists = mysqli_stmt_fetch($checkEmailStmt);

            if ($emailExists) {
                $this->response = ["message" => "Password may be incorrect. Please try again."];
            } else {
                $this->response = ["message" => "Invalid email. Please check your email address."];
            }
        }

        return $this->response;

    }

    //Module:Admin
    //SubModule:Static Info
    public function A_updateStaticInfo($mobNo, $daddress, $whatsappLink, $gmailLink)
    {
        // Prepare the SQL query with placeholders for parameters
        $updateInfo = "UPDATE static_info SET ";
        $params = array();
        if (!empty($mobNo)) {
            $updateInfo .= "mobibleno = ?, ";
            $params[] = $mobNo;
        }
        if (!empty($daddress)) {
            $updateInfo .= "address = ?, ";
            $params[] = $daddress;
        }
        if (!empty($whatsappLink)) {
            $updateInfo .= "whatsapplink = ?, ";
            $params[] = $whatsappLink;
        }
        if (!empty($gmailLink)) {
            $updateInfo .= "gmaillink = ?, ";
            $params[] = $gmailLink;
        }
        
        // Remove the trailing comma and space
        $updateInfo = rtrim($updateInfo, ', ');
    
        $updateStmt = mysqli_prepare($this->conn, $updateInfo);
    
        if ($updateStmt) {
            // Bind parameters to the placeholders
            $types = str_repeat('s', count($params)); // Assuming all parameters are strings
            mysqli_stmt_bind_param($updateStmt, $types, ...$params);
    
            // Execute the statement
            $updateResult = mysqli_stmt_execute($updateStmt);
    
            if ($updateResult) {
                $this->response = ["message" => "Updated"];
            } else {
                $this->response = ["message" => "Error executing update query: " . mysqli_stmt_error($updateStmt)];
            }
    
            // Close the statement
            mysqli_stmt_close($updateStmt);
        } else {
            // Handle query preparation error
            $this->response = ["message" => "Error preparing update query: " . mysqli_error($this->conn)];
        }
    
        return $this->response;
    }

    //Module:Admin
    //SubModule:Achievement->Update
    public function A_updateAchievement($id,$content)
    {
        $updateQuery = "UPDATE achievement SET content=? WHERE sno=? ";
        $updateStmt = mysqli_prepare($this->conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'si', $content, $id);
        $updateResult = mysqli_stmt_execute($updateStmt);
        if($updateResult)
        {
            $this->response = ["message" => "success"];
        }
        else
        {
            $this->response = ["message" => "not success"];
        }
        return $this->response;
    }    
    
    //Module:Admin
    //SubModule:Course->Update  
    public function A_updateCourse($adminId, $id, $name, $about, $description)
    {
        // Prepare the SQL query with placeholders for parameters
        $updateInfo = "UPDATE course SET ";
        $params = array();
        if (!empty($name)) {
            $updateInfo .= "name = ?, ";
            $params[] = $name;
        }
        if (!empty($about)) {
            $updateInfo .= "about = ?, ";
            $params[] = $about;
        }
        if (!empty($description)) {
            $updateInfo .= "description = ?, ";
            $params[] = $description;
        }       
        $updateInfo = rtrim($updateInfo, ', '); // Remove the trailing comma and space
        $updateInfo .= " WHERE sno = ? "; // Add the WHERE clause
        $params[] = $id; // Add the ID parameter
        
        $updateStmt = mysqli_prepare($this->conn, $updateInfo);
    
        if ($updateStmt) {
            // Bind parameters to the placeholders
            $types = str_repeat('s', count($params)); // Assuming all parameters are strings
            mysqli_stmt_bind_param($updateStmt, $types, ...$params);
    
            // Execute the statement
            $updateResult = mysqli_stmt_execute($updateStmt);
    
            if ($updateResult) {
                $this->response = ["message" => "Updated"];
            } else {
                $this->response = ["message" => "Error executing update query: " . mysqli_stmt_error($updateStmt)];
            }
    
            // Close the statement
            mysqli_stmt_close($updateStmt);
        } else {
            // Handle query preparation error
            $this->response = ["message" => "Error preparing update query: " . mysqli_error($this->conn)];
        }
    
        return $this->response;
    }
    
}
?>