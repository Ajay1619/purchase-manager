<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $employee_id = $_POST['employee_id'];
    $status = $_POST['status'];
    if ($status == 'true') {
        $status = 1;
    } else {
        $status = 0;
    }
    $procedure_params = [
        ['value' => $employee_id, 'type' => 'i'],
        ['value' => $status, 'type' => 'i']
    ];
    $response = callProcedure('update_employee_status', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Employee status updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update employee status']);
    }
}
