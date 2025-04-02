<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Sales</h3>
                <p class="statistic" id="total-sales-count"></p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" class="card-icon" width="50px" fill="#000000">
                <path d="M856-390 570-104q-12 12-27 18t-30 6q-15 0-30-6t-27-18L103-457q-11-11-17-25.5T80-513v-287q0-33 23.5-56.5T160-880h287q16 0 31 6.5t26 17.5l352 353q12 12 17.5 27t5.5 30q0 15-5.5 29.5T856-390ZM260-640q25 0 42.5-17.5T320-700q0-25-17.5-42.5T260-760q-25 0-42.5 17.5T200-700q0 25 17.5 42.5T260-640Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Revenue</h3>
                <p class="statistic" id="total-revenue"></p>
            </div>

            <svg xmlns="http://www.w3.org/2000/svg" class="card-icon" height="50px" viewBox="0 -960 960 960" width="50px" fill="#000000">
                <path d="M531-260h96v-3L462-438l1-3h10q54 0 89.5-33t43.5-77h40v-47h-41q-3-15-10.5-28.5T576-653h70v-47H314v57h156q26 0 42.5 13t22.5 32H314v47h222q-6 20-23 34.5T467-502H367v64l164 178ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/sales_report/ajax/fetch_sales_report_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_sales_count = response.total_sales_count;
                    const total_revenue = response.total_revenue;
                    // Populate modal with fetched vendor details
                    $('#total-sales-count').text(total_sales_count);
                    $('#total-revenue').text(total_revenue);

                } else {
                    alert('Failed to fetch vendor details: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching vendor details: ' + error);
            }
        });
    </script>
<?php } ?>