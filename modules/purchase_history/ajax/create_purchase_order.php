<?php
require_once('../../../config/sparrow.php'); // Include your configuration file

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Fetch and sanitize POST data
    $vendor_id = isset($_POST['vendor_id']) ? sanitizeInput($_POST['vendor_id'], 'int') : '';
    $po_number = isset($_POST['po_number']) ? sanitizeInput($_POST['po_number'], 'string') : '';
    $date = isset($_POST['date']) ? sanitizeInput($_POST['date'], 'string') : '';
    $subtotal = isset($_POST['subtotal']) ? sanitizeInput($_POST['subtotal'], 'float') : 0.00;
    $discount_percentage = isset($_POST['discount_percentage']) ? sanitizeInput($_POST['discount_percentage'], 'float') : 0.00;
    $discount_amount = isset($_POST['discount_amount']) ? sanitizeInput($_POST['discount_amount'], 'float') : 0.00;
    $adjustment_amount = isset($_POST['adjustment_amount']) ? sanitizeInput($_POST['adjustment_amount'], 'float') : 0.00;
    $grand_total = isset($_POST['grand_total']) ? sanitizeInput($_POST['grand_total'], 'float') : 0.00;
    $amount_in_words = isset($_POST['amount-in-words']) ? sanitizeInput($_POST['amount-in-words'], 'string') : '';

    // Extract item data
    $item_names = isset($_POST['purchase_order_item_name']) ? sanitizeInput($_POST['purchase_order_item_name'], 'string') : [];
    $item_ids = isset($_POST['item_id']) ? sanitizeInput($_POST['item_id'], 'int') : [];
    $units_of_measure = isset($_POST['purchase_order_unit_of_measure']) ? sanitizeInput($_POST['purchase_order_unit_of_measure'], 'string') : [];
    $rates = isset($_POST['purchase_order_rate']) ? sanitizeInput($_POST['purchase_order_rate'], 'float') : [];
    $quantities = isset($_POST['purchase_order_quantity']) ? sanitizeInput($_POST['purchase_order_quantity'], 'int') : [];
    $amounts = isset($_POST['purchase_order_amount']) ? sanitizeInput($_POST['purchase_order_amount'], 'float') : [];


    // Validate input
    $errors = [];
    if (empty($vendor_id)) $errors[] = "Vendor ID is required.";
    if (empty($po_number)) $errors[] = "Purchase Order Number is required.";
    if (empty($date)) $errors[] = "Date is required.";

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

    // Prepare parameters for insertion
    $created_by = $_SESSION['user_id'];
    $items_count = count($item_names);
    $item_ids_json = json_encode($item_ids);
    $item_names_json = json_encode($item_names);
    $units_of_measure_json = json_encode($units_of_measure);
    $quantities_json = json_encode($quantities);
    $rates_json = json_encode($rates);
    $amounts_json = json_encode($amounts);

    $procedure_params = [
        ['value' => $vendor_id, 'type' => 'i'],
        ['value' => $po_number, 'type' => 's'],
        ['value' => $subtotal, 'type' => 'd'],
        ['value' => $discount_percentage, 'type' => 'd'],
        ['value' => $adjustment_amount, 'type' => 'd'],
        ['value' => $discount_amount, 'type' => 'd'],
        ['value' => $grand_total, 'type' => 'd'],
        ['value' => $amount_in_words, 'type' => 's'],
        ['value' => $created_by, 'type' => 'i'],
        ['value' => $items_count, 'type' => 'i'],
        ['value' => $item_ids_json, 'type' => 's'],
        ['value' => $item_names_json, 'type' => 's'],
        ['value' => $units_of_measure_json, 'type' => 's'],
        ['value' => $quantities_json, 'type' => 's'],
        ['value' => $rates_json, 'type' => 's'],
        ['value' => $amounts_json, 'type' => 's']
    ];

    try {
        // Call stored procedure
        $result = callProcedure('insert_purchase_order_with_items', $procedure_params);
        echo json_encode(['status' => 'success', 'messages' => 'Purchase Order created successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'messages' => $e->getMessage()]);
    }
}
