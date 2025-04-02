<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $purchase_order_item_id = isset($_POST['order_id']) ? sanitizeInput($_POST['order_id'], 'int') : '';
    $out_of_stock_id = isset($_POST['out_of_stock_id']) ? sanitizeInput($_POST['out_of_stock_id'], 'int') : '';
    try {
        // Fetch restock details
        $procedure_params = [
            ['value' => $purchase_order_item_id, 'type' => 'i'],
        ];
        $response = callProcedure('fetch_restock_details', $procedure_params);
        if ($response['status'] == 'success' && isset($response['data'][0])) {
            $result = $response['data'][0];
            $order_number = $response['data'][1][0]['next_purchase_order_number'];
            $vendor_id = $result['vendor_id'];
            $product_id = $result['product_id'];
            $unit_price = $result['unit_price'];
            $po_unit_of_measure = $result['po_unit_of_measure'];
            $order_quantity = $result['order_quantity'];
            $product_unit_of_measure = $result['product_unit_of_measure'];

            $new_unit_price = convertUnitAmount($unit_price, $po_unit_of_measure, $product_unit_of_measure);
            $amount = $new_unit_price * $order_quantity;

            // Prepare parameters for inserting the purchase order
            $purchase_order_number = $order_number; // Generate a unique purchase order number
            $subtotal = $amount; // Assuming subtotal is just the amount for this example
            $discount = 0; // Set default values for discount, adjustment, etc.
            $adjustment = 0;
            $discount_amount = 0;
            $grand_total = $subtotal - $discount + $adjustment; // Compute grand total
            $amount_in_words = convertNumberToWords($amount); // You may want to convert the grand_total to words
            $created_by = $user_id; // Set the ID of the user creating the order
            $items_count = 1; // Number of items in the order

            // Prepare JSON arrays for items
            $item_ids = json_encode([$product_id]);
            $item_names = json_encode([$result['product_name']]); // Assuming you have a product name
            $item_uoms = json_encode([$product_unit_of_measure]);
            $item_quantities = json_encode([$order_quantity]);
            $item_rates = json_encode([$new_unit_price]);
            $item_amounts = json_encode([$amount]);

            // Prepare parameters for the insert procedure
            $insert_params = [
                ['value' => $vendor_id, 'type' => 'i'],
                ['value' => $purchase_order_number, 'type' => 's'],
                ['value' => $subtotal, 'type' => 'd'],
                ['value' => $discount, 'type' => 'd'],
                ['value' => $adjustment, 'type' => 'd'],
                ['value' => $discount_amount, 'type' => 'd'],
                ['value' => $grand_total, 'type' => 'd'],
                ['value' => $amount_in_words, 'type' => 's'],
                ['value' => $user_id, 'type' => 'i'],
                ['value' => $items_count, 'type' => 'i'],
                ['value' => $item_ids, 'type' => 's'],
                ['value' => $item_names, 'type' => 's'],
                ['value' => $item_uoms, 'type' => 's'],
                ['value' => $item_quantities, 'type' => 's'],
                ['value' => $item_rates, 'type' => 's'],
                ['value' => $item_amounts, 'type' => 's'],
            ];

            // Call the insert procedure
            $insert_response = callProcedure('insert_purchase_order_with_items', $insert_params);

            if ($insert_response['status'] == 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Purchase order created successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error creating purchase order']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No data found']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
