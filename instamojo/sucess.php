<?php
include_once '../config/database.php';
// include_once '../models/post.php';

include 'Instamojo.php';

$db = new Database();
$conn = $db->connect();
$response = [];
// $obj=new Post();


$api = new Instamojo\Instamojo('test_b12c6b6a099a3278c32553de8ad', 'test_1793331e9d1047a0b7e42a234a0', 'https://test.instamojo.com/api/1.1/');

$id = $_GET['payment_request_id'];
// echo $id;
try {
    $response = $api->paymentRequestStatus($id);
} catch (Exception $e) {
    print('Error: ' . $e->getMessage());
}
// echo $response['status'];
if ($response['status'] == "Completed") {
    // echo $response['status'];
    // echo 'refirect';

    $result = getDetails($id, $conn);
    // print_r($result);

    foreach ($result as $data) {
        //  echo "fg";   
        $gmail = $data['gmail'];
        $account_type = $data['account_type'];
        $c_Enrollment = $data['c_enrollment_date'];
        $c_Expiry = $data['c_expiry_date'];
        $cpp_Enrollment = $data['cpp_enrollment_date'];
        $cpp_Expiry = $data['cpp_expiry_date'];
        $java_Enrollment = $data['java_enrollment_date'];
        $java_Expiry = $data['java_expiry_date'];
        $python_Enrollment = $data['python_enrollment_date'];
        $python_Expiry = $data['python_expiry_date'];
        $c_amount = $data['c_amount'];
        $cpp_amount = $data['cpp_amount'];
        $java_amount = $data['java_amount'];
        $python_amount = $data['python_amount'];
        // echo $python_amount;

        $result = insert_std_data($conn, $gmail, $account_type, $c_Enrollment, $c_Expiry, $cpp_Enrollment, $cpp_Expiry, $java_Enrollment, $java_Expiry, $python_Enrollment, $python_Expiry, $c_amount, $cpp_amount, $java_amount, $python_amount);
        //   print_r($result);

        if ($result['message'] == 'success') {
            echo '<h1>Please wait! we will redirect your page</h1>';
            header("refresh:5;URL=https://teststudent.vebbox.in/purchase");
        }

    }
} else {
    $result = ['message' => 'not success'];

}
function getDetails($id, $conn)
{
    // echo "conn";
    $temp = [];
    $select = "select * from purchase_temp where pay_id='$id' ";
    $selectQuery = mysqli_query($conn, $select);
    while ($row = mysqli_fetch_assoc($selectQuery)) {
        $temp[] = $row;
    }
    return $temp;

}
function insert_std_data($conn, $gmail, $account_type, $c_JD, $c_ED, $cpp_JD, $cpp_ED, $java_ED, $java_JD, $python_JD, $python_ED, $c_amount, $cpp_amount, $java_amount, $python_amount)
{
    // echo $gmail;
// echo $account_type;
    $courseDates = [
        'c' => ['enrollment_date' => $c_JD, 'expiry_date' => $c_ED, 'amount' => $c_amount],
        'cpp' => ['enrollment_date' => $cpp_JD, 'expiry_date' => $cpp_ED, 'amount' => $cpp_amount],
        'java' => ['enrollment_date' => $java_JD, 'expiry_date' => $java_ED, 'amount' => $java_amount],
        'python' => ['enrollment_date' => $python_JD, 'expiry_date' => $python_ED, 'amount' => $python_amount]
    ];
    $checkQuery = "SELECT * FROM student WHERE gmail = ? and account_type=?";
    // echo $checkQuery;
    $paramTypes = ''; // Initialize $paramTypes

    $paramTypes .= 'ss'; // Add 's' for the WHERE clause
    // echo $checkQuery."\n\n";
    $stmtCheck = mysqli_prepare($conn, $checkQuery);
    //   print_r($stmtCheck);                     
    if ($stmtCheck) {
        mysqli_stmt_bind_param($stmtCheck, $paramTypes, $gmail, $account_type);
        mysqli_stmt_execute($stmtCheck);

        // Check for errors
        if (mysqli_errno($conn) !== 0) {
            echo "Error: " . mysqli_error($conn);
        }
        // Set the timezone to Asia/Kolkata
        date_default_timezone_set('Asia/Kolkata');
        $currentDateTime = date('Y-m-d H:i:s');
        $results = mysqli_stmt_get_result($stmtCheck);
        $getData = mysqli_fetch_assoc($results);
        // echo  mysqli_num_rows($results);
        if (mysqli_num_rows($results) > 0) {
            $updateExpiryQuery = "UPDATE student SET join_date=now(),";
            foreach ($courseDates as $updateLang => $updateDates) {
                if ($updateDates['enrollment_date'] != 0 && $updateDates['expiry_date'] != 0 && $updateDates['amount'] != 0) {
                    $totalAmount = $updateDates['amount'];

                    $updateExpiryQuery .= "{$updateLang}_amount=$totalAmount,{$updateLang}_enrollment_date = NOW(), {$updateLang}_expiry_date = ";

                    $updateExpiryQuery .= "CASE WHEN DATE_FORMAT({$updateLang}_expiry_date, '%Y-%m-%d') < DATE_FORMAT(NOW(), '%Y-%m-%d') THEN DATE_ADD(NOW(), INTERVAL 1 YEAR) ELSE DATE_ADD({$updateLang}_expiry_date, INTERVAL 1 YEAR) END, ";
                    
                }

            }
            $updateExpiryQuery = rtrim($updateExpiryQuery, ', ') . " WHERE gmail = ?";
            // echo $updateExpiryQuery."\n\n";
            $stmtUpdateExpiry = mysqli_prepare($conn, $updateExpiryQuery);

            if ($stmtUpdateExpiry) {
                mysqli_stmt_bind_param($stmtUpdateExpiry, 's', $gmail);
                mysqli_stmt_execute($stmtUpdateExpiry);
                mysqli_stmt_close($stmtUpdateExpiry);

                $params = [];
                foreach ($courseDates as $lang => $dates) {
                    if ($dates['enrollment_date'] != 0 && $dates['expiry_date'] != 0 && $dates['amount'] != 0) {
                        $params[] = $dates['enrollment_date'];
                        $params[] = $dates['expiry_date'];

                        //for chatgpt
                        $insertChatgpt = std_chatgpt($conn,$lang,$gmail);
                        $checkQuery = "SELECT 1 FROM $lang WHERE gmail = ?";
                        // echo $checkQuery."\n\n";
                        $stmtCheck = mysqli_prepare($conn, $checkQuery);

                        if ($stmtCheck) {
                            mysqli_stmt_bind_param($stmtCheck, 's', $gmail);
                            mysqli_stmt_execute($stmtCheck);
                            mysqli_stmt_store_result($stmtCheck);

                            // If no rows are fetched, it means a record with the same gmail does not exist
                            if (mysqli_stmt_num_rows($stmtCheck) == 0) {
                                $sno_colName = $lang . "_" . 'sno';
                                $insertCourseQuery = "INSERT INTO $lang (gmail, score,learning_total,exercise_total,practice_total,last_read,$sno_colName) VALUES (?, ?,?,?,?,?,?)";
                                // echo $insertCourseQuery."\n\n";
                                $stmtInsertCourse = mysqli_prepare($conn, $insertCourseQuery);

                                if ($stmtInsertCourse) {
                                    mysqli_stmt_bind_param($stmtInsertCourse, 'siiiiii', $gmail, $initialScore, $learning_total, $exercise_total, $practice_total, $last_read, $sno_colInsertVal);
                                    $initialScore = 0; // You can set the initial score as needed
                                    $sno_colInsertVal = 1;
                                    $last_read = 1;
                                    $learning_total = 100;
                                    $exercise_total = 100;
                                    $practice_total = 100;
                                    mysqli_stmt_execute($stmtInsertCourse);

                                    if ($stmtInsertCourse) {
                                        //  $insertChatgpt = std_chatgpt($conn,$lang,$gmail);
                                        // if($insertChatgpt == 'success'){
                                        $insertCertifyData = insertCertificateData($conn, $lang, $gmail);
                                        if ($insertCertifyData == 'success') {
                                            $last_read = '1.E';
                                            $tableName = fetchTableName($conn, $gmail, $lang);

                                            if (checkChapterTitleExerciseLike($conn, $tableName, $last_read)) {
                                                if (checkChapterTitleChapterStatusStdAnswer($conn, $tableName, $last_read)) {
                                                    // if($this->)
                                                    $parts = explode(".E", $last_read);
                                                    // $parts[0] will contain the part before the dot
                                                    $Exercise = $parts[0];
                                                    // echo $Exercise . "\n";
                                                    $nextExercise = $Exercise + 1;
                                                    $nextExercise = $nextExercise . '.E';
                                                    // echo $nextExercise;
                                                    if (checkNextExercise($conn, $tableName, $nextExercise)) {
                                                        $next_learn = $Exercise + 1 . '.1';
                                                        echo $next_learn;
                                                        $allLearn = $Exercise . '.%';
                                                        $allExercise = $Exercise . '.E.%';
                                                        $updateLearn = updateLearnChapterStatus($conn, $tableName, $allLearn, $allExercise, $nextExercise);
                                                        if ($updateLearn == 'success') {
                                                            $updateStatus = updateNextLearnExerciseChapterOpen($conn, $tableName, $nextExercise, $next_learn);
                                                            if ($updateStatus == 'success') {
                                                                return ['message' => 'success'];
                                                            } else {
                                                                return ['message' => 'not success'];
                                                            }
                                                        } else {
                                                            return ['message' => 'not success'];
                                                        }

                                                    } else {
                                                        $response = ['message' => 'not success'];

                                                    }
                                                } else {
                                                    $response = ['message' => 'success'];

                                                }
                                            } else {
                                                $response = ['message' => 'not success'];

                                            }

                                        } else {
                                            $response = ['message' => 'not success'];

                                        }
                                    //     }
                                    // else {
                                    //     $response = ['message' => 'not success'];
                                    // }

                                        //        

                                    } else {
                                        $response = ['message' => 'not success'];
                                    }
                                } else {
                                    $response = ["message" => "not success"];
                                }
                            } else {
                                $response = ['message' => 'success'];

                            }


                        } else {
                            $response = ['message' => 'not success'];
                        }
                    }


                }


            } else {
                $response = ["message" => "error updating expiry date"];
            }
        } else {
            $response = ['message' => 'no more data'];
        }

        mysqli_stmt_close($stmtCheck);
    } else {
        $response = ['message' => 'not sucess'];
    }
    //   print_r($response);         
    mysqli_close($conn);
    return $response;

}

