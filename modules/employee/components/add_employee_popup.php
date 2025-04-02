<?php require_once('../../../config/sparrow.php'); ?>
<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'add') {
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Add Employee</h2>
                <div class="modal-body">
                    <div id="toast-container"></div>
                    <section id="view-profile">
                        <form id="add-employee-form" enctype="multipart/form-data">
                            <div class="section">
                                <h2 class="section-title">Employee Details</h2>
                                <div class="flex-container">
                                    <div class="flex-row">
                                        <div class="flex-column">
                                            <label for="employee-id">Employee ID:</label>
                                            <input type="text" id="employee-id" name="employee-id" placeholder="Enter employee ID" readonly>
                                        </div>
                                        <div class="flex-column">
                                            <label for="employee-name">Employee Name:</label>
                                            <input type="text" id="employee-name" name="employee-name" placeholder="Enter full name" required>
                                        </div>
                                        <div class="flex-column">
                                            <label for="employee-dob">Employee DOB:</label>
                                            <input type="date" id="employee-dob" name="employee-dob" placeholder="Enter Date of Birth" required>
                                        </div>
                                        <div class="flex-column">
                                            <label for="employee-photo">Profile Picture:</label>
                                            <input type="file" id="employee-photo" name="employee-photo">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Contact Info</h3>
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="contact-number">Contact Number:</label>
                                        <input type="text" id="contact-number" name="contact-number" placeholder="Enter contact number">
                                    </div>
                                    <div class="flex-column">
                                        <label for="emergency-contact-number">Emergency Contact Number:</label>
                                        <input type="text" id="emergency-contact-number" name="emergency-contact-number" placeholder="Enter Emergency contact number">
                                    </div>
                                    <div class="flex-column">
                                        <label for="email-id">Email ID:</label>
                                        <input type="email" id="email-id" name="email-id" placeholder="Enter email ID">
                                    </div>
                                </div>
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="address">Address:</label>
                                        <input type="text" id="street" name="street" placeholder="Street">
                                        <input type="text" id="locality" name="locality" placeholder="Locality">
                                        <input type="text" id="pincode" name="pincode" placeholder="Pincode">
                                        <input type="text" id="district" name="district" placeholder="District">
                                        <input type="text" id="state" name="state" placeholder="State">
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Official Details</h3>
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="designation">Designation:</label>
                                        <input type="text" id="designation" name="designation" placeholder="Enter designation">
                                    </div>
                                    <div class="flex-column">
                                        <label for="role">Role:</label>
                                        <select name="role" id="role" placeholder="Select role"></select>
                                    </div>
                                    <div class="flex-column">
                                        <label for="joined-date">Joined Date:</label>
                                        <input type="date" id="joined-date" name="joined-date">
                                    </div>
                                    <div class="flex-column">
                                        <label for="username">Username:</label>
                                        <input type="text" id="username" name="username" placeholder="Enter username" required>
                                    </div>
                                    <div class="flex-column">
                                        <label for="password">Password:</label>
                                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                                    </div>
                                </div>
                            </div>

                            <input type="submit" value="Submit" class="submit">
                        </form>


                </div>
            </div>
        </div>

        <script>
            //shipping-address
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

            $('#add-employee-form').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this); // Create a FormData object with the form data
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/employee/ajax/add_employee_form.php' ?>',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        //response = JSON.parse(response)
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

            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/employee/json/fetch_all_roles.php' ?>',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    const data = response.data;

                    if (response.status === 'error') {
                        showToast('error', response.message);
                    } else if (response.status === 'success') {

                        // Populate the select element
                        const roleSelect = $('#role');
                        roleSelect.empty(); // Clear existing options

                        // Add a default option
                        roleSelect.append('<option value="">Select role</option>');

                        // Check if data is an array or an object
                        if (Array.isArray(data)) {
                            // Loop through the data array and append options
                            data.forEach(function(role) {
                                roleSelect.append(`<option value="${role.role_id}">${role.role_name}</option>`);
                            });
                        } else {
                            // If data is a single object, add it directly
                            roleSelect.append(`<option value="${data.role_id}">${data.role_name}</option>`);
                        }
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });

            $.ajax({
                type: 'POST',
                url: '<?= MODULES . '/employee/json/fetch_employee_id.php' ?>',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    const data = response.data;

                    if (response.status === 'error') {
                        showToast('error', response.message);
                    } else if (response.status === 'success') {
                        $('#employee-id').val(data)

                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        </script>
<?php }
} ?>