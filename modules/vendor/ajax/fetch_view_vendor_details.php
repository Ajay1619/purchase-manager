<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Check if 'vendor_id' is set in GET parameters
    if (isset($_GET['vendor_id'])) {
        $vendor_id = $_GET['vendor_id'];

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 'i', 'value' => $vendor_id]
        ];

        // Call stored procedure to fetch vendor details
        try {
            $response = callProcedure('fetch_vendor_details', $inputParams);
            if ($response['status'] == 'success') {
                // Process and return JSON response with vendor details
                $vendorDetails = $response['data']; // Result set contains vendor details

                $result = [
                    'status' => 'success',
                    'vendor_details' => $vendorDetails
                ];
                echo json_encode($result);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendor details']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Vendor ID is not provided']);
    }
} else {
    // Handle non-AJAX requests or direct access to this file
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
