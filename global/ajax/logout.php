<?php
include_once('../../config/sparrow.php');
// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && $_SERVER['REQUEST_METHOD'] == 'POST') {


    // Unset session variables
    // unset($_SESSION['user_id']);
    // unset($_SESSION['username']);
    // unset($_SESSION['role']);
    // unset($_SESSION['employee_name']);
    // unset($_SESSION['login_id']);

    // Alternatively, you can unset all session variables at once
    // session_unset();

    //call logout procedure
    $logout_procedure_params = [
        ['value' => $login_id, 'type' => 'i']
    ];
    $result = callProcedure('logout', $logout_procedure_params);
    if ($result['data'][0]['result'] == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Logout failed']);
        exit;
    } else {
        // Optionally, destroy the session completely
        session_destroy();

        //result
        echo json_encode(['status' => 'success', 'message' => 'Logout successful']);
    }

    exit; // Make sure to exit to prevent further output
}
