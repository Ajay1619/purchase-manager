<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $data = $_POST;
    $inventory_unit_of_measure = isset($data['inventory_unit_of_measure'])?$data['inventory_unit_of_measure']:'' ;
    $quantity_in_stock = isset($data['quantity_in_stock'])?$data['quantity_in_stock']:'' ;
    $quotation_quantity = isset($data['quotation_quantity'])?$data['quotation_quantity']:'' ; 
    $quotation_unit_of_measure = isset($data['quotation_unit_of_measure'])?$data['quotation_unit_of_measure']:'' ;
    $quotation_item_name =isset($data['quotation_item_name'])?$data['quotation_item_name']:'' ; 
    $item_id = isset($data['item_id'])?$data['item_id']:'' ;
    $product_code = isset($data['product_code'])?$data['product_code']:'' ;

    if(isset($quantity_in_stock) && $quantity_in_stock!=''){
    $converted_quantities = [];
    foreach ($quotation_quantity as $index => $quantity) {
        $fromUnit = $quotation_unit_of_measure[$index];
        $toUnit = $inventory_unit_of_measure[$index];
        $converted_quantities[] = convertUnitQuantity($quantity, $fromUnit, $toUnit);
    }

            foreach ($converted_quantities as $key => $value) {
        if ($quantity_in_stock[$key] < $value) {
            try {
                $vendor_params = [
                    ['value' => $item_id[$key], 'type' => 'i']
                ];
                $vendors_result = callProcedure('fetch_vendors_from_purchase_order', $vendor_params);
                if ($vendors_result['status'] == 'success') {
                    $data = [
                        'vendor_details' => $vendors_result['data'],
                        'product_data' => [
                            'unit_of_measure' => $inventory_unit_of_measure[$key],
                            'product_code' => $product_code[$key],
                            'product_name' => $quotation_item_name[$key],
                            'product_id' => $item_id[$key],
                            'order_quantity' => $value
                        ]
                    ];

                    echo json_encode(['status' => 'warning', 'message' => $quotation_item_name[$key] . ' is less than the Quanity in Stock', 'data' => $data]);
                    exit;
                }
            } catch (\Throwable $th) {
                echo json_encode(['status' => 'error', 'message' => 'There is an Error in Fetching Vendor Details']);
                exit;
            }
        }
    }
    }

    $totalAmount = 0.00;
    $rowCounter = count($data['quotation_quantity']);
    $amounts = []; // Array to store the amount for each row


    // Calculate total amount and store amounts per row
    for ($i = 0; $i < $rowCounter; $i++) {
        $quantity = isset($data['quotation_quantity'][$i]) ? (float)$data['quotation_quantity'][$i] : 0;
        $rate = isset($data['quotation_rate'][$i]) ? (float)$data['quotation_rate'][$i] : 0;
        $amount = $quantity * $rate;
        $totalAmount += $amount;
        $amounts[] = number_format($amount, 2, '.', ''); // Store formatted amount for each row
    }

    // Calculate discount and grand total
    $discountPercentage = isset($data['discount_percentage']) ? (float)$data['discount_percentage'] : 0;
    $discountAmount = $totalAmount * $discountPercentage / 100;
    $adjustmentAmount = isset($data['adjustment-amount']) ? (float)$data['adjustment-amount'] : 0;
    $grandTotal = $totalAmount - $discountAmount + $adjustmentAmount;

    $amountInWords = convertNumberToWords($grandTotal);
    $amountInWords = capitalizeFirstLetter($amountInWords);

    // Return JSON response with amounts array
    echo json_encode([
        'status' => 'success',
        'subtotal' => number_format($totalAmount, 2, '.', ''),
        'discount_amount' => number_format($discountAmount, 2, '.', ''),
        'grand_total' => number_format($grandTotal, 2, '.', ''),
        'amounts' => $amounts, // Include the array of amounts
        'amount_in_words' => $amountInWords
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
