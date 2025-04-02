<?php
include_once('../../../config/sparrow.php');
// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_no = sanitizeInput($_POST['reservation_no']);
    $name = sanitizeInput($_POST['name']);
    $surname = sanitizeInput($_POST['surname']);
    $first_name = sanitizeInput($_POST['first_name']);
    $birthday = sanitizeInput($_POST['birthday']);
    $anniversary = sanitizeInput($_POST['anniversary']);
    $nationality = sanitizeInput($_POST['nationality']);
    $employed_in_india = sanitizeInput($_POST['employed_in_india']);
    $mobile_no = sanitizeInput($_POST['mobile_no']);
    $email_id = sanitizeInput($_POST['email_id']);
    $phone = sanitizeInput($_POST['phone']);
    $address_street = sanitizeInput($_POST['address_street']);
    $address_locality = sanitizeInput($_POST['address_locality']);
    $address_city = sanitizeInput($_POST['address_city']);
    $address_district = sanitizeInput($_POST['address_district']);
    $address_state = sanitizeInput($_POST['address_state']);
    $adults = intval($_POST['adults']);
    $children = intval($_POST['children']);
    $extra_bed = intval($_POST['extra_bed']);
    $designation = sanitizeInput($_POST['designation']);
    $company = sanitizeInput($_POST['company']);
    $voucher_no = sanitizeInput($_POST['voucher_no']);
    $gstin = sanitizeInput($_POST['gstin']);
    $purpose_of_visit = sanitizeInput($_POST['purpose_of_visit']);
    $arrived_from = sanitizeInput($_POST['arrived_from']);
    $proceeding_to = sanitizeInput($_POST['proceeding_to']);
    $arrival_date_time = sanitizeInput($_POST['arrival_date_time']);
    $departure_date_time = sanitizeInput($_POST['departure_date_time']);
    $mode_of_payment = sanitizeInput($_POST['mode_of_payment']);
    $card_no = sanitizeInput($_POST['card_no']);
    $card_expiry_date = sanitizeInput($_POST['card_expiry_date']);
    $passport_no = sanitizeInput($_POST['passport_no']);
    $place_of_issue = sanitizeInput($_POST['place_of_issue']);
    $date_of_issue = sanitizeInput($_POST['date_of_issue']);
    $passport_expiry = sanitizeInput($_POST['passport_expiry']);
    $visa_type = sanitizeInput($_POST['visa_type']);
    $visa_expiry = sanitizeInput($_POST['visa_expiry']);


    $registration_procedure_params = [
        ['value' => $reservation_no, 'type' => 's'],
        ['value' => $name, 'type' => 's'],
        ['value' => $surname, 'type' => 's'],
        ['value' => $first_name, 'type' => 's'],
        ['value' => $birthday, 'type' => 's'],
        ['value' => $anniversary, 'type' => 's'],
        ['value' => $nationality, 'type' => 's'],
        ['value' => $employed_in_india, 'type' => 's'],
        ['value' => $mobile_no, 'type' => 's'],
        ['value' => $email_id, 'type' => 's'],
        ['value' => $phone, 'type' => 's'],
        ['value' => $address_street, 'type' => 's'],
        ['value' => $address_locality, 'type' => 's'],
        ['value' => $address_city, 'type' => 's'],
        ['value' => $address_district, 'type' => 's'],
        ['value' => $address_state, 'type' => 's'],
        ['value' => $adults, 'type' => 's'],
        ['value' => $children, 'type' => 's'],
        ['value' => $extra_bed, 'type' => 's'],
        ['value' => $designation, 'type' => 's'],
        ['value' => $voucher_no, 'type' => 's'],
        ['value' => $gstin, 'type' => 's'],
        ['value' => $purpose_of_visit, 'type' => 's'],
        ['value' => $proceeding_to, 'type' => 's'],
        ['value' => $arrival_date_time, 'type' => 's'],
        ['value' => $departure_date_time, 'type' => 's'],
        ['value' => $mode_of_payment, 'type' => 's'],
        ['value' => $card_no, 'type' => 's'],
        ['value' => $card_expiry_date, 'type' => 's'],
        ['value' => $passport_no, 'type' => 's'],
        ['value' => $place_of_issue, 'type' => 's'],
        ['value' => $date_of_issue, 'type' => 's'],
        ['value' => $passport_expiry, 'type' => 's'],
        ['value' => $visa_type, 'type' => 's'],
        ['value' => $visa_expiry, 'type' => 's'],

    ];

    try {
        // Call the stored procedure
        $result = callProcedure('insert_guest_room_registration', $registration_procedure_params);

        //Handle the result
        // if ($result && $result['status'] == 'success') {
        //     // Set session variables

        //     echo json_encode(['status' => 'success', 'redirect' => BASEPATH . '/dashboard']);
        // } else {
        //     echo json_encode(['status' => 'error', 'message' => $result['message']]);
        // }
    } catch (Exception $e) {
        //echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
