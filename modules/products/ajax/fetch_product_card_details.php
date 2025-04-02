<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        $response = callProcedure('fetch_product_card_details');

        if ($response['status'] == 'success') {
            // Process and return JSON response with product and item details
            $total_products_count = $response['data'][0]['TotalProducts'];
            $total_active_products_count = $response['data'][0]['TotalActiveProducts'];
            $total_inactive_products_count = $response['data'][0]['TotalInactiveProducts'];

            $result = [
                'status' => 'success',
                'total_products_count' => $total_products_count,
                'total_active_products_count' => $total_active_products_count,
                'total_inactive_products_count' => $total_inactive_products_count
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch product details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
