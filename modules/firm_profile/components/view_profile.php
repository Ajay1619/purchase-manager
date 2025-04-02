<?php require_once('../../../config/sparrow.php'); ?>
<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
?>

        <section id="view-profile">
            <div id="button-container">
                <button class="button" id="popupButton" onclick="edit_firm_profile()">
                    Edit
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                        <path d="M160-400v-80h280v80H160Zm0-160v-80h440v80H160Zm0-160v-80h440v80H160Zm360 560v-123l221-220q9-9 20-13t22-4q12 0 23 4.5t20 13.5l37 37q8 9 12.5 20t4.5 22q0 11-4 22.5T863-380L643-160H520Zm263-224 37-39-37-37-38 38 38 38Z" />
                    </svg>
                </button>
            </div>


            <div class="view-container">
                <div class="section">
                    <h2 class="section-title">Firm Details</h2>

                    <div class="flex-container">
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="firm-name">Firm Name:</label>
                                <span class="value" id="firm-name">Firm A</span>
                            </div>
                            <div class="flex-column">
                                <label for="registration-number">Registration Number:</label>
                                <span class="value" id="registration-number">87640</span>
                            </div>
                            <div class="flex-column">
                                <label for="firm-logo"> Logo:</label>
                                <img src="" id="firm-logo" alt="Please Upload a Logo!!">
                            </div>

                        </div>

                    </div>
                </div>

                <div class="section">
                    <h3 class="section-title">Contact Info</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="contact-number">Contact Number</label>
                            <span class="value" id="contact-number">9876543210</span>
                        </div>
                        <div class="flex-column">
                            <label for="email-id">Email ID</label>
                            <span class="value" id="email-id">abc@gmail.com</span>
                        </div>
                        <div class="flex-column">
                            <label for="address">Address:</label>
                            <span id="street">12,Rose Street</span>
                            <span id="locality">Lawspet</span>
                            <span id="city">Lawspet</span>
                            <span id="district">Puducherry</span>
                            <span id="state">Pondicherry</span>
                            <span id="country">India</span>
                            <span class="value" id="pincode">605008</span>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <h3 class="section-title">Tax Info</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="gstin">GSTIN</label>
                            <span class="value" id="gstin">QWER1234TYU5678</span>
                        </div>
                        <div class="flex-column">
                            <label for="pan">PAN</label>
                            <span class="value" id="email-id">ASD9876FG</span>
                        </div>
                        <div class="flex-column">
                            <label for="tax-registration-number">Tax Registration Number:</label>
                            <span id="tax-registration-number"></span>
                        </div>
                        <div class="flex-column">
                            <label for="default-tax-percentage">Default Tax Percentage:</label>
                            <span id="default-tax-percentage">35694</span>
                        </div>
                    </div>
                </div>


                <div class="section">
                    <h3 class="section-title">Bank Info</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="bank-name">Bank Name:</label>
                            <span class="value" id="bank-name">Mariamman Indian Bank</span>
                        </div>
                        <div class="flex-column">
                            <label for="account-number">Account Number:</label>
                            <span class="value" id="account-number">987123654785268</span>
                        </div>
                        <div class="flex-column">
                            <label for="ifsc-code">IFSC Code:</label>
                            <span class="value" id="ifsc-code">MIB102AV</span>
                        </div>
                        <div class="flex-column">
                            <label for="bank-branch">Bank Branch:</label>
                            <span class="value" id="bank-branch">Edinburgh Branch</span>
                        </div>

                    </div>

                </div>
            </div>
        </section>
        <script>
            $.ajax({
                url: '<?= MODULES . '/firm_profile/json/fetch_inv_firm_profile.php' ?>',
                type: 'POST',
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == 'success') {
                        var firm_profile = data.firm_profile;
                        $('#firm-name').text(firm_profile.firm_name);
                        $('#registration-number').text(firm_profile.registration_number);
                        $('#firm-logo').attr('src', '<?= FILES . '/logo/' ?>' + firm_profile.logo);
                        $('#contact-number').text(firm_profile.phone_number);
                        $('#email-id').text(firm_profile.email_id);
                        $('#street').text(firm_profile.street);
                        $('#locality').text(firm_profile.locality);
                        $('#city').text(firm_profile.city);
                        $('#district').text(firm_profile.district);
                        $('#state').text(firm_profile.state);
                        $('#country').text(firm_profile.country);
                        $('#pin-code').text(firm_profile.pin_code);
                        $('#gstin').text(firm_profile.gstin);
                        $('#pan').text(firm_profile.pan);
                        $('#tax-registration-number').text(firm_profile.tax_registration_number);
                        $('#default-tax-percentage').text(firm_profile.default_tax_percentage);
                        $('#bank-name').text(firm_profile.bank_name);
                        $('#account-number').text(firm_profile.account_number);
                        $('#ifsc-code').text(firm_profile.ifsc_code);
                        $('#bank-branch').text(firm_profile.bank_branch);
                        $('#invoice-terms-and-conditions').text(firm_profile.invoice_terms_and_conditions);
                        $('#invoice-number-prefix').text(firm_profile.invoice_number_prefix);
                        $('#customer-code-prefix').text(firm_profile.customer_code_prefix);
                        $('#vendor-code-prefix').text(firm_profile.vendor_code_prefix);
                        $('#purchase-number-prefix').text(firm_profile.purchase_number_prefix);
                        $('#product-code-prefix').text(firm_profile.product_code_prefix);
                        $('#employee-code-prefix').text(firm_profile.employee_code_prefix);
                        $('#role-code-prefix').text(firm_profile.role_code_prefix);
                    } else {
                        console.error(response);
                    }
                }
            });
        </script>
<?php }
} ?>