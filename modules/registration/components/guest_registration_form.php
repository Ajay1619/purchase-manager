<?php
include_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    $type = $_GET['type'];
?>
    <h1>Guest Room Registration</h1>
    <form action="#" id="regform" method="POST">
        <!-- Personal Information -->
        <div class="form-section">
            <h2>Personal Information</h2>
            <div class="grid">
                <div class="form-group flex-item">
                    <label for="reservation_no">Reservation No </label>
                    <input type="text" id="reservation_no" name="reservation_no" value="<?= $type ?>" placeholder="Reservation Number" readonly>
                </div>
                <div class="form-group flex-item">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Your Name">
                </div>
                <div class="form-group flex-item">
                    <label for="surname">Surname</label>
                    <input type="text" id="surname" name="surname" placeholder="Your Surname">
                </div>
                <div class="form-group flex-item">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Your First Name">
                </div>
                <div class="form-group flex-item">
                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" name="birthday" placeholder="Date of Birth">
                </div>
                <div class="form-group flex-item">
                    <label for="anniversary">Anniversary</label>
                    <input type="date" id="anniversary" name="anniversary" placeholder="Anniversary Date">
                </div>
                <div class="form-group flex-item">
                    <label for="nationality">Nationality</label>
                    <input type="text" id="nationality" name="nationality" placeholder="Your Nationality">
                </div>
                <div class="form-group flex-item">
                    <label>Employed in India</label>
                    <div class="form-group radio-group">
                        <label>
                            <input type="radio" id="employed_in_india_yes" name="employed_in_india" value="yes"> Yes
                        </label>
                        <label>
                            <input type="radio" id="employed_in_india_no" name="employed_in_india" value="no"> No
                        </label>
                    </div>
                </div>
            </div>
        </div>


        <!-- Contact Information -->
        <div class="form-section">
            <h2>Contact Information</h2>
            <div class="grid">
                <div class="form-group flex-item">
                    <label for="mobile_no">Mobile No</label>
                    <input type="text" id="mobile_no" name="mobile_no" placeholder="Your Mobile Number">
                </div>
                <div class="form-group flex-item">
                    <label for="email_id">Email ID</label>
                    <input type="email" id="email_id" name="email_id" placeholder="Your Email Address">
                </div>
                <div class="form-group flex-item">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" placeholder="Your Phone Number">
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="form-section">
            <h2>Address Information</h2>
            <div class="grid">
                <div class="form-group flex-item">
                    <label for="address_street">Street Address</label>
                    <input type="text" id="address_street" name="address_street" placeholder="Street Address">
                </div>
                <div class="form-group flex-item">
                    <label for="address_locality">Locality</label>
                    <input type="text" id="address_locality" name="address_locality" placeholder="Locality">
                </div>
                <div class="form-group flex-item">
                    <label for="address_city">City</label>
                    <input type="text" id="address_city" name="address_city" placeholder="City">
                </div>
                <div class="form-group flex-item">
                    <label for="address_district">District</label>
                    <input type="text" id="address_district" name="address_district" placeholder="District">
                </div>
                <div class="form-group flex-item">
                    <label for="address_state">State</label>
                    <input type="text" id="address_state" name="address_state" placeholder="State">
                </div>
                <div class="form-group flex-item">
                    <label for="pincode">Pincode</label>
                    <input type="text" id="pincode" name="pincode" placeholder="Pincode">
                </div>
                <div class="form-group flex-item">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" placeholder="Detailed Address"></textarea>
                </div>
            </div>
        </div>
        <!-- Room Information -->
        <div class="form-section">
            <h2>Room Information</h2>
            <div class="grid">
                <!--div class="form-group flex-item">
                    <label for="room_no">Room No</label>
                    <input type="text" id="room_no" name="room_no" placeholder="Enter room number" >
                </div><br>
                <div class="form-group flex-item">
                    <label for="room_type">Room Type</label>
                    <input type="text" id="room_type" name="room_type" placeholder="Enter room type" >
                </div><br>
                <div class="form-group flex-item">
                    <label for="room_rate">Room Rate</label>
                    <input type="number" step="0.01" id="room_rate" name="room_rate" placeholder="Enter room rate" >
                </div><br>
                <div class="form-group flex-item">
                    <label for="tax">Tax</label>
                    <input type="number" step="0.01" id="tax" name="tax" placeholder="Enter tax amount" >
                </div><br-->
                <div class="form-group flex-item">
                    <label for="adults">Adults</label>
                    <input type="number" id="adults" name="adults" placeholder="Number of adults">
                </div><br>
                <div class="form-group flex-item">
                    <label for="children">Children</label>
                    <input type="number" id="children" name="children" placeholder="Number of children">
                </div><br>
                <div class="form-group flex-item">
                    <label for="extra_bed">Extra Bed</label>
                    <input type="number" id="extra_bed" name="extra_bed" placeholder="Number of extra beds">
                </div><br>
                <!--div class="form-group flex-item">
                    <label for="booked_by">Booked By</label>
                    <input type="text" id="booked_by" name="booked_by" placeholder="Enter booking person's name">
                </div><br>
                <div class="form-group flex-item">
                    <label for="billing_instruction">Billing Instruction</label>
                    <textarea id="billing_instruction" name="billing_instruction" placeholder="Enter billing instructions"></textarea>
                </div><br-->
            </div>
        </div>
        <!-- Official Information -->
        <div class="form-section">
            <h2>Corporate Guest</h2>
            <div class="grid">
                <div class="form-group flex-item">
                    <label for="designation">Designation</label>
                    <input type="text" id="designation" name="designation" placeholder="Your Designation">
                </div>
                <div class="form-group flex-item">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" placeholder="Your Company Name">
                </div>
                <div class="form-group flex-item">
                    <label for="voucher_no">Employee No</label>
                    <input type="text" id="voucher_no" name="voucher_no" placeholder="Voucher Number">
                </div>
                <div class="form-group flex-item">
                    <label for="gstin">GSTIN</label>
                    <input type="text" id="gstin" name="gstin" placeholder="GSTIN Number">
                </div>
                <div class="form-group">
                    <label for="purpose_of_visit">Purpose of Visit</label>
                    <select id="purpose_of_visit" name="purpose_of_visit" placeholder="Purpose of Visit">
                        <option value="holiday">Holiday</option>
                        <option value="conference">Conference</option>
                        <option value="group_tour">Group Tour</option>
                        <option value="company_work">Company Work</option>
                        <option value="own_business">Own Business</option>
                        <option value="others">Others</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Travel Information -->
        <div class="form-section">
            <h2>Travel Information</h2>
            <div class="grid">
                <div class="form-group flex-item">
                    <label for="arrived_from">Arrived From</label>
                    <input type="text" id="arrived_from" name="arrived_from" placeholder="Arrived From">
                </div>
                <div class="form-group flex-item">
                    <label for="proceeding_to">Proceeding To</label>
                    <input type="text" id="proceeding_to" name="proceeding_to" placeholder="Proceeding To">
                </div>
                <div class="form-group flex-item">
                    <label for="arrival_date_time">Arrival Date & Time</label>
                    <input type="datetime-local" id="arrival_date_time" name="arrival_date_time" placeholder="Arrival Date & Time">
                </div>
                <div class="form-group flex-item">
                    <label for="departure_date_time">Departure Date & Time</label>
                    <input type="datetime-local" id="departure_date_time" name="departure_date_time" placeholder="Departure Date & Time">
                </div>
                <div class="form-group">
                    <label for="mode_of_payment">Mode of Payment</label>
                    <select id="mode_of_payment" name="mode_of_payment" onchange="toggleCardFields(this.value)" placeholder="Mode of Payment">
                        <option value="cash">Cash</option>
                        <option value="company">Company</option>
                        <option value="travellers_cheque">Travellers Cheque</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>
                <div class="form-group flex-item hidden" id="credit_card_fields">
                    <label for="card_no">Card No</label>
                    <input type="text" id="card_no" name="card_no" placeholder="Credit Card Number">
                </div>
                <div class="form-group flex-item hidden" id="credit_card_expiry">
                    <label for="card_expiry_date">Card Expiry Date</label>
                    <input type="date" id="card_expiry_date" name="card_expiry_date" placeholder="Credit Card Expiry Date">
                </div>
            </div>
        </div>

        <!-- Passport and Visa Information -->
        <div class="form-section">
            <h2>Passport and Visa Information</h2>
            <div class="grid">
                <div class="form-group flex-item">
                    <label for="passport_no">Passport No</label>
                    <input type="text" id="passport_no" name="passport_no" placeholder="Passport Number">
                </div>
                <div class="form-group flex-item">
                    <label for="place_of_issue">Place of Issue</label>
                    <input type="text" id="place_of_issue" name="place_of_issue" placeholder="Place of Passport Issue">
                </div>
                <div class="form-group flex-item">
                    <label for="date_of_issue">Date of Issue</label>
                    <input type="date" id="date_of_issue" name="date_of_issue" placeholder="Passport Issue Date">
                </div>
                <div class="form-group flex-item">
                    <label for="passport_expiry">Passport Expiry</label>
                    <input type="date" id="passport_expiry" name="passport_expiry" placeholder="Passport Expiry Date">
                </div>
                <div class="form-group flex-item">
                    <label for="visa_no">Visa No</label>
                    <input type="text" id="visa_no" name="visa_no" placeholder="Visa Number">
                </div>
                <div class="form-group flex-item">
                    <label for="visa_type">Visa Type</label>
                    <input type="text" id="visa_type" name="visa_type" placeholder="Visa Type">
                </div>
                <div class="form-group flex-item">
                    <label for="visa_expiry">Visa Expiry</label>
                    <input type="date" id="visa_expiry" name="visa_expiry" placeholder="Visa Expiry Date">
                </div>
            </div>
        </div>


        <!-- Submit Button -->
        <div class="form-section">
            <div class="grid">
                <input type="submit">Submit</input>
            </div>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script>
        $(document).ready(function() {
            $('#regform').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'http://localhost/rex/modules/registration/ajax/reg_form.php', // The current PHP file
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        // if (response.status === 'success') {
                        //     window.location.href = response.redirect;
                        // } else {
                        //     //alert(response.message);
                        // }
                    },
                    error: function() {
                        // alert('An error occurred. Please try again.');
                    }
                });
            });
        });
    </script>
<?php
} ?>