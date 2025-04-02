<?php
require_once('../../../config/sparrow.php');
require_once('../../../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

    // Retrieve data from POST request
    $po_id = $_POST['purchase_order_id'];
    $po_code = $_POST['purchase_order_number'];

    // Call stored procedure to fetch purchase order details
    $inputParams = [
        ['type' => 'i', 'value' => $po_id]
    ];
    $response = callProcedure('fetch_purchase_order_details', $inputParams);
    $logo = callProcedure('GetFirmProfileLogo');
    // print_r($logo);
    if ($logo['status'] == 'success' && isset($logo['data'][0])) {
        $firm_logo = $logo['data'][0];
        //  print_r($firm_logo);
        $IMAGE = GLOBAL_PATH . '/files/logo/'  . $firm_logo['logo'];
        //      print_r($IMAGE);




    }


    if ($response['status'] == 'success' && isset($response['data'][0])) {
        // Extract data from response
        $statusText = '';

        $purchase_order = $response['data'][0];
        if (isset($purchase_order['purchase_order_status'])) {
            switch ($purchase_order['purchase_order_status']) {
                case 0:
                    $statusText = 'Pending';
                    break;
                case 1:
                    $statusText = 'Purchased';
                    break;
                case 2:
                    $statusText = 'Canceled';
                    break;
                default:
                    $statusText = 'Unknown'; // Optional: in case there's an unexpected status
            }
        }

        // Create a new TCPDF object
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Order - ' . $po_code);
        $pdf->SetSubject('Purchase Order Details');
        $pdf->SetKeywords('TCPDF, PDF, Purchase Order');
        //header


        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // Set margins to 0 for full-screen content
        $pdf->SetMargins(5, 0, 0); // Remove all margins
        $pdf->setHeaderMargin(0);
        $pdf->setFooterMargin(0);
        $pdf->SetAutoPageBreak(FALSE, 4); // Disable automatic page breaks
        // set default monospaced font
        $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins



        // set auto page breaks
        // $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(1.61);
        // Set default font and add a page
        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();

        // HTML content with placeholders replaced by actual data
        $html = '
        
       <style>
  /* Reset and Basic Styling */
  * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
  }

  body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f9;
      color: #333;
      padding: 20px;
      font-size: 12px;
      margin: 0;
      padding: 0;
  }

  /* Container */
  .container {
      max-width: 900px;
      margin: 0 auto;
      background-color: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  /* Header */
  .header {
      border-bottom: 2px solid #e0e0e0;
      padding-bottom: 20px;
      margin-bottom: 20px;
  }

  .header-table {
      width: 100%;
  }

  .header .logo img {
      width: 150px;
      height: auto;
  }

  .header .po-details h1 {
      font-size: 24px;
      margin-bottom: 10px;
      color: #2c3e50;
  }

  .header .po-details p {
      margin-bottom: 5px;
      font-size: 14px;
  }

  /* Section Titles */
  .section-title {
      font-size: 18px;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 15px;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 5px;
  }

  /* Details Tables */
  .details-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
  }

  .details-table td {
      padding: 10px;
      vertical-align: top;
      font-size: 14px;
  }

  .details-table td b {
      display: inline-block;
      width: 180px;
      color: #555;
  }

  /* Address Blocks */
  .address-block {
      margin-bottom: 20px;
  }

  .address-block b {
      color: #2c3e50;
      margin-bottom: 5px;
      display: block;
      font-size: 14px;
  }

  .address-block p {
      font-size: 14px;
      line-height: 1.6;
      color: #555;
  }

  /* Items Table */
  .items-table {
  
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
  }

  .items-table thead {
  border: 1px solid black;
     background-color: #000000;
      color: #ffffff;
  }

  .items-table th {
      padding: 12px;
       background-color: light-blue;
      border: 1px solid black;
      text-align: center;
      font-size: 14px;
  }
       .items-table td {
      padding: 12px;
       
      border: 1px solid black;
      text-align: center;
      font-size: 14px;
  }
     


  .items-table tbody tr:hover {
      background-color: #f1f1f1;
  }

  /* Totals Section */
  .totals {
      margin-top: 20px;
  }

  .totals table {
      width: 100%;
      border-collapse: collapse;
  }

  .totals td {
      padding: 10px;
      font-size: 14px;
  }

  .totals td.description {
      text-align: right;
      color: #555;
  }

  .totals td.amount {
      text-align: right;
      color: #2c3e50;
      font-weight: 600;
  }

  /* Footer */
  .footer {
      clear: both;
      border-top: 2px solid #e0e0e0;
      padding-top: 20px;
      text-align: center;
      font-size: 12px;
      color: #888;
  }
      img{
      width:250px;
      height:250px;
      border-radius: 30px;
      }
</style>

