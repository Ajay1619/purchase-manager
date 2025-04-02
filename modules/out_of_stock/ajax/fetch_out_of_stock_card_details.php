<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        $response = callProcedure('fetch_out_of_stock_card_details');

        if ($response['status'] == 'success') {
            // Process and return JSON response with product and item details
            $OutOfStockCount = $response['data'][0]['OutOfStockCount'];
            $CanceledOutOfStockCount = $response['data'][0]['CanceledOutOfStockCount'];

            $result = [
                'status' => 'success',
                'OutOfStockCount' => $OutOfStockCount,
                'CanceledOutOfStockCount' => $CanceledOutOfStockCount
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendor details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
