<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Employees</h3>
                <p class="statistic" id="total-employee-count"></p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#000000">
                <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Active Employees</h3>
                <p class="statistic" id="total-employee-active-count"></p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#4CAF50">
                <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Inactive Employees</h3>
                <p class="statistic" id="total-employee-inactive-count"></p>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#FF5733">
                <path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Z" />
            </svg>
        </div>
    </div>


    <script>
        $.ajax({
            url: '<?= MODULES . '/employee/ajax/fetch_employee_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_employee_count = response.total_employee_count;
                    const total_active_employee_count = response.total_active_employee_count;
                    const total_inactive_employee_count = response.total_inactive_employee_count;
                    // Populate modal with fetched purchase details
                    $('#total-employee-count').text(total_employee_count);
                    $('#total-employee-active-count').text(total_active_employee_count);
                    $('#total-employee-inactive-count').text(total_inactive_employee_count);

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