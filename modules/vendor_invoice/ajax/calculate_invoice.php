<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $total_item_count = $_POST['total_item_count'];
    $items = [];
    $net_total = 0;
    $sgst = 0;
    $cgst = 0;
    $igst = 0;
    $discount_total = 0;
    $gst_amount = 0;
    $gst_enable = isset($_POST['gst-enable']) && $_POST['gst-enable'] == 1;
    $vendor_state = $_POST['vendor_state'];
    for ($i = 0; $i < $total_item_count; $i++) {
        $item_id = $_POST['item-id'][$i];
        $quantity = $_POST['invoice_quantity'][$i];
        $unit_price = $_POST['unit-price'][$i];
        $invoice_unit_of_measure = $_POST['invoice_unit_of_measure'][$i];
        $discount_percentage = isset($_POST['discount_rate'][$i]) && $_POST['discount_rate'][$i] != "" ? $_POST['discount_rate'][$i] : "";
        $discount_amount = isset($_POST['discount_amount'][$i]) && $_POST['discount_amount'][$i] != "" ? $_POST['discount_amount'][$i] : "";

        $price_comparison = 0;

        // Calculate tax based on state comparison
        if ($gst_enable) {
            if (isset($_POST['tax_inclusive_enable'][$i]) && $_POST['tax_inclusive_enable'][$i] == "1") {
                $tax_percentage = $_POST['tax_percentage'][$i];
                $tax_amount = ($unit_price * $tax_percentage) / (100 + $tax_percentage);
                $unit_price -= $tax_amount;
                $amount = $quantity * $unit_price;
                $gst_amount = $tax_amount * $quantity;
                // Check state for CGST/SGST or IGST
                if ($vendor_state === STATE) {
                    // Calculate CGST and SGST
                    $sgst += ($tax_amount * $quantity) / 2;
                    $cgst += ($tax_amount * $quantity) / 2;
                } else {
                    // Calculate IGST
                    $igst += $tax_amount * $quantity;
                }
            } else {
                // Calculate tax without reducing the rate if tax_inclusive_enable is not checked
                $tax_percentage = $_POST['tax_percentage'][$i];
                $tax_amount = ((float)$unit_price * (float)$tax_percentage) / 100;
                $gst_amount = $tax_amount * $quantity;
                // Check state for CGST/SGST or IGST
                if ($vendor_state === STATE) {
                    // Calculate CGST and SGST
                    $sgst += ($tax_amount * $quantity) / 2;
                    $cgst += ($tax_amount * $quantity) / 2;
                } else {
                    // Calculate IGST
                    $igst += $tax_amount * $quantity;
                }

                //$amount += $tax_amount * $quantity;
            }
        }

        // Calculate discount if enabled
        if ($discount_percentage > 0) {
            $discount_amount = ($unit_price * $discount_percentage) / 100;
            $unit_price -= $discount_amount;
            $discount_total += $discount_amount * $quantity;
        } else if ($discount_amount > 0) {
            $unit_price -= $discount_amount;
            $discount_total += $discount_amount * $quantity;
        } else {
            $discount_amount = 0;
        }
        $amount = $quantity * $unit_price;

        try {
            // Fetch the latest purchase price and unit of measure
            $procedure_params = [
                ['value' => $item_id, 'type' => 'i']
            ];
            $response = callProcedure('fetch_invoice_latest_price_unit', $procedure_params);
            if ($response['status'] == 'success') {
                $data = $response['data'][0];
                $purchase_price = $data['unit_price'];
                $purchase_unit_of_measure = $data['unit_of_measure'];
                // Convert purchase price to invoice unit of measure
                $converted_purchase_price = convertUnitAmount($purchase_price, $purchase_unit_of_measure, $invoice_unit_of_measure);

                // Compare the unit price with the converted purchase price
                $price_comparison = ($unit_price > $converted_purchase_price) ? 1 : 0;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch invoice details']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }

        $net_total += $amount;


        $items[] = [
            'item_id' => $item_id,
            'quantity' => $quantity,
            'rate' => number_format((float)$unit_price, 2, '.', ''),
            'amount' => number_format((float)$amount, 2, '.', ''),
            //'discount_percentage' => $discount_percentage,
            'discount_amount' => number_format((float)$discount_amount, 2, '.', ''),
            'price_comparison' => $price_comparison,
            'unit_of_measure' => $invoice_unit_of_measure,
            'gst_amount' => number_format((float)$gst_amount, 2, '.', '')
        ];
    }

    $total_value = $net_total + $sgst + $cgst + $igst + $_POST['shipping_charges'] + $_POST['handling_fees_amount'] + $_POST['storage_fees'];
    $adjustment_amount = $_POST['adjustment'];
    $grand_total = $total_value + $adjustment_amount;
    $response = [
        'status' => 'success',
        'data' => [
            'nettotal' => number_format((float)$net_total, 2, '.', ''),
            'sgst' => number_format((float)$sgst, 2, '.', ''),
            'cgst' => number_format((float)$cgst, 2, '.', ''),
            'igst' => number_format((float)$igst, 2, '.', ''),
            'total_value' => number_format((float)$total_value, 2, '.', ''),
            'total_gst_amount' => number_format((float)$sgst, 2, '.', '') + number_format((float)$cgst, 2, '.', '') + number_format((float)$igst, 2, '.', ''),
            'grand_total' => number_format((float)$grand_total, 2, '.', ''),
            'amount_in_words' => capitalizeFirstLetter(convertNumberToWords($grand_total)),
            'items' => $items // Include the items array in the response
        ]
    ];

    echo json_encode($response);
    exit;
}

$response = [
    'status' => 'error',
    'message' => 'Invalid request'
];

echo json_encode($response);
