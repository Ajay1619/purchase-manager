<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $vendor_id = $_POST['vendor_id'];
    $status = $_POST['status'];
    if ($status == 'true') {
        $status = 1;
    } else {
        $status = 0;
    }
    $procedure_params = [
        ['value' => $vendor_id, 'type' => 'i'],
        ['value' => $status, 'type' => 'i']
    ];
    $response = callProcedure('update_vendor_status', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Vendor status updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update Vendor status']);
    }
}
