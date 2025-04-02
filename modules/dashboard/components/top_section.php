<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="revenue-section">
        <div class="today-revenue">
            <p class="title">Today's Revenue</p>
            <h2 id="todaysRevenue" class="revenue-amount"><?= CURRENCY_SYMBOL ?><span id="today-revenue"></span>
                <span id="percentageBadge" class="percentage-badge">
                    <span id="revenueIcon">
                    </span>
                    <span id="percentage">0.00</span>%
                </span>
                <span id="revenueComparison" class="percentage-badge"><?= CURRENCY_SYMBOL ?> <span id="amount-difference">0.00</span></span>
            </h2>
            <div class="yesterday-revenue">
                <p class="light-text">VS Yesterday: <?= CURRENCY_SYMBOL ?> <span id="yesterdaysRevenue">0.00</span> - <span id="yesterday-date"><?= date('d/m/Y', strtotime('-1 day')) ?></span></p>
            </div>
        </div>
    </div>
    <div class="inventory-card">
        <div class="card-content">
            <div class="card-title">Today's Sales</div>
            <div class="card-description" id="today-sale-count">0</div>
        </div>
    </div>
    <div class="inventory-card">
        <div class="card-content">
            <div class="card-title">Today's Purchases</div>
            <div class="card-description" id="today-purchases-count">0</div>
        </div>
    </div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/dashboard/ajax/top_section.php' ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    $('#today-revenue').text(data[0].TodaysRevenue);
                    $('#percentage').text(data[0].RevenuePercentage);
                    $('#amount-difference').text(data[0].RevenueDifference);
                    $('#yesterdaysRevenue').text(data[0].YesterdaysRevenue);
                    $('#today-purchases-count').text(data[0].TodaysPurchasesCount);
                    $('#today-sale-count').text(data[0].TodaysSalesCount);

                    // Set the class based on ProfitOrLoss
                    if (data[0].ProfitOrLoss === 0) {
                        $('.percentage-badge').addClass('loss').removeClass('profit');
                        // Change to loss icon
                        $('#revenueIcon').html(`
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#FFFFFF">
                                <path d="M480-200 240-440l56-56 184 183 184-183 56 56-240 240Zm0-240L240-680l56-56 184 183 184-183 56 56-240 240Z" />
                            </svg>
                        `);
                    } else {
                        $('.percentage-badge').addClass('profit').removeClass('loss');
                        // Change to profit icon
                        $('#revenueIcon').html(`
                            <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#FFFFFF" style="margin-right: 5px;">
                                <path d="m296-224-56-56 240-240 240 240-56 56-184-183-184 183Zm0-240-56-56 240-240 240 240-56 56-184-183-184 183Z" />
                            </svg>
                        `);
                    }
                } else {
                    showToast(response.status, response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching purchase history: ' + error);
            }
        });
    </script>
<?php }
?>