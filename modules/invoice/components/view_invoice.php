<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $invoice_id = $_POST['invoice_id'];
?>

        <section id="view-invoice">
            <div id="button-container">
                <button class="button" id="export-invoice">
                    Export
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Z" />
                    </svg>
                </button>
            </div>


            <div class="view-container">
                <div class="section">
                    <h2 class="section-title">Invoice Details</h2>

                    <div class="flex-container">
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="customer-name">Customer Name:</label>
                                <span class="value" id="customer-name">Customer A</span>
                            </div>
                            <div class="flex-column">
                                <label for="invoice-number">Invoice Number:</label>
                                <span class="value" id="invoice-number">In-12345</span>
                                <input type="hidden" id="in-code">
                            </div>
                            <div class="flex-column">
                                <label for="invoice-date"> Invoice Date:</label>
                                <span class="value" id="invoice-date">2024-07-01</span>
                            </div>

                        </div>
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="due-date"> Due Date:</label>
                                <span class="value" id="due-date">2024-07-01</span>
                            </div>
                            <div class="flex-column">
                                <label for="billing-address">Billing Address:</label>
                                <span id="billing-address-street">12,Rose Street</span>
                                <span id="billing-address-locality">Lawspet</span>
                                <span id="billing-address-district">Puducherry</span>
                                <span id="billing-address-state">Pondicherry</span>
                                <span class="value" id="billing-address-pincode">605008</span>
                            </div>
                            <div class="flex-column">
                                <label for="shipping-address">Shipping Address:</label>
                                <span id="shipping-address-street">12,Rose Street</span>
                                <span id="shipping-address-locality">Lawspet</span>
                                <span id="shipping-address-district">Puducherry</span>
                                <span id="shipping-address-state">Pondicherry</span>
                                <span class="value" id="shipping-address-pincode">605008</span>
                            </div>
                        </div>
                    </div>
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
                        <tbody id="items-ordered">

                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3 class="section-title">Summary</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="subtotal">Subtotal (<?= CURRENCY_SYMBOL ?>):</label>
                            <span class="value" id="subtotal">1250.00</span>
                        </div>
                        <div class="flex-column">
                            <label for="sgst">S GST Amount ( <?= CURRENCY_SYMBOL ?> ):</label>
                            <span class="value" id="sgst">200.00</span>
                        </div>
                        <div class="flex-column">
                            <label for="cgst">C GST Amount ( <?= CURRENCY_SYMBOL ?> ):</label>
                            <span class="value" id="cgst">200.00</span>
                        </div>
                    </div>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="igst">I GST Amount ( <?= CURRENCY_SYMBOL ?> ):</label>
                            <span class="value" id="igst">0.00</span>
                        </div>
                        <div class="flex-column">
                            <label for="adjustment">Adjustment ( <?= CURRENCY_SYMBOL ?> ):</label>
                            <span class="value" id="adjustment">0.00</span>
                        </div>
                        <div class="flex-column">
                            <label for="grand-total">Grand Total ( <?= CURRENCY_SYMBOL ?> ):</label>
                            <span class="value" id="grand-total">2400.00</span>
                        </div>
                    </div>
                    <div class="flex-row">

                        <div class="flex-column">
                            <label for="grand-total">Amount In Words</label>
                            <span class="value" id="grand-total">One Thousand One Hundrend And Five</span>
                        </div>
                        <div class="flex-column">
                            <label for="status">Status</label>
                            <span class="badge " id="status"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            $.ajax({
                url: '<?= MODULES . '/invoice/json/fetch_view_invoice.php' ?>',
                type: 'POST',
                data: {
                    'invoice_id': <?= $_POST['invoice_id'] ?>
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    var invoiceDetails = data.invoice_details;
                    var itemDetails = data.item_details;

                    // Populate Invoice Details
                    $('#customer-name').text(invoiceDetails.customer_name);
                    $('#invoice-number').text(invoiceDetails.invoice_number);
                    $('#in-code').val(invoiceDetails.invoice_number);
                    $('#invoice-date').text(invoiceDetails.invoice_date);
                    $('#due-date').text(invoiceDetails.invoice_due_date);
                    $('#billing-address-street').text(invoiceDetails.address_street);
                    $('#billing-address-locality').text(invoiceDetails.address_locality);
                    $('#billing-address-district').text(invoiceDetails.address_district);
                    $('#billing-address-state').text(invoiceDetails.address_state);
                    $('#billing-address-pincode').text(invoiceDetails.address_pincode);
                    $('#shipping-address-street').text(invoiceDetails.address_street);
                    $('#shipping-address-locality').text(invoiceDetails.address_locality);
                    $('#shipping-address-district').text(invoiceDetails.address_district);
                    $('#shipping-address-state').text(invoiceDetails.address_state);
                    $('#shipping-address-pincode').text(invoiceDetails.address_pincode);
                    $('#status').text(invoiceDetails.invoice_status == 0 ? 'Unpaid' : 'Paid');
                    if (invoiceDetails.invoice_status == 0) {
                        $('#status').addClass('badge-alert');
                    } else {
                        $('#status').addClass('badge-success');
                    }
                    $('#subtotal').text(invoiceDetails.subtotal);
                    $('#discount-percentage').text(invoiceDetails.discount_percentage + ' %');
                    $('#gst-percentage').text(invoiceDetails.sgst ? '18%' : '0%'); // Adjust this based on your tax structure
                    $('#discount-amount').text(invoiceDetails.discount_amount);
                    $('#sgst').text(invoiceDetails.sgst);
                    $('#cgst').text(invoiceDetails.cgst);
                    $('#igst').text(invoiceDetails.igst);
                    $('#adjustment').text(invoiceDetails.adjustments);
                    $('#grand-total').text(invoiceDetails.grand_total);

                    // Populate Items Ordered
                    var itemsHtml = '';
                    itemDetails.forEach(function(item, index) {
                        itemsHtml += `<tr>
                <td>${index + 1}</td>
                <td>${item.product_name}</td>
                <td>${item.unit_of_measure}</td>
                <td>${item.quantity}</td>
                <td>${item.unit_price}</td>
                <td>${item.amount}</td>
            </tr>`;
                    });
                    $('#items-ordered').html(itemsHtml);
                }
            });

            $('#export-invoice').on('click', function() {
                $.ajax({
                    url: '<?= MODULES . '/invoice/ajax/invoice_pdf.php' ?>',
                    type: 'POST',
                    data: {
                        in_id: <?= $invoice_id ?>,
                        in_code: $('#in-code').val()
                    },
                    xhrFields: {
                        responseType: 'blob' // Set the response type to blob
                    },
                    success: function(response) {
                        console.log(response)
                        // Create a link to download the PDF
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "invoice.pdf"; // Set the download filename
                        link.click(); // Trigger the download
                    },
                    error: function() {
                        $('#response').text('An error occurred');
                    }
                });
            });
        </script>
<?php }
} ?>