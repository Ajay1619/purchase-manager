<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $purchase_order_id = $_POST['purchase_order_id'];
    $procedure_params = [
        ['value' => $purchase_order_id, 'type' => 'i']
    ];
    $response = callProcedure('cancel_purchase_order', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Purchase Order Canceled Successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to Cancel Purchase Order']);
    }
}
