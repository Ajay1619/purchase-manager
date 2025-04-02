<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $customer_id = $_POST['customer_id'];
    $procedure_params = [
        ['value' => $customer_id, 'type' => 'i']
    ];
    $response = callProcedure('delete_customer', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Customer deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete Customer']);
    }
}
