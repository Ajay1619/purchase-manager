<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Call the stored procedure for fetching purchase history details
        $response = callProcedure('fetch_employee_card_details');

        // Check if the response is successful
        if ($response['status'] == 'success') {
            // Process and return JSON response with purchase history details
            $data = $response['data'][0]; // Assuming the data is in the first row

            $result = [
                'status' => 'success',
                'total_employee_count' => $data['TotalEmployees'],
                'total_active_employee_count' => $data['TotalActiveEmployees'],
                'total_inactive_employee_count' => $data['TotalInactiveEmployees'],
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch purchase history details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
