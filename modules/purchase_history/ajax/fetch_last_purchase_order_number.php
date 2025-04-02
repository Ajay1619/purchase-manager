<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Call the stored procedure for fetching purchase history details
        $response = callProcedure('fetch_last_purchase_order_number');

        // Check if the response is successful
        if ($response['status'] == 'success') {
            // Process and return JSON response with purchase history details
            $data = $response['data'][0]; // Assuming the data is in the first row

            $result = [
                'status' => 'success',
                'new_purchase_order_number' => $data['new_purchase_order_number']
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch purchase history details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
