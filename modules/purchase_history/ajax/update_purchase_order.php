<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Fetch and sanitize POST data
    $vendor_id = isset($_POST['vendor_id']) ? sanitizeInput($_POST['vendor_id'], 'int') : '';
    $po_id = isset($_POST['po-id']) ? sanitizeInput($_POST['po-id'], 'int') : '';
    $po_number = isset($_POST['po_number']) ? sanitizeInput($_POST['po_number'], 'string') : '';
    $date = isset($_POST['date']) ? sanitizeInput($_POST['date'], 'string') : '';
    $subtotal = isset($_POST['subtotal']) ? sanitizeInput($_POST['subtotal'], 'float') : 0.00;
    $discount_percentage = isset($_POST['discount_percentage']) ? sanitizeInput($_POST['discount_percentage'], 'float') : 0.00;
    $discount_amount = isset($_POST['discount_amount']) ? sanitizeInput($_POST['discount_amount'], 'float') : 0.00;
    $adjustment_amount = isset($_POST['adjustment-amount']) ? sanitizeInput($_POST['adjustment-amount'], 'float') : 0.00;
    $grand_total = isset($_POST['grand_total']) ? sanitizeInput($_POST['grand_total'], 'float') : 0.00;
    $amount_in_words = isset($_POST['amount-in-words']) ? sanitizeInput($_POST['amount-in-words'], 'string') : '';

    // New fields
    $purchase_order_status = isset($_POST['purchase-order-status']) ? sanitizeInput($_POST['purchase-order-status'], 'int') : 0;
    $purchased_date = isset($_POST['purchased-date']) ? sanitizeInput($_POST['purchased-date'], 'string') : '';

    // Extract item data
    $purchase_order_item_id = isset($_POST['purchase_order_item_id']) ? $_POST['purchase_order_item_id'] : [];
    $item_names = isset($_POST['purchase_order_item_name']) ? $_POST['purchase_order_item_name'] : [];
    $item_ids = isset($_POST['item_id']) ? $_POST['item_id'] : [];
    $units_of_measure = isset($_POST['purchase_order_unit_of_measure']) ? $_POST['purchase_order_unit_of_measure'] : [];
    $rates = isset($_POST['purchase_order_rate']) ? $_POST['purchase_order_rate'] : [];
    $quantities = isset($_POST['purchase_order_quantity']) ? $_POST['purchase_order_quantity'] : [];
    $amounts = isset($_POST['purchase_order_amount']) ? $_POST['purchase_order_amount'] : [];
    $product_unit_of_measure = isset($_POST['product_unit_of_measure']) ? $_POST['product_unit_of_measure'] : [];

    // Validate input
    $errors = [];
    if (empty($vendor_id)) $errors[] = "Vendor ID is required.";
    if (empty($po_number)) $errors[] = "Purchase Order Number is required.";
    if (empty($date)) $errors[] = "Date is required.";
    if ($purchase_order_status == 1 && empty($purchased_date)) $errors[] = "Purchased Date is required when status is 'Purchased'.";

    // Check for empty fields in item details
    foreach ($item_names as $index => $item_name) {
        if (empty($item_name)) $errors[] = "Item Name is required for item " . ($index + 1);
        if (empty($rates[$index])) $errors[] = "Rate is required for item " . ($index + 1);
        if (empty($quantities[$index])) $errors[] = "Quantity is required for item " . ($index + 1);
        if (empty($amounts[$index])) $errors[] = "Amount is required for item " . ($index + 1);
    }

    // Return errors if there are any
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
        exit();
    }

    // Convert unit of measure quantities
    $converted_quantities = [];
    foreach ($quantities as $index => $quantity) {
        $fromUnit = $units_of_measure[$index];
        $toUnit = $product_unit_of_measure[$index];
        $converted_quantities[] = convertUnitQuantity($quantity, $fromUnit, $toUnit);
    }

    // Format purchased_date to 'd-m-Y' if status is 'Purchased'
    if ($purchase_order_status == 1) {
        $purchased_date = date('d-m-Y', strtotime($purchased_date));
    } else {
        $purchased_date = null;
    }

    // Prepare parameters for the stored procedure
    $procedure_params = [
        ['value' => $po_id, 'type' => 'i'],
        ['value' => $vendor_id, 'type' => 'i'],
        ['value' => $po_number, 'type' => 's'],
        ['value' => $subtotal, 'type' => 'd'],
        ['value' => $discount_percentage, 'type' => 'd'],
        ['value' => $discount_amount, 'type' => 'd'],
        ['value' => $adjustment_amount, 'type' => 'd'],
        ['value' => $grand_total, 'type' => 'd'],
        ['value' => $amount_in_words, 'type' => 's'],
        ['value' => count($item_names), 'type' => 'i'],
        ['value' => json_encode($item_ids), 'type' => 's'],
        ['value' => json_encode($purchase_order_item_id), 'type' => 's'],
        ['value' => json_encode($product_unit_of_measure), 'type' => 's'], // Save product_unit_of_measure
        ['value' => json_encode($converted_quantities), 'type' => 's'], // Save converted quantities
        ['value' => json_encode($rates), 'type' => 's'],
        ['value' => json_encode($amounts), 'type' => 's'],
        ['value' => $purchase_order_status, 'type' => 'i'],
        ['value' => $purchased_date, 'type' => 's'],
        ['value' => $user_id, 'type' => 'i']
    ];
    try {
        // Call stored procedure
        $result = callProcedure('update_purchase_order_with_items', $procedure_params);
        echo json_encode(['status' => 'success', 'messages' => 'Purchase order updated successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'messages' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'messages' => 'Invalid request.']);
}
