<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    $vendor_id = isset($_POST['vendor_name']) ? sanitizeInput($_POST['vendor_name'], 'int') : '';
    $product_id = isset($_POST['product_id']) ? sanitizeInput($_POST['product_id'], 'int') : '';
    $product_name = isset($_POST['product_name']) ? sanitizeInput($_POST['product_name'], 'string') : '';
    $unit_of_measure = isset($_POST['unit_of_measure']) ? sanitizeInput($_POST['unit_of_measure'], 'string') : '';
    $order_quantity = isset($_POST['order_quantity']) ? sanitizeInput($_POST['order_quantity'], 'string') : '';
    $unit_price = isset($_POST['unit_price']) ? sanitizeInput($_POST['unit_price'], 'float') : '';
    $order_quantity = isset($_POST['order_quantity']) ? sanitizeInput($_POST['order_quantity'], 'float') : '';
    $subtotal = $unit_price * $order_quantity;
    $po_number = callProcedure('fetch_last_purchase_order_number');
    
    $procedure_params = [
        ['value' => $vendor_id, 'type' => 'i'],
        ['value' => $po_number['data'][0]['new_purchase_order_number'], 'type' => 's'],
        ['value' => $subtotal, 'type' => 'd'],
        ['value' => 0.00, 'type' => 'd'],
        ['value' => 0.00, 'type' => 'd'],
        ['value' => 0.00, 'type' => 'd'],
        ['value' => $subtotal, 'type' => 'd'],
        ['value' => convertNumberToWords($subtotal), 'type' => 's'],
        ['value' => $user_id, 'type' => 'i'],
        ['value' => 1, 'type' => 'i'],
        ['value' => json_encode($product_id), 'type' => 's'],
        ['value' => json_encode($product_name), 'type' => 's'],
        ['value' => json_encode($unit_of_measure), 'type' => 's'],
        ['value' => json_encode($order_quantity), 'type' => 's'],
        ['value' => json_encode($unit_price), 'type' => 's'],
        ['value' => json_encode($subtotal), 'type' => 's']
    ];
    try {
        // Call stored procedure
        $result = callProcedure('insert_purchase_order_with_items', $procedure_params);
        echo json_encode(['status' => 'success', 'messages' => 'Purchase Order created successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'messages' => $e->getMessage()]);
    }
}
