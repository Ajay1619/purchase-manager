<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Check if 'customer_id' is set in GET parameters
    if (isset($_GET['customer_id'])) {
        $customer_id = $_GET['customer_id']; // Ensure the customer_id is an integer

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 'i', 'value' => $customer_id]
        ];

        // Call stored procedure to fetch customer details
        try {
            // Function call to execute the stored procedure
            $response = callProcedure('fetch_view_customer_details', $inputParams);

            if ($response['status'] == 'success') {
                // Process and return JSON response with customer details
                $customerDetails = $response['data'][0]; // The result set of the customer details

                $result = [
                    'status' => 'success',
                    'customer_details' => $customerDetails
                ];
                echo json_encode($result);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch customer details']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Customer ID is not provided']);
    }
} else {
    // Handle non-AJAX requests or direct access to this file
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
