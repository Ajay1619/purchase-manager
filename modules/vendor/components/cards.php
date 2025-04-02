<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Vendors</h3>
                <p class="statistic" id="total-vendors-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" class="card-icon" width="50px" height="50px">
                <path d="m19.667,15.667v.333c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2v-.333s0,.333,0,.333h0c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2l1.238-3h10.524l1.238,3c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2h0m-4.667-10c0-3.309-2.691-6-6-6S3,2.691,3,6s2.691,6,6,6,6-2.691,6-6Zm7.143,14h-.619c-.673,0-1.306-.18-1.856-.495-.552.315-1.185.495-1.857.495h-.619c-.673,0-1.306-.18-1.857-.495-.551.315-1.184.495-1.856.495h-.619c-.296,0-.581-.042-.857-.108v4.108h11v-4.108c-.277.066-.562.108-.857.108Zm-13.143-4v-.396l.662-1.604h-4.662c-2.761,0-5,2.239-5,5v5h10v-5.338c-.615-.709-1-1.636-1-2.662Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Active Vendors</h3>
                <p class="statistic" id="total-active-vendors-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" class="card-icon" width="50px" height="50px" fill="#4CAF50">
                <path d="m19.667,15.667v.333c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2v-.333s0,.333,0,.333h0c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2l1.238-3h10.524l1.238,3c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2h0m-4.667-10c0-3.309-2.691-6-6-6S3,2.691,3,6s2.691,6,6,6,6-2.691,6-6Zm7.143,14h-.619c-.673,0-1.306-.18-1.856-.495-.552.315-1.185.495-1.857.495h-.619c-.673,0-1.306-.18-1.857-.495-.551.315-1.184.495-1.856.495h-.619c-.296,0-.581-.042-.857-.108v4.108h11v-4.108c-.277.066-.562.108-.857.108Zm-13.143-4v-.396l.662-1.604h-4.662c-2.761,0-5,2.239-5,5v5h10v-5.338c-.615-.709-1-1.636-1-2.662Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Inactive Vendors</h3>
                <p class="statistic" id="total-inactive-vendors-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" class="card-icon" width="50px" height="50px" fill="#FF5733">
                <path d="m19.667,15.667v.333c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2v-.333s0,.333,0,.333h0c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2l1.238-3h10.524l1.238,3c0,1.105-.831,2-1.857,2h-.619c-1.026,0-1.857-.895-1.857-2h0m-4.667-10c0-3.309-2.691-6-6-6S3,2.691,3,6s2.691,6,6,6,6-2.691,6-6Zm7.143,14h-.619c-.673,0-1.306-.18-1.856-.495-.552.315-1.185.495-1.857.495h-.619c-.673,0-1.306-.18-1.857-.495-.551.315-1.184.495-1.856.495h-.619c-.296,0-.581-.042-.857-.108v4.108h11v-4.108c-.277.066-.562.108-.857.108Zm-13.143-4v-.396l.662-1.604h-4.662c-2.761,0-5,2.239-5,5v5h10v-5.338c-.615-.709-1-1.636-1-2.662Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/vendor/ajax/fetch_vendor_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_vendors_count = response.total_vendors_count;
                    const total_active_vendors_count = response.total_active_vendors_count;
                    const total_inactive_vendors_count = response.total_inactive_vendors_count;
                    // Populate modal with fetched vendor details
                    $('#total-vendors-count').text(total_vendors_count);
                    $('#total-active-vendors-count').text(total_active_vendors_count);
                    $('#total-inactive-vendors-count').text(total_inactive_vendors_count);

                } else {
                    alert('Failed to fetch vendor details: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching vendor details: ' + error);
            },
        });
    </script>
<?php } ?>