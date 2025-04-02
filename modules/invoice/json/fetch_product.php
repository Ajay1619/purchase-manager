<?php require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    $product_id = sanitizeInput($_POST['product_id']);

    $get_products_by_id_procedure_params = [
        ['value' => $product_id, 'type' => 's'],
    ];

    try {
        // Call the stored procedure
        $result = callProcedure('get_product_details_invoice_with_id', $get_products_by_id_procedure_params);

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
