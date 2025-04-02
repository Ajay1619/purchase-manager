<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $employee_id = $_POST['employee_id'];
    $procedure_params = [
        ['value' => $employee_id, 'type' => 'i']
    ];
    $response = callProcedure('delete_employee', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Employee deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete employee']);
    }
}
