<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Retrieve form data
    $vendor_id = isset($_POST['vendor-id']) ? sanitizeInput($_POST['vendor-id'], 'int') : '';
    $salutation = isset($_POST['salutation']) ? sanitizeInput($_POST['salutation'], 'string') : '';
    $company_name = isset($_POST['company-name']) ? sanitizeInput($_POST['company-name'], 'string') : '';
    $contact_name = isset($_POST['contact-name']) ? sanitizeInput($_POST['contact-name'], 'string') : '';
    $contact_number = isset($_POST['contact-number']) ? sanitizeInput($_POST['contact-number'], 'string') : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email'], 'email') : '';

    $billing_street = isset($_POST['billing-street']) ? sanitizeInput($_POST['billing-street'], 'string') : '';
    $billing_locality = isset($_POST['billing-locality']) ? sanitizeInput($_POST['billing-locality'], 'string') : '';
    $billing_pincode = isset($_POST['billing-pincode']) ? sanitizeInput($_POST['billing-pincode'], 'string') : '';
    $billing_city = isset($_POST['billing-city']) ? sanitizeInput($_POST['billing-city'], 'string') : '';
    $billing_district = isset($_POST['billing-district']) ? sanitizeInput($_POST['billing-district'], 'string') : '';
    $billing_state = isset($_POST['billing-state']) ? sanitizeInput($_POST['billing-state'], 'string') : '';
    $billing_country = isset($_POST['billing-country']) ? sanitizeInput($_POST['billing-country'], 'string') : '';

    $shipping_street = isset($_POST['shipping-street']) ? sanitizeInput($_POST['shipping-street'], 'string') : '';
    $shipping_locality = isset($_POST['shipping-locality']) ? sanitizeInput($_POST['shipping-locality'], 'string') : '';
    $shipping_pincode = isset($_POST['shipping-pincode']) ? sanitizeInput($_POST['shipping-pincode'], 'string') : '';
    $shipping_city = isset($_POST['shipping-city']) ? sanitizeInput($_POST['shipping-city'], 'string') : '';
    $shipping_district = isset($_POST['shipping-district']) ? sanitizeInput($_POST['shipping-district'], 'string') : '';
    $shipping_state = isset($_POST['shipping-state']) ? sanitizeInput($_POST['shipping-state'], 'string') : '';
    $shipping_country = isset($_POST['shipping-country']) ? sanitizeInput($_POST['shipping-country'], 'string') : '';

    $gstin = isset($_POST['gstin']) ? sanitizeInput($_POST['gstin'], 'string') : '';
    $pan = isset($_POST['pan']) ? sanitizeInput($_POST['pan'], 'string') : '';
    $bank_name = isset($_POST['bank-name']) ? sanitizeInput($_POST['bank-name'], 'string') : '';
    $account_number = isset($_POST['account-number']) ? sanitizeInput($_POST['account-number'], 'string') : '';
    $ifsc_code = isset($_POST['ifsc-code']) ? sanitizeInput($_POST['ifsc-code'], 'string') : '';
    $branch_name = isset($_POST['branch-name']) ? sanitizeInput($_POST['branch-name'], 'string') : '';

    // Check for empty fields
    $errors = [];
    $errors[] = checkEmptyField($vendor_id, "Vendor ID");
    $errors[] = checkEmptyField($company_name, "Company Name");
    $errors[] = checkEmptyField($contact_name, "Contact Name");
    $errors[] = checkEmptyField($contact_number, "Contact Number");
    $errors[] = checkEmptyField($email, "Email ID");
    $errors[] = checkEmptyField($billing_street, "Billing Street");
    $errors[] = checkEmptyField($billing_locality, "Billing Locality");
    $errors[] = checkEmptyField($billing_pincode, "Billing Pin Code");
    $errors[] = checkEmptyField($billing_city, "Billing City");
    $errors[] = checkEmptyField($billing_district, "Billing District");
    $errors[] = checkEmptyField($billing_state, "Billing State");
    $errors[] = checkEmptyField($billing_country, "Billing Country");
    $errors[] = checkEmptyField($shipping_street, "Shipping Street");
    $errors[] = checkEmptyField($shipping_locality, "Shipping Locality");
    $errors[] = checkEmptyField($shipping_pincode, "Shipping Pin Code");
    $errors[] = checkEmptyField($shipping_city, "Shipping City");
    $errors[] = checkEmptyField($shipping_district, "Shipping District");
    $errors[] = checkEmptyField($shipping_state, "Shipping State");
    $errors[] = checkEmptyField($shipping_country, "Shipping Country");
    $errors[] = checkEmptyField($gstin, "GSTIN");
    $errors[] = checkEmptyField($pan, "PAN");
    $errors[] = checkEmptyField($bank_name, "Bank Name");
    $errors[] = checkEmptyField($account_number, "Account Number");
    $errors[] = checkEmptyField($ifsc_code, "IFSC Code");
    $errors[] = checkEmptyField($branch_name, "Branch Name");

    if (isEmail($email) == false) {
        $errors[] = "Invalid Email ID";
    }

    if (isPhoneNumber($contact_number) == false) {
        $errors[] = "Invalid Contact Number";
    }

    $company_name = capitalizeFirstLetter($company_name);
    $contact_name = capitalizeFirstLetter($contact_name);
    // Remove empty errors
    $errors = array_filter($errors);

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
    } else {
        // Prepare the parameters for the procedure
        $procedure_params = [
            ['value' => $vendor_id, 'type' => 'i'],
            ['value' => $salutation, 'type' => 's'],
            ['value' => $company_name, 'type' => 's'],
            ['value' => $contact_name, 'type' => 's'],
            ['value' => $contact_number, 'type' => 's'],
            ['value' => $email, 'type' => 's'],
            ['value' => $billing_street, 'type' => 's'],
            ['value' => $billing_locality, 'type' => 's'],
            ['value' => $billing_city, 'type' => 's'],
            ['value' => $billing_district, 'type' => 's'],
            ['value' => $billing_state, 'type' => 's'],
            ['value' => $billing_country, 'type' => 's'],
            ['value' => $billing_pincode, 'type' => 's'],
            ['value' => $shipping_street, 'type' => 's'],
            ['value' => $shipping_locality, 'type' => 's'],
            ['value' => $shipping_city, 'type' => 's'],
            ['value' => $shipping_district, 'type' => 's'],
            ['value' => $shipping_state, 'type' => 's'],
            ['value' => $shipping_country, 'type' => 's'],
            ['value' => $shipping_pincode, 'type' => 's'],
            ['value' => $gstin, 'type' => 's'],
            ['value' => $pan, 'type' => 's'],
            ['value' => $bank_name, 'type' => 's'],
            ['value' => $account_number, 'type' => 's'],
            ['value' => $ifsc_code, 'type' => 's'],
            ['value' => $branch_name, 'type' => 's']
        ];

        try {
            $result = callProcedure('update_vendor', $procedure_params);
            echo json_encode(['status' => 'success', 'message' => 'Vendor updated successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