function insertCertificateData($conn, $lang, $gmail)
{
    $insert = "insert into certificate (gmail,language,status,other) values ('$gmail','$lang','not download','not filled')";
    // echo $insert;
    $inserQuery = mysqli_query($conn, $insert);
    if ($inserQuery) {
        $response = 'success';
    } else {
        $response = 'not success';

    }
    return $response;
}
function fetchTableName($conn, $userId, $language)
{
    $tableName = "";
    // Split the email address into an array using "@" as the delimiter
    $mailParts = explode('@', $userId);

    // Get the part before the "@" symbol
    $gmail = $mailParts[0];
    $updateGmail = str_replace('.', '_', $gmail);

    $tableName = "content_" . $language . "_" . $updateGmail;

    return $tableName;
}
// module:student
// submodule:prepare->exercise->submit next
function checkChapterTitleExerciseLike($conn, $tableName, $last_read)
{
    $select = "select count(chapter_title) as count from $tableName where chapter_title like '$last_read.%'";
    $stmt = mysqli_prepare($conn, $select);
    // mysqli_stmt_bind_param($stmt, "s", $userId);
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
// module:student
// submodule:prepare->exercise->submit next
function checkChapterTitleChapterStatusStdAnswer($conn, $tableName, $last_read)
{
    $query = "SELECT
        COUNT(CASE WHEN std_status = 'complete' AND chapter_status = 'open' and chapter_title LIKE ? THEN 1 END) AS count_condition1,
        COUNT(CASE WHEN chapter_title LIKE ? THEN 1 END) AS count_condition2
        FROM $tableName";

    // Using bind_param to avoid SQL injection
    $likeParameter = $last_read . '.%';
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $likeParameter, $likeParameter);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch the results
    $row = $result->fetch_assoc();

    // Compare the counts
    return ($row['count_condition1'] == $row['count_condition2']) ? 1 : 0;
}

