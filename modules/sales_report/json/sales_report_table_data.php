<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    try {
        // Read parameters
        $start = isset($_POST['start']) ? $_POST['start'] : 0;
        $length = isset($_POST['length']) ? $_POST['length'] : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        // Define column names array (should match DataTables column indexes)
        $columns = ['product_id', 'product_name', 'product_code', 'unit_of_measure', 'total_bills', 'total_quantity_sold', 'revenue'];

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 's', 'value' => $search_value],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length]
        ];

        // Call stored procedure
        $response = callProcedure('fetch_sales_report_table_data', $inputParams);
        if ($response['status'] == 'success') {
            // Extract results from the response
            $total_records = isset($response['data'][0]['COUNT(*)']) ? intval($response['data'][0]['COUNT(*)']) : 0;
            $data = isset($response['data'][1]) ? $response['data'][1] : [];

            // Process data to get unique products
            $products = [];
            foreach ($data as $row) {


                $product_id = $row['product_id'];

                if (!isset($products[$product_id])) {
                    $products[$product_id] = [
                        'product_id' => $row['product_id'],
                        'product_name' => $row['product_name'],
                        'product_code' => $row['product_code'],
                        'unit_of_measure' => $row['product_unit_of_measure'],
                        'total_bills' => 0,
                        'total_quantity_sold' => 0,
                        'revenue' => 0
                    ];
                }

                // Convert amount and quantity to product's unit of measure
                //$converted_amount = convertUnitAmount($row['amount'], $row['sales_unit_of_measure'],  $row['product_unit_of_measure']);
                $converted_quantity = convertUnitQuantity($row['quantity'], $row['sales_unit_of_measure'],  $row['product_unit_of_measure']);

                // Aggregate the values
                $products[$product_id]['total_bills'] += 1;
                $products[$product_id]['total_quantity_sold'] += $converted_quantity;
                $products[$product_id]['revenue'] += $row['amount'];
            }
            // Sort the products array based on the selected column
            usort($products, function ($a, $b) use ($columns, $order_column, $order_dir) {
                $column = $columns[$order_column];
                if ($order_dir === 'asc') {
                    return $a[$column] <=> $b[$column];
                } else {
                    return $b[$column] <=> $a[$column];
                }
            });

            // Prepare data array for DataTables
            $table_data = [];
            $s_no = $start + 1;
            foreach ($products as $product_id => $product) {
                $action_buttons = <<<HTML
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#7D8ABC" class="table-action" onclick="view_sales_report({$product['product_id']})">
                    <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z" />
                </svg>
            HTML;


                $table_data[] = [
                    's_no' => $s_no++,
                    'product_name' => $product['product_name'],
                    'product_code' => $product['product_code'],
                    'unit_of_measure' => $product['unit_of_measure'],
                    'total_bills' => $product['total_bills'],
                    'total_quantity_sold' => number_format($product['total_quantity_sold'], 3),
                    'revenue' => formatNumberIndian($product['revenue']),
                    'action' => $action_buttons
                ];
            }

            // Prepare data for DataTables response
            $result = [
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                "recordsTotal" => $total_records,
                "recordsFiltered" => $total_records, // Currently not implementing server-side filtering
                "data" => $table_data
            ];

            // Return JSON response
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data from the stored procedure']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
