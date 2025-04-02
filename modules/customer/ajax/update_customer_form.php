<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Retrieve and sanitize form data
    $customer_id = isset($_POST['customer-id']) ? sanitizeInput($_POST['customer-id'], 'int') : '';
    $salutation = isset($_POST['salutation']) ? sanitizeInput($_POST['salutation'], 'string') : '';
    $customer_name = isset($_POST['customer-name']) ? sanitizeInput($_POST['customer-name'], 'string') : '';
    $contact_number = isset($_POST['contact-number']) ? sanitizeInput($_POST['contact-number'], 'string') : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email'], 'email') : '';
    $gstin = isset($_POST['gstin']) ? sanitizeInput($_POST['gstin'], 'string') : '';

    // Address fields
    $street = isset($_POST['street']) ? sanitizeInput($_POST['street'], 'string') : '';
    $locality = isset($_POST['locality']) ? sanitizeInput($_POST['locality'], 'string') : '';
    $pincode = isset($_POST['pincode']) ? sanitizeInput($_POST['pincode'], 'string') : '';
    $city = isset($_POST['city']) ? sanitizeInput($_POST['city'], 'string') : '';
    $district = isset($_POST['district']) ? sanitizeInput($_POST['district'], 'string') : '';
    $state = isset($_POST['state']) ? sanitizeInput($_POST['state'], 'string') : '';
    $country = isset($_POST['country']) ? sanitizeInput($_POST['country'], 'string') : '';

    // Created by user ID from session
    $created_by = $_SESSION['user_id'];

    // Validate required fields
    $errors = [];
    $errors[] = checkEmptyField($customer_id, "Customer ID");
    $errors[] = checkEmptyField($customer_name, "Customer Name");
    $errors[] = checkEmptyField($contact_number, "Contact Number");
    $errors[] = checkEmptyField($email, "Email ID");
    $errors[] = checkEmptyField($gstin, "GSTIN");
    $errors[] = checkEmptyField($street, "Street");
    $errors[] = checkEmptyField($locality, "Locality");
    $errors[] = checkEmptyField($pincode, "Pin Code");
    $errors[] = checkEmptyField($city, "City");
    $errors[] = checkEmptyField($district, "District");
    $errors[] = checkEmptyField($state, "State");
    $errors[] = checkEmptyField($country, "Country");

    // Remove empty errors
    $errors = array_filter($errors);

    // Validate email format
    if (isEmail($email) == false) {
        $errors[] = "Invalid Email ID";
    }

    // Validate phone number format (you may need a custom validation function for this)
    if (isPhoneNumber($contact_number) == false) {
        $errors[] = "Invalid Contact Number";
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
    } else {
        // Prepare parameters for the procedure
        $procedure_params = [
            ['value' => $customer_id, 'type' => 'i'],
            ['value' => $salutation, 'type' => 's'],
            ['value' => $customer_name, 'type' => 's'],
            ['value' => $contact_number, 'type' => 's'],
            ['value' => $email, 'type' => 's'],
            ['value' => $gstin, 'type' => 's'],
            ['value' => $street, 'type' => 's'],
            ['value' => $locality, 'type' => 's'],
            ['value' => $pincode, 'type' => 's'],
            ['value' => $city, 'type' => 's'],
            ['value' => $district, 'type' => 's'],
            ['value' => $state, 'type' => 's'],
            ['value' => $country, 'type' => 's'],
            ['value' => $created_by, 'type' => 'i']
        ];

        try {
            // Call the stored procedure
            $result = callProcedure('update_customer', $procedure_params);
            echo json_encode(['status' => 'success', 'message' => 'Customer updated successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
