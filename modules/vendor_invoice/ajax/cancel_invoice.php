<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    $invoice_id = isset($_POST['invoice_id']) ? sanitizeInput($_POST['invoice_id'], 'int') : '';
    $procedure_params = [
        ['value' => $invoice_id, 'type' => 'i']
    ];
    $response = callProcedure('cancel_invoice', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Invoice Canceled Successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to Cancel Invoice']);
    }
}
