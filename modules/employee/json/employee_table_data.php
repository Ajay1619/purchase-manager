<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    try {
        // Read DataTables parameters
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'asc';

        // Define column names for sorting (should match DataTables column indexes)
        $columns = ['employee_id', 'employee_name', 'contact_number', 'designation', 'role', 'status'];

        // Get the column name to sort by, ensure $order_column is within the valid range
        if ($order_column >= 0 && $order_column < count($columns)) {
            $sort_column = $columns[$order_column];
        } else {
            $sort_column = 'employee_id'; // Default sort column
        }

        // Prepare input parameters for stored procedure or SQL query
        $inputParams = [
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir],
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length]
        ];

        // Call your stored procedure or SQL query to fetch employee data
        $response = callProcedure('fetch_employee_table_data', $inputParams); // Replace this with your actual procedure call

        if ($response['status'] == 'success') {
            // Extract data from the response
            $total_records = isset($response['data'][0]['total_records']) ? intval($response['data'][0]['total_records']) : 0;
            $data = isset($response['data'][1]) ? $response['data'][1] : [];

            // Prepare data for DataTables
            $table_data = [];
            $s_no = $start + 1;
            foreach ($data as $row) {
                $checked = $row['employee_status'] == 1 ? 'checked' : '';

                // Status switch checkbox
                $status_checkbox = <<<HTML
                    <label class="switch">
                    <input type="checkbox" {$checked} onchange="checkStatus({$row['employee_account_id']}, this.checked)">
                    <span class="slider round"></span>
                    </label>
                HTML;

                // Action buttons
                $action_buttons = <<<HTML
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#7D8ABC" class="table-action" onclick="view_employee({$row['employee_account_id']})">
                    <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z" />
                </svg>

                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#7D8ABC" class="table-action" onclick="edit_employee({$row['employee_account_id']})">
                    <path d="M280-160v-80h400v80H280Zm160-160v-327L336-544l-56-56 200-200 200 200-56 56-104-103v327h-80Z" />
                </svg>

                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#7D8ABC" class="table-action" onclick="delete_employee({$row['employee_account_id']})">
                    <path d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm80-160h80v-360h-80v360Zm160 0h80v-360h-80v360Z" />
                </svg>
                HTML;

                // Add the row data to the table
                $table_data[] = [
                    's_no' => $s_no++,
                    'employee_name' => $row['employee_name'],
                    'employee_id' => $row['employee_id'],
                    'contact_number' => $row['employee_contact_number'],
                    'designation' => $row['employee_designation'],
                    'role' => $row['employee_role'],
                    'status' => $status_checkbox,
                    'action' => $action_buttons
                ];
            }

            // Prepare the response for DataTables
            $result = [
                "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
                "recordsTotal" => $total_records,
                "recordsFiltered" => $total_records,
                "data" => $table_data
            ];

            // Send the response as JSON
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch employee data.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
