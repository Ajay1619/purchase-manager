<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Call the stored procedure
        $response = callProcedure('fetch_customer_card_details');

        // Check if the response is successful
        if ($response['status'] == 'success') {
            // Process and return JSON response with customer details
            $data = $response['data'][0]; // Assuming the data is in the first row

            $result = [
                'status' => 'success',
                'total_customers_count' => $data['TotalCustomers'],
                'total_active_customers_count' => $data['TotalActiveCustomers'],
                'total_inactive_customers_count' => $data['TotalInactiveCustomers']
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch customer details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
