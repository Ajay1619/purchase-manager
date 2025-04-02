<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $product_id = isset($_POST['product_id']) ? sanitizeInput($_POST['product_id'], 'int') : '';
    $vendor_id = isset($_POST['vendor_id']) ? sanitizeInput($_POST['vendor_id'], 'int') : '';
    $price_period = isset($_POST['price_period']) ? sanitizeInput($_POST['price_period'], 'string') : '';

    $procedure_params = [
        ['value' => $product_id, 'type' => 'i'],
        ['value' => $price_period, 'type' => 's']
    ];

    try {
        $result = callProcedure('fetch_view_price_history_data', $procedure_params);
        if ($result && $result['status'] == 'success') {
            $product_details = $result['data'][0];
            $vendor_data = [];
            $price_history = [];
            $xAxisLabels = [];
            $first_vendor_id = null;

            foreach ($result['data'][1] as $item) {
                $vendorId = $item['vendor_id'];

                // Set first_vendor_id if not already set
                if (is_null($first_vendor_id)) {
                    $first_vendor_id = $vendorId;
                }

                // Add data to vendor_data for all vendors
                if (!isset($vendor_data[$vendorId])) {
                    $vendor_data[$vendorId] = [
                        'vendor_id' => $item['vendor_id'],
                        'name' => $item['vendor_company_name'],
                        'data' => []
                    ];
                }

                // Format date based on price period
                switch ($price_period) {
                    case 'daily':
                        $label = date('Y-m-d', strtotime($item['date']));
                        break;
                    case 'monthly':
                        $label = date('F Y', strtotime($item['date']));
                        break;
                    case 'yearly':
                        $label = date('Y', strtotime($item['date']));
                        break;
                    default:
                        $label = '';
                }

                $vendor_data[$vendorId]['data'][] = [
                    'x' => $label,
                    'y' => number_format((float)$item['unit_price'], 2, '.', '')
                ];

                // Add detailed price history for the specified vendor_id or the first vendor
                if ($vendorId == $vendor_id || (empty($vendor_id) && $vendorId == $first_vendor_id)) {
                    if (!isset($price_history[$vendorId])) {
                        $price_history[$vendorId] = [
                            'vendor_id' => $item['vendor_id'],
                            'vendor_company_name' => $item['vendor_company_name'],
                            'items' => []
                        ];
                    }

                    $price_history[$vendorId]['items'][] = [
                        'purchase_order_item_id' => $item['purchase_order_item_id'],
                        'purchase_order_id' => $item['purchase_order_id'],
                        'purchase_order_number' => $item['purchase_order_number'],
                        'unit_price' => number_format((float)$item['unit_price'], 2, '.', ''),
                        'unit_of_measure' => $item['unit_of_measure'],
                        'date' => date(DATE_FORMAT, strtotime($item['date']))

                    ];
                }

                // Collect x-axis labels if not already present
                if (!in_array($label, $xAxisLabels)) {
                    $xAxisLabels[] = $label;
                }
            }

            // Prepare series for chart
            $chart_data = [];
            foreach ($vendor_data as $vendor) {
                $chart_data[] = [
                    'name' => $vendor['name'],
                    'vendor_id' => $vendor['vendor_id'],
                    'data' => $vendor['data']
                ];
            }

            // Output the data
            echo json_encode([
                'status' => 'success',
                'product_details' => $product_details,
                'vendor_data' => $chart_data,
                'price_history' => array_values($price_history), // Filtered for the specified vendor_id or first vendor
                'x_axis_labels' => $xAxisLabels
            ]);
        } else {
            throw new Exception('Failed to fetch price history data.');
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
