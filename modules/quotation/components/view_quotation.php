<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $qo_id = $_POST['qo_id'];
?>

        <section id="view-quotation">
            <div id="button-container">
                <button class="button" id="export-quotation">
                    Export
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Z" />
                    </svg>
                </button>
            </div>

            <div class="view-container">
                <div class="section">
                    <h2 class="section-title">Quotation Details</h2>
                    <div class="flex-container">
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="customer-name">Customer Name:</label>
                                <span class="value" id="customer-name"></span>
                            </div>
                            <div class="flex-column">
                                <label for="qo-number">Quotation Number:</label>
                                <span class="value" id="qo-number"></span>
                                <input type="hidden" id="qo-code">
                            </div>
                            <div class="flex-column">
                                <label for="date">Date:</label>
                                <span class="value" id="date"></span>
                            </div>
                        </div>
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="customer-contact-name">Customer Contact Name:</label>
                                <span class="value" id="customer-contact-name"></span>
                            </div>
                            <div class="flex-column">
                                <label for="customer-contact-number">Customer Contact Number:</label>
                                <span class="value" id="customer-contact-number"></span>
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
                            <label for="quotation-status">Status</label>
                            <span class="badge quotation-status" id="quotation-status"></span>
                        </div>
                        <div class="flex-column" id="quotated_date">
                            <label for="quotated-date">Quotated Date</label>
                            <span class="quotated-date" id="quotated-date"></span>
                        </div>
                        <div class="flex-column"></div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            $.ajax({
                url: '<?= MODULES . '/quotation/ajax/fetch_view_quotation.php' ?>',
                type: 'GET',
                data: {
                    quotation_id: <?= $qo_id ?>
                },
                success: function(response) {
                    response = JSON.parse(response);
                    console.log(response)
                    const quotation = response.quotation_details;
                    const itemDetails = response.item_details;
                    console.log(quotation);
                    $('#customer-name').text(quotation.customer_name);
                    $('#qo-number').text(quotation.quotation_number);
                    $('#qo-code').val(quotation.quotation_number);
                    $('#date').text(quotation.quotation_date);
                    $('#customer-contact-name').text(quotation.customer_name);
                    $('#customer-contact-number').text(quotation.customer_phone_number);
                    $('#gstin').text(quotation.customer_email_id); // Assuming GSTIN needs to be set
                    $('#billing-street').text(quotation.address_street);
                    $('#billing-locality').text(quotation.address_locality);
                    $('#billing-city').text(quotation.address_city);
                    $('#billing-district').text(quotation.address_district);
                    $('#billing-state').text(quotation.address_state);
                    $('#billing-pincode').text(quotation.address_pincode);

                    // Shipping address
                    $('#shipping-street').text(quotation.address_street);
                    $('#shipping-locality').text(quotation.address_locality);
                    $('#shipping-city').text(quotation.address_city);
                    $('#shipping-district').text(quotation.address_district);
                    $('#shipping-state').text(quotation.address_state);
                    $('#shipping-pincode').text(quotation.address_pincode);

                    $('#subtotal').text(quotation.subtotal);
                    $('#discount-percentage').text(quotation.discount); // Assuming percentage is the same as discount amount
                    $('#discount-amount').text(quotation.discount);
                    $('#adjustment').text(quotation.adjustment);
                    $('#grand-total').text(quotation.grand_total);
                    $('#grand-total-words').text(quotation.amount_in_words);
                    //populate quotation_status
                    if (quotation.quotation_status == '1') {
                        $('#quotation-status').text('quotated');
                        $('#quotation-status').addClass('badge-success');
                        $('#quotated-date').text(quotation.quotation_date); // Show the quotated_date element
                        $('#quotated_date').show(); // Show the quotated_date element
                    } else if (quotation.quotation_status == '2') {
                        $('#quotation-status').text('Cancelled');
                        $('#quotation-status').addClass('badge-danger');
                        $('#quotated_date').hide(); // Hide the quotated_date element
                    } else {
                        $('#quotation-status').text('Pending');
                        $('#quotation-status').addClass('badge-warning');
                        $('#quotated_date').hide(); // Hide the quotated_date element
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

            $('#export-quotation').on('click', function() {
                $.ajax({
                    url: '<?= MODULES . '/quotation/ajax/quotation_pdf.php' ?>',
                    type: 'POST',
                    data: {
                        qo_id: <?= $qo_id ?>,
                        qo_code: $('#qo-code').val()
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
                        link.download = "quotation.pdf"; // Set the download filename
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