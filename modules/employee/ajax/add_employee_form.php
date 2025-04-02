<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Retrieve form data
    $employeeId = isset($_POST['employee-id']) ? sanitizeInput($_POST['employee-id'], 'string') : '';
    $employeeName = isset($_POST['employee-name']) ? sanitizeInput($_POST['employee-name'], 'string') : '';
    $employeeDob = isset($_POST['employee-dob']) ? sanitizeInput($_POST['employee-dob'], 'string') : '';
    $employeePhoto = isset($_FILES['employee-photo']) ? $_FILES['employee-photo'] : null;

    $contactNumber = isset($_POST['contact-number']) ? sanitizeInput($_POST['contact-number'], 'string') : '';
    $emergencyContactNumber = isset($_POST['emergency-contact-number']) ? sanitizeInput($_POST['emergency-contact-number'], 'string') : '';
    $emailId = isset($_POST['email-id']) ? sanitizeInput($_POST['email-id'], 'email') : '';

    $street = isset($_POST['street']) ? sanitizeInput($_POST['street'], 'string') : '';
    $locality = isset($_POST['locality']) ? sanitizeInput($_POST['locality'], 'string') : '';
    $pincode = isset($_POST['pincode']) ? sanitizeInput($_POST['pincode'], 'string') : '';
    $district = isset($_POST['district']) ? sanitizeInput($_POST['district'], 'string') : '';
    $state = isset($_POST['state']) ? sanitizeInput($_POST['state'], 'string') : '';

    $designation = isset($_POST['designation']) ? sanitizeInput($_POST['designation'], 'string') : '';
    $role = isset($_POST['role']) ? sanitizeInput($_POST['role'], 'string') : '';
    $joinedDate = isset($_POST['joined-date']) ? sanitizeInput($_POST['joined-date'], 'string') : '';
    $username = isset($_POST['username']) ? sanitizeInput($_POST['username'], 'string') : '';
    $password = isset($_POST['password']) ? sanitizeInput($_POST['password'], 'string') : '';

    $createdBy = $_SESSION['user_id']; // Assuming user ID is stored in session

    // Check for empty fields
    $errors = [];
    $errors[] = checkEmptyField($employeeName, "Employee Name");
    $errors[] = checkEmptyField($employeeDob, "Employee DOB");
    $errors[] = checkEmptyField($contactNumber, "Contact Number");
    $errors[] = checkEmptyField($emailId, "Email ID");
    $errors[] = checkEmptyField($street, "Street");
    $errors[] = checkEmptyField($locality, "Locality");
    $errors[] = checkEmptyField($pincode, "Pincode");
    $errors[] = checkEmptyField($district, "District");
    $errors[] = checkEmptyField($state, "State");
    $errors[] = checkEmptyField($designation, "Designation");
    $errors[] = checkEmptyField($role, "Role");
    $errors[] = checkEmptyField($joinedDate, "Joined Date");
    $errors[] = checkEmptyField($username, "Username");
    $errors[] = checkEmptyField($password, "Password");

    if (!isEmail($emailId)) {
        $errors[] = "Invalid Email ID";
    }

    if (!isPhoneNumber($contactNumber)) {
        $errors[] = "Invalid Contact Number";
    }

    // Remove empty errors
    $errors = array_filter($errors);

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
    } else {
        // Handle file upload for the profile picture
        $employeePhotoPath = '';

        if (isset($_FILES['employee-photo']) && $_FILES['employee-photo']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = ROOT . '/global/files/profile_pictures'; // Change to your upload directory
            $uploadResult = uploadFile($_FILES['employee-photo'], $uploadDir, 'employee_photo_' . $employeeId);
            if ($uploadResult['status'] == 'success' || $uploadResult['status'] == 'warning') {
                // Get the first uploaded file path
                $employeePhotoPath = $uploadResult['files'][0];
            } else {
                echo json_encode(['status' => 'error', 'message' => $uploadResult['message']]);
                exit;
            }
        }


        // Prepare the parameters for the procedure
        $procedure_params = [
            ['value' => $employeeName, 'type' => 's'], // p_employee_name
            ['value' => $employeeDob, 'type' => 's'], // p_employee_dob
            ['value' => $employeePhotoPath, 'type' => 's'], // p_employee_photo
            ['value' => $contactNumber, 'type' => 's'], // p_employee_contact_number
            ['value' => $emergencyContactNumber, 'type' => 's'], // p_employee_emergency_contact_number
            ['value' => $emailId, 'type' => 's'], // p_employee_email_id
            ['value' => $street, 'type' => 's'], // p_employee_street
            ['value' => $locality, 'type' => 's'], // p_employee_locality
            ['value' => $district, 'type' => 's'], // p_employee_district
            ['value' => $state, 'type' => 's'], // p_employee_state
            ['value' => $pincode, 'type' => 's'], // p_employee_pincode
            ['value' => $joinedDate, 'type' => 's'], // p_employee_joined_date
            ['value' => $designation, 'type' => 's'], // p_employee_designation
            ['value' => $role, 'type' => 'i'], // p_employee_role_id
            ['value' => $username, 'type' => 's'], // p_employee_username
            ['value' => hashPassword($password), 'type' => 's'], // p_employee_password
            ['value' => $createdBy, 'type' => 'i'] // p_created_by
        ];

        try {
            $result = callProcedure('insert_employee', $procedure_params); // Ensure you have this stored procedure created
            echo json_encode(['status' => 'success', 'message' => 'Employee added successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
