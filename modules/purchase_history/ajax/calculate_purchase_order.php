<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $data = $_POST;
    $totalAmount = 0.00;
    $rowCounter = count($data['purchase_order_quantity']);
    $amounts = []; // Array to store the amount for each row

    // Calculate total amount and store amounts per row
    for ($i = 0; $i < $rowCounter; $i++) {
        $quantity = isset($data['purchase_order_quantity'][$i]) ? (float)$data['purchase_order_quantity'][$i] : 0;
        $rate = isset($data['purchase_order_rate'][$i]) ? (float)$data['purchase_order_rate'][$i] : 0;
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
