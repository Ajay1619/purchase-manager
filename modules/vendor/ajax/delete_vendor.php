<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $vendor_id = $_POST['vendor_id'];
    $procedure_params = [
        ['value' => $vendor_id, 'type' => 'i']
    ];
    $response = callProcedure('delete_vendor', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Vendor deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete Vendor']);
    }
}
