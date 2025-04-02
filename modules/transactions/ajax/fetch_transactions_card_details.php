<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        $result = callProcedure('fetch_transactions_card_details');

        if ($result && $result['status'] == 'success') {
            // Extract data from result
            $data = $result['data'][0]; // Assuming the data is in the first row of the result set

            $total_income = $data['total_income'];
            $total_expense = $data['total_expense'];
            $today_income = $data['today_income'];
            $today_expense = $data['today_expense'];

            // Prepare JSON response
            $response = [
                'status' => 'success',
                'total_income' => formatNumberIndian($total_income),
                'total_expense' => formatNumberIndian($total_expense),
                'today_income' => formatNumberIndian($today_income),
                'today_expense' => formatNumberIndian($today_expense)
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to fetch transaction details'
            ];
        }
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => 'Error fetching transaction details: ' . $e->getMessage()
        ];
    }

    echo json_encode($response);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
}
