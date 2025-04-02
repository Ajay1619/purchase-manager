<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Call the stored procedure for fetching role permission card details
        $response = callProcedure('fetch_role_permission_card_details');

        // Check if the response is successful
        if ($response['status'] == 'success') {
            // Process and return JSON response with role permission details
            $data = $response['data'][0]; // Assuming the data is in the first row

            $result = [
                'status' => 'success',
                'total_roles_count' => $data['TotalRoles'],
                'total_active_roles_count' => $data['TotalActiveRoles'],
                'total_inactive_roles_count' => $data['TotalInactiveRoles']
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch role permission details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
