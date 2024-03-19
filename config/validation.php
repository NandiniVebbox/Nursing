<?php
// register college admin
function validateCollegeAdminRegData($data) {
    // Validate the required fields
    $requiredFields = ['adminName', 'adminEmail', 'collegeName', 'adminDesignation', 'mobile', 'account', 'password', 'district', 'languages'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    
       
        foreach ($requiredFields as $field)
        {
            // Check if 'languages' field is an array
            if ($field === 'languages' && !is_array($data->languages)) {
                return ['error' => "Invalid data. Field 'languages' must be an array."];
            }

            // Convert uppercase strings to lowercase
            $data->{$field} = is_array($data->{$field})
                ? array_map('strtolower', $data->{$field})
                : strtolower($data->{$field});

            // Validate length for certain fields
            if (in_array($field, ['adminName', 'adminDesignation', 'district']) && strlen($data->{$field}) > MAX_FIELD_LENGTH) {
                return ['error' => "$field should contain up to " . MAX_FIELD_LENGTH . " letters."];
            }

            // Validate if fields should not start or end with a space
            if (in_array($field, ['adminName', 'adminEmail', 'collegeName', 'adminDesignation', 'password', 'district', 'mobile']) && hasLeadingOrTrailingSpace($data->{$field})) {
                return ['error' => "$field should not start or end with a space or special character."];
            }

           

            // Validate account number format
            if ($field === 'account' && !isValidAccountNumber($data->{$field})) {
                return ['error' => "Invalid account number. It should contain only non-zero numbers."];
            }

            // Validate individual fields
            if ($field === 'adminEmail' && isValidEmail($data->adminEmail)==FALSE) {
                return ['error' => 'Invalid email address format or contains spaces.'];
            }
        }

    // Validate language codes and map to 0 or 1
    $languageCodes = ['c', 'cpp', 'java', 'python'];
    $languageValues = array_fill_keys($languageCodes, 0);

    // Set the values to 1 where the language is present in the input
    foreach ($data->languages as $inputLanguage) {
        if (in_array($inputLanguage, $languageCodes)) {
            $languageValues[$inputLanguage] = 1;
        }
    }

    return [
        'adminName' => $data->adminName,
        'adminEmail' => $data->adminEmail,
        'collegeName' => $data->collegeName,
        'adminDesignation' => $data->adminDesignation,
        'mobile' => $data->mobile,
        'account' => $data->account,
        'password' => $data->password,
        'district' => $data->district,
        'languageValues' => $languageValues
    ];
}
// login college admin
function validateCollegeAdminLoginData($data){
    // Validate the required fields
    $requiredFields = ['admin_id', 'password'];

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
   
    
    foreach ($requiredFields as $field) {
        // Convert field to lowercase
        $data->{$field} = convertToLowercase($data->{$field});
        // Validate individual fields
            if (isValidEmail($data->admin_id)==false) {
                return ['error' => 'Invalid email address format or contains spaces.'];
            }
             // Validate if fields should not start or end with a space
         if (in_array($field, ['admin_id','password']) && hasLeadingOrTrailingSpace($data->{$field}))
         {
             return ['error' => "$field should not start or end with a space or special character."];
         }       
        
        
    }
    
    return [        
        'admin_id' => $data->admin_id,
        'password' => $data->password,
    ];
}
// dashboard college admin
function validateCollegeAdminDashboardData($data){
    $requiredFields = ['admin_id'];

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    foreach ($requiredFields as $field) {
        // Convert field to lowercase
        $data->{$field} = convertToLowercase($data->{$field});
        // Validate individual fields
        if (isValidEmail($data->admin_id)==false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        // Validate if fields should not start or end with a space
        if (in_array($field, ['admin_id','password']) && hasLeadingOrTrailingSpace($data->{$field}))
        {
            return ['error' => "$field should not start or end with a space or special character."];
        }         
    }
    return [        
        'admin_id' => $data->admin_id
    ];
}
function validateCollegeAdminLeaderBoardData($data){
    $requiredFields = ['admin_id','college_name'];

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    foreach ($requiredFields as $field) {
        // Convert field to lowercase
        $data->{$field} = convertToLowercase($data->{$field});
        // Validate individual fields
        if (isValidEmail($data->admin_id)==false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        // Validate if fields should not start or end with a space
        if (in_array($field, ['admin_id','college_name']) && hasLeadingOrTrailingSpace($data->{$field}))
        {
            return ['error' => "$field should not start or end with a space or special character."];
        }
                 
    }
    if (!isset($data->college_name)) {
        handleResponse(400, "College name is empty.");
    } elseif (!is_string($data->college_name)) {
        handleResponse(400, "College name is a non-empty string.");
    }
    // elseif (!preg_match('/^[A-Za-z\s]+$/', $data->college_name)) {
    //     handleResponse(400, "College name is a valid string without digits.");
    // } 
    return [        
        'admin_id' => $data->admin_id,
        'college_name'=>$data->college_name
    ];
}
// student block and unblock college admin
function validateCollegeAdminStudentBlockUnblockData($data){
    $requiredFields = ['gmail','status'];

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    foreach ($requiredFields as $field) {
        // Convert field to lowercase
        $data->{$field} = convertToLowercase($data->{$field});
        // Validate individual fields
        if (isValidEmail($data->gmail)==false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        // Validate if fields should not start or end with a space
        if (in_array($field, ['gmail','status']) && hasLeadingOrTrailingSpace($data->{$field}))
        {
            return ['error' => "$field should not start or end with a space or special character."];
        }  
        // Validate 'status' field for 'block' or 'unblock'
        if ($field === 'status' && !in_array($data->{$field}, ['block', 'unblock'])) {
            return ['error' => "Invalid value for 'status'. It should be either 'block' or 'unblock'."];
        }       
    }
    return [        
        'gmail' => $data->gmail,
        'status'=>$data->status
    ]; 
}
// help and send notification college admin
function validateCollegeAdminHelpData($data)
{
    $requiredFields = ['admin_id','subject','query'];

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    foreach ($requiredFields as $field) {
        // Convert field to lowercase
        $data->{$field} = convertToLowercase($data->{$field});
        // Validate individual fields
        if (isValidEmail($data->admin_id)==false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        // Validate if fields should not start or end with a space
        if (in_array($field, ['admin_id','subject','query']) && hasLeadingOrTrailingSpace($data->{$field}))
        {
            return ['error' => "$field should not start or end with a space or special character."];
        }  
        // Validate 'subject' field 
        if ($field === 'subject' && !in_array($data->{$field}, ['performance issue', 'purchase issue','wrong information display','need documentation','others'])) {
            return ['error' => "Invalid value for 'subject'"];
        }       
    }
    return[
        'admin_id'=>$data->admin_id,
        'subject'=>$data->subject,
        'query'=>$data->query
    ];
}
// profile update college admin
function validateCollegeAdminUpdateProfile($data){
    // Validate the required fields
    $requiredFields = ['adminName', 'admin_id', 'collegeName', 'adminDesignation', 'mobile','district'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    
       
        foreach ($requiredFields as $field)
        {     
            // convert string into lower case 
            $data->{$field} = convertToLowercase($data->{$field});

            // Validate length for certain fields
            if (in_array($field, ['adminName', 'adminDesignation', 'district']) && strlen($data->{$field}) > MAX_FIELD_LENGTH) {
                return ['error' => "$field should contain up to " . MAX_FIELD_LENGTH . " letters."];
            }

            // Validate if fields should not start or end with a space
            if (in_array($field, ['adminName', 'adminEmail', 'collegeName', 'adminDesignation', 'mobile']) && hasLeadingOrTrailingSpace($data->{$field})) {
                return ['error' => "$field should not start or end with a space or special character."];
            }

            // Validate mobile number format
            if ($field === 'mobile' && !isValidMobileNumber($data->{$field})) {
                return ['error' => "Invalid mobile number. It should contain exactly 10 digits."];
            }            

            // Validate individual fields
            if ($field === 'adminEmail' && isValidEmail($data->adminEmail)==FALSE) {
                return ['error' => 'Invalid email address format or contains spaces.'];
            }
        }

    

   
    return [
        'adminName' => $data->adminName,
        'admin_id' => $data->admin_id,
        'collegeName' => $data->collegeName,
        'adminDesignation' => $data->adminDesignation,
        'mobile' => $data->mobile,
        'district' => $data->district
    ];
}
// verify otp -forget password college admin
function validateCollegeAdminVerifyOtpData($data){
    // Validate the required fields
    $requiredFields = ['admin_id', 'otp'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    
       
        foreach ($requiredFields as $field)
        {     
            // convert string into lower case 
            $data->{$field} = convertToLowercase($data->{$field});
             // Validate if fields should not start or end with a space
             if (in_array($field, ['admin_id']) && hasLeadingOrTrailingSpace($data->{$field})) {
                return ['error' => "$field should not start or end with a space or special character."];
            }
            // Validate individual fields
            if ($field === 'admin_id' && isValidEmail($data->admin_id)==FALSE) {
                return ['error' => 'Invalid email address format or contains spaces.'];
            }
            // Check if 'otp' is a 6-digit number
            if ($field === 'otp' && !preg_match('/^\d{6}$/', $data->otp)) {
                return ['error' => 'Invalid OTP format. It should be a 6-digit number.'];
            }
        }
    return ["admin_id"=>$data->admin_id,
    "otp"=>$data->otp];

}
// update otp college admin
function validateCollegeAdminUpdateOtpData($data){
     // Validate the required fields
     $requiredFields = ['admin_id','password'];
     $commonValidationResult = commonValidation($data, $requiredFields);
 
     if (!empty($commonValidationResult)) {
         return $commonValidationResult;
     }
     
        
         foreach ($requiredFields as $field)
         {     
             // convert string into lower case 
             $data->{$field} = convertToLowercase($data->{$field});
              // Validate if fields should not start or end with a space
              if (in_array($field, ['admin_id']) && hasLeadingOrTrailingSpace($data->{$field})) {
                 return ['error' => "$field should not start or end with a space or special character."];
             }
             // Validate individual fields
             if ($field === 'admin_id' && isValidEmail($data->admin_id)==FALSE) {
                 return ['error' => 'Invalid email address format or contains spaces.'];
             }
             // Check if 'otp' is a 6-digit number
             if ($field === 'otp' && !preg_match('/^\d{6}$/', $data->otp)) {
                 return ['error' => 'Invalid OTP format. It should be a 6-digit number.'];
             }
         }
         // Use isValidPasswordLength for password validation
        if (!isValidPasswordLength($data->password)) {
            return ['error' => 'Password must contain at least 8 characters.'];
        }
        
        return ["admin_id"=>$data->admin_id,
        "password"=>$data->password];
}
// purchased course
function validateCollegeAdminPurchasedData($data) {
    // Validate the required fields
    $requiredFields = [ 'adminEmail',  'account', 'languages'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
          
        foreach ($requiredFields as $field)
        {
            // Check if 'languages' field is an array
            if ($field === 'languages' && !is_array($data->languages)) {
                return ['error' => "Invalid data. Field 'languages' must be an array."];
            }

            // Convert uppercase strings to lowercase
            $data->{$field} = is_array($data->{$field})
                ? array_map('strtolower', $data->{$field})
                : strtolower($data->{$field});             

            
            // Validate account number format
            if ($field === 'account' && !isValidAccountNumber($data->{$field})) {
                return ['error' => "Invalid account number. It should contain only non-zero numbers."];
            }

            // Validate individual fields
            if ($field === 'adminEmail' && isValidEmail($data->adminEmail)==FALSE) {
                return ['error' => 'Invalid email address format or contains spaces.'];
            }
        }

    // Validate language codes and map to 0 or 1
    $languageCodes = ['c', 'cpp', 'java', 'python'];
    $languageValues = array_fill_keys($languageCodes, 0);

    // Set the values to 1 where the language is present in the input
    foreach ($data->languages as $inputLanguage) {
        if (in_array($inputLanguage, $languageCodes)) {
            $languageValues[$inputLanguage] = 1;
        }
    }

    return [
        'adminEmail' => $data->adminEmail,
        'account' => $data->account,
        'languageValues' => $languageValues
    ];
}

// reg test data
function validateCollegeAdminTestMetaRegData($data) {
    $requiredFields = ['admin_id', 'testName','topic', 'duration', 'total_qus', 'available_qus', 'start_time', 'end_time', 'questions', 'students', 'date'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }

    foreach ($requiredFields as $field) {

        if (in_array($field, ['admin_id', 'testName','topic', 'duration', 'total_qus', 'available_qus', 'questions', 'students'])) {
            // Check if the field exists in the data object
            if (isset($data->{$field})) {
                $fieldValue = $data->{$field};
    
                // Check if the field value is a string
                if (is_string($fieldValue) && hasLeadingOrTrailingSpace($fieldValue)) {
                    return ['error' => "$field should not start or end with a space or special character."];
                } 
            } else {
                return ['error' => "$field is required."];
            }
        }
        // convert string into lower case 
        $data->{$field} = convertToLowercase($data->{$field});

        if ($field === 'admin_id' && isValidEmail($data->admin_id) == false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        
       

        if (in_array($field, ['total_qus', 'available_qus']) && validateNumericValues($data->total_qus, $data->available_qus)==false) {
            return ['error' => "Invalid values"];
        }

        if (in_array($field, ['start_time', 'end_time']) && validate24HourTimeFormat($data->start_time, $data->end_time) == false) {
            return ['error' => "Invalid times"];
        }
        // if (in_array($field, ['start_time', 'end_time']) && validateDateTimeFormat($data->start_time, $data->end_time) == false) {
        //     return ['error' => "Invalid date-time format"];
        // }

        if ($field === 'questions' && !is_array($data->questions)) {
            return ['error' => "Invalid data. Field 'questions' must be an array."];
        }

        if ($field === 'students' && !is_array($data->students)) {
            return ['error' => "Invalid data. Field 'students' must be an array."];
        }
    }
    //  echo validateTestDuration($data->duration)."\n\n";
        if ($data->duration && validateTestDuration($data->duration) == false) {
            return ['error' => "Invalid test duration and give 15 mts for test duration"];
        }
    // testname
    $testName=validateAndModifyString($data->testName);
        // echo $testName;

    // Additional validations and filtering for students and questions arrays
    $students = validateArrayStudentData_insertTest($data->students);
    $questions = validateArrayQuestionData($data->questions);

    // Validate date format and compare with the current date
    $date=validateDateFormatAndPastDate($data->date);
    $dateTimeString = $date->format('Y-m-d');

    // Validate question keys, status values, answer validity, and options and qus
    validateQuestionKeys($questions);
    validateStatusValues_question($questions);
    validateQuestionOptionsAnswer($questions);
    validateQuestionOptionsandQus($questions);
    
    return [
        "admin_id" => $data->admin_id,
        "test_name" => $testName,
        "topic"=>$data->topic,
        "duration"=>$data->duration,
        "total_qus"=> $data->total_qus,
        "available_qus"=> $data->available_qus,
        "start_time"=> $data->start_time,
        "end_time"=> $data->end_time,
        "questions"=> $questions,
        "students"=> $students,
        "date"=> $dateTimeString
    ];
}
// add student test data
function validateCollegeAdminAddStudentTestData($data){

    $requiredFields = ['id','admin_id', 'testName','test_id', 'students'];
    

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }

    foreach ($requiredFields as $field) {

        if (in_array($field, ['admin_id', 'testName','test_id', 'students'])) {
            // Check if the field exists in the data object
            if (isset($data->{$field})) {
                $fieldValue = $data->{$field};
    
                // Check if the field value is a string
                if (is_string($fieldValue) && hasLeadingOrTrailingSpace($fieldValue)) {
                    return ['error' => "$field should not start or end with a space or special character."];
                } 
            } else {
                return ['error' => "$field is required."];
            }
        }
        // convert string into lower case 
        $data->{$field} = convertToLowercase($data->{$field});

        if ($field === 'admin_id' && isValidEmail($data->admin_id) == false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        
        
        // if ($field === 'duration' && validateTestDuration($data->duration) == false) {
        //     return ['error' => "Invalid test duration and give 30 mts greater than $field"];
        // }

        // if (in_array($field, ['total_qus', 'available_qus']) && validateNumericValues($data->total_qus, $data->available_qus)==false) {
        //     return ['error' => "Invalid values"];
        // }

        // if (in_array($field, ['start_time', 'end_time']) && validate24HourTimeFormat($data->start_time, $data->end_time) == false) {
        //     return ['error' => "Invalid times"];
        // }
        // if (in_array($field, ['start_time', 'end_time']) && validateDateTimeFormat($data->start_time, $data->end_time) == false) {
        //     return ['error' => "Invalid date-time format"];
        // }

        if ($field === 'students' && !is_array($data->students)) {
            return ['error' => "Invalid data. Field 'students' must be an array."];
        }
    }
    // testname
    $testName=validateAndModifyString($data->testName);
    $qusandstuTableName="questd_".$data->testName;

        // echo $testName;

    // Additional validations and filtering for students and questions arrays
    $students = validateArrayStudentData_updateTest($data->students);

    // Validate date format and compare with the current date
    // $date=validateDateFormatAndPastDate($data->date);
    // $dateTimeString = $date->format('Y-m-d');

    return [
        "id"=>$data->id,
        "admin_id" => $data->admin_id,
        "test_name" => $testName,
        "test_id" => $data->test_id,
        "qusandstuTableName"=>$qusandstuTableName,
        // "topic"=>$data->topic,
        // "duration"=>$data->duration,
        // "total_qus"=> $data->total_qus,
        // "available_qus"=> $data->available_qus,
        // "start_time"=> $data->start_time,
        // "end_time"=> $data->end_time,
        "students"=> $students,
        // "date"=> $dateTimeString
    ];
}
// notification status read and star
function validateCollegeAdminNotificationData($data){
    $requiredFields = ['sno','super_admin_id','college_admin_id','read_sts','star_sts','receiver_type'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    
    foreach ($requiredFields as $field)
    {
        // convert string into lower case 
        $data->{$field} = convertToLowercase($data->{$field});

        if (in_array($field, ['super_admin_id', 'college_admin_id']) && !isValidEmail($data->{$field}))
        {
            return['error'=>"invalid email address"];
        }
        // Validate if fields should not start or end with a space
        if (in_array($field, ['super_admin_id', 'college_admin_id', 'sno', 'read_sts', 'star_sts','receiver_type']) && hasLeadingOrTrailingSpace($data->{$field}))
        {
            return ['error' => "$field should not start or end with a space or special character."];
        }
        if(in_array($field,['sno', 'read_sts', 'star_sts']) && !is_numeric($data->{$field}))
        {
            return['error'=>"$field contain only number or digits"];
        }
        if(in_array($field,['read_sts','star_sts'])&& !validate_sts_values($data->read_sts, $data->star_sts))
        {
            return['error'=>"Invalid values. Both read_sts and star_sts must be either 0 or 1."];
        }
        if($field=='receiver_type' && !in_array($data->{$field},['custom','both student and college admins', 'college admins', 'students']))
        {
            return['error'=>"invalid receiver type"];

        }
    }
    return[
        'super_admin_id'=>$data->super_admin_id,
        'sno'=>$data->sno,
        'college_admin_id'=>$data->college_admin_id,
        'read_sts'=>$data->read_sts,
        'star_sts'=>$data->star_sts,
        'receiver_type'=>$data->receiver_type
    ] ;
}
// college admin student data reg
function validateCollegeAdminRegStudentData($data){
    // echo "college admin";
    $studDataLength=0;

    $currentDate = new DateTime();
    $currentDateString = $currentDate->format('Y-m-d');

    // Add one year to the current date
    $oneYearLater = $currentDate->modify('+1 year'); 
    $oneYearLaterString = $oneYearLater->format('Y-m-d');
    $requiredFields = ['admin_id', 'purchase_id', 'purchased_language','college_name', 'studData', 'account_length', 'account_type'];

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    

    foreach ($requiredFields as $field)
    {
        // convert string into lower case 
        // $data->{$field} = convertToLowercase($data->{$field});
        if(in_array($field,['admin_id','account_type','college_name']) &&  !convertToLowercase($data->{$field}))
        {
            handleResponse(400,'convert lower issue');
        }

        if (($field=='admin_id') && !isValidEmail($data->admin_id))
        {
            return['error'=>"invalid email address"];
        }
        // Check if 'languages' field is an array
        if ($field === 'purchased_language' && !is_array($data->purchased_language)) {
            return ['error' => "Invalid data. Field 'languages' must be an array."];
        }
        // Check if 'student' field is an array
        if ($field === 'studData' && !is_array($data->studData)) {
            return ['error' => "Invalid data. Field 'student data' must be an array."];
        } 
        // Validate if fields should not start or end with a space
        if (in_array($field, ['admin_id', 'purchase_id', 'account_type','college_name']) && hasLeadingOrTrailingSpace($data->{$field}))
        {
            return ['error' => "$field should not start or end with a space or special character."];
        }     

    }
     // Check if 'purchase_id' field is greater than 8
     if (strlen($data->purchase_id)<=8)
     {
         handleResponse(400, 'Purchase ID must be greater than 8 characters');
     }

    if (isset($data->studData) && is_array($data->studData)) {
        $studDataLength = count($data->studData);
    } else {
        handleResponse(400,"studData is not set or is not an array.");
    }

    if($studDataLength!=$data->account_length)
    {
        handleResponse(400,"account_length is not equal to student data.");

    }
    if($data->account_type!="college admin")
    {
        handleResponse(400,'account type is invalid');
    }
    $students=validateStdData($data->studData);
    
    
    // Define language codes
    $allowedLanguageCodes = ['c', 'cpp', 'java', 'python'];

    // Remove elements with empty strings or whitespace-only strings
    $filteredData = array_filter($data->purchased_language, function ($value)
    {
        if(trim($value)=='')
        {
            handleResponse(400, 'please give an array value');
        }
        return trim($value) !== '';    
    });

    // Check if the filtered array is empty
    if (empty($filteredData)) {
        handleResponse(400, 'please give an array value');
    }

    // Convert language codes to lowercase
    $filteredData = convertToLowercase($filteredData);

    // Validate language codes
    foreach ($filteredData as $languagescode) {
        if (!in_array($languagescode, $allowedLanguageCodes)) {
            handleResponse(400, 'invalid language code');
        }
    }
   
    // Initialize language enrollment and expiry dates
    $languageEnrollments = $languageExpiries = [];

    // Loop through language codes to assign enrollment and expiry dates
    foreach ($allowedLanguageCodes as $languageCode)
    {
        $languageEnrollments[$languageCode . '_enrollment_date'] = in_array($languageCode, $filteredData) ? $currentDateString: 0;
        
        $languageExpiries[$languageCode . '_expiry_date'] =in_array($languageCode, $filteredData)? ($languageEnrollments[$languageCode . '_enrollment_date'] == $currentDateString? $oneYearLaterString: 0): 0;       
    }
     
    return [
        'admin_id'=>$data->admin_id,
        'purchase_id'=>$data->purchase_id,
        'account_length'=>$data->account_length,
        'college_name'=>$data->college_name,
       'c_enrollment_date'=> $languageEnrollments['c_enrollment_date'],
       'cpp_enrollment_date'=> $languageEnrollments['cpp_enrollment_date'],
       'java_enrollment_date'=> $languageEnrollments['java_enrollment_date'],
       'python_enrollment_date'=> $languageEnrollments['python_enrollment_date'],
       'c_expiry_date'=> $languageExpiries['c_expiry_date'],
      'cpp_expiry_date'=>  $languageExpiries['cpp_expiry_date'],
       'java_expiry_date'=> $languageExpiries['java_expiry_date'],
       'python_expiry_date'=> $languageExpiries['python_expiry_date'],
        'students'=>$students,
       'account_type'=>$data->account_type
    ];
}
// college admin upcoming test
function validateCollegeAdminUpcomingTestData($data){
    $requiredFields = ['admin_id', 'test_id', 'test_name'];

    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }
    

    foreach ($requiredFields as $field)
    {
        // convert string into lower case 
        $data->{$field} = convertToLowercase($data->{$field});

        if ($field=='admin_id' && !isValidEmail($data->{$field}))
        {
            return['error'=>"invalid email address"];
        }

       // Validate if fields should not start or end with a space
       if (in_array($field, ['admin_id', 'test_id', 'test_name']) && hasLeadingOrTrailingSpace($data->{$field}))
       {
           return ['error' => "$field should not start or end with a space or special character."];
       } 
       $data->{$field}=convertToLowercase($data->{$field});    
    }
    return[
        'admin_id'=>$data->admin_id,
        'test_id'=>$data->test_id,
        'test_name'=>$data->test_name
    ];
}
// update test meta modify
function validateCollegeAdminUpdateTestMetaRegData($data)
{
    // echo "validateCollegeAdminUpdateTestMetaRegData";
    $requiredFields = ['id','admin_id', 'testName','test_id','topic', 'duration', 'total_qus', 'available_qus', 'start_time', 'end_time', 'questions', 'students', 'date'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }

    foreach ($requiredFields as $field) {

        if (in_array($field, ['id','admin_id', 'testName','test_id','topic', 'duration', 'total_qus', 'available_qus', 'questions', 'students'])) {
            // Check if the field exists in the data object
            if (isset($data->{$field})) {
                $fieldValue = $data->{$field};
    
                // Check if the field value is a string
                if (is_string($fieldValue) && hasLeadingOrTrailingSpace($fieldValue)) {
                    return ['error' => "$field should not start or end with a space or special character."];
                } 
            } else {
                return ['error' => "$field is required."];
            }
        }
        // convert string into lower case 
        $data->{$field} = convertToLowercase($data->{$field});

        if ($field === 'admin_id' && isValidEmail($data->admin_id) == false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        
        
        if ($field === 'duration' && validateTestDuration($data->duration) == false) {
            return ['error' => "Invalid test duration and give 15 mts greater than $field"];
        }

        if (in_array($field, ['total_qus', 'available_qus']) && validateNumericValues($data->total_qus, $data->available_qus)==false) {
            return ['error' => "Invalid values"];
        }

        // if (in_array($field, ['start_time', 'end_time']) && validate24HourTimeFormat($data->start_time, $data->end_time) == false) {
        //     return ['error' => "Invalid times"];
        // }
        if ($field === 'questions' && !is_array($data->questions)) {
            return ['error' => "Invalid data. Field 'questions' must be an array."];
        }

        if ($field === 'students' && !is_array($data->students)) {
            return ['error' => "Invalid data. Field 'students' must be an array."];
        }
    }

    if (!isDurationGreaterThan30Minutes($data->start_time, $data->end_time)) {
        handleResponse(400, "The duration between $data->start_time and $data->end_time is not greater than 15 minutes.");
    }
    
    $result = isStartTimeGreaterThanCurrentTimeToday($data->date, $data->start_time);

    if (!$result ) {
        handleResponse(400, "The given date is not greater than the given start time.");
    }  

    if (!isEndTimeWithinToday($data->end_time)) {
        handleResponse(400, "The end time $data->end_time is not within today and within the next 24 hours.");
    }

    // Example usage:
    if (!isEndTimeWithinTodayCheck($data->start_time, $data->end_time)) {
        handleResponse(400, "The time range is not valid for today and within the next 24 hours.");
    } 

    // testname
    $testName=validateAndModifyString($data->testName);
    // echo $testName;
    // !is_array($array) || empty($array)
    if(empty($data->students))
    {
        $students=0;
    }
    else
    {
        // Additional validations and filtering for students and questions arrays
        $students = validateArrayStudentData_updateTest($data->students);
        validateStatusValues_student($students);

    }
    // print_r($students);
    $questions = validateArrayQuestionData($data->questions);
    // Validate date format and compare with the current date
    $date=validateDateFormatAndPastDate($data->date);
    $dateTimeString = $date->format('Y-m-d');

    // Validate question keys, status values, answer validity, and options and qus
    validateQuestionKeys($questions);
    validateStatusValues_question($questions);
    validateQuestionOptionsAnswer($questions);
    validateQuestionOptionsandQus($questions);
    validateQuestionandAvailableQusLength($questions,$data->total_qus,$data->available_qus);

    return [
        "id"=>$data->id,
        "admin_id" => $data->admin_id,
        "test_name" => $testName,
        "test_id"=>$data->test_id,
        "topic"=>$data->topic,
        "duration"=>$data->duration,
        "total_qus"=> $data->total_qus,
        "available_qus"=> $data->available_qus,
        "start_time"=> $data->start_time,
        "end_time"=> $data->end_time,
        "questions"=> $questions,
        "students"=> $students,
        "date"=> $dateTimeString
    ];
}
function validateCollegeAdminUpdateTestInformation($data)
{
    // echo "hi";
    $requiredFields = ['admin_id', 'testName','test_id','topic', 'duration', 'total_qus', 'available_qus', 'start_time', 'end_time', 'date'];
    $commonValidationResult = commonValidation($data, $requiredFields);

    if (!empty($commonValidationResult)) {
        return $commonValidationResult;
    }

    foreach ($requiredFields as $field) {

        if (in_array($field, ['admin_id', 'testName','test_id','topic', 'duration', 'total_qus', 'available_qus'])) {
            // Check if the field exists in the data object
            if (isset($data->{$field})) {
                $fieldValue = $data->{$field};
    
                // Check if the field value is a string
                if (is_string($fieldValue) && hasLeadingOrTrailingSpace($fieldValue)) {
                    return ['error' => "$field should not start or end with a space or special character."];
                } 
            } else {
                return ['error' => "$field is required."];
            }
        }
        // convert string into lower case 
        $data->{$field} = convertToLowercase($data->{$field});

        if ($field === 'admin_id' && isValidEmail($data->admin_id) == false) {
            return ['error' => 'Invalid email address format or contains spaces.'];
        }
        
        
        if ($field === 'duration' && validateTestDuration($data->duration) == false) {
            return ['error' => "Invalid test duration and give 15 mts greater than $field"];
        }

        if (in_array($field, ['total_qus', 'available_qus']) && validateNumericValues($data->total_qus, $data->available_qus)==false) {
            return ['error' => "Invalid values"];
        }
       
        
    }
    $date=validateDateFormatAndPastDate($data->date);
    $dateTimeString = $date->format('Y-m-d');

    if (!isDurationGreaterThan30Minutes($data->start_time, $data->end_time)) {
        handleResponse(400, "The duration between $data->start_time and $data->end_time is not greater than 15 minutes.");
    }
    
    $result = isStartTimeGreaterThanCurrentTimeToday($data->date, $data->start_time);

    if (!$result ) {
        handleResponse(400, "The given date is not greater than the given start time.");
    }  

    if (!isEndTimeWithinToday($data->end_time)) {
        handleResponse(400, "The end time $data->end_time is not within today and within the next 24 hours.");
    }

    // Example usage:
    if (!isEndTimeWithinTodayCheck($data->start_time, $data->end_time)) {
        handleResponse(400, "The time range is not valid for today and within the next 24 hours.");
    } 

    // testname
    $testName=validateAndModifyString($data->testName);
    // echo $testName;

    // Set timezone to Asia/Kolkata
    // date_default_timezone_set('Asia/Kolkata');
    // $start_time =DateTime::createFromFormat('H:i:s', $data->start_time);
    // // Current time
    // $current_time = new DateTime();

   
    // // Check if the current time is before the start time
    // if ($current_time > $start_time) {

    //     // Access not allowed before the start time
    //     handleResponse(400, "Access not allowed start time.");
    //     // You may choose to redirect, display an error message, or take other actions here
    // } 
    //     // Validate date format and compare with the current date
    

   
    return [
        "admin_id" => $data->admin_id,
        "test_name" => $testName,
        "test_id"=>$data->test_id,
        "topic"=>$data->topic,
        "duration"=>$data->duration,
        "total_qus"=> $data->total_qus,
        "available_qus"=> $data->available_qus,
        "start_time"=> $data->start_time,
        "end_time"=> $data->end_time,       
        "date"=> $dateTimeString
    ];
}
// Constants
define('MAX_FIELD_LENGTH', 20);

// Helper functions
function hasLeadingOrTrailingSpace($value) {
    return strpos($value, ' ') === 0 || substr($value, -1) === ' ' || preg_match('/^[,.]/', $value);
}
// validate mobile number
function isValidMobileNumber($value) {
    return preg_match('/^\d{10}$/', $value);
}
// validate account 
function isValidAccountNumber($value) {
    return preg_match('/^[1-9]\d*$/', $value);
}
// Function to validate if an email is a valid Gmail address
function isValidGmail($email)
{
    // echo $email;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email) || !strpos($email, '@gmail.com')) {
       return false;
    }
    else{
        return true;
    }

}
// Function to validate email address format and check for spaces
function isValidEmail($email)
{
    // echo $email;
    return isValidGmail($email) && !strpos($email, ' ') ;
}
// check password length
function isValidPasswordLength($password)
{
    return strlen($password) >= 8;
}
// Common Validation Function for empty value
function commonValidation($data, $requiredFields) {
    foreach ($requiredFields as $field) {
        // echo $field;
        // if(isset($data->{$field}))
        // {
        //     echo "ok";
        // }
         if (!isset($data->{$field})) {
            return ['error' => "Invalid data. Required field '$field' is missing."];
        }
    }
    return [];
}
// comman Function to convert strings or array values to lowercase
function convertToLowercase($value)
{
    if (is_array($value)) {
        return array_map('convertToLowercase', $value);  // Recursively apply convertToLowercase to each element in the array
    } elseif (is_object($value)) {
        // If $value is an object, convert its properties to lowercase
        foreach ($value as $key => $property) {
            $value->$key = convertToLowercase($property);
        }
        return $value;
    } else {
        return strtolower($value);
    }
}
function validateAndModifyString($inputString) {
    if (strpos($inputString, ' ') !== false) {
        // String contains a space, replace it with underscore
        $modifiedString = str_replace(' ', '_', $inputString);
        return $modifiedString;
    } else {
        // String does not contain a space, return the same string
        return $inputString;
    }
}
function validateTestDuration($duration) {
    // echo $duration;
    $minAllowedMaxDuration = 15;
// echo  is_numeric($duration);
    // Check if the duration is a  not numeric value and greater than 30
   if (is_numeric($duration) && $duration >= $minAllowedMaxDuration) {
        // Validation successful
        return true;
    } else {
        // Validation failed
        return false;
    }
}
function validateNumericValues($totalQus, $availableAus) {
    // Check if both values contain only digits (numbers)
    if (ctype_digit((string) $totalQus) && ctype_digit((string) $availableAus)) {
        // Validation successful
        return true;
    }

    // Validation failed
    return false;
}
function validate24HourTimeFormat($startTime, $endTime) {
    $pattern = '/^(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]$/';

    if (preg_match($pattern, $startTime) && preg_match($pattern, $endTime)) {
        return true; // Both start and end times are in the correct format
    }

    return false; // Either start or end time is not in the correct format
}
// Function to handle errors and send response
function handleResponse($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(['error' => $message]);
    exit();
}
function validateStatusValues_question($questions) {
    foreach ($questions as $question) {
        if (!isset($question['status']) || !is_string($question['status']) || !in_array($question['status'], ['edit', 'remove', 'default','active'])) {
            handleResponse(400, 'Invalid question status values.');
        }
    }
}
function validateStatusValues_student($students) {
    // echo "hi";
    foreach ($students as $student) {
        // echo "hiiiiiiii".$student['id'];
        // Check if the status is either 'add' or 'remove'
        if ($student['status'] !== 'add' && $student['status'] !== 'remove') {
            handleResponse(400, 'Invalid student status value: ' . $student['status']);
        }
    }

}
function validateQuestionOptionsAnswer($questions) {
    foreach ($questions as $question) {
        if (!isset($question['answer']) || !in_array($question['answer'], [$question['option1'], $question['option2'], $question['option3'], $question['option4']])) {
            handleResponse(400, 'Answer is not valid.');
        }
    }
}
function validateQuestionOptionsandQus($questions) {
    foreach ($questions as $question) {
        if (empty($question['option1']) || empty($question['option2']) || empty($question['option3']) || empty($question['option4']) || empty($question['question'])) {
            handleResponse(400, 'Invalid options or question.');
        }
    }
}

function validateQuestionKeys($questions) {
    $allowedKeys = ['sno','question', 'option1', 'option2', 'option3', 'option4', 'answer', 'status'];

    foreach ($questions as $question) {
        // print_r($question);
        if (array_keys($question) !== $allowedKeys) {
            handleResponse(400, 'Invalid question keys.');
        }
    }
}
function validateArrayStudentData_insertTest($array) {

    // Assuming $array is an array of objects with "id" and "gmail" properties
    $getArray = $array;
    // Check if $getArray is an array and is not empty
    if (is_array($getArray) && !empty($getArray)) {
        // Extract the 'gmail' values from the students array
        $values = [];
    
        foreach ($getArray as $getvalue) {
    
            // Check if $student is an object with 'gmail' and 'id' properties
            if (is_object($getvalue) && isset($getvalue->gmail) && is_string($getvalue->gmail) && isset($getvalue->id) && is_numeric($getvalue->id) ) {
                $values[] = [
                    'gmail' => $getvalue->gmail,
                    'id'    => $getvalue->id,
                ];
            }
            if(!is_numeric($getvalue->id))
            {
                handleResponse(400, 'id must be number or digit');
            }
            if(!isset($getvalue->gmail)&&!isValidEmail($getvalue->gmail))
            {
                handleResponse(400, 'invalid gmail');
    
            }
            
            
        } 
    
        return $values;
    } else {
        handleResponse(400, 'Empty or missing "students" array');
    }    
}
function validateArrayStudentData_updateTest($array) {
// Check if $array is an array and is not empty
if (!is_array($array) || empty($array)) {
    handleResponse(400, 'Empty or missing "students" array');
}

// Extract the 'gmail', 'id', and 'std_status' values from the students array
$values = [];

foreach ($array as $student) {
    // echo $student->std_status;
    // Check if $student is an object with required properties
    if (!is_object($student) || !isset($student->gmail, $student->student_id, $student->std_status) ||
        !is_string($student->gmail) || !is_numeric($student->student_id) || !is_string($student->std_status)) {
        handleResponse(400, 'Invalid student data structure');
    }

    // Additional checks for specific properties
    if (!filter_var($student->gmail, FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $student->gmail) || !strpos($student->gmail, '@gmail.com')) {
        handleResponse(400,'invalid gmail address in student data');
     }

    if (!in_array($student->std_status, ['add', 'remove'])) {
        handleResponse(400, 'Invalid student status value: ' . $student->std_status);
    }

    // Add validated data to the values array
    $values[] = [
        'gmail' => $student->gmail,
        'id'    => $student->student_id,
        'status' => $student->std_status
    ];
}

return $values;    
}
function validateArrayQuestionData($array){
// Assuming $array is an array of objects with "id" and "gmail" properties
$getArray = $array;
// Check if $getArray is an array and is not empty
if (is_array($getArray) && !empty($getArray)) {
    // Extract the 'gmail' values from the students array
    $values = [];

    foreach ($getArray as $getvalue) {

        // Check if $student is an object with 'gmail' and 'id' properties
        if (is_object($getvalue) ) {
            $values[] = [
                'sno'=>$getvalue->sno,
                'question' => $getvalue->question,
                'option1'    => $getvalue->option1,
                'option2'    => $getvalue->option2,
                'option3'    => $getvalue->option3,
                'option4'    => $getvalue->option4,
                'answer'    => $getvalue->answer,
                'status'=>$getvalue->status

            ];
        }
        if(!is_string($getvalue->status))
        {
            handleResponse(400, 'status must be string');
        }
        
        
    } 

    return $values;
} else {
    handleResponse(400, 'Empty or missing "students" array');
}
}
function validateDateFormatAndPastDate($date) {
    // Create a DateTime object from the provided date
    $formattedDate = DateTime::createFromFormat('Y-m-d', $date);

    // Check if the date format is valid
    if (!$formattedDate || $formattedDate->format('Y-m-d') !== $date) {
        handleResponse(400, 'Invalid date. Please provide a date in the format YYYY-MM-DD.');
    }

    // Set the time component of both dates to midnight
    $formattedDate->setTime(0, 0, 0);
    $currentDate = new DateTime();
    $currentDate->setTime(0, 0, 0);

    // Compare the dates
    if ($formattedDate < $currentDate) {
        handleResponse(400, 'Invalid date. Please provide a date that is not in the past.');
    }

    // Return the validated date
    return $formattedDate;
}
function validate_sts_values($read_sts, $star_sts) {
    if (!in_array($read_sts, [0, 1]) || !in_array($star_sts, [0, 1])) {
        return false;
    } else {
        return true;
    }
}
function validateStdData($student)
{
    $getArray=$student;
    // // Check if $getArray is an array and is not empty
    if (is_array($getArray) && !empty($getArray)) {
        // Extract the 'gmail' values from the students array
        foreach ($getArray as $getvalue) {
            if(empty($getvalue->gmail)||empty($getvalue->dept)||empty($getvalue->year)||empty($getvalue->name))
            {
                handleResponse(400, 'required field');
               
            }
            // Check if $student is an object with 'gmail' and other properties
            if (is_object($getvalue) && isset($getvalue->gmail) && is_string($getvalue->gmail) && isset($getvalue->name) && is_string($getvalue->name) && isset($getvalue->dept) && is_string($getvalue->dept) && is_string($getvalue->deptFull) && isset($getvalue->deptFull) && isset($getvalue->year) && is_string($getvalue->year)) {
                $values[] = [
                    'gmail' => $getvalue->gmail,
                    'name'    => $getvalue->name,
                    'dept'    => $getvalue->dept,
                    'deptFull'=>$getvalue->deptFull,
                    'year'    => $getvalue->year

                ];
            }
           
                
         
                
            if(!isset($getvalue->gmail))
            {                
                handleResponse(400, 'gmail is empty');                
            }
            if(!isValidEmail($getvalue->gmail))
            {
                    handleResponse(400, 'invalid gmail');
            }
            
            
        } 
        return $values;
        
    }
    else {
        handleResponse(400, 'Empty or missing "students" array');
    }
}
function validateQuestionandAvailableQusLength($questions,$total_qus,$available_qus)
{
    // echo count($questions);
    if(!($total_qus<=count($questions)))
    {
        handleResponse(400,'total question is not equal to question length');
    }
    if(!($total_qus>=$available_qus))
    {
        handleResponse(400,'total question is not equal to available length');
    }
}
function isDurationGreaterThan30Minutes($start_time, $end_time) {
    // Convert start_time and end_time to DateTime objects
    $start_datetime = new DateTime($start_time);
    $end_datetime = new DateTime($end_time);

    // Calculate the duration
    $duration = $start_datetime->diff($end_datetime);

    // Check if the duration is greater than 30 minutes
    return ($duration->i >= 15 || $duration->h > 0);
}
function isStartTimeGreaterThanCurrentTimeToday($date, $start_time) {
    // Combine date and start time into a single string
    $combined_datetime = "$date $start_time";

    // Convert combined_datetime to DateTime object
    $combined_datetime_obj = new DateTime($combined_datetime);

    // Get the current date and time
    $current_datetime = new DateTime();
    // echo $combined_datetime_obj > $current_datetime;
    // Compare combined_datetime with current time
    return $combined_datetime_obj > $current_datetime;
}
function isEndTimeWithinToday($end_time) {
    // Convert end_time to DateTime object
    $end_datetime = new DateTime($end_time);

    // Get the current date
    $current_date = new DateTime();

    // Set the end time to the current date
    $end_datetime->setDate($current_date->format('Y'), $current_date->format('m'), $current_date->format('d'));

    // Compare end time with current time
    return $end_datetime <= $current_date->modify('+24 hours');
}
function isEndTimeWithinTodayCheck($start_time, $end_time) {
    // Convert start_time and end_time to DateTime objects
    $start_datetime = new DateTime($start_time);
    $end_datetime = new DateTime($end_time);

    // Get the current date
    $current_date = new DateTime();

    // Set the start and end times to the current date
    $start_datetime->setDate($current_date->format('Y'), $current_date->format('m'), $current_date->format('d'));
    $end_datetime->setDate($current_date->format('Y'), $current_date->format('m'), $current_date->format('d'));

    // Check if end time is less than start time or more than 24 hours ahead
    if ($end_datetime <= $start_datetime || $end_datetime > $start_datetime->modify('+24 hours')) {
        return false; // Invalid time range
    }

    return true; // Valid time range

}
?>

