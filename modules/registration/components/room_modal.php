<?php
include_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="modal-content">
        <div class="modal-header">
            <h2>Room Details</h2>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body" id="modal-body">
            <div class="room-details">
                <div class="detail-row">
                    <div class="detail-item">
                        <h3>Room Number</h3>
                        <p>101</p>
                    </div>
                    <div class="detail-item">
                        <h3>Room Type</h3>
                        <p>Deluxe</p>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <h3>Price per Night</h3>
                        <p>$150</p>
                    </div>
                    <div class="detail-item">
                        <h3>Availability</h3>
                        <p>Available</p>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <h3>Max Occupancy</h3>
                        <p>3 Persons</p>
                    </div>
                    <div class="detail-item">
                        <h3>Amenities</h3>
                        <p>Wi-Fi, Air Conditioning, Breakfast</p>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item full-width">
                        <h3>Description</h3>
                        <p>A spacious room with a beautiful sea view.</p>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item full-width">
                        <h3>Notes</h3>
                        <p>Early check-in available upon request.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>