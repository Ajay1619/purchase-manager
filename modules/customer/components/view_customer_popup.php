<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $customer_id = $_POST['customer_id'];
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">View Customer </h2>
                <div class="modal-body">
                    <div class="view-container">
                        <div class="view-section">
                            <h2>Basic Info</h2>
                            <div class="view-row">
                                <div class="view-item">
                                    <span class="view-label">Salutation:</span>
                                    <span class="view-value" id="view-salutation">Messrs.</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Customer Name:</span>
                                    <span class="view-value" id="view-company-name">Ajay S</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">GSTIN:</span>
                                    <span class="view-value" id="view-gstin">Q123QWER567KJH325</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Contact Number:</span>
                                    <span class="view-value" id="view-contact-number">123-456-7890</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Email ID:</span>
                                    <span class="view-value" id="view-email">johndoe@gmail.com</span>
                                </div>
                            </div>
                        </div>

                        <div class="view-section">
                            <h2>Address</h2>
                            <div class="view-row">
                                <div class="view-item">
                                    <span class="view-label">Street:</span>
                                    <span class="view-value" id="view-billing-street">103,Rose Street</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Locality:</span>
                                    <span class="view-value" id="view-billing-locality">Edinburgh</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">City:</span>
                                    <span class="view-value" id="view-billing-city">Pondicherry</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">District:</span>
                                    <span class="view-value" id="view-billing-district">Pondicherry</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">State:</span>
                                    <span class="view-value" id="view-billing-state">Pondicherry</span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Country:</span>
                                    <span class="view-value" id="view-billing-country">India</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        </div>

        <script>
            $.ajax({
                url: '<?= MODULES . '/customer/ajax/fetch_view_customer_details.php' ?>',
                type: 'GET',
                data: {
                    customer_id: <?= $customer_id ?>
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const customerDetails = response.customer_details;

                        $('#view-salutation').text(customerDetails.salutation);
                        $('#view-company-name').text(customerDetails.customer_name);
                        $('#view-gstin').text(customerDetails.customer_gstin);
                        $('#view-contact-number').text(customerDetails.customer_phone_number);
                        $('#view-email').text(customerDetails.customer_email_id);
                        $('#view-billing-street').text(customerDetails.address_street);
                        $('#view-billing-locality').text(customerDetails.address_locality);
                        $('#view-billing-city').text(customerDetails.address_city);
                        $('#view-billing-district').text(customerDetails.address_district);
                        $('#view-billing-state').text(customerDetails.address_state);
                        $('#view-billing-country').text(customerDetails.address_country);


                    } else {
                        alert('Failed to fetch customer details: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching customer details: ' + error);
                }
            });
        </script>
<?php }
} ?>