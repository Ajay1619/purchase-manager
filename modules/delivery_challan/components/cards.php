<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Delivery Challan</h3>
                <p class="statistic" id="total-delivery-challan-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px">
                <path d="M240-80q-33 0-56.5-23.5T160-160v-480q0-33 23.5-56.5T240-720h80q0-66 47-113t113-47q66 0 113 47t47 113h80q33 0 56.5 23.5T800-640v480q0 33-23.5 56.5T720-80H240Zm160-640h160q0-33-23.5-56.5T480-800q-33 0-56.5 23.5T400-720Zm200 200q17 0 28.5-11.5T640-560v-80h-80v80q0 17 11.5 28.5T600-520Zm-240 0q17 0 28.5-11.5T400-560v-80h-80v80q0 17 11.5 28.5T360-520Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/delivery_challan/ajax/fetch_delivery_challan_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_delivery_challan_count = response.total_delivery_challan_count;
                    // Populate modal with fetched purchase details
                    $('#total-delivery-challan-count').text(total_delivery_challan_count);

                } else {
                    showToast(response.status, response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching purchase history: ' + error);
            }
        });
    </script>

<?php } ?>