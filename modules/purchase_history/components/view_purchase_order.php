<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $po_id = $_POST['po_id'];
?>

        <section id="view-purchase-order">
            <div id="button-container">
                <button class="button" id="export-purchase-order">
                    Export
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Z" />
                    </svg>
                </button>
            </div>

            <div class="view-container">
                <div class="section">
                    <h2 class="section-title">Purchase Order Details</h2>
                    <div class="flex-container">
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="vendor-name">Vendor Company Name:</label>
                                <span class="value" id="vendor-name"></span>
                            </div>
                            <div class="flex-column">
                                <label for="po-number">Purchase Order Number:</label>
                                <span class="value" id="po-number"></span>
                                <input type="hidden" id="po-code">
                            </div>
                            <div class="flex-column">
                                <label for="date">Date:</label>
                                <span class="value" id="date"></span>
                            </div>
                        </div>
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="vendor-contact-name">Vendor Contact Name:</label>
                                <span class="value" id="vendor-contact-name"></span>
                            </div>
                            <div class="flex-column">
                                <label for="vendor-contact-number">Vendor Contact Number:</label>
                                <span class="value" id="vendor-contact-number"></span>
                            </div>
                            <div class="flex-column">
                                <label for="gstin">GSTIN:</label>
                                <span class="value" id="gstin"></span>
                            </div>
                        </div>
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="billing-address">Billing Address:</label>
                                <span id="billing-street"></span>
                                <span id="billing-locality"></span>
                                <span id="billing-city"></span>
                                <span id="billing-district"></span>
                                <span id="billing-state"></span>
                                <span class="value" id="billing-pincode"></span>
                            </div>
                            <div class="flex-column full-width">
                                <label for="shipping-address">Shipping Address:</label>
                                <span id="shipping-street"></span>
                                <span id="shipping-locality"></span>
                                <span id="shipping-city"></span>
                                <span id="shipping-district"></span>
                                <span id="shipping-state"></span>
                                <span class="value" id="shipping-pincode"></span>
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
                        <tbody id="items-table-body">
                            <!-- Table rows will be inserted here -->
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <h3 class="section-title">Summary</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="subtotal">Subtotal:</label>
                            <span class="value" id="subtotal"></span>
                        </div>
                        <div class="flex-column">
                            <label for="discount-percentage">Discount Percentage:</label>
                            <span class="value" id="discount-percentage"></span>
                        </div>
                        <div class="flex-column">
                            <label for="discount-amount">Discount Amount:</label>
                            <span class="value" id="discount-amount"></span>
                        </div>
                    </div>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="adjustment">Adjustment:</label>
                            <span class="value" id="adjustment"></span>
                        </div>
                        <div class="flex-column">
                            <label for="grand-total">Grand Total:</label>
                            <span class="value" id="grand-total"></span>
                        </div>
                        <div class="flex-column">
                            <label for="grand-total-words">Amount In Words</label>
                            <span class="value" id="grand-total-words"></span>
                        </div>
                    </div>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="purchase-order-status">Status</label>
                            <span class="badge purchase-order-status" id="purchase-order-status"></span>
                        </div>
                        <div class="flex-column" id="purchased_date">
                            <label for="purchased-date">Purchased Date</label>
                            <span class="purchased-date" id="purchased-date"></span>
                        </div>
                        <div class="flex-column"></div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            $.ajax({
                url: '<?= MODULES . '/purchase_history/ajax/fetch_view_purchase_order.php' ?>',
                type: 'GET',
                data: {
                    purchase_order_id: <?= $po_id ?>
                },
                success: function(response) {
                    response = JSON.parse(response);
                    const purchaseOrder = response.purchase_order_details;
                    const itemDetails = response.item_details;
                    $('#vendor-name').text(purchaseOrder.vendor_company_name);
                    $('#po-number').text(purchaseOrder.purchase_order_number);
                    $('#po-code').val(purchaseOrder.purchase_order_number);
                    $('#date').text(purchaseOrder.purchase_order_date);
                    $('#vendor-contact-name').text(purchaseOrder.vendor_contact_name);
                    $('#vendor-contact-number').text(purchaseOrder.vendor_phone_number);
                    $('#gstin').text(purchaseOrder.vendor_gstin); // Assuming GSTIN needs to be set
                    $('#billing-street').text(purchaseOrder.billing_address_street);
                    $('#billing-locality').text(purchaseOrder.billing_address_locality);
                    $('#billing-city').text(purchaseOrder.billing_address_city);
                    $('#billing-district').text(purchaseOrder.billing_address_district);
                    $('#billing-state').text(purchaseOrder.billing_address_state);
                    $('#billing-pincode').text(purchaseOrder.billing_address_pincode);

                    // Shipping address
                    $('#shipping-street').text(purchaseOrder.shipping_address_street);
                    $('#shipping-locality').text(purchaseOrder.shipping_address_locality);
                    $('#shipping-city').text(purchaseOrder.shipping_address_city);
                    $('#shipping-district').text(purchaseOrder.shipping_address_district);
                    $('#shipping-state').text(purchaseOrder.shipping_address_state);
                    $('#shipping-pincode').text(purchaseOrder.shipping_address_pincode);

                    $('#subtotal').text(purchaseOrder.subtotal);
                    $('#discount-percentage').text(purchaseOrder.discount); // Assuming percentage is the same as discount amount
                    $('#discount-amount').text(purchaseOrder.discount);
                    $('#adjustment').text(purchaseOrder.adjustment);
                    $('#grand-total').text(purchaseOrder.grand_total);
                    $('#grand-total-words').text(purchaseOrder.amount_in_words);
                    //populate purchase_order_status
                    if (purchaseOrder.purchase_order_status == '1') {
                        $('#purchase-order-status').text('Purchased');
                        $('#purchase-order-status').addClass('badge-success');
                        $('#purchased-date').text(purchaseOrder.purchase_order_date); // Show the purchased_date element
                        $('#purchased_date').show(); // Show the purchased_date element
                    } else if (purchaseOrder.purchase_order_status == '2') {
                        $('#purchase-order-status').text('Cancelled');
                        $('#purchase-order-status').addClass('badge-danger');
                        $('#purchased_date').hide(); // Hide the purchased_date element
                    } else {
                        $('#purchase-order-status').text('Pending');
                        $('#purchase-order-status').addClass('badge-warning');
                        $('#purchased_date').hide(); // Hide the purchased_date element
                    }


                    // Populate items table
                    const itemsTableBody = $('#items-table-body');
                    itemsTableBody.empty(); // Clear existing rows

                    itemDetails.forEach((item, index) => {
                        itemsTableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.product_name || 'N/A'}</td> <!-- Assuming you might have product_name in the response -->
                                <td>${item.unit_of_measure}</td>
                                <td>${item.quantity}</td>
                                <td>${item.unit_price}</td>
                                <td>${item.amount}</td>
                            </tr>
                        `);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('AJAX request failed:', status, error);
                }
            });

            $('#export-purchase-order').on('click', function() {
                $.ajax({
                    url: '<?= MODULES . '/purchase_history/ajax/purchase_order_pdf.php' ?>',
                    type: 'POST',
                    data: {
                        purchase_order_id: <?= $po_id ?>,
                        purchase_order_number: $('#po-code').val()
                    },
                    xhrFields: {
                        responseType: 'blob' // Set the response type to blob
                    },
                    success: function(response) {
                        // Create a link to download the PDF
                        var blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "purchase_order.pdf"; // Set the download filename
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