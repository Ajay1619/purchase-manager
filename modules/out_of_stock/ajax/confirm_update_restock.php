<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $out_of_stock_id = isset($_POST['out_of_stock_id']) ? sanitizeInput($_POST['out_of_stock_id'], 'int') : '';
    $update = isset($_POST['update']) ? sanitizeInput($_POST['update'], 'int') : '';
    try {
        // Prepare the procedure parameters
        $procedure_params = [
            ['value' => $out_of_stock_id, 'type' => 'i'],
            ['value' => $update, 'type' => 'i'],
        ];

        // Call the stored procedure to cancel the restock
        $response = callProcedure('update_restock', $procedure_params);
        if ($response['status'] == 'success') {
            echo json_encode(['status' => 'success', 'messages' => 'Out of stock status updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'messages' => 'Error updating out-of-stock status']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
