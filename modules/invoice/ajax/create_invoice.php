<?php
require_once('../../../config/sparrow.php'); // Include your configuration file

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Fetch and sanitize POST data
    $customer_id = isset($_POST['customer_id']) ? sanitizeInput($_POST['customer_id'], 'int') : '';
    $invoice_number = isset($_POST['invoice_number']) ? sanitizeInput($_POST['invoice_number'], 'string') : '';
    $invoice_date = isset($_POST['invoice_date']) ? sanitizeInput($_POST['invoice_date'], 'string') : '';
    $due_date = isset($_POST['due-date']) ? sanitizeInput($_POST['due-date'], 'string') : '';
    $subtotal = isset($_POST['nettotal']) ? sanitizeInput($_POST['nettotal'], 'float') : 0.00;
    $adjustment_amount = isset($_POST['adjustment']) ? sanitizeInput($_POST['adjustment'], 'float') : 0.00;
    $grand_total = isset($_POST['grand_total']) ? sanitizeInput($_POST['grand_total'], 'float') : 0.00;
    $total_value = isset($_POST['total_value']) ? sanitizeInput($_POST['total_value'], 'float') : 0.00;
    $total_gst_amount = isset($_POST['total-gst-amount']) ? sanitizeInput($_POST['total-gst-amount'], 'float') : 0.00;
    $amount_in_words = isset($_POST['amount-inwords']) ? sanitizeInput($_POST['amount-inwords'], 'string') : '';
    $gst_enable = isset($_POST['gst-enable']) ? 1 : 0;
    $sgst = isset($_POST['sgst']) ? sanitizeInput($_POST['sgst'], 'float') : 0.00;
    $cgst = isset($_POST['cgst']) ? sanitizeInput($_POST['cgst'], 'float') : 0.00;
    $igst = isset($_POST['igst']) ? sanitizeInput($_POST['igst'], 'float') : 0.00;
    $payment_mode = isset($_POST['payment_mode']) ? sanitizeInput($_POST['payment_mode'], 'string') : '';
    $invoice_status = isset($_POST['invoice_status']) ? sanitizeInput($_POST['invoice_status'], 'int') : '';

    // Extract item data
    $item_ids = isset($_POST['item-id']) ? $_POST['item-id'] : [];
    $units_of_measure = isset($_POST['invoice_unit_of_measure']) ? $_POST['invoice_unit_of_measure'] : [];
    $product_unit_of_measure = isset($_POST['product_unit_of_measure']) ? $_POST['product_unit_of_measure'] : [];
    $rates = isset($_POST['invoice_rate']) ? $_POST['invoice_rate'] : [];
    $quantities = isset($_POST['invoice_quantity']) ? $_POST['invoice_quantity'] : [];
    $amounts = isset($_POST['invoice_amount']) ? $_POST['invoice_amount'] : [];
    $discount_enable = isset($_POST['discount_enable']) ? $_POST['discount_enable'] : [];
    $discount_rate = isset($_POST['discount_rate']) ? $_POST['discount_rate'] : [];
    $discount_amount = isset($_POST['discount_amount']) ? $_POST['discount_amount'] : [];
    $item_gst_amount = isset($_POST['item-gst-amount']) ? $_POST['item-gst-amount'] : [];
    $tax_inclusive_enable = isset($_POST['tax_inclusive_enable']) ? $_POST['tax_inclusive_enable'] : [];
    $tax_percentage = isset($_POST['tax_percentage']) ? $_POST['tax_percentage'] : [];

    // Sanitize arrays
    $item_ids = sanitizeInput($item_ids, 'int');
    $units_of_measure = sanitizeInput($units_of_measure, 'string');
    $product_unit_of_measure = sanitizeInput($product_unit_of_measure, 'string');
    $rates = sanitizeInput($rates, 'float');
    $quantities = sanitizeInput($quantities, 'int');
    $amounts = sanitizeInput($amounts, 'float');
    $discount_enable = sanitizeInput($discount_enable, 'int');
    $discount_rate = sanitizeInput($discount_rate, 'float');
    $discount_amount = sanitizeInput($discount_amount, 'float');
    $item_gst_amount = sanitizeInput($item_gst_amount, 'float');
    $tax_inclusive_enable = sanitizeInput($tax_inclusive_enable, 'string');
    $tax_percentage = sanitizeInput($tax_percentage, 'float');

    // Validate input
    $errors = [];
    if (empty($customer_id)) $errors[] = "Customer ID is required.";
    if (empty($invoice_number)) $errors[] = "Invoice Number is required.";
    if (empty($invoice_date)) $errors[] = "Invoice Date is required.";
    if (empty($due_date)) $errors[] = "Due Date is required.";

    // Check for empty fields in item details
    foreach ($item_ids as $index => $item_id) {
        if (empty($item_id)) $errors[] = "Item ID is required for item " . ($index + 1);
        if (empty($units_of_measure[$index])) $errors[] = "Unit of Measure is required for item " . ($index + 1);
        if (empty($rates[$index])) $errors[] = "Rate is required for item " . ($index + 1);
        if (empty($quantities[$index])) $errors[] = "Quantity is required for item " . ($index + 1);
        if (empty($amounts[$index])) $errors[] = "Amount is required for item " . ($index + 1);
    }

    // Return errors if there are any
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'messages' => $errors]);
        exit();
    }
    //convert quantity to base unit of measure
    foreach ($item_ids as $index => $item_id) {
        $converted_quantity = convertUnitQuantity($quantities[$index], $units_of_measure[$index], $product_unit_of_measure[$index]);
        $converted_quantities[$index] = $converted_quantity;
    }
    // Prepare parameters for insertion
    $created_by = $_SESSION['user_id'];
    $items_count = count($item_ids);
    $item_ids_json = json_encode($item_ids);
    $units_of_measure_json = json_encode($units_of_measure);
    $converted_quantities_json = json_encode($converted_quantities);
    $quantities_json = json_encode($quantities);
    $rates_json = json_encode($rates);
    $amounts_json = json_encode($amounts);
    $discount_enable_json = json_encode($discount_enable);
    $discount_rate_json = json_encode($discount_rate);
    $discount_amount_json = json_encode($discount_amount);
    $item_gst_amount_json = json_encode($item_gst_amount); // New field
    $tax_inclusive_enable_json = json_encode($tax_inclusive_enable);
    $tax_percentage_json = json_encode($tax_percentage);
    $tax_percentage_json = json_encode($tax_percentage);

    $procedure_params = [
        ['value' => $customer_id, 'type' => 'i'],
        ['value' => $invoice_number, 'type' => 's'],
        ['value' => $invoice_date, 'type' => 's'],
        ['value' => $due_date, 'type' => 's'],
        ['value' => $subtotal, 'type' => 'd'],
        ['value' => $adjustment_amount, 'type' => 'd'],
        ['value' => $grand_total, 'type' => 'd'],
        ['value' => $total_gst_amount, 'type' => 'd'],
        ['value' => $amount_in_words, 'type' => 's'],
        ['value' => $gst_enable, 'type' => 'i'],
        ['value' => $sgst, 'type' => 'd'],
        ['value' => $cgst, 'type' => 'd'],
        ['value' => $igst, 'type' => 'd'],
        ['value' => $payment_mode, 'type' => 's'],
        ['value' => $created_by, 'type' => 'i'],
        ['value' => $items_count, 'type' => 'i'],
        ['value' => $item_ids_json, 'type' => 's'],
        ['value' => $units_of_measure_json, 'type' => 's'],
        ['value' => $quantities_json, 'type' => 's'],
        ['value' => $rates_json, 'type' => 's'],
        ['value' => $amounts_json, 'type' => 's'],
        ['value' => $discount_enable_json, 'type' => 's'],
        ['value' => $discount_rate_json, 'type' => 's'],
        ['value' => $discount_amount_json, 'type' => 's'],
        ['value' => $item_gst_amount_json, 'type' => 's'],
        ['value' => $tax_inclusive_enable_json, 'type' => 's'],
        ['value' => $tax_percentage_json, 'type' => 's'],
        ['value' => $total_value, 'type' => 'd'],
        ['value' => $invoice_status, 'type' => 'i'],
        ['value' => $converted_quantities_json, 'type' => 's']
    ];
    try {
        // Call stored procedure
        $result = callProcedure('insert_invoice_with_items', $procedure_params);
        echo json_encode(['status' => 'success', 'messages' => 'Invoice created successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'messages' => $e->getMessage()]);
    }
}
