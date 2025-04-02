<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $vendor_id = $_POST['vendor_id'];
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">View Vendor <?= $vendor_id ?></h2>
                <div class="modal-body">
                    <div class="view-container">
                        <div class="view-section">
                            <h2>Basic Info</h2>
                            <div class="view-row">
                                <div class="view-item">
                                    <span class="view-label">Salutation:</span>
                                    <span class="view-value" id="view-salutation"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Company Name:</span>
                                    <span class="view-value" id="view-company-name"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Contact Name:</span>
                                    <span class="view-value" id="view-contact-name"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Contact Number:</span>
                                    <span class="view-value" id="view-contact-number"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Email ID:</span>
                                    <span class="view-value" id="view-email"></span>
                                </div>
                            </div>
                        </div>

                        <div class="view-section">
                            <h2>Billing Address</h2>
                            <div class="view-row">
                                <div class="view-item">
                                    <span class="view-label">Street:</span>
                                    <span class="view-value" id="view-billing-street"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Locality:</span>
                                    <span class="view-value" id="view-billing-locality"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">City:</span>
                                    <span class="view-value" id="view-billing-city"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">District:</span>
                                    <span class="view-value" id="view-billing-district"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">State:</span>
                                    <span class="view-value" id="view-billing-state"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Country:</span>
                                    <span class="view-value" id="view-billing-country"></span>
                                </div>
                            </div>
                        </div>

                        <div class="view-section">
                            <h2>Shipping Address</h2>
                            <div class="view-row">
                                <div class="view-item">
                                    <span class="view-label">Street:</span>
                                    <span class="view-value" id="view-shipping-street"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Locality:</span>
                                    <span class="view-value" id="view-shipping-locality"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">City:</span>
                                    <span class="view-value" id="view-shipping-city"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">District:</span>
                                    <span class="view-value" id="view-shipping-district"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">State:</span>
                                    <span class="view-value" id="view-shipping-state"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Country:</span>
                                    <span class="view-value" id="view-shipping-country"></span>
                                </div>
                            </div>
                        </div>

                        <div class="view-section">
                            <h2>Tax & Bank Info</h2>
                            <div class="view-row">
                                <div class="view-item">
                                    <span class="view-label">GSTIN:</span>
                                    <span class="view-value" id="view-gstin"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">PAN:</span>
                                    <span class="view-value" id="view-pan"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Bank Name:</span>
                                    <span class="view-value" id="view-bank-name"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Account Number:</span>
                                    <span class="view-value" id="view-account-number"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">IFSC Code:</span>
                                    <span class="view-value" id="view-ifsc-code"></span>
                                </div>
                                <div class="view-item">
                                    <span class="view-label">Branch Name:</span>
                                    <span class="view-value" id="view-branch-name"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $.ajax({
                url: '<?= MODULES . '/vendor/ajax/fetch_view_vendor_details.php' ?>',
                type: 'GET',
                data: {
                    vendor_id: <?= $vendor_id ?>
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const vendorDetails = response.vendor_details[0];
                        // Populate modal with fetched vendor details
                        $('#view-salutation').text(vendorDetails.salutation);
                        $('#view-company-name').text(vendorDetails.vendor_company_name);
                        $('#view-contact-name').text(vendorDetails.vendor_contact_name);
                        $('#view-contact-number').text(vendorDetails.vendor_phone_number);
                        $('#view-email').text(vendorDetails.vendor_email_id);
                        $('#view-billing-street').text(vendorDetails.billing_address_street);
                        $('#view-billing-locality').text(vendorDetails.billing_address_locality);
                        $('#view-billing-city').text(vendorDetails.billing_address_city);
                        $('#view-billing-district').text(vendorDetails.billing_address_district || '');
                        $('#view-billing-state').text(vendorDetails.billing_address_state);
                        $('#view-billing-country').text(vendorDetails.billing_address_country || '');
                        $('#view-shipping-street').text(vendorDetails.shipping_address_street);
                        $('#view-shipping-locality').text(vendorDetails.shipping_address_locality);
                        $('#view-shipping-city').text(vendorDetails.shipping_address_city);
                        $('#view-shipping-district').text(vendorDetails.shipping_address_district || '');
                        $('#view-shipping-state').text(vendorDetails.shipping_address_state);
                        $('#view-shipping-country').text(vendorDetails.shipping_address_country || '');
                        $('#view-gstin').text(vendorDetails.vendor_gstin);
                        $('#view-pan').text(vendorDetails.vendor_pan_number)
                        $('#view-bank-name').text(vendorDetails.vendor_bank_name);
                        $('#view-account-number').text(vendorDetails.vendor_account_number);
                        $('#view-ifsc-code').text(vendorDetails.vendor_ifsc_code);
                        $('#view-branch-name').text(vendorDetails.vendor_branch_name);

                    } else {
                        alert('Failed to fetch vendor details: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching vendor details: ' + error);
                }
            });
        </script>
<?php }
} ?>