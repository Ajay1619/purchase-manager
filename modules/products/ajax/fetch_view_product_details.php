<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Check if 'product_id' is set in GET parameters
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];

        // Prepare input parameters for the stored procedure or query
        $inputParams = [
            ['type' => 'i', 'value' => $product_id]
        ];

        // Call stored procedure to fetch product and item details
        try {
            $response = callProcedure('fetch_view_product_details', $inputParams);
            if ($response['status'] == 'success') {
                // Process and return JSON response with product and item details
                $productDetails = $response['data'][0]; // First result set is product details
                $itemDetails = $response['data'][1];    // Second result set is item details

                $result = [
                    'status' => 'success',
                    'product_details' => $productDetails,
                    'item_details' => $itemDetails
                ];
                echo json_encode($result);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch product details']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product ID is not provided']);
    }
} else {
    // Handle non-AJAX requests or direct access to this file
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
