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

    //Module:Admin
    //SubModule: PMCQ -> Add institution
    public function A_InsertPmcqInstitution($adminId,$name,$desc,$instruction)
    {
        $insert = "INSERT INTO pmcq (institution_name, institution_desc, institution_instruction) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $insert);
    
        if (!$stmt) {
            return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
        }
    
        mysqli_stmt_bind_param($stmt, "sss", $name,$desc,$instruction);
        $result = mysqli_stmt_execute($stmt);
    
        if ($result) {
            return ["message" => "Institution Added successful"];
        } else {
            return ["message" => "Institution Added failed: " . mysqli_error($this->conn)];
        }
    }
    public function A_InsertPmcqQuestion($adminId,$institutionId,$year,$month,$questions)
    {
        //check already inserted or not
        if(!$this->A_check_pmcq_YearMonth($institutionId,$year,$month))
        {
            $insert = "INSERT INTO pmcq_meta (institution_id, year, month) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($this->conn, $insert);
        
            if (!$stmt) {
                return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
            }
        
            mysqli_stmt_bind_param($stmt, "sss", $institutionId,$year,$month);
            $result = mysqli_stmt_execute($stmt);
        
            if ($result) {
                //fetch paperid from pmcq_meta
                $paperId=$this->A_fetchPaperId_pmcq_meta($institutionId,$year,$month);
                //add question to pmcq_question
                $getStatusQueandAnsTable=$this->A_addQuestion($questions,$institutionId,$paperId);
                if($getStatusQueandAnsTable=='success')
                {                
                    return ["message" => "Questions Added successful"];
                }else
                {
                    return ["message" => "Questions Added failed: " . mysqli_error($this->conn)];
                }
            } else {
                return ["message" => "Questions Added failed: " . mysqli_error($this->conn)];
            }
        }
        else
        {
            return ["message" => "Already added"];
        }
       
    }
    public function A_addQuestion($questions, $institutionid, $paperId)
    {
        // Check if $questions is an array
        if (!is_array($questions)) {
            return ["message" => "Questions should be an array"];
        }
        
        foreach ($questions as $question) {
            $questionText = $question->questionText; // Accessing object property
            $option1 = $question->option1; // Accessing object property
            $option2 = $question->option2; // Accessing object property
            $option3 = $question->option3; // Accessing object property
            $option4 = $question->option4; // Accessing object property
            $answer = $question->answer; // Accessing object property
            
            // Prepare and execute the SQL query
            $insert = "INSERT INTO pmcq_question (institution_id, paper_id, questions, option1, option2, option3, option4, answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->conn, $insert);
    
            if (!$stmt) {
                return ["message" => "Query preparation error: " . mysqli_error($this->conn)];
            }
    
            mysqli_stmt_bind_param($stmt, "iissssss", $institutionid, $paperId, $questionText, $option1, $option2, $option3, $option4, $answer);
            $result = mysqli_stmt_execute($stmt);
    
            if (!$result) {
                return ["message" => "Question insertion failed: " . mysqli_error($this->conn)];
            }
        }
        $this->response = "success";
        return $this->response;
    }
    

    public function A_fetchPaperId_pmcq_meta($institutionId,$year,$month)
    { 
        $query = "SELECT sno FROM pmcq_meta WHERE institution_id=? AND year=? AND month=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "iss", $institutionId,$year,$month);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_errno($stmt)) {
            return false;
        }
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {
            return false;
        }
        $count = mysqli_fetch_assoc($result)['sno'];
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);        
        return $count;

    }
    public function A_check_pmcq_YearMonth($institutionId,$year,$month)
    {
        $query = "SELECT COUNT(sno) AS count FROM pmcq_meta WHERE institution_id=? AND year=? AND month=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "iss", $institutionId,$year,$month);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_errno($stmt)) {
            return false;
        }
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {
            return false;
        }
        $count = mysqli_fetch_assoc($result)['count'];
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        // Return 1 if the Gmail address exists, otherwise return 0        
        return $count;
        
    }
    
}
?> 

