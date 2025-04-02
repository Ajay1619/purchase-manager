<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Read parameters from DataTables
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        // Define column names array (should match DataTables column indexes)
        $columns = ['product_name', 'unit_of_measure', 'order_quantity', 'vendor_name'];

        // Get the column name to sort
        $sort_column = isset($columns[$order_column]) ? $columns[$order_column] : 'product_name';

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 's', 'value' => $search_value], // p_search_value
            ['type' => 's', 'value' => $sort_column],  // p_sort_column
            ['type' => 's', 'value' => $order_dir],    // p_order_dir
            ['type' => 'i', 'value' => $start],        // p_start
            ['type' => 'i', 'value' => $length]        // p_length
        ];

        // Call stored procedure to fetch out-of-stock products
        $response = callProcedure('fetch_out_of_stock_table_data', $inputParams);
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
            foreach ($data as $key => $row) {
                // Generate the select tag for vendor_name with vendor_id as the value
                $vendor_options = '';
                $vendors = json_decode($row['purchase_orders'], true);
                foreach ($vendors as $vendor) {
                    $vendor_options .= '<option value="' . $vendor['purchase_order_item_id'] . '">' . $vendor['vendor_name'] . ' (' . $vendor['unit_of_measure'] . ' - ' . CURRENCY_SYMBOL . $vendor['unit_price'] . ')' . '</option>';
                }
                $vendor_select = '<select id="vendor_name_' . $key . '">' . $vendor_options . '</select>';

                $action_buttons = '';
                switch ($row['out_of_stock_status']) {
                    case 0:
                        $action_buttons = <<<HTML
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#4CAF50" class="table-action" onclick="restock_product({$key},{$row['out_of_stock_id']})">
                            <path d="M360-120q-100 0-170-70t-70-170v-240q0-100 70-170t170-70h240q100 0 170 70t70 170v240q0 100-70 170t-170 70H360Zm80-200 240-240-56-56-184 184-88-88-56 56 144 144Z" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#FF5733" class="table-action" onclick="delete_out_of_stock({$row['out_of_stock_id']})">
                            <path d="m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Z" />
                        </svg>
                        HTML;
                        break;
                    case 1:
                        $action_buttons = '<span class="badge badge-info">confirmed</span>';
                        break;
                    case 2:
                        $action_buttons = '<span class="badge badge-success">Purchased</span>';
                        break;
                    case 3:
                        $action_buttons = '<span class="badge badge-error">Canceled</span>';
                        break;
                }

                // Build row data for DataTables
                $table_data[] = [
                    's_no' => $s_no++,
                    'product_name' => $row['product_name'],
                    'unit_of_measure' => $row['unit_of_measure'],
                    'order_quantity' => $row['order_quantity'],
                    'vendor_name' => $vendor_select,
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
