<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        $response = callProcedure('fetch_vendor_card_details');

        if ($response['status'] == 'success') {

            // Process and return JSON response with product and item details
            $total_vendors_count = $response['data'][0]['TotalVendors'];
            $total_active_vendors_count = $response['data'][0]['TotalActiveVendors'];
            $total_inactive_vendors_count = $response['data'][0]['TotalInactiveVendors'];

            $result = [
                'status' => 'success',
                'total_vendors_count' => $total_vendors_count,
                'total_active_vendors_count' => $total_active_vendors_count,
                'total_inactive_vendors_count' => $total_inactive_vendors_count
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendor details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