// module:student
// submodule:prepare->exercise->check next exercise
function checkNextExercise($conn, $tableName, $nextExercise)
{
    $select = "SELECT COUNT(chapter_title) AS count FROM $tableName WHERE chapter_title LIKE ?";
    $nextExercisePattern = "$nextExercise.%";

    $stmt = mysqli_prepare($conn, $select);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $nextExercisePattern);
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

        return $count;
    }

    return false;
}
// module:student
// submodule:prepare->exercise->submit next
function updateNextLearnExerciseChapterOpen($conn, $tableName, $nextExercise, $next_learn)
{
    $update3 = "update $tableName set chapter_status='open',std_status='incomplete' where chapter_title='$next_learn'";

    $updateQuery3 = mysqli_query($conn, $update3);


    if ($updateQuery3) {

        $update4 = "update $tableName set chapter_status='open',std_status='incomplete' where chapter_title like '$nextExercise.%'";

        $updateQuery4 = mysqli_query($conn, $update4);

        if ($updateQuery4) {
            $response = 'success';

        } else {
            $response = 'not success';

        }
    } else {
        $response = 'not success';
    }
    return $response;
}
function updateLearnChapterStatus($conn,$tableName, $allLearn, $allExercise, $nextExercise)
{
    $query = "SELECT
                SUM(CASE WHEN std_status = 'incomplete' AND chapter_title LIKE ? AND chapter_title NOT LIKE ? THEN 1 ELSE 0 END) AS count_condition1
                -- SUM(CASE WHEN chapter_title LIKE ? AND chapter_title NOT LIKE ? THEN 1 ELSE 0 END) AS count_condition2
            FROM
                $tableName";
                echo $query;

    // Prepare the statement
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        // Handle the case where the statement preparation fails
        return 'not success' . mysqli_error($conn);
    }

    // Bind parameters to the prepared statement using query parameters
    $stmt->bind_param("ss", $allLearn, $allExercise);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    // Fetch the results
    $row = $result->fetch_assoc();

    // Compare the counts

    // Compare the counts
    if ($row['count_condition1']) {
        $update = "UPDATE $tableName SET chapter_status='open', std_status='complete' WHERE chapter_title LIKE ? AND chapter_title NOT LIKE ?";
        $stmt = mysqli_prepare($conn, $update);

        if (!$stmt) {
            // Handle the case where the statement preparation fails
            return 'not success' . mysqli_error($conn);
        }

        // Bind parameters to the prepared statement using query parameters
        mysqli_stmt_bind_param($stmt, "ss", $allLearn, $allExercise);

        // Execute the prepared statement
        $updateStatus = mysqli_stmt_execute($stmt);

        if ($updateStatus) {
            return 'success';

        } else {

            return 'not success';
        }

        // Close the prepared statement
    } else {
        // echo "suc";
        return 'success';
    }
}

 //Module:Student,CollegeAdmin,SuperAdmin
    //SubModule:Add chatgpt data

    function std_chatgpt($conn,$lang,$gmail)
    {
        $columnName = $lang . "_chatgpt";
        $updateQuery = "UPDATE student SET $columnName = $columnName + 1000 WHERE gmail = ?";
    
        $updateStmt = mysqli_prepare($conn, $updateQuery);  // Fix: Use $conn instead of conn
    
        if ($updateStmt) {
            $updateStmt->bind_param("s", $gmail);
            $updateStatusQuery = mysqli_stmt_execute($updateStmt);
    
            if ($updateStatusQuery) {
                // $response = ["message" => "Updated"];
                 $response = 'success';
            } else {
                // $response = ["message" => "Error executing update query"];
                 $response = 'not success';
            }
    
            mysqli_stmt_close($updateStmt);
        } else {
            $response = 'not success';
        }
    
        return $response;
    }
    


?>