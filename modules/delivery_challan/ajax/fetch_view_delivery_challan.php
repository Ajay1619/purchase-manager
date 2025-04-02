<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Check if 'dc_id' is set in GET parameters
    if (isset($_GET['dc_id'])) {
        $dc_id = $_GET['dc_id'];

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 'i', 'value' => $dc_id]
        ];

        // Call stored procedure to fetch purchase order details and items
        try {
            $response = callProcedure('fetch_delivery_challan_details', $inputParams);
            if ($response['status'] == 'success') {
                // Fetch result sets from response
                $results = $response['data'];
                $poDetails = $results[0]; // First result set is purchase order details
                $itemDetails = $results[1]; // Second result set is item details
                $poDetails['delivery_challan_date'] = date(DATE_FORMAT, strtotime($poDetails['delivery_challan_date']));


                $result = [
                    'status' => 'success',
                    'delivery_challan_details' => $poDetails,
                    'item_details' => $itemDetails
                ];
                echo json_encode($result);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch purchase order details']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Purchase Order ID is not provided']);
    }
} else {
    // Handle non-AJAX requests or direct access to this file
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
