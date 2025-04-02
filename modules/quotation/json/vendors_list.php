<?php require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    $search_input = sanitizeInput($_POST['search_input']);

    $get_vendors_by_name_procedure_params = [
        ['value' => $search_input, 'type' => 's'],
    ];

    try {
        // Call the stored procedure
        $result = callProcedure('get_vendors_by_name', $get_vendors_by_name_procedure_params);
        // Handle the result
        if ($result && $result['status'] == 'success') {
            echo json_encode(['status' => 'success', 'data' => $result['data']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
