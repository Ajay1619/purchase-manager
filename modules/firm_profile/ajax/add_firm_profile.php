<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Retrieve form data
    $firm_name = isset($_POST['firm-name']) ? sanitizeInput($_POST['firm-name'], 'string') : '';
    $registration_number = isset($_POST['registration-number']) ? sanitizeInput($_POST['registration-number'], 'string') : '';
    $phone_number = isset($_POST['contact-number']) ? sanitizeInput($_POST['contact-number'], 'string') : '';
    $email_id = isset($_POST['email-id']) ? sanitizeInput($_POST['email-id'], 'email') : '';
    $street = isset($_POST['street']) ? sanitizeInput($_POST['street'], 'string') : '';
    $locality = isset($_POST['locality']) ? sanitizeInput($_POST['locality'], 'string') : '';
    $city = isset($_POST['city']) ? sanitizeInput($_POST['city'], 'string') : '';
    $district = isset($_POST['district']) ? sanitizeInput($_POST['district'], 'string') : '';
    $state = isset($_POST['state']) ? sanitizeInput($_POST['state'], 'string') : '';
    $country = isset($_POST['country']) ? sanitizeInput($_POST['country'], 'string') : '';
    $pin_code = isset($_POST['pincode']) ? sanitizeInput($_POST['pincode'], 'string') : '';
    $gstin = isset($_POST['gstin']) ? sanitizeInput($_POST['gstin'], 'string') : '';
    $pan = isset($_POST['pan']) ? sanitizeInput($_POST['pan'], 'string') : '';
    $tax_registration_number = isset($_POST['tax-registration-number']) ? sanitizeInput($_POST['tax-registration-number'], 'string') : '';
    $default_tax_percentage = isset($_POST['default-tax-percentage']) ? sanitizeInput($_POST['default-tax-percentage'], 'float') : 0.00;
    $bank_name = isset($_POST['bank-name']) ? sanitizeInput($_POST['bank-name'], 'string') : '';
    $account_number = isset($_POST['account-number']) ? sanitizeInput($_POST['account-number'], 'string') : '';
    $ifsc_code = isset($_POST['ifsc-code']) ? sanitizeInput($_POST['ifsc-code'], 'string') : '';
    $bank_branch = isset($_POST['bank-branch']) ? sanitizeInput($_POST['bank-branch'], 'string') : '';
    $invoice_terms_and_conditions = 'Your invoice terms here'; // Static for now

    // Check for empty fields (validation)
    $errors = [];
    $errors[] = checkEmptyField($firm_name, "Firm Name");
    $errors[] = checkEmptyField($registration_number, "Registration Number");
    $errors[] = checkEmptyField($phone_number, "Phone Number");
    $errors[] = checkEmptyField($email_id, "Email ID");
    $errors[] = checkEmptyField($street, "Street");
    $errors[] = checkEmptyField($locality, "Locality");
    $errors[] = checkEmptyField($city, "City");
    $errors[] = checkEmptyField($district, "District");
    $errors[] = checkEmptyField($state, "State");
    $errors[] = checkEmptyField($pin_code, "Pin Code");
    $errors[] = checkEmptyField($gstin, "GSTIN");
    $errors[] = checkEmptyField($pan, "PAN");
    $errors[] = checkEmptyField($tax_registration_number, "Tax Registration Number");
    $errors[] = checkEmptyField($default_tax_percentage, "Default Tax Percentage");
    $errors[] = checkEmptyField($bank_name, "Bank Name");
    $errors[] = checkEmptyField($account_number, "Account Number");
    $errors[] = checkEmptyField($ifsc_code, "IFSC Code");
    $errors[] = checkEmptyField($bank_branch, "Bank Branch");

    // Handle file upload for the logo.
    $logoPath = '';
    if (isset($_POST['previous-logo']) && !empty($_POST['previous-logo'])) {
        $previous_logo = sanitizeInput($_POST['previous-logo'], 'string');
        $logoPath = $previous_logo;
    } else {

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {

            $uploadDir = ROOT . '/global/files/logo';
            $uploadResult = uploadFile($_FILES['logo'], $uploadDir, 'firm_logo_');
            if ($uploadResult['status'] == 'success' || $uploadResult['status'] == 'warning') {
                // Get the first uploaded file path
                $logoPath = $uploadResult['files'][0];
            } else {
                echo json_encode(['status' => 'error', 'message' => $uploadResult['message']]);
                exit;
            }
        }
    }
    // Validation for email and phone
    if (!isEmail($email_id)) {
        $errors[] = "Invalid Email ID";
    }

    if (!isPhoneNumber($phone_number)) {
        $errors[] = "Invalid Phone Number";
    }

    // Remove empty errors
    $errors = array_filter($errors);

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
    } else {
        // Prepare the parameters for the procedure
        $procedure_params = [
            ['value' => $firm_name, 'type' => 's'],
            ['value' => $registration_number, 'type' => 's'],
            ['value' => $logoPath, 'type' => 's'],  // Pass the logo path here
            ['value' => $phone_number, 'type' => 's'],
            ['value' => $email_id, 'type' => 's'],
            ['value' => $street, 'type' => 's'],
            ['value' => $locality, 'type' => 's'],
            ['value' => $city, 'type' => 's'],
            ['value' => $district, 'type' => 's'],
            ['value' => $state, 'type' => 's'],
            ['value' => $country, 'type' => 's'],
            ['value' => $pin_code, 'type' => 's'],
            ['value' => $gstin, 'type' => 's'],
            ['value' => $pan, 'type' => 's'],
            ['value' => $tax_registration_number, 'type' => 's'],
            ['value' => $default_tax_percentage, 'type' => 'd'],
            ['value' => $bank_name, 'type' => 's'],
            ['value' => $account_number, 'type' => 's'],
            ['value' => $ifsc_code, 'type' => 's'],
            ['value' => $bank_branch, 'type' => 's'],
            ['value' => $invoice_terms_and_conditions, 'type' => 's']
        ];
        // Call the procedure to update the firm profile
        try {
            $result = callProcedure('update_inv_firm_profile', $procedure_params);
            echo json_encode(['status' => 'success', 'message' => 'Firm profile updated successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
