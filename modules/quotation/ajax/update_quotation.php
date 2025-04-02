<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Fetch and sanitize POST data
    $customer_id = isset($_POST['customer_id']) ? sanitizeInput($_POST['customer_id'], 'int') : '';
    $qo_id = isset($_POST['qo-id']) ? sanitizeInput($_POST['qo-id'], 'int') : '';
    $qo_number = isset($_POST['qo_number']) ? sanitizeInput($_POST['qo_number'], 'string') : '';
    $date = isset($_POST['date']) ? sanitizeInput($_POST['date'], 'string') : '';
    $subtotal = isset($_POST['subtotal']) ? sanitizeInput($_POST['subtotal'], 'float') : 0.00;
    $discount_percentage = isset($_POST['discount_percentage']) ? sanitizeInput($_POST['discount_percentage'], 'float') : 0.00;
    $discount_amount = isset($_POST['discount_amount']) ? sanitizeInput($_POST['discount_amount'], 'float') : 0.00;
    $adjustment_amount = isset($_POST['adjustment-amount']) ? sanitizeInput($_POST['adjustment-amount'], 'float') : 0.00;
    $grand_total = isset($_POST['grand_total']) ? sanitizeInput($_POST['grand_total'], 'float') : 0.00;
    $amount_in_words = isset($_POST['amount-in-words']) ? sanitizeInput($_POST['amount-in-words'], 'string') : '';

    // New fields
    $quotation_status = isset($_POST['quotation-status']) ? sanitizeInput($_POST['quotation-status'], 'int') : 0;
    $invoiced_date = isset($_POST['invoiced_date']) ? sanitizeInput($_POST['invoiced_date'], 'string') : '';

    // Extract item data
    $quotation_item_id = isset($_POST['quotation_item_id']) ? $_POST['quotation_item_id'] : [];
    $item_names = isset($_POST['quotation_item_name']) ? $_POST['quotation_item_name'] : [];
    $item_ids = isset($_POST['item_id']) ? $_POST['item_id'] : [];
    $units_of_measure = isset($_POST['quotation_unit_of_measure']) ? $_POST['quotation_unit_of_measure'] : [];
    $rates = isset($_POST['quotation_rate']) ? $_POST['quotation_rate'] : [];
    $quantities = isset($_POST['quotation_quantity']) ? $_POST['quotation_quantity'] : [];
    $amounts = isset($_POST['quotation_amount']) ? $_POST['quotation_amount'] : [];
    $product_unit_of_measure = isset($_POST['product_unit_of_measure']) ? $_POST['product_unit_of_measure'] : [];
    $product_details = [];


    // Validate input
    $errors = [];
    if (empty($customer_id)) $errors[] = "Customer ID is required.";
    if (empty($qo_number)) $errors[] = "Quotation Number is required.";
    if (empty($date)) $errors[] = "Date is required.";
    if ($quotation_status == 1 && empty($invoiced_date)) $errors[] = "Purchased Date is required when status is 'Purchased'.";

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



    // Prepare parameters for the stored procedure
    $procedure_params = [
        ['value' => $qo_id, 'type' => 'i'],
        ['value' => $customer_id, 'type' => 'i'],
        ['value' => $qo_number, 'type' => 's'],
        ['value' => $subtotal, 'type' => 'd'],
        ['value' => $discount_percentage, 'type' => 'd'],
        ['value' => $discount_amount, 'type' => 'd'],
        ['value' => $adjustment_amount, 'type' => 'd'],
        ['value' => $grand_total, 'type' => 'd'],
        ['value' => $amount_in_words, 'type' => 's'],
        ['value' => count($item_names), 'type' => 'i'],
        ['value' => json_encode($item_ids), 'type' => 's'],
        ['value' => json_encode($quotation_item_id), 'type' => 's'],
        ['value' => json_encode($units_of_measure), 'type' => 's'], // Save product_unit_of_measure
        ['value' => json_encode($quantities), 'type' => 's'], // Save converted quantities
        ['value' => json_encode($rates), 'type' => 's'],
        ['value' => json_encode($amounts), 'type' => 's'],
        ['value' => $quotation_status, 'type' => 'i'],
        ['value' => $user_id, 'type' => 'i']
    ];
    try {
        // Call stored procedure
        $result = callProcedure('update_quotation_with_items', $procedure_params);
        if ($result['status'] == 'success') {
            if ($quotation_status == 1) {
                $invoice_number = $result['data'][0]['new_invoice_number'];
                $invoice_params = [
                    ['value' => $customer_id, 'type' => 'i'],
                    ['value' => $invoice_number, 'type' => 's'],
                    ['value' => $invoiced_date, 'type' => 's'],
                    ['value' => $invoiced_date, 'type' => 's'],
                    ['value' => $subtotal, 'type' => 'd'],
                    ['value' => $adjustment_amount, 'type' => 'd'],
                    ['value' => $grand_total, 'type' => 'd'],
                    ['value' => 0, 'type' => 'd'],
                    ['value' => $amount_in_words, 'type' => 's'],
                    ['value' => 1, 'type' => 'i'],
                    ['value' => 0, 'type' => 'd'],
                    ['value' => 0, 'type' => 'd'],
                    ['value' => 0, 'type' => 'd'],
                    ['value' => '', 'type' => 's'],
                    ['value' => $user_id, 'type' => 'i'],
                    ['value' => count($item_names), 'type' => 'i'],
                    ['value' => json_encode($item_ids), 'type' => 's'],
                    ['value' => json_encode($units_of_measure), 'type' => 's'], // Save product_unit_of_measure
                    ['value' => json_encode($quantities), 'type' => 's'], // Save converted quantities
                    ['value' => json_encode($rates), 'type' => 's'],
                    ['value' => json_encode($amounts), 'type' => 's'],
                    ['value' => "", 'type' => 's'],
                    ['value' => "", 'type' => 's'],
                    ['value' => "", 'type' => 's'],
                    ['value' => "", 'type' => 's'],
                    ['value' => "", 'type' => 's'],
                    ['value' => "", 'type' => 's'],
                    ['value' => $subtotal, 'type' => 'd'],
                    ['value' => 0, 'type' => 'i'],
                    ['value' => json_encode($converted_quantities), 'type' => 's']
                ];
                $invoice_result = callProcedure('insert_invoice_with_items', $invoice_params);

                if ($invoice_result['status'] == 'error') {
                    echo json_encode(['status' => 'error', 'messages' => $invoice_result['messages']]);
                    exit;
                } else {
                    echo json_encode(['status' => 'success', 'messages' => 'Quotation updated successfully And Invoice Has Been Created.']);
                    exit;
                }
            }

            echo json_encode(['status' => 'success', 'messages' => 'Quotation updated successfully.']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'messages' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'messages' => 'Invalid request.']);
}
