<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $role_id = $_POST['role_id'];
    $procedure_params = [
        ['value' => $role_id, 'type' => 'i']
    ];
    $response = callProcedure('delete_role', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'role deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete role']);
    }
}
