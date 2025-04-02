<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $quotation_id = $_POST['quotation_id'];
    $procedure_params = [
        ['value' => $quotation_id, 'type' => 'i']
    ];
    $response = callProcedure('cancel_quotation', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Quotation Canceled Successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to Cancel Quotation']);
    }
}
