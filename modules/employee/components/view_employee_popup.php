<?php require_once('../../../config/sparrow.php'); ?>
<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $employee_id = isset($_POST['employee_id']) ? sanitizeInput($_POST['employee_id']) : 0;
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">View Employee</h2>
                <div class="modal-body">
                    <section id="view-profile">
                        <form>
                            <div class="section">
                                <h2 class="section-title">Employee Details</h2>
                                <div class="flex-container">
                                    <div class="flex-row">
                                        <div class="flex-column">
                                            <label for="employee-name">Employee Name:</label>
                                            <span id="employee-name" class="value"></span>
                                        </div>
                                        <div class="flex-column">
                                            <label for="employee-id">Employee ID:</label>
                                            <span id="employee-id" class="value"></span>
                                        </div>
                                        <div class="flex-column">
                                            <label for="employee-photo">Profile Picture:</label>
                                            <img id="profile-pic" alt="Profile Picture">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Contact Info</h3>
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="contact-number">Contact Number:</label>
                                        <span id="contact-number" class="value"></span>
                                    </div>
                                    <div class="flex-column">
                                        <label for="emergency-contact-number">Emergency Contact Number:</label>
                                        <span id="emergency-contact-number" class="value"></span>
                                    </div>
                                    <div class="flex-column">
                                        <label for="email-id">Email ID:</label>
                                        <span id="email-id" class="value"></span>
                                    </div>
                                </div>
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="address">Address:</label>
                                        <span id="street" class="value"></span>
                                        <span id="locality" class="value"></span>
                                        <span id="district" class="value"></span>
                                        <span id="state" class="value"></span>
                                        <span id="pincode" class="value"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="section">
                                <h3 class="section-title">Official Details</h3>
                                <div class="flex-row">
                                    <div class="flex-column">
                                        <label for="designation">Designation:</label>
                                        <span id="designation" class="value"></span>
                                    </div>
                                    <div class="flex-column">
                                        <label for="role">Role:</label>
                                        <span id="role" class="value"></span>
                                    </div>
                                    <div class="flex-column">
                                        <label for="joined-date">Joined Date:</label>
                                        <span id="joined-date" class="value"></span>
                                    </div>
                                    <div class="flex-column">
                                        <label for="username">Username:</label>
                                        <span id="username" class="value"></span>
                                    </div>
                                </div>
                            </div>
                        </form>
                </div>
            </div>

            <script>
                $.ajax({
                    url: '<?= MODULES . '/employee/json/fetch_employee_data.php' ?>',
                    type: 'POST',
                    data: {
                        employee_id: <?= $employee_id; ?>
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        const data = response.data;
                        // Populate values from the data object into the respective HTML elements
                        $('#employee-name').text(data.employee_name || 'N/A');
                        $('#employee-id').text(data.employee_id || 'N/A');
                        $('#contact-number').text(data.employee_contact_number || 'N/A');
                        $('#emergency-contact-number').text(data.employee_emergency_contact_number || 'N/A');
                        $('#email-id').text(data.employee_email_id || 'N/A');
                        $('#profile-pic').attr('src', '<?= GLOBAL_PATH . "/files/profile_pictures/" ?>' + data.employee_pic);


                        // Populate address details
                        $('#street').text(data.employee_street + ',' || 'N/A');
                        $('#locality').text(data.employee_locality + ',' || 'N/A');
                        $('#district').text(data.employee_district + ',' || 'N/A');
                        $('#state').text(data.employee_state + ',' || 'N/A');
                        $('#pincode').text(data.employee_pincode + ',' || 'N/A');

                        // Official details
                        $('#designation').text(data.employee_designation || 'N/A');
                        $('#role').text(data.role_name || 'N/A');
                        $('#joined-date').text(data.employee_joined_date || 'N/A');
                        $('#username').text(data.employee_username || 'N/A');
                    }
                });
            </script>

    <?php }
} ?>