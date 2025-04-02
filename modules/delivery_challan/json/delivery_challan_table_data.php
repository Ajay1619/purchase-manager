<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    try {
        // Read parameters sent by DataTables
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $order_column = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        // Define column names array (should match DataTables column indexes)
        $columns = ['delivery_challan_id', 'delivery_challan_number', 'delivery_challan_date', 'customer_name', 'delivery_date'];

        // Get the column name to sort
        $sort_column = isset($columns[$order_column]) ? $columns[$order_column] : 'delivery_challan_id';

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 'i', 'value' => $start],
            ['type' => 'i', 'value' => $length],
            ['type' => 's', 'value' => $search_value],
            ['type' => 's', 'value' => $sort_column],
            ['type' => 's', 'value' => $order_dir]
        ];

        // Call stored procedure for fetching delivery challan data
        $response = callProcedure('fetch_delivery_challan_table_data', $inputParams);

        if ($response['status'] == 'success') {
            // Extract results from the response
            $total_records = isset($response['data'][0]['COUNT(*)']) ? intval($response['data'][0]['COUNT(*)']) : 0;
            $data = isset($response['data'][1]) ? $response['data'][1] : [];

            // Prepare data array for DataTables
            $table_data = [];
            $s_no = $start + 1;
            foreach ($data as $row) {
                $action_buttons = '';

                // Create action buttons for view and edit
                $action_buttons = <<<HTML
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#7D8ABC" class="table-action" onclick="view_challan({$row['delivery_challan_id']})">
                        <path d="M480-320q75 0 127.5-52.5T660-500q0-75-52.5-127.5T480-680q-75 0-127.5 52.5T300-500q0 75 52.5 127.5T480-320Zm0-72q-45 0-76.5-31.5T372-500q0-45 31.5-76.5T480-608q45 0 76.5 31.5T588-500q0 45-31.5 76.5T480-392Zm0 192q-146 0-266-81.5T40-500q54-137 174-218.5T480-800q146 0 266 81.5T920-500q-54 137-174 218.5T480-200Z" />
                    </svg>
                HTML;

                // Build row data for DataTables
                $table_data[] = [
                    's_no' => $s_no++,
                    'delivery_challan_number' => $row['delivery_challan_number'],
                    'delivery_challan_date' => $row['delivery_challan_date'], // Already formatted in SQL
                    'customer_name' => $row['customer_name'],
                    'delivery_date' => $row['delivery_date'],
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
