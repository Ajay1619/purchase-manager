<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        $response = callProcedure('fetch_sales_report_card_details');

        if ($response['status'] == 'success') {
            // Process and return JSON response with product and item details
            $total_sales_count = $response['data'][0]['TotalSales'];
            $total_revenue = $response['data'][0]['TotalRevenue'];

            $result = [
                'status' => 'success',
                'total_sales_count' => $total_sales_count,
                'total_revenue' => formatNumberIndian($total_revenue)
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch vendor details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
