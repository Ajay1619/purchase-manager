<?php
include_once('../../config/sparrow.php');
// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && $_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

    $ipAddress = getUserIP();

    //check empty fields
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields']);
        exit();
    }
    $login_procedure_params = [
        ['value' => $username, 'type' => 's']
    ];

    try {
        $result = callProcedure('validate_login', $login_procedure_params);
        if ($result && $result['data'][0]['status'] == 'error') {
            if ($result['data'][0]['code'] == 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid username']);
            }
        } else {
            if (verifyPassword($password, $result['data'][0]['employee_password'])) {
                $login_log_procedure_params = [
                    ['value' => $username, 'type' => 's'],
                    ['value' => $ipAddress, 'type' => 's']
                ];

                // Call the stored procedure
                $log_result = callProcedure('validate_and_log_login', $login_log_procedure_params);
                // Handle the log_result
                if ($log_result && $log_result['status'] == 'success') {
                    // Set session variables
                    $_SESSION['user_id'] = $log_result['data'][0]['user_id'];
                    $_SESSION['username'] = $log_result['data'][0]['username'];
                    $_SESSION['role_id'] = $log_result['data'][0]['role_id'];
                    $_SESSION['employee_name'] = $log_result['data'][0]['employee_name'];
                    $_SESSION['login_id'] = $log_result['data'][0]['login_id'];
                    $_SESSION['employee_pic'] = $log_result['data'][0]['employee_pic'];

                    echo json_encode(['status' => 'success', 'redirect' => BASEPATH . '/dashboard']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => $result['data'][0]['message']]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
            }
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
