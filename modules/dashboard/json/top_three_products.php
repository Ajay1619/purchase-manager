<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    try {
        $result = callProcedure('dashboard_top_three_products');
        if ($result && $result['status'] == 'success') {
            $chart_data = $result['data'];
            echo json_encode(['status' => 'success', 'data' => $chart_data]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch transaction details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error fetching transaction details: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
