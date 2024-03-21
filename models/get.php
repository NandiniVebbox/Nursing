<?php
include_once '../../../../config/database.php';

class Get
{
    public $conn;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Function to handle errors and send response
    private function handleResponse($statusCode, $message) 
    {
        http_response_code($statusCode);
        echo json_encode(['error' => $message]);
        exit();
    }

    //Module:Admin
    //SubModule:Achievement->View
    public function A_viewAchievement($adminId) 
    {        
        $query = "SELECT sno, content FROM achievement";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $achievementContent = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $achievementContent;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:Course->View Name
    public function A_viewCourseName($adminId) 
    {
        $query = "SELECT sno, course_name FROM course";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $courseName = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $courseName;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:Course->View Content
    public function A_viewCourseContent($adminId, $id) 
    {
        $query = "SELECT sno, course_name, course_about, course_description FROM course WHERE sno = ?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $courseContent = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $courseContent;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:Static Info->View
    public function A_viewStaticInfo($adminId) 
    {
        $query = "SELECT * FROM static_info";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $staticInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $staticInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:PMCQ ->View Institution
    public function A_viewPmcqInstitution($adminId) 
    {
        $query = "SELECT * FROM pmcq";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $institutionInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $institutionInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:PMCQ ->View Paper
    public function A_viewPmcqPaper($adminId, $id) 
    {
        $query = "SELECT * FROM pmcq_meta WHERE institution_id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $paperInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $paperInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:PMCQ ->View Question Count
    public function A_viewPmcqQuestionCount($adminId, $institutionId, $paperId) 
    {        
        $query = "SELECT sno FROM pmcq_question WHERE institution_id=? and paper_id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $institutionId, $paperId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $questionCount = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $questionCount;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:PMCQ ->View Question 
    public function A_viewPmcqQuestions($adminId, $institutionId, $paperId, $questionId) 
    {        
        $query = "SELECT * FROM pmcq_question WHERE institution_id=? and paper_id=? and sno=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'iii', $institutionId, $paperId, $questionId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $paperInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $paperInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:SW ->View Subject
    public function A_viewSubWiseSubject($adminId) 
    {
        $query = "SELECT * FROM subject_wise";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $subjectInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $subjectInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:SW ->View Paper
    public function A_viewsubWisePaper($adminId, $id) 
    {
        $query = "SELECT * FROM subject_wise_meta WHERE subject_id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $paperInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $paperInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:SW ->View Question Count
    public function A_viewSubWiseQuestionCount($adminId, $subjectId, $paperId) 
    {        
        $query = "SELECT sno FROM subject_wise_question WHERE subject_id=? and paper_id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $subjectId, $paperId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $questionCount = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $questionCount;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:SW ->View Question 
    public function A_viewSubWiseQuestions($adminId, $subjectId, $paperId, $questionId) 
    {        
        $query = "SELECT * FROM subject_wise_question WHERE subject_id=? and paper_id=? and sno=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'iii', $subjectId, $paperId, $questionId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $paperInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $paperInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

     //Module:Admin
    //SubModule:NN ->View Category
    public function A_viewNonNursingCategory($adminId) 
    {
        $query = "SELECT * FROM non_nursing";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $categoryInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $categoryInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:NN ->View Paper
    public function A_viewNonNursingPaper($adminId, $id) 
    {
        $query = "SELECT * FROM non_nursing_meta WHERE category_id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $paperInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $paperInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:NN ->View Question Count
    public function A_viewNonNursingQuestionCount($adminId, $categoryId, $paperId) 
    {        
        $query = "SELECT sno FROM non_nursing_question WHERE category_id=? and paper_id=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $categoryId, $paperId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $questionCount = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $questionCount;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }

    //Module:Admin
    //SubModule:NN ->View Question 
    public function A_viewNonNursingQuestions($adminId, $categoryId, $paperId, $questionId) 
    {        
        $query = "SELECT * FROM non_nursing_question WHERE category_id=? and paper_id=? and sno=?";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, 'iii', $categoryId, $paperId, $questionId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_errno($stmt)) {
            $this->handleResponse(500, 'Internal server error');
        }

        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $paperInfo = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            return $paperInfo;
        } else {
            mysqli_stmt_close($stmt);
            return ['error' => 'No Details Found'];
        }
    }
}
?>

