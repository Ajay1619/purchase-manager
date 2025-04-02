<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $role_id = $_POST['role_id'];
    $status = $_POST['status'];
    if ($status == 'true') {
        $status = 1;
    } else {
        $status = 0;
    }
    $procedure_params = [
        ['value' => $role_id, 'type' => 'i'],
        ['value' => $status, 'type' => 'i']
    ];
    $response = callProcedure('update_role_status', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'role status updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update role status']);
    }
}
