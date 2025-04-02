<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $product_id = isset($_POST['product_id']) ? sanitizeInput($_POST['product_id'], 'int') : 1;
    $customer_id = isset($_POST['customer_id']) ? sanitizeInput($_POST['customer_id'], 'int') : '';
    $sales_period = isset($_POST['sales_period']) ? sanitizeInput($_POST['sales_period'], 'string') : 'daily';

    $procedure_params = [
        ['value' => $product_id, 'type' => 'i'],
        ['value' => $sales_period, 'type' => 's']
    ];

    try {
        $result = callProcedure('fetch_view_sales_report_data', $procedure_params);

        if ($result && $result['status'] == 'success') {
            $product_details = $result['data'][0];
            $customer_data = [];
            $sales_history = [];
            $xAxisLabels = [];
            $salesData = [];
            $quantityData = [];
            $revenueData = [];
            $first_customer_id = null;

            foreach ($result['data'][1] as $item) {
                $customerId = $item['customer_id'];

                // Set first_customer_id if not already set
                if (is_null($first_customer_id)) {
                    $first_customer_id = $customerId;
                }

                // Add customer data
                if (!isset($customer_data[$customerId])) {
                    $customer_data[$customerId] = [
                        'customer_id' => $item['customer_id'],
                        'name' => $item['customer_name']
                    ];
                }

                // Convert quantity to product unit of measure
                $converted_quantity = convertUnitQuantity($item['quantity'], $item['unit_of_measure'], $product_details['unit_of_measure']);

                // Format date based on sales period
                switch ($sales_period) {
                    case 'daily':
                        $label = date('Y-m-d', strtotime($item['invoice_date']));
                        break;
                    case 'monthly':
                        $label = date('F Y', strtotime($item['invoice_date']));
                        break;
                    case 'yearly':
                        $label = date('Y', strtotime($item['invoice_date']));
                        break;
                    default:
                        $label = '';
                }

                // Check if date already exists in xAxisLabels
                if (($key = array_search($label, $xAxisLabels)) !== false) {
                    // If the date exists, increment the quantity and revenue
                    $quantityData[$key] += number_format((float)$converted_quantity, 2);
                    $revenueData[$key] += $item['amount'];
                    $salesData[$key]++; // Increment the bill count
                } else {
                    // Add new entry for the date
                    $xAxisLabels[] = $label;
                    $salesData[] = 1; // Start the count for this date with 1 bill
                    $quantityData[] = number_format((float)$converted_quantity, 2);
                    $revenueData[] = $item['amount'];
                }

                // Populate sales history table for the selected customer
                if (empty($customer_id) && $customerId == $first_customer_id) {
                    $sales_history[] = [
                        'date' => $item['invoice_date'],
                        'invoice_no' => $item['invoice_number'],
                        'unit_of_measure' => $item['unit_of_measure'],
                        'unit_price' => $item['unit_price'],
                        'quantity_sold' => $converted_quantity
                    ];
                } elseif (!empty($customer_id) && $customerId == $customer_id) {
                    $sales_history[] = [
                        'date' => $item['invoice_date'],
                        'invoice_no' => $item['invoice_number'],
                        'unit_of_measure' => $item['unit_of_measure'],
                        'unit_price' => $item['unit_price'],
                        'quantity_sold' => $converted_quantity
                    ];
                }
            }

            // Prepare response for chart and table
            $response = [
                'status' => 'success',
                'product_details' => $product_details,
                'sales_history' => $sales_history,
                'xAxisLabels' => $xAxisLabels,
                'customer_data' => $customer_data,
                'selected_customer_id' => $customer_id, // Include the selected customer ID
                'chartData' => [
                    'sales' => $salesData,
                    'quantity' => $quantityData,
                    'revenue' => $revenueData
                ]
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error fetching sales report data'
            ];
        }
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => 'Error fetching sales report data: ' . $e->getMessage()
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid request'
    ];
}

echo json_encode($response);
