<?php
require_once('../../../config/sparrow.php');
require_once('../../../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Retrieve data from POST request
    $qo_id = $_POST['qo_id'];
    $qo_code = $_POST['qo_code'];

    // Call stored procedure to fetch Quotation details
    $inputParams = [
        ['type' => 'i', 'value' => $qo_id]
    ];
    $response = callProcedure('fetch_quotation_details', $inputParams);

    if ($response['status'] == 'success' && isset($response['data'][0])) {
        // Extract data from response
        $purchase_order = $response['data'][0];
        $items = $response['data'][1]; // Assuming items are in the second array element

        // Create a new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Quotation - ' . $qo_code);
        $pdf->SetSubject('Quotation Details');
        $pdf->SetKeywords('TCPDF, PDF, Quotation');

        // Set margins to 0 for full-screen content
        $pdf->SetMargins(0, 0, 0); // Remove all margins
        $pdf->SetAutoPageBreak(FALSE, 0); // Disable automatic page breaks

        // Set default font and add a page
        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();

        // HTML content with placeholders replaced by actual data
        $html = '
        <style>
        /* General styling for the PDF content */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        
        /* Container for the content */
        .view-container {
            width: 100%;
            box-sizing: border-box;
            padding: 10mm;
        }
        
        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            overflow-x: auto;
        }
        
        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        /* Responsive adjustments */
        @media print {
            body {
                margin: 0;
            }
        
            .view-container {
                padding: 0;
            }
        }
        </style>
        
        <div class="view-container">
            <div class="section">
                <h2 class="section-title">Quotation Details</h2>
                <table>
                    <tr>
                        <td><strong>Customer Name:</strong></td>
                        <td>' . $purchase_order['customer_name'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Quotation Number:</strong></td>
                        <td>' . $purchase_order['quotation_number'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>' . date('Y-m-d', strtotime($purchase_order['quotation_date'])) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Customer Contact Name:</strong></td>
                        <td>' . $purchase_order['customer_name'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Customer Contact Number:</strong></td>
                        <td>' . $purchase_order['customer_phone_number'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>GSTIN:</strong></td>
                        <td>' . $purchase_order['customer_gstin'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Billing Address:</strong></td>
                        <td>' . $purchase_order['address_street'] . '<br>' .
            $purchase_order['address_locality'] . '<br>' .
            $purchase_order['address_city'] . '<br>' .
            $purchase_order['address_district'] . '<br>' .
            $purchase_order['address_state'] . ' - ' .
            $purchase_order['address_pincode'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Shipping Address:</strong></td>
                        <td>' . $purchase_order['address_street'] . '<br>' .
            $purchase_order['address_locality'] . '<br>' .
            $purchase_order['address_city'] . '<br>' .
            $purchase_order['address_district'] . '<br>' .
            $purchase_order['address_state'] . ' - ' .
            $purchase_order['address_pincode'] . '</td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h3 class="section-title">Items Ordered</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Item Name</th>
                            <th>Unit of Measure</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>';

        // Populate items table rows
        foreach ($items as $index => $item) {
            $html .= '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . $item['product_name'] . '</td>
                            <td>' . $item['product_unit_of_measure'] . '</td>
                            <td>' . $item['quantity'] . '</td>
                            <td>' . formatNumberIndian($item['unit_price']) . '</td>
                            <td>' . formatNumberIndian($item['amount']) . '</td>
                        </tr>';
        }

        // Closing tags for HTML content
        $html .= '
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h3 class="section-title">Summary</h3>
                <table>
                    <tr>
                        <td><strong>Subtotal:</strong></td>
                        <td>' . formatNumberIndian($purchase_order['subtotal']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Discount Percentage:</strong></td>
                        <td>' . $purchase_order['discount'] . '</td>
                    </tr>
                    <tr>
                        <td><strong>Discount Amount:</strong></td>
                        <td>' . formatNumberIndian($purchase_order['discount_amount']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Adjustment:</strong></td>
                        <td>' . formatNumberIndian($purchase_order['adjustment']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Grand Total:</strong></td>
                        <td>' . formatNumberIndian($purchase_order['grand_total']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Amount In Words:</strong></td>
                        <td>' . $purchase_order['amount_in_words'] . '</td>
                    </tr>
                </table>
            </div>
        </div>';

        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF document
        $pdf->Output('purchase_order_' . $qo_code . '.pdf', 'I');
    } else {
        // Error handling if data fetch fails
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch quotation details.']);
    }
} else {
    // Handle non-AJAX requests here
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
