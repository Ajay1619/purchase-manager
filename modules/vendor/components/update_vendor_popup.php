<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $vendor_id = $_POST['vendor_id'];
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <div id="toast-container"></div>
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Edit Vendor</h2>
                <div class="modal-body">
                    <form id="update-vendor-form" method="POST">
                        <div class="form-section">
                            <h2>Basic Info</h2>
                            <div class="form-group">
                                <label for="salutation">Salutation</label>
                                <select id="salutation" name="salutation">
                                    <option value="" disabled selected>Select Salutation</option>
                                    <option value="Messrs." selected>Messrs.</option>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Dr.">Dr.</option>
                                    <option value="Prof.">Prof.</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="company-name">Company Name</label>
                                <input type="text" id="company-name" name="company-name" placeholder="Enter Company Name" value="Globe Engineering" required>
                                <input type="hidden" name="vendor-id" value="<?= $vendor_id ?>">
                            </div>
                            <div class="form-group">
                                <label for="contact-name">Contact Name</label>
                                <input type="text" id="contact-name" name="contact-name" placeholder="Enter Contact Name" value="John Doe" required>
                            </div>
                            <div class="form-group">
                                <label for="contact-number">Contact Number</label>
                                <input type="text" id="contact-number" name="contact-number" placeholder="Enter Contact Number" value="123-456-7890" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email ID</label>
                                <input type="email" id="email" name="email" placeholder="Enter Email ID" value="johndoe@gmail.com" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Billing Address</h2>
                            <div class="form-group">
                                <label for="billing-street">Street</label>
                                <input type="text" id="billing-street" name="billing-street" placeholder="Enter Street" value="103,Rose Street" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-locality">Locality</label>
                                <input type="text" id="billing-locality" name="billing-locality" placeholder="Enter Locality" value="Edinburgh" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-pincode">Pin Code</label>
                                <input type="text" id="billing-pincode" name="billing-pincode" placeholder="Enter Pin Code" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-city">City</label>
                                <input type="text" id="billing-city" name="billing-city" placeholder="Enter City" value="Pondicherry" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-district">District</label>
                                <input type="text" id="billing-district" name="billing-district" placeholder="Enter District" value="Pondicherry" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-state">State</label>
                                <input type="text" id="billing-state" name="billing-state" placeholder="Enter State" value="Pondicherry" required>
                            </div>
                            <div class="form-group">
                                <label for="billing-country">Country</label>
                                <input type="text" id="billing-country" name="billing-country" placeholder="Enter Country" value="India" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Shipping Address</h2>
                            <div class="form-group">
                                <label for="shipping-street">Street</label>
                                <input type="text" id="shipping-street" name="shipping-street" placeholder="Enter Street" value="103,Rose Street" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping-locality">Locality</label>
                                <input type="text" id="shipping-locality" name="shipping-locality" placeholder="Enter Locality" value="Edinburgh" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping-pincode">Pin Code</label>
                                <input type="text" id="shipping-pincode" name="shipping-pincode" placeholder="Enter Pin Code" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping-city">City</label>
                                <input type="text" id="shipping-city" name="shipping-city" placeholder="Enter City" value="Pondicherry" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping-district">District</label>
                                <input type="text" id="shipping-district" name="shipping-district" placeholder="Enter District" value="Pondicherry" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping-state">State</label>
                                <input type="text" id="shipping-state" name="shipping-state" placeholder="Enter State" value="Pondicherry" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping-country">Country</label>
                                <input type="text" id="shipping-country" name="shipping-country" placeholder="Enter Country" value="India" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Tax & Bank Info</h2>
                            <div class="form-group">
                                <label for="gstin">GSTIN</label>
                                <input type="text" id="gstin" name="gstin" placeholder="Enter GSTIN" value="Q123QWER567KJH325" required>
                            </div>
                            <div class="form-group">
                                <label for="pan">PAN</label>
                                <input type="text" id="pan" name="pan" placeholder="Enter PAN" value="QWE123HGF" required>
                            </div>
                            <div class="form-group">
                                <label for="bank-name">Bank Name</label>
                                <input type="text" id="bank-name" name="bank-name" placeholder="Enter Bank Name" value="Mariamman Indian Bank" required>
                            </div>
                            <div class="form-group">
                                <label for="account-number">Account Number</label>
                                <input type="text" id="account-number" name="account-number" placeholder="Enter Account Number" value="1234567890123" required>
                            </div>
                            <div class="form-group">
                                <label for="ifsc-code">IFSC Code</label>
                                <input type="text" id="ifsc-code" name="ifsc-code" placeholder="Enter IFSC Code" value="MIB007AV" required>
                            </div>
                            <div class="form-group">
                                <label for="branch-name">Branch Name</label>
                                <input type="text" id="branch-name" name="branch-name" placeholder="Enter Branch Name" value="Paris" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-group full-width">
                                <input type="submit" value="Submit" class="button submit-button" />
                            </div>
                        </div>

                    </form>

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
                        // Populate form fields with fetched vendor details
                        $('#salutation').val(vendorDetails.salutation);
                        $('#company-name').val(vendorDetails.vendor_company_name);
                        $('#contact-name').val(vendorDetails.vendor_contact_name);
                        $('#contact-number').val(vendorDetails.vendor_phone_number);
                        $('#email').val(vendorDetails.vendor_email_id);
                        $('#billing-street').val(vendorDetails.billing_address_street);
                        $('#billing-locality').val(vendorDetails.billing_address_locality);
                        $('#billing-pincode').val(vendorDetails.billing_address_pincode);
                        $('#billing-city').val(vendorDetails.billing_address_city);
                        $('#billing-district').val(vendorDetails.billing_address_district || '');
                        $('#billing-state').val(vendorDetails.billing_address_state);
                        $('#billing-country').val(vendorDetails.billing_address_country || '');
                        $('#shipping-street').val(vendorDetails.shipping_address_street);
                        $('#shipping-locality').val(vendorDetails.shipping_address_locality);
                        $('#shipping-pincode').val(vendorDetails.shipping_address_pincode);
                        $('#shipping-city').val(vendorDetails.shipping_address_city);
                        $('#shipping-district').val(vendorDetails.shipping_address_district || '');
                        $('#shipping-state').val(vendorDetails.shipping_address_state);
                        $('#shipping-country').val(vendorDetails.shipping_address_country || '');
                        $('#gstin').val(vendorDetails.vendor_gstin);
                        $('#pan').val(vendorDetails.vendor_pan_number);
                        $('#bank-name').val(vendorDetails.vendor_bank_name);
                        $('#account-number').val(vendorDetails.vendor_account_number);
                        $('#ifsc-code').val(vendorDetails.vendor_ifsc_code);
                        $('#branch-name').val(vendorDetails.vendor_branch_name);

                    } else {
                        alert('Failed to fetch vendor details: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching vendor details: ' + error);
                }
            });

            $(document).ready(function() {


                //billing-address
                $('#billing-pincode').on('blur', function() {
                    var pincode = $('#billing-pincode').val();

                    if (pincode) {
                        $.ajax({
                            url: 'https://api.postalpincode.in/pincode/' + pincode, // Use 'in' for India. Change the country code as needed.
                            method: 'GET',
                            success: function(data) {
                                if (data[0].Status == "Success") {
                                    var place = data[0].PostOffice[0];
                                    $('#billing-city').val(place.Name);
                                    $('#billing-district').val(place.District);
                                    $('#billing-state').val(place.State);
                                    $('#billing-country').val(place.Country);

                                } else {
                                    $('#billing-address-details').html('<p>No data available for this pincode.</p>');
                                }
                            },
                            error: function() {
                                $('#billing-address-details').html('<p>Invalid pincode or no data available.</p>');
                            }
                        });
                    } else {
                        $('#billing-address-details').html('<p>Please enter a pincode.</p>');
                    }
                });

                //shipping-address
                $('#shipping-pincode').on('blur', function() {
                    var pincode = $('#shipping-pincode').val();

                    if (pincode) {
                        $.ajax({
                            url: 'https://api.postalpincode.in/pincode/' + pincode, // Use 'in' for India. Change the country code as needed.
                            method: 'GET',
                            success: function(data) {
                                if (data[0].Status == "Success") {
                                    var place = data[0].PostOffice[0];
                                    $('#shipping-city').val(place.Name);
                                    $('#shipping-district').val(place.District);
                                    $('#shipping-state').val(place.State);
                                    $('#shipping-country').val(place.Country);

                                } else {
                                    $('#shipping-address-details').html('<p>No data available for this pincode.</p>');
                                }
                            },
                            error: function() {
                                $('#shipping-address-details').html('<p>Invalid pincode or no data available.</p>');
                            }
                        });
                    } else {
                        $('#shipping-address-details').html('<p>Please enter a pincode.</p>');
                    }
                });
            });


            $('#update-vendor-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/vendor/ajax/update_vendor_form.php' ?>',
                    data: $(this).serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        // Show the loading content
                        $('#loading').fadeIn();
                    },
                    success: function(response) {
                        if (response.status === 'error') {
                            showToast('error', response.message);
                        } else if (response.status === 'success') {

                            showToast('success', response.message);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    },

                    complete: function() {
                        // Hide the loading content after the request is complete
                        $('#loading').fadeOut();
                    }
                });
            });
        </script>
<?php }
} ?>