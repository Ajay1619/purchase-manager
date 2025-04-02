<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $product_id = $_POST['product_id'];
    $procedure_params = [
        ['value' => $product_id, 'type' => 'i']
    ];
    $response = callProcedure('delete_product', $procedure_params);
    if ($response['status'] == 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
    }
}
