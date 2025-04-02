<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'edit') {
        $customer_id = $_POST['customer_id'];
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <div id="toast-container"></div>
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Edit Customer</h2>
                <div class="modal-body">
                    <form id="update-customer-form" method="POST">
                        <div class="form-section">
                            <h2>Basic Info</h2>
                            <div class="form-group">
                                <label for="salutation">Salutation</label>
                                <select id="salutation" name="salutation">
                                    <option value="" disabled selected>Select Salutation</option>
                                    <option value="Messrs.">Messrs.</option>
                                    <option value="Mr.">Mr.</option>
                                    <option value="Ms.">Ms.</option>
                                    <option value="Mrs.">Mrs.</option>
                                    <option value="Dr.">Dr.</option>
                                    <option value="Prof.">Prof.</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="customer-name">Customer Name</label>
                                <input type="text" id="customer-name" name="customer-name" placeholder="Enter customer Name" required>
                                <input type="hidden" name="customer-id" value="<?= $customer_id ?>">
                            </div>
                            <div class="form-group">
                                <label for="contact-number">Contact Number</label>
                                <input type="text" id="contact-number" name="contact-number" placeholder="Enter Contact Number" required>
                            </div>
                            <div class="form-group">
                                <label for="gstin">GSTIN</label>
                                <input type="text" id="gstin" name="gstin" placeholder="Enter GSTIN" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email ID</label>
                                <input type="email" id="email" name="email" placeholder="Enter Email ID" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <h2>Address</h2>
                            <div class="form-group">
                                <label for="street">Street</label>
                                <input type="text" id="street" name="street" placeholder="Enter Street" required>
                            </div>
                            <div class="form-group">
                                <label for="locality">Locality</label>
                                <input type="text" id="locality" name="locality" placeholder="Enter Locality" required>
                            </div>
                            <div class="form-group">
                                <label for="pincode">Pin Code</label>
                                <input type="text" id="pincode" name="pincode" placeholder="Enter Pin Code" required>
                            </div>
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" placeholder="Enter City" required>
                            </div>
                            <div class="form-group">
                                <label for="district">District</label>
                                <input type="text" id="district" name="district" placeholder="Enter District" required>
                            </div>
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" id="state" name="state" placeholder="Enter State" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" id="country" name="country" placeholder="Enter Country" required>
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
                url: '<?= MODULES . '/customer/ajax/fetch_view_customer_details.php' ?>',
                type: 'GET',
                data: {
                    customer_id: <?= $customer_id ?>
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const customerDetails = response.customer_details;

                        $('#salutation').val(customerDetails.salutation);
                        $('#customer-name').val(customerDetails.customer_name);
                        $('#contact-number').val(customerDetails.customer_phone_number);
                        $('#gstin').val(customerDetails.customer_gstin);
                        $('#email').val(customerDetails.customer_email_id);
                        $('#street').val(customerDetails.address_street);
                        $('#locality').val(customerDetails.address_locality);
                        $('#city').val(customerDetails.address_city);
                        $('#state').val(customerDetails.address_state);
                        $('#pincode').val(customerDetails.address_pincode);




                    } else {
                        alert('Failed to fetch customer details: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error fetching customer details: ' + error);
                }
            });

            $(document).ready(function() {


                //address
                $('#pincode').on('blur', function() {
                    var pincode = $('#pincode').val();

                    if (pincode) {
                        $.ajax({
                            url: 'https://api.postalpincode.in/pincode/' + pincode, // Use 'in' for India. Change the country code as needed.
                            method: 'GET',
                            success: function(data) {
                                if (data[0].Status == "Success") {
                                    var place = data[0].PostOffice[0];
                                    $('#city').val(place.Name);
                                    $('#district').val(place.District);
                                    $('#state').val(place.State);
                                    $('#country').val(place.Country);

                                } else {
                                    $('#address-details').html('<p>No data available for this pincode.</p>');
                                }
                            },
                            error: function() {
                                $('#address-details').html('<p>Invalid pincode or no data available.</p>');
                            }
                        });
                    } else {
                        $('#address-details').html('<p>Please enter a pincode.</p>');
                    }
                });

            });


            $('#update-customer-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/customer/ajax/update_customer_form.php' ?>',
                    data: $(this).serialize(),
                    dataType: 'json',
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
                    }
                });
            });
        </script>
<?php }
} ?>