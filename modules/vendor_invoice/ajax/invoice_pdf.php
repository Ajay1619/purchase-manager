<?php
require_once('../../../config/sparrow.php');
require_once('../../../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Retrieve data from POST request
    $in_id = $_POST['in_id'];
    $in_code = $_POST['in_code'];

    // Call stored procedure to fetch purchase order details
    $inputParams = [
        ['type' => 'i', 'value' => $in_id]
    ];
    $response = callProcedure('fetch_view_vendor_invoice_details', $inputParams);
    $logo = callProcedure('GetFirmProfileLogo');
    if ($logo['status'] == 'success' && isset($logo['data'][0])) {
        $firm_logo = $logo['data'][0];
        //  print_r($firm_logo);
        $IMAGE = GLOBAL_PATH . '/files/logo/'  . $firm_logo['logo'];
        //      print_r($IMAGE);




    }
    if ($response['status'] == 'success' && isset($response['data'][0])) {
        // Extract data from response
        $purchase_order = $response['data'][0];
        // print_r($purchase_order);

        // Create a new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Order - ' . $in_code);
        $pdf->SetSubject('Purchase Order Details');
        $pdf->SetKeywords('TCPDF, PDF, Purchase Order');
        $pdf->setHeaderMargin(-15);
        // Set margins to 0 for full-screen content
        $pdf->SetMargins(5, 0, 5); // Remove all margins
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

        /* Section styles */
        .section {
            margin-bottom: 15px;
        }

        .topic{
            margin-top:100px;
    border: 0px solid black;
            }
            img{
           
      width:75px;
      height:75px;
      
      }
        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
             text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Label styling */
        label {
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
            .topic{
                margin-top:100px;
        border: 0px solid black;
                }
                img{
               
          width:75px;
          height:75px;
          
          }
        }
        </style>
        
        <div class="view-container">
            <div class="section">
            <table class="topic">
            <tr>
            
            <td> <h2 class="section-title">Vendor\'s Invoice Details</h2> </td>
            <td><img src="' . htmlspecialchars($IMAGE) . '" alt="Company Logo"> </td>
          
            </tr>
    </table>
                <table>
                    <tr>
                        <td><label for="customer-name">Vendor Name:</label> <span class="value">' . htmlspecialchars($purchase_order['vendor_contact_name']) . '</span></td>
                        <td><label for="invoice-number">Invoice Number:</label> <span class="value">' . htmlspecialchars($purchase_order['vendor_invoice_number']) . '</span></td>
                        <td><label for="invoice-date">Invoice Date:</label> <span class="value">' . date('Y-m-d', strtotime($purchase_order['invoice_date'])) . '</span></td>
                    </tr>
                    <tr>
                        <td><label for="due-date">Due Date:</label> <span class="value">' . date('Y-m-d', strtotime($purchase_order['invoice_due_date'])) . '</span></td>
                        <td colspan="2"><label for="billing-address">Billing Address:</label>
                            <div>' . htmlspecialchars($purchase_order['billing_address_street']) . '<br>
                           ' . htmlspecialchars($purchase_order['billing_address_locality']) . ', ' . htmlspecialchars($purchase_order['billing_address_district']) . ', ' . htmlspecialchars($purchase_order['billing_address_state']) . ' - ' . htmlspecialchars($purchase_order['billing_address_pincode']) . '</div>
                        </td>
                        
                    </tr>
                    <tr>
                        <td colspan="3"><label for="shipping-address">Shipping Address:</label>
                            <div>' . htmlspecialchars($purchase_order['shipping_address_street']) . '<br>' . htmlspecialchars($purchase_order['shipping_address_locality']) . ', ' . htmlspecialchars($purchase_order['shipping_address_district']) . ', ' . htmlspecialchars($purchase_order['shipping_address_state']) . ' - ' . htmlspecialchars($purchase_order['shipping_address_pincode']) . '</div>
                        </td>
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
                    <tbody id="items-ordered">';

        // Assuming items are in the second element of the response
        foreach ($response['data'][1] as $index => $item) {
            $html .= '
                        <tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . htmlspecialchars($item['product_name']) . '</td>
                            <td>' . htmlspecialchars($item['product_unit_of_measure']) . '</td>
                            <td>' . htmlspecialchars($item['quantity']) . '</td>
                            <td>' . htmlspecialchars($item['unit_price']) . '</td>
                            <td>' . htmlspecialchars($item['amount']) . '</td>
                        </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </div>

            <div class="section">
                <h3 class="section-title">Summary</h3>
                <table>
                    <tr>
                        <td><label for="subtotal">Subtotal (' . CURRENCY . '):</label> <span class="value">' . htmlspecialchars($purchase_order['subtotal']) . '</span></td>
                        <td><label for="sgst">S GST Amount (' . CURRENCY . '):</label> <span class="value">' . htmlspecialchars($purchase_order['sgst']) . '</span></td>
                        <td><label for="cgst">C GST Amount (' . CURRENCY . '):</label> <span class="value">' . htmlspecialchars($purchase_order['cgst']) . '</span></td>
                    </tr>
                    <tr>
                        <td><label for="igst">I GST Amount (' . CURRENCY . '):</label> <span class="value">' . htmlspecialchars($purchase_order['igst']) . '</span></td>
                        <td><label for="adjustment">Adjustment (' . CURRENCY . '):</label> <span class="value">' . htmlspecialchars($purchase_order['adjustments']) . '</span></td>
                        <td><label for="grand-total">Grand Total (' . CURRENCY . '):</label> <span class="value">' . htmlspecialchars($purchase_order['grand_total']) . '</span></td>
                    </tr>
                    <tr>
                        <td colspan="2"><label for="amount-in-words">Amount In Words:</label> <span class="value">' . htmlspecialchars($purchase_order['amount_in_words']) . '</span></td>
                        <td><label for="status">Status:</label> <span class="badge">' . htmlspecialchars($purchase_order['invoice_status'] == 0 ? 'Pending' : 'Paid') . '</span></td>
                    </tr>
                </table>
            </div>
        </div>';

        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Set file name based on purchase order code
        $pdf_file_name = "Purchase_Order_" . $in_code . ".pdf";

        // Output PDF to the browser (force download)
        $pdf->Output($pdf_file_name, 'D');
    }
} else {
    echo "Invalid request.";
}
