<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Read parameters from DataTables
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        // Define column names array (should match DataTables column indexes)
        $columns = ['product_name', 'unit_of_measure', 'current_price'];

        // Get the column name to sort
        $sort_column = isset($columns[$order_column]) ? $columns[$order_column] : 'product_name';

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 's', 'value' => $search_value], // p_search_value
            ['type' => 's', 'value' => $sort_column],  // p_sort_column
            ['type' => 's', 'value' => $order_dir],     // p_order_dir
            ['type' => 'i', 'value' => $start],          // p_start
            ['type' => 'i', 'value' => $length]          // p_length
        ];

        // Call stored procedure
        $response = callProcedure('fetch_price_history_table_data', $inputParams);

        if ($response['status'] == 'success') {
            // Extract results from the response
            $total_records = 0;
            $filtered_records = 0;
            $data = [];

            // Extract total and filtered records from the first result set
            if (isset($response['data'][0])) {
                $total_records = isset($response['data'][0]['total_records']) ? intval($response['data'][0]['total_records']) : 0;
                $filtered_records = isset($response['data'][0]['filtered_records']) ? intval($response['data'][0]['filtered_records']) : 0;
            }

            // Extract actual data from the second result set
            if (isset($response['data'][1])) {
                $data = $response['data'][1];
            }

            // Prepare data array for DataTables
            $table_data = [];
            $s_no = $start + 1;
            foreach ($data as $row) {
                $current_price = formatNumberIndian(convertUnitAmount($row['current_price'], $row['purchase_unit_of_measure'], $row['unit_of_measure']));

                // Prepare action buttons with product_id parameter (sample actions)
                $action_buttons = <<<HTML
                
                
                <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#7D8ABC"class="table-action" onclick="view_price_history({$row['product_id']})"><path d="M280-280h80v-200h-80v200Zm320 0h80v-400h-80v400Zm-160 0h80v-120h-80v120Zm0-200h80v-80h-80v80ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Z"/>
                </svg>
                HTML;

                // Build row data for DataTables
                $table_data[] = [
                    's_no' => $s_no++,
                    'product_name' => $row['product_name'],
                    'unit_of_measure' => $row['unit_of_measure'],
                    'current_price' => $current_price,
                    'action' => $action_buttons
                ];
            }

            // Prepare data for DataTables response
            $result = [
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                "recordsTotal" => $total_records,
                "recordsFiltered" => $filtered_records,
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
