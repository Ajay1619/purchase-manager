<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Customers</h3>
                <p class="statistic" id="total-customers-count"></p>
            </div>

            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px">
                <path d="M51-404q-26-43-38.5-86.5T0-576q0-110 77-187t187-77q63 0 119.5 26t96.5 71q40-45 96.5-71T696-840q110 0 187 77t77 187q0 42-12.5 85T909-405q-10-12-22.5-20.5T860-440q20-35 30-69t10-67q0-85-59.5-144.5T696-780q-55 0-108.5 32.5T480-649q-54-66-107.5-98.5T264-780q-85 0-144.5 59.5T60-576q0 33 10 67t30 69q-14 6-26.5 15T51-404ZM0-80v-53q0-39 42-63t108-24q13 0 24 .5t22 2.5q-8 17-12 34.5t-4 37.5v65H0Zm240 0v-65q0-65 66.5-105T480-290q108 0 174 40t66 105v65H240Zm540 0v-65q0-20-3.5-37.5T765-217q11-2 22-2.5t23-.5q67 0 108.5 24t41.5 63v53H780ZM150-250q-29 0-49.5-20.5T80-320q0-29 20.5-49.5T150-390q29 0 49.5 20.5T220-320q0 29-20.5 49.5T150-250Zm660 0q-29 0-49.5-20.5T740-320q0-29 20.5-49.5T810-390q29 0 49.5 20.5T880-320q0 29-20.5 49.5T810-250Zm-330-70q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-440q0 50-34.5 85T480-320Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Active Customers</h3>
                <p class="statistic" id="total-active-customers-count"></p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#4CAF50">
                <path d="M51-404q-26-43-38.5-86.5T0-576q0-110 77-187t187-77q63 0 119.5 26t96.5 71q40-45 96.5-71T696-840q110 0 187 77t77 187q0 42-12.5 85T909-405q-10-12-22.5-20.5T860-440q20-35 30-69t10-67q0-85-59.5-144.5T696-780q-55 0-108.5 32.5T480-649q-54-66-107.5-98.5T264-780q-85 0-144.5 59.5T60-576q0 33 10 67t30 69q-14 6-26.5 15T51-404ZM0-80v-53q0-39 42-63t108-24q13 0 24 .5t22 2.5q-8 17-12 34.5t-4 37.5v65H0Zm240 0v-65q0-65 66.5-105T480-290q108 0 174 40t66 105v65H240Zm540 0v-65q0-20-3.5-37.5T765-217q11-2 22-2.5t23-.5q67 0 108.5 24t41.5 63v53H780ZM150-250q-29 0-49.5-20.5T80-320q0-29 20.5-49.5T150-390q29 0 49.5 20.5T220-320q0 29-20.5 49.5T150-250Zm660 0q-29 0-49.5-20.5T740-320q0-29 20.5-49.5T810-390q29 0 49.5 20.5T880-320q0 29-20.5 49.5T810-250Zm-330-70q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-440q0 50-34.5 85T480-320Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Inactive Customers</h3>
                <p class="statistic" id="total-inactive-customers-count"></p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#FF5733">
                <path d="M51-404q-26-43-38.5-86.5T0-576q0-110 77-187t187-77q63 0 119.5 26t96.5 71q40-45 96.5-71T696-840q110 0 187 77t77 187q0 42-12.5 85T909-405q-10-12-22.5-20.5T860-440q20-35 30-69t10-67q0-85-59.5-144.5T696-780q-55 0-108.5 32.5T480-649q-54-66-107.5-98.5T264-780q-85 0-144.5 59.5T60-576q0 33 10 67t30 69q-14 6-26.5 15T51-404ZM0-80v-53q0-39 42-63t108-24q13 0 24 .5t22 2.5q-8 17-12 34.5t-4 37.5v65H0Zm240 0v-65q0-65 66.5-105T480-290q108 0 174 40t66 105v65H240Zm540 0v-65q0-20-3.5-37.5T765-217q11-2 22-2.5t23-.5q67 0 108.5 24t41.5 63v53H780ZM150-250q-29 0-49.5-20.5T80-320q0-29 20.5-49.5T150-390q29 0 49.5 20.5T220-320q0 29-20.5 49.5T150-250Zm660 0q-29 0-49.5-20.5T740-320q0-29 20.5-49.5T810-390q29 0 49.5 20.5T880-320q0 29-20.5 49.5T810-250Zm-330-70q-50 0-85-35t-35-85q0-51 35-85.5t85-34.5q51 0 85.5 34.5T600-440q0 50-34.5 85T480-320Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/customer/ajax/fetch_customer_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_customers_count = response.total_customers_count;
                    const total_active_customers_count = response.total_active_customers_count;
                    const total_inactive_customers_count = response.total_inactive_customers_count;
                    // Populate modal with fetched customer details
                    $('#total-customers-count').text(total_customers_count);
                    $('#total-active-customers-count').text(total_active_customers_count);
                    $('#total-inactive-customers-count').text(total_inactive_customers_count);

                } else {
                    alert('Failed to fetch customer details: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching customer details: ' + error);
            }
        });
    </script>
<?php } ?>