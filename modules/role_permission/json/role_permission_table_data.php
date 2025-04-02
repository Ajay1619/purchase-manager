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
        $columns = ['role_name', 'role_code', 'status'];

        // Get the column name to sort
        $sort_column = isset($columns[$order_column]) ? $columns[$order_column] : 'role_name';

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 's', 'value' => $search_value], // p_search_value
            ['type' => 's', 'value' => $sort_column],  // p_sort_column
            ['type' => 's', 'value' => $order_dir],     // p_order_dir
            ['type' => 'i', 'value' => $start],          // p_start
            ['type' => 'i', 'value' => $length]          // p_length
        ];

        // Call stored procedure
        $response = callProcedure('fetch_role_permission_table_data', $inputParams);

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
                $checked = $row['role_permission_status'] == 1 ? 'checked' : '';

                // Prepare status checkbox with onchange function
                $status_checkbox = <<<HTML
                    <label class="switch">
                    <input type="checkbox" {$checked} onchange="checkStatus({$row['role_id']}, this.checked)">
                        <span class="slider round"></span>
                    </label>
                HTML;

                // Prepare action buttons with role_id parameter (sample actions)
                $action_buttons = <<<HTML
                
                <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#7D8ABC" class="table-action" onclick="edit_role({$row['role_id']})">
                    <path d="M280-160v-80h400v80H280Zm160-160v-327L336-544l-56-56 200-200 200 200-56 56-104-103v327h-80Z" />
                </svg>

                <svg xmlns="http://www.w3.org/2000/svg" height="30px" viewBox="0 -960 960 960" width="30px" fill="#7D8ABC" class="table-action" onclick="delete_role({$row['role_id']})">
                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm80-160h80v-360h-80v360Zm160 0h80v-360h-80v360Z" />
                </svg>
                HTML;

                // Build row data for DataTables
                $table_data[] = [
                    's_no' => $s_no++,
                    'role_name' => $row['role_name'],
                    'role_code' => $row['role_code'],
                    'status' => $status_checkbox,
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
