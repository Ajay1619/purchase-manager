<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $inventory_id = isset($_POST['inventory_id']) ? sanitizeInput($_POST['inventory_id'], 'int') : '';
    $stock_period = isset($_POST['stock_period']) ? sanitizeInput($_POST['stock_period'], 'string') : '';
    if ($inventory_id) {
        $inputParams = [
            ['type' => 'i', 'value' => $inventory_id],
            ['type' => 's', 'value' => $stock_period],
        ];

        try {
            $response = callProcedure('fetch_view_inventory_history_details', $inputParams);
            if ($response['status'] == 'success') {

                $inventory_details = $response['data'];
                $product_details = $inventory_details[0];
                $inventory_history = $inventory_details[1];
                $stock_usage_chart = $inventory_details[2];
                foreach ($inventory_history as $key => $value) {
                    $inventory_history[$key]['created_on'] = date(DATE_FORMAT, strtotime($value['created_on']));
                }
                // Initialize arrays
                $labels = [];
                $stock_in_values = [];
                $stock_out_values = [];

                // Process based on stock_period
                foreach ($stock_usage_chart as $data) {
                    if ($stock_period == 'daily') {
                        // For daily, use day names
                        $labels[] = $data['day']; // Day of the week
                    } elseif ($stock_period == 'monthly') {
                        // For monthly, convert YYYY-MM to month names
                        $monthNumber = (int)date('n', strtotime($data['month']));
                        $labels[] = date('F', mktime(0, 0, 0, $monthNumber, 1)); // Month name
                    } elseif ($stock_period == 'yearly') {
                        // For yearly, use year directly
                        $labels[] = $data['year']; // Year
                    }

                    $stock_in_values[] = (float)$data['stock_in'];
                    $stock_out_values[] = (float)$data['stock_out'];
                }
                $stock_usage_chart_data = [
                    'labels' => $labels,
                    'stock_in' => $stock_in_values,
                    'stock_out' => $stock_out_values
                ];
                $result = [
                    'status' => 'success',
                    'product_details' => $product_details,
                    'inventory_history' => $inventory_history,
                    'stock_usage_chart_data' => $stock_usage_chart_data
                ];
                echo json_encode($result);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch inventory details']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Inventory ID is not provided']);
    }
}
