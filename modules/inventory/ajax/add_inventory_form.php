<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Retrieve and sanitize form data
    $product_id = isset($_POST['product-id']) ? sanitizeInput($_POST['product-id'], 'int') : '';
    $unit_of_quantity = isset($_POST['unit-of-quantity']) ? sanitizeInput($_POST['unit-of-quantity'], 'string') : '';
    $quantity_added = isset($_POST['quantity-added']) ? sanitizeInput($_POST['quantity-added'], 'float') : '';
    $product_unit_of_measure = isset($_POST['product-unit-of-measure']) ? sanitizeInput($_POST['product-unit-of-measure'], 'string') : '';

    // Validate required fields
    $errors = [];
    $errors[] = checkEmptyField($unit_of_quantity, "Unit of Quantity");
    $errors[] = checkEmptyField($quantity_added, "Quantity Added");

    // Remove empty errors
    $errors = array_filter($errors);

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
    } else {
        // Convert quantity to the base unit if necessary
        $converted_quantity = convertUnitQuantity($quantity_added, $unit_of_quantity, $product_unit_of_measure);

        // Prepare parameters for the procedure
        $procedure_params = [
            ['value' => $product_id, 'type' => 'i'],
            ['value' => $quantity_added, 'type' => 'd'],
            ['value' => $converted_quantity, 'type' => 'd'],
            ['value' => $unit_of_quantity, 'type' => 's'],
            ['value' => $product_unit_of_measure, 'type' => 's'],
            ['value' => $user_id, 'type' => 'i']
        ];

        try {
            // Call the stored procedure
            $result = callProcedure('insert_inventory_item', $procedure_params);
            echo json_encode(['status' => 'success', 'messages' => 'Inventory updated successfully.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
