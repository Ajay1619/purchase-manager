<?php
require_once('../../../config/sparrow.php');

// Check if the request is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    // Check if 'invoice_id' is set in GET parameters
    if (isset($_POST['invoice_id'])) {
        $invoice_id = $_POST['invoice_id'];

        // Prepare input parameters for the stored procedure
        $inputParams = [
            ['type' => 'i', 'value' => $invoice_id]
        ];

        // Call stored procedure to fetch invoice details and items
        try {
            $response = callProcedure('fetch_view_invoice_details', $inputParams);
            if ($response['status'] == 'success') {
                // Fetch result sets from response
                $results = $response['data'];
                $invoiceDetails = $results[0]; // First result set is invoice details
                $itemDetails = $results[1]; // Second result set is item details

                if (!isset($_POST['fetch_type'])) {
                    foreach ($itemDetails as $key => $item) {
                        // Check if the values are not empty before formatting
                        $itemDetails[$key]['unit_price'] = !empty($item['unit_price']) ? formatNumberIndian($item['unit_price']) : $item['unit_price'];
                        $itemDetails[$key]['amount'] = !empty($item['amount']) ? formatNumberIndian($item['amount']) : $item['amount'];
                        $itemDetails[$key]['discount_amount'] = !empty($item['discount_amount']) ? formatNumberIndian($item['discount_amount']) : $item['discount_amount'];
                    }

                    // Check if the values are not empty before formatting
                    $invoiceDetails['cgst'] = !empty($invoiceDetails['cgst']) ? formatNumberIndian($invoiceDetails['cgst']) : $invoiceDetails['cgst'];
                    $invoiceDetails['sgst'] = !empty($invoiceDetails['sgst']) ? formatNumberIndian($invoiceDetails['sgst']) : $invoiceDetails['sgst'];
                    $invoiceDetails['igst'] = !empty($invoiceDetails['igst']) ? formatNumberIndian($invoiceDetails['igst']) : $invoiceDetails['igst'];
                    $invoiceDetails['grand_total'] = !empty($invoiceDetails['grand_total']) ? formatNumberIndian($invoiceDetails['grand_total']) : $invoiceDetails['grand_total'];
                    $invoiceDetails['subtotal'] = !empty($invoiceDetails['subtotal']) ? formatNumberIndian($invoiceDetails['subtotal']) : $invoiceDetails['subtotal'];
                }

                // Check if the date values are not empty before using strtotime
                $invoiceDetails['invoice_date'] = !empty($invoiceDetails['invoice_date']) ? date(DATE_FORMAT, strtotime($invoiceDetails['invoice_date'])) : null;
                $invoiceDetails['invoice_due_date'] = !empty($invoiceDetails['invoice_due_date']) ? date(DATE_FORMAT, strtotime($invoiceDetails['invoice_due_date'])) : null;

                $invoiceDetails['edit_invoice_date'] = !empty($invoiceDetails['invoice_date']) ? date("Y-m-d", strtotime($invoiceDetails['invoice_date'])) : null;
                $invoiceDetails['edit_invoice_due_date'] = !empty($invoiceDetails['invoice_due_date']) ? date("Y-m-d", strtotime($invoiceDetails['invoice_due_date'])) : null;

                // Return the result with status
                $result = [
                    'status' => 'success',
                    'invoice_details' => $invoiceDetails,
                    'item_details' => $itemDetails
                ];

                echo json_encode($result);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to fetch invoice details']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invoice ID is not provided']);
    }
} else {
    // Handle non-AJAX requests or direct access to this file
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
