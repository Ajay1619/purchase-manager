<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Call the stored procedure for fetching the last role code
        $response = callProcedure('fetch_pages_with_pre_role_code');
        // Check if the response is successful
        if ($response['status'] == 'success') {
            // Process and return JSON response with the new role code
            $new_role_code = $response['data'][0]['new_role_code']; // Assuming the data is in the first row
            $pages = $response['data'][1];
            $result = [
                'status' => 'success',
                'new_role_code' => $new_role_code,
                'pages' => $pages
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch last role code']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
