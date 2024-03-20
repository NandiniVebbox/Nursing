<?php
include_once '../../../../config/database.php';

class Get
{
    public $conn;
    public $response;

    function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    //Module:Admin
    //SubModule:Achievement->View
    public function A_viewAchievement($adminId)
    {        
        $selectData = "SELECT sno,content FROM achievement";
        $stmt = mysqli_prepare($this->conn, $selectData);           
        mysqli_stmt_execute($stmt);       
        if (mysqli_stmt_errno($stmt)) {       
        return false;
        }
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {            
            return false;
        }   

        if (mysqli_num_rows($result) > 0) {
        $achievementContent = mysqli_fetch_all($result, MYSQLI_ASSOC);        
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        return $achievementContent;
        } else {
        return ["error"=>"No Details Found"]; 
        }
          
    }

    //Module:Admin
    //SubModule:Course->View Name
    public function A_viewCourseName($adminId)
    {        
        $selectData = "SELECT sno,course_name FROM course";
        $stmt = mysqli_prepare($this->conn, $selectData);           
        mysqli_stmt_execute($stmt);       
        if (mysqli_stmt_errno($stmt)) {       
        return false;
        }
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {            
            return false;
        }   

        if (mysqli_num_rows($result) > 0) {
        $courseName = mysqli_fetch_all($result, MYSQLI_ASSOC);        
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        return $courseName;
        } else {
        return ["error"=>"No Details Found"]; 
        }
          
    }

    //Module:Admin
    //SubModule:Course->View Content
    public function A_viewCourseContent($adminId,$id)
    {        
        $selectData = "SELECT sno,course_name,course_about,course_description FROM course WHERE sno=?";
        $stmt = mysqli_prepare($this->conn, $selectData); 
        mysqli_stmt_bind_param($stmt, 'i', $id);          
        mysqli_stmt_execute($stmt);       
        if (mysqli_stmt_errno($stmt)) {       
        return false;
        }
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {            
            return false;
        }   

        if (mysqli_num_rows($result) > 0) {
        $courseContent = mysqli_fetch_all($result, MYSQLI_ASSOC);        
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        return $courseContent;
        } else {
        return ["error"=>"No Details Found"]; 
        }
          
    }

}
?>