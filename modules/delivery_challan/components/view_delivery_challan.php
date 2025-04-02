<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $dc_id = $_POST['dc_id'];
?>

        <section id="view-delivery-challan">
            <div id="button-container">
                <button class="button" id="export-delivery-challan">
                    Export
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path d="M360-460h40v-80h40q17 0 28.5-11.5T480-580v-40q0-17-11.5-28.5T440-660h-80v200Zm40-120v-40h40v40h-40Zm120 120h80q17 0 28.5-11.5T640-500v-120q0-17-11.5-28.5T600-660h-80v200Zm40-40v-120h40v120h-40Zm120 40h40v-80h40v-40h-40v-40h40v-40h-80v200ZM320-240q-33 0-56.5-23.5T240-320v-480q0-33 23.5-56.5T320-880h480q33 0 56.5 23.5T880-800v480q0 33-23.5 56.5T800-240H320ZM160-80q-33 0-56.5-23.5T80-160v-560h80v560h560v80H160Z" />
                    </svg>
                </button>
            </div>

            <div class="view-container">
                <div class="section">
                    <h2 class="section-title">Delivery Challan Details</h2>
                    <div class="flex-container">
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="delivery-challan-number">Delivery Challan Number:</label>
                                <span class="value" id="delivery-challan-number"></span>
                                <input type="hidden" id="dc-code">
                            </div>
                            <div class="flex-column">
                                <label for="customer-id">Customer Name:</label>
                                <span class="value" id="customer-name"></span>
                            </div>
                            <div class="flex-column">
                                <label for="delivery-challan-date">Date:</label>
                                <span class="value" id="delivery-challan-date"></span>
                            </div>
                        </div>
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="delivery-status">Status:</label>
                                <span class="badge delivery-status" id="delivery-status"></span>
                            </div>
                            <div class="flex-column full-width">
                                <label for="delivery-date">Delivery Date:</label>
                                <span class="value" id="delivery-date"></span>
                            </div>
                        </div>
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="billing-address">Billing Address:</label>
                                <span id="billing-address-street">12,Rose Street</span>
                                <span id="billing-address-locality">Lawspet</span>
                                <span id="billing-address-district">Puducherry</span>
                                <span id="billing-address-state">Pondicherry</span>
                                <span class="value" id="billing-address-pincode">605008</span>
                            </div>
                            <div class="flex-column full-width">
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
                        <tbody id="items-table-body">
                            <!-- Table rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <script>
            $.ajax({
                url: '<?= MODULES . '/delivery_challan/ajax/fetch_view_delivery_challan.php' ?>',
                type: 'GET',
                data: {
                    dc_id: <?= $dc_id ?>
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.status == 'success') {
                        const deliveryChallan = response.delivery_challan_details;
                        const itemDetails = response.item_details;

                        $('#delivery-challan-number').text(deliveryChallan.delivery_challan_number);
                        $('#dc-code').text(deliveryChallan.delivery_challan_number);
                        $('#customer-name').text(deliveryChallan.customer_name);
                        $('#delivery-challan-date').text(deliveryChallan.delivery_challan_date);
                        $('#delivery-date').text(deliveryChallan.delivery_date);
                        $('#billing-address-street').text(deliveryChallan.address_street);
                        $('#billing-address-locality').text(deliveryChallan.address_locality);
                        $('#billing-address-district').text(deliveryChallan.address_district);
                        $('#billing-address-state').text(deliveryChallan.address_state);
                        $('#billing-address-pincode').text(deliveryChallan.address_pincode);
                        $('#shipping-address-street').text(deliveryChallan.address_street);
                        $('#shipping-address-locality').text(deliveryChallan.address_locality);
                        $('#shipping-address-district').text(deliveryChallan.address_district);
                        $('#shipping-address-state').text(deliveryChallan.address_state);
                        $('#shipping-address-pincode').text(deliveryChallan.address_pincode);
                        // Populate delivery status
                        const status = deliveryChallan.delivery_challan_status == '1' ? 'Delivered' : (deliveryChallan.delivery_challan_status == '2' ? 'Canceled' : 'Pending');
                        $('#delivery-status').text(status).addClass(status === 'Delivered' ? 'badge-success' : (status === 'Canceled' ? 'badge-danger' : 'badge-warning'));

                        // Populate items table
                        const itemsTableBody = $('#items-table-body');
                        itemsTableBody.empty(); // Clear existing rows

                        itemDetails.forEach((item, index) => {
                            itemsTableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.product_name || 'N/A'}</td>
                                <td>${item.unit_of_measure || 'N/A'}</td>
                                <td>${item.quantity || 'N/A'}</td>
                            </tr>
                        `);
                        });
                    } else {
                        showToast(response.status, response.message);
                    }

                },
                error: function(xhr, status, error) {
                    showToast('error', error);
                }
            });


            $('#export-delivery-challan').on('click', function() {
                $.ajax({
                    url: '<?= MODULES . '/delivery_challan/ajax/delivery_challan_pdf.php' ?>',
                    type: 'POST',
                    data: {
                        dc_id: <?= $dc_id ?>,
                        dc_no: $('#dc-code').val()
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
                        link.download = "delivery_challan.pdf"; // Set the download filename
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