<div class="container">
  <!-- Header Section -->
  <div class="header">
      <table class="header-table">
          <tr>
              <td class="logo">
                  <!-- Replace the src with your company logo -->
                  
                  <img src="' . htmlspecialchars($IMAGE) . '" alt="Company Logo">
              </td>
              <td class="po-details" style="text-align:right;">
                  <h1>Purchase Order</h1>
                  <p><b>PO Number:</b> ' . htmlspecialchars($purchase_order['purchase_order_number']) . '</p>
                  <p><b>Date:</b> ' . htmlspecialchars($purchase_order['purchase_order_date']) . '</p>
                  <p><b>Status:</b> ' . htmlspecialchars($statusText) . '</p>
                  <p><b>Purchased Date:</b> ' .
            (isset($purchase_order['purchased_date']) && !is_null($purchase_order['purchased_date'])
                ? htmlspecialchars($purchase_order['purchased_date'])
                : '') .
            '</p>
                 

              </td>
          </tr>
      </table>
  </div>

  <!-- Purchase Order Details -->
  <div class="section">
      <div class="section-title">Vendor Information</div>
      <table class="details-table">
          <tr>
          <br>
              <td><b>Vendor Company Name:</b> ' . htmlspecialchars($purchase_order['vendor_company_name']) . '</td>
              
              <td><b>GSTIN:</b> ' . htmlspecialchars($purchase_order['vendor_gstin']) . '</td>
          </tr>
          <tr>
          <br>
              <td><b>Vendor Contact Name:</b> ' . htmlspecialchars($purchase_order['vendor_contact_name']) . '</td>
              
              <td><b>Vendor Contact Number:</b> ' . htmlspecialchars($purchase_order['vendor_phone_number']) . '</td>
          </tr>
      </table>
  </div>

  <!-- Billing and Shipping Addresses -->
  <div class="section">
<table class="">
<tr>
<td>
 <div class="address-block">
          <b>Billing Address:</b>
          <p>
              ' . nl2br(htmlspecialchars($purchase_order['billing_address_street'])) . '<br>
              ' . nl2br(htmlspecialchars($purchase_order['billing_address_locality'])) . '<br>
              ' . htmlspecialchars($purchase_order['billing_address_city']) . ', ' . htmlspecialchars($purchase_order['billing_address_state']) . ' ' . htmlspecialchars($purchase_order['billing_address_pincode']) . '<br>
              ' . htmlspecialchars($purchase_order['billing_address_district']) . '
          </p>
      </div>
</td>
<td>
<div class="address-block">
<br>
          <b>Shipping Address:</b>
          <p>
              ' . nl2br(htmlspecialchars($purchase_order['shipping_address_street'])) . '<br>
              ' . nl2br(htmlspecialchars($purchase_order['shipping_address_locality'])) . '<br>
              ' . htmlspecialchars($purchase_order['shipping_address_city']) . ', ' . htmlspecialchars($purchase_order['shipping_address_state']) . ' ' . htmlspecialchars($purchase_order['shipping_address_pincode']) . '<br>
              ' . htmlspecialchars($purchase_order['shipping_address_district']) . '
          </p>
      </div>
</td>
</tr>

<tr>
<td>

</td>
</tr>
</table>
     
      
  </div>

  <!-- Items Ordered -->
  <div class="section">
      <div class="section-title">Items Ordered</div>
      <table class="items-table">
          <thead>
              <tr>
                  <th>Sl No</th>
                  <th>Item Name</th>
                  <th>Unit</th>
                  <th>Quantity</th>
                  <th>Rate</th>
                  <th>Amount</th>
              </tr>
          </thead>
          <tbody>';

        foreach ($response['data'][1] as $item) {
            $html .= '
              <tr>
                  <td>' . htmlspecialchars($item['purchase_order_item_id']) . '</td>
                  <td>' . htmlspecialchars($item['product_name']) . '</td>
                  <td>' . htmlspecialchars($item['unit_of_measure']) . '</td>
                  <td>' . htmlspecialchars($item['quantity']) . '</td>
                  <td>' . htmlspecialchars($item['unit_price']) . '</td>
                  <td>' . htmlspecialchars($item['amount']) . '</td>
              </tr>';
        }

        $html .= '
          </tbody>
      </table>
  </div>

  <!-- Summary Section -->
  <div class="section">
      <div class="section-title">Summary</div>
      <div class="totals">
          <table>
              <tr>
                  <td class="description"><b>Subtotal:</b></td>
                  <td class="amount">' . htmlspecialchars($purchase_order['subtotal']) . '</td>
              </tr>
              <tr>
                  <td class="description"><b>Discount Percentage:</b></td>
                  <td class="amount">' . htmlspecialchars($purchase_order['discount']) . '%</td>
              </tr>
              <tr>
                  <td class="description"><b>Discount Amount:</b></td>
                  <td class="amount">' . htmlspecialchars($purchase_order['discount_amount']) . '</td>
              </tr>
              <tr>
                  <td class="description"><b>Adjustment:</b></td>
                  <td class="amount">' . htmlspecialchars($purchase_order['adjustment']) . '</td>
              </tr>
              <tr>
                  <td class="description"><b>Grand Total:</b></td>
                  <td class="amount">' . htmlspecialchars($purchase_order['grand_total']) . '</td>
              </tr>
              <tr>
                  <td class="description"><b>Amount In Words:</b></td>
                  <td class="amount">' . htmlspecialchars($purchase_order['amount_in_words']) . '</td>
              </tr>
          </table>
      </div>
  </div>

  <!-- Footer Section -->
 
</div>
';

        // Write HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Set file name based on purchase order code
        $pdf_file_name = "Purchase_Order_" . $po_code . ".pdf";

        // Output PDF to the browser (force download)
        $pdf->Output($pdf_file_name, 'D');
    }
} else {
    echo "Invalid request.";
}
