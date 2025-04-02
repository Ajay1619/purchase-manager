<?php
require_once('../../../config/sparrow.php');
require_once('../../../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Retrieve data from POST request
    $dc_id = $_POST['dc_id'];

    // Call stored procedure to fetch Delivery Challan details
    $inputParams = [
        ['type' => 'i', 'value' => $dc_id]
    ];
    $response = callProcedure('fetch_delivery_challan_details', $inputParams);
    $logo = callProcedure('GetFirmProfileLogo');
    if ($logo['status'] == 'success' && isset($logo['data'][0])) {
        $firm_logo = $logo['data'][0];
        //  print_r($firm_logo);
        $IMAGE = GLOBAL_PATH . '/files/logo/' . $firm_logo['logo'];
        //      print_r($IMAGE);




    }

    if ($response['status'] == 'success' && isset($response['data'][0])) {
        // Extract data from response
        $delivery_challan = $response['data'][0];
        $items = $response['data'][1];

        // Create a new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Delivery Challan - ' . $dc_id);
        $pdf->SetSubject('Delivery Challan Details');
        $pdf->SetKeywords('TCPDF, PDF, Delivery Challan');

        // Set margins to 0 for full-screen content
        $pdf->SetMargins(0, 0, 0); // Remove all margins
        $pdf->setHeaderMargin(-50);
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
        
        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
              .topic{
            margin-top:100px;
    border: 0px solid black;
            }
            img{
           
      width:100px;
      height:100px;
      
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
           
      width:100px;
      height:100px;
      
      }
        }
        </style>
        
        <div class="view-container">
            <div class="section">
                
                 <table class="topic">
                    <tr>
                    
                    <td> <h2 class="section-title">Delivery Challan Details</h2> </td>
                    <td><img src="' . htmlspecialchars($IMAGE) . '" alt="Company Logo"> </td>
                  
                    </tr>
            </table>
                <table>
                    <tr>
                        <td><strong>Delivery Challan Number:</strong> ' . $delivery_challan['delivery_challan_number'] . '</td>
                        <td><strong>Customer Name:</strong> ' . $delivery_challan['customer_name'] . '</td>
                        <td><strong>Date:</strong> ' . date('d-m-Y', strtotime($delivery_challan['delivery_challan_date'])) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong> ' . ($delivery_challan['delivery_challan_status'] == 0 ? "Pending" : "Completed") . '</td>
                        <td colspan="2"><strong>Delivery Date:</strong> ' . date('d-m-Y', strtotime($delivery_challan['delivery_date'])) . '</td>
                    </tr>
                </table>
                <div>
                    <strong>Billing Address:</strong><br>
                    ' . $delivery_challan['address_street'] . '<br>
                    ' . $delivery_challan['address_locality'] . ', ' . $delivery_challan['address_district'] . '<br>
                    ' . $delivery_challan['address_city'] . ', ' . $delivery_challan['address_state'] . ' - ' . $delivery_challan['address_pincode'] . '<br>
                    ' . $delivery_challan['address_country'] . '<br>
                </div>
                <div>
                    <strong>Shipping Address:</strong><br>
                    ' . $delivery_challan['address_street'] . '<br>
                    ' . $delivery_challan['address_locality'] . ', ' . $delivery_challan['address_district'] . '<br>
                    ' . $delivery_challan['address_city'] . ', ' . $delivery_challan['address_state'] . ' - ' . $delivery_challan['address_pincode'] . '<br>
                    ' . $delivery_challan['address_country'] . '<br>
                </div>
            </div>
        
            <div class="section">
                <h3 class="section-title">Items Delivered</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Sl No</th>
                            <th>Product Name</th>
                            <th>Unit of Measure</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="items-table-body">';

        // Populate items dynamically
        foreach ($response['data'][1] as $index => $item) {
            $html .= '<tr>
                            <td>' . ($index + 1) . '</td>
                            <td>' . $item['product_name'] . '</td>
                            <td>' . $item['unit_of_measure'] . '</td>
                            <td>' . $item['quantity'] . '</td>
                        </tr>';
        }

        $html .= '</tbody>
                </table>
            </div>
        </div>';


        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Set file name based on Delivery Challan code
        $pdf_file_name = "Delivery_Challan_" . $dc_id . ".pdf";

        // Output PDF to the browser (force download)
        $pdf->Output($pdf_file_name, 'D');
    }
} else {
    echo "Invalid request.";
}
