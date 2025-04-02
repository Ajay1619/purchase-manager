<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        $response = callProcedure('fetch_inventory_card_details');

        if ($response['status'] == 'success') {

            // Process and return JSON response with product and item details
            $total_inventory_products_count = $response['data'][0]['total_inventory_products_count'];
            $total_in_stock_products_count = $response['data'][0]['total_in_stock_products_count'];
            $inventory_value = $response['data'][0]['inventory_value'];

            $result = [
                'status' => 'success',
                'total_inventory_products_count' => $total_inventory_products_count,
                'total_in_stock_products_count' => $total_in_stock_products_count,
                'inventory_value' => formatNumberIndian($inventory_value)
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendor details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
