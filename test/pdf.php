<?php
// Include the TCPDF library
require_once('../packages/vendor/tecnickcom/tcpdf/examples/tcpdf_include.php');


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 061');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
  $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(15, 0, 15);
$pdf->setHeaderMargin(0);
$pdf->setFooterMargin(0);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(1.5);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->setFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

// Set A4 page size without margins
//$pdf->setPageFormat('A4', 'P'); // A4 size and portrait mode

// Define the HTML content with CSS including a funky design
$html = '
    <style>
    /* Reset and Basic Styling */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        color: #333;
    }

    /* Container */
    .container {
        max-width: 100%;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #e0e0e0;
    }

    .header .logo {
        max-width: 150px;
    }

    .header .logo img {
        width: 100%;
        height: auto;
    }

    .header .po-details {
        text-align: right;
    }

    .header .po-details h1 {
        font-size: 24px;
        color: #2c3e50;
    }

    .header .po-details p {
        font-size: 14px;
    }

    /* Section Titles */
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 1px solid #e0e0e0;
    }

    /* Details Tables */
    .details-table {
        width: 100%;
        border-collapse: collapse;
    }

    .details-table td {
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
    }

    .address-block b {
        color: #2c3e50;
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
    }

    .items-table tr th {
        background-color: blue;
        color: #ffffff;
    }

    .items-table th,
    .items-table td {
        border: 1px solid #ddd;
        text-align: center;
        font-size: 14px;
    }

    .items-table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Totals Section */
    .totals {
        float: right;
        width: 40%;
    }

    .totals table {
        width: 100%;
        border-collapse: collapse;
    }

    .totals td {
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
        text-align: center;
        font-size: 12px;
        color: #888;
    }

    /* Responsive Design */
  
</style>



    <div class="container">
        <!-- Header Section -->
        <div class="header">
           
            <div class="po-details">
                <h1>Purchase Order</h1>
                <p><b>PO Number:</b> PO12345</p>
                <p><b>Date:</b> 2024-10-05</p>
            </div>
        </div>

        <!-- Purchase Order Details -->
        <div class="section">
            <div class="section-title">Vendor Information</div>
            <table class="details-table">
                <tr>
                    <td><b>Vendor Company Name:</b> XYZ Pvt Ltd</td>
                    <td><b>GSTIN:</b> 22AAAAA0000A1Z5</td>
                </tr>
                <tr>
                    <td><b>Vendor Contact Name:</b> John Doe</td>
                    <td><b>Vendor Contact Number:</b> +91-9876543210</td>
                </tr>
            </table>
        </div>

        <!-- Billing and Shipping Addresses -->
        <div class="section">
            <div class="address-block">
                <b>Billing Address:</b>
                <p>
                    123 Main St., Park Avenue<br>
                    New York, NY 10001<br>
                    United States
                </p>
            </div>
            <div class="address-block">
                <b>Shipping Address:</b>
                <p>
                    456 Elm St., Lakeview Road<br>
                    Los Angeles, CA 90001<br>
                    United States
                </p>
            </div>
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
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Sample Item 1</td>
                        <td>pcs</td>
                        <td>5</td>
                        <td>$10.00</td>
                        <td>$50.00</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Sample Item 2</td>
                        <td>box</td>
                        <td>2</td>
                        <td>$25.00</td>
                        <td>$50.00</td>
                    </tr>
                    <!-- Add more items as needed -->
                </tbody>
            </table>
        </div>

        <!-- Totals Section -->
        <div class="totals">
            <table>
                <tr>
                    <td class="description">Subtotal:</td>
                    <td class="amount">$100.00</td>
                </tr>
                <tr>
                    <td class="description">Tax (10%):</td>
                    <td class="amount">$10.00</td>
                </tr>
                <tr>
                    <td class="description"><b>Total:</b></td>
                    <td class="amount"><b>$110.00</b></td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            Thank you for your business!<br>
            Please make the payment within 30 days.
        </div>
    </div>


';

// Write HTML content to the PDF
$pdf->writeHTML($html, true, false, true, false, '');
// echo $html;

// Output the PDF as a file (or send it to the browser)
$pdf->Output('funky_example.pdf', 'I'); // Use 'I' to send to browser, 'D' to download
