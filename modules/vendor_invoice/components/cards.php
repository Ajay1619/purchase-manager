<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Invoices</h3>
                <p class="statistic" id="total-invoice-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px">
                <path d="M120-80v-800l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v800l-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60Zm120-200h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Paid Invoices</h3>
                <p class="statistic" id="total-active-invoice-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#4CAF50">
                <path d="M120-80v-800l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v800l-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60Zm120-200h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Pending Invoices</h3>
                <p class="statistic" id="total-inactive-invoice-count"></p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#FF5733">
                <path d="M120-80v-800l60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60 60 60 60-60v800l-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60-60-60-60 60Zm120-200h480v-80H240v80Zm0-160h480v-80H240v80Zm0-160h480v-80H240v80Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/vendor_invoice/ajax/fetch_invoice_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_invoice_count = response.total_invoice_count;
                    const total_active_invoice_count = response.total_active_invoice_count;
                    const total_inactive_invoice_count = response.total_inactive_invoice_count;
                    // Populate modal with fetched purchase details
                    $('#total-invoice-count').text(total_invoice_count);
                    $('#total-active-invoice-count').text(total_active_invoice_count);
                    $('#total-inactive-invoice-count').text(total_inactive_invoice_count);

                } else {
                    alert('Failed to fetch invoice: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching invoice: ' + error);
            }
        });
    </script>
<?php } ?>