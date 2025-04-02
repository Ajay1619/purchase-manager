<?php require_once('../../../config/sparrow.php'); ?>
<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $employee_id = isset($_POST['employee_id']) ? sanitizeInput($_POST['employee_id']) : 0;

    try {
        // Call the stored procedure for fetching purchase history details
        $procedure_params = [
            ['name' => 'employee_id', 'value' => $employee_id, 'type' => 'i']
        ];
        $response = callProcedure('fetch_view_employee_details', $procedure_params);
        // Check if the response is successful 
        if ($response['status'] == 'success') {
            // Process and return JSON response with purchase history details
            $data = $response['data'][0]; // Assuming the data is in the first row

            $result = [
                'status' => 'success',
                'data' => $data
            ];
            echo json_encode($result);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to fetch purchase history details']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>