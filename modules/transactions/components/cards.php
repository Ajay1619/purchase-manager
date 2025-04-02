<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Income (<?= CURRENCY_SYMBOL ?>)</h3>
                <p class="statistic" id="total-income">1000.00</p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#4CAF50">
                <path d="m136-240-56-56 296-298 160 160 208-206H640v-80h240v240h-80v-104L536-320 376-480 136-240Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Today's Income (<?= CURRENCY_SYMBOL ?>)</h3>
                <p class="statistic" id="today-income">1000.00</p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#4CAF50">
                <path d="M440-160v-487L216-423l-56-57 320-320 320 320-56 57-224-224v487h-80Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Total Expense (<?= CURRENCY_SYMBOL ?>)</h3>
                <p class="statistic" id="total-expense">30000.00</p>
            </div>

            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#F44336">
                <path d="M640-240v-80h104L536-526 376-366 80-664l56-56 240 240 160-160 264 264v-104h80v240H640Z" />
            </svg>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="text-content">
                <h3>Today's Expense (<?= CURRENCY_SYMBOL ?>)</h3>
                <p class="statistic" id="today-expense">30000.00</p>
            </div>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" height="50px" viewBox="0 -960 960 960" width="50px" fill="#F44336">
                <path d="M440-800v487L216-537l-56 57 320 320 320-320-56-57-224 224v-487h-80Z" />
            </svg>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/transactions/ajax/fetch_transactions_card_details.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const total_income = response.total_income;
                    const total_expense = response.total_expense;
                    const today_income = response.today_income;
                    const today_expense = response.today_expense;
                    // Populate modal with fetched vendor details
                    $('#total-income').text(total_income);
                    $('#today-income').text(today_income);
                    $('#total-expense').text(total_expense);
                    $('#today-expense').text(today_expense);

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