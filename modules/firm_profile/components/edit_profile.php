<?php require_once('../../../config/sparrow.php'); ?>
<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
?>

        <section id="view-profile">
            <form id="add-firm-profile" enctype="multipart/form-data">
                <div class="section">
                    <h2 class="section-title">Firm Details</h2>
                    <div class="flex-container">
                        <div class="flex-row">
                            <div class="flex-column">
                                <label for="firm-name">Firm Name:</label>
                                <input type="text" id="firm-name" name="firm-name" value="Firm A">
                            </div>
                            <div class="flex-column">
                                <label for="registration-number">Registration Number:</label>
                                <input type="text" id="registration-number" name="registration-number" value="87640">
                            </div>
                            <div class="flex-column">
                                <label for="logo">Logo:</label>
                                <input type="file" id="logo" name="logo" onchange="previewImage(event)">
                                <input type="hidden" name="previous-logo" id="previous-logo">
                            </div>
                            <div class="flex-column">
                                <img id="logoPreview" src="" alt="Logo Preview">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="section">
                    <h3 class="section-title">Contact Info</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="contact-number">Contact Number:</label>
                            <input type="text" id="contact-number" name="contact-number" value="9876543210">
                        </div>
                        <div class="flex-column">
                            <label for="email-id">Email ID:</label>
                            <input type="email" id="email-id" name="email-id" value="abc@gmail.com">
                        </div>
                    </div>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="address">Address:</label>
                            <label for="street">Street</label>
                            <input class="address" type="text" id="street" name="street" value="12, Rose Street">
                            <label for="locality">Locality</label>
                            <input class="address" type="text" id="locality" name="locality" value="Lawspet">
                            <label for="city">Locality</label>
                            <input class="address" type="text" id="city" name="city" value="Lawspet">
                            <label for="district">District</label>
                            <input class="address" type="text" id="district" name="district" value="Puducherry">
                            <label for="state">State</label>
                            <input class="address" type="text" id="state" name="state" value="Pondicherry">
                            <label for="country">Country</label>
                            <input class="address" type="text" id="country" name="country" value="Pondicherry">
                            <label for="pincode">Pincode</label>
                            <input class="address" type="text" id="pincode" name="pincode" value="605008">
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3 class="section-title">Tax Info</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="gstin">GSTIN:</label>
                            <input type="text" id="gstin" name="gstin" value="QWER1234TYU5678">
                        </div>
                        <div class="flex-column">
                            <label for="pan">PAN:</label>
                            <input type="text" id="pan" name="pan" value="ASD9876FG">
                        </div>
                        <div class="flex-column">
                            <label for="tax-registration-number">Tax Registration Number:</label>
                            <input type="text" id="tax-registration-number" name="tax-registration-number" value="35694">
                        </div>
                        <div class="flex-column">
                            <label for="default-tax-percentage">Default Tax Percentage:</label>
                            <input type="text" id="default-tax-percentage" name="default-tax-percentage">
                        </div>
                    </div>
                </div>

                <div class="section">
                    <h3 class="section-title">Bank Info</h3>
                    <div class="flex-row">
                        <div class="flex-column">
                            <label for="bank-name">Bank Name:</label>
                            <input type="text" id="bank-name" name="bank-name" value="Mariamman Indian Bank">
                        </div>
                        <div class="flex-column">
                            <label for="account-number">Account Number:</label>
                            <input type="text" id="account-number" name="account-number" value="987123654785268">
                        </div>
                        <div class="flex-column">
                            <label for="ifsc-code">IFSC Code:</label>
                            <input type="text" id="ifsc-code" name="ifsc-code" value="MIB102AV">
                        </div>
                        <div class="flex-column">
                            <label for="bank-branch">Bank Branch:</label>
                            <input type="text" id="bank-branch" name="bank-branch" value="Edinburgh Branch">
                        </div>
                    </div>
                </div>
                <input type="submit" value="Submit" class="button submit-button" />

            </form>
        </section>

        <script>
            $.ajax({
                url: '<?= MODULES . '/firm_profile/json/fetch_inv_firm_profile.php' ?>',
                type: 'POST',
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == 'success') {
                        var firm_profile = data.firm_profile;
                        $('#firm-name').val(firm_profile.firm_name);
                        $('#registration-number').val(firm_profile.registration_number);
                        if (firm_profile.logo) {
                            $('#logoPreview').css('display', 'block');
                            $('#logoPreview').attr('src', '<?= FILES . '/logo/' ?>' + firm_profile.logo);
                            $('#previous-logo').val(firm_profile.logo);

                        } else {
                            $('#logoPreview').css('display', 'none');
                        }

                        $('#contact-number').val(firm_profile.phone_number);
                        $('#email-id').val(firm_profile.email_id);
                        $('#street').val(firm_profile.street);
                        $('#locality').val(firm_profile.locality);
                        $('#city').val(firm_profile.city);
                        $('#district').val(firm_profile.district);
                        $('#state').val(firm_profile.state);
                        $('#country').val(firm_profile.country);
                        $('#pin-code').val(firm_profile.pin_code);
                        $('#gstin').val(firm_profile.gstin);
                        $('#pan').val(firm_profile.pan);
                        $('#tax-registration-number').val(firm_profile.tax_registration_number);
                        $('#default-tax-percentage').val(firm_profile.default_tax_percentage);
                        $('#bank-name').val(firm_profile.bank_name);
                        $('#account-number').val(firm_profile.account_number);
                        $('#ifsc-code').val(firm_profile.ifsc_code);
                        $('#bank-branch').val(firm_profile.bank_branch);
                    } else {
                        console.error(response);
                    }
                }
            });


            $('#add-firm-profile').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                var formData = new FormData(this); // Create a FormData object with the form data

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/firm_profile/ajax/add_firm_profile.php' ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'error') {
                            showToast('error', response.messages);
                        } else if (response.status === 'success') {
                            showToast('success', response.message);
                            var newUrl = window.location.pathname;
                            history.pushState({}, '', newUrl);
                            location.reload();
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        </script>
<?php }
} ?>