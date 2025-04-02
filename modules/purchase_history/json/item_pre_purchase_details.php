<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $vendor_id = sanitizeInput($_POST['vendor_id']);
    $product_id = sanitizeInput($_POST['item_id']);

    try {
        // Call the stored procedure
        $procedure_params = [
            ['value' => $vendor_id, 'type' => 'i'],
            ['value' => $product_id, 'type' => 'i']
        ];
        $result = callProcedure('item_pre_purchase_details', $procedure_params);

        // Handle the result
        if ($result && isset($result['data'][0])) {
            echo json_encode([
                'status' => 'success',
                'data' => $result['data'][0]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No data found'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
