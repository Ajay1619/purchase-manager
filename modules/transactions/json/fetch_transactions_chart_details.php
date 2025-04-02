<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $time_frame = isset($_POST['timeframe']) ? sanitizeInput($_POST['timeframe'], 'string') : 'daily';

    try {
        $procedure_params = [
            ['value' => $time_frame, 'type' => 's']
        ];
        $result = callProcedure('fetch_transactions_chart_details', $procedure_params);
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
