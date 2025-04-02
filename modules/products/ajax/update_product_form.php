<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Sanitize input data
    $product_id = isset($_POST['product_id']) ? sanitizeInput($_POST['product_id'], 'int') : '';
    $product_name = isset($_POST['product_name']) ? sanitizeInput($_POST['product_name'], 'string') : '';
    $hsn_code = isset($_POST['hsn_code']) ? sanitizeInput($_POST['hsn_code'], 'string') : '';
    $product_type = isset($_POST['product_type']) ? sanitizeInput($_POST['product_type'], 'string') : '';
    $product_category = isset($_POST['product_category']) ? sanitizeInput($_POST['product_category'], 'string') : '';
    $unit_of_measure = isset($_POST['unit_of_measure']) ? sanitizeInput($_POST['unit_of_measure'], 'string') : '';
    $product_price = isset($_POST['product_price']) ? sanitizeInput($_POST['product_price'], 'float') : '';
    $pricing_type = isset($_POST['pricing_type']) ? sanitizeInput($_POST['pricing_type'], 'int') : '';
    $tax_percentage = isset($_POST['tax_percentage']) ? sanitizeInput($_POST['tax_percentage'], 'float') : '';
    $discount_enable = isset($_POST['discount_enable']) ? sanitizeInput($_POST['discount_enable'], 'int') : '';
    $bottom_stock = isset($_POST['bottom_stock']) ? sanitizeInput($_POST['bottom_stock'], 'int') : '';
    $order_quantity = isset($_POST['order_quantity']) ? sanitizeInput($_POST['order_quantity'], 'int') : '';
    $product_notes = isset($_POST['product_notes']) ? sanitizeInput($_POST['product_notes'], 'string') : '';
    $created_by = $_SESSION['user_id'];
    $dpuoms = isset($_POST['dpuom']) ? $_POST['dpuom'] : [];
    $dpqs = isset($_POST['dpq']) ? $_POST['dpq'] : [];
    $itemid = isset($_POST['itemid']) ? $_POST['itemid'] : [];
    $usedproductid = isset($_POST['usedproductid']) ? $_POST['usedproductid'] : [];

    // Check for empty fields
    $errors = [];
    $errors[] = checkEmptyField($product_name, "Product Name");
    $errors[] = checkEmptyField($hsn_code, "HSN Code");
    $errors[] = checkEmptyField($product_type, "Product Type");
    $errors[] = checkEmptyField($product_category, "Product Category");
    $errors[] = checkEmptyField($unit_of_measure, "Unit of Measure");
    $errors[] = checkEmptyField($product_price, "Unit Price");
    $errors[] = checkEmptyField($pricing_type, "Pricing Type");
    $errors[] = checkEmptyField($tax_percentage, "GST Tax Percentage");
    $errors[] = checkEmptyField($bottom_stock, "Bottom Stock");
    $errors[] = checkEmptyField($order_quantity, "Order Quantity");

    // Remove empty errors
    $errors = array_filter($errors);

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
    } else {
        // Convert item details to JSON
        $item_unit_of_measure = json_encode($dpuoms);
        $item_quantity_used = json_encode($dpqs);
        $item_id = json_encode($itemid);
        $used_product_id = json_encode($usedproductid);
        // Prepare the parameters for the procedure
        $procedure_params = [
            ['value' => $product_id, 'type' => 'i'],
            ['value' => $product_name, 'type' => 's'],
            ['value' => $hsn_code, 'type' => 's'],
            ['value' => $product_category, 'type' => 's'],
            ['value' => $product_type, 'type' => 'i'],
            ['value' => $unit_of_measure, 'type' => 's'],
            ['value' => $bottom_stock, 'type' => 'i'],
            ['value' => $order_quantity, 'type' => 'i'],
            ['value' => $product_price, 'type' => 'd'],
            ['value' => $pricing_type, 'type' => 'i'],
            ['value' => $discount_enable, 'type' => 'i'],
            ['value' => $tax_percentage, 'type' => 'd'],
            ['value' => $product_notes, 'type' => 's'],
            ['value' => $created_by, 'type' => 'i'],
            ['value' => $item_id, 'type' => 's'],
            ['value' => $item_unit_of_measure, 'type' => 's'],
            ['value' => $item_quantity_used, 'type' => 's'],
            ['value' => $used_product_id, 'type' => 's']
        ];

        try {
            // Call the stored procedure
            $result = callProcedure('update_product_details', $procedure_params);
            if ($result['status'] == 'error') {
                echo json_encode(['status' => 'error', 'message' => $result['message']]);
            } else {
                echo json_encode(['status' => 'success', 'message' => 'Product and items updated successfully.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
