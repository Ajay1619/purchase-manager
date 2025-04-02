<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $inventory_id = isset($_POST['inventory_id']) ? sanitizeInput($_POST['inventory_id'], 'int') : '';
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">View Inventory</h2>
                <div class="modal-body">
                    <div class="inventory-container">
                        <div class="product-details">
                            <div class="detail-item">
                                <label>Product Name</label>
                                <p id="product-name"></p>
                            </div>
                            <div class="detail-item">
                                <label>Product Code</label>
                                <p id="product-code"></p>
                            </div>
                            <div class="detail-item">
                                <label>Unit Of Measure</label>
                                <p id="product-unit"></p>
                            </div>
                            <div class="detail-item">
                                <label>Quantity In Stock</label>
                                <p id="product-stock"></p>
                            </div>
                        </div>
                        <div class="stock-usage">
                            <h2>Stock Usage</h2>
                            <select id="stock-period" name="stock-period" required onchange="fetchInventoryViewDetails()">
                                <option value="" disabled>Select a Period</option>
                                <option value="daily" selected>Daily</option>
                                <option value="monthly">Monthly</option>
                                <option value="yearly">Yearly</option>
                            </select>
                            <!-- stock usage chart -->
                            <div id="chart"></div>

                        </div>
                        <div class="inventory-history">
                            <h2>Inventory History</h2>
                            <div class="history-item">
                                <label>Date</label>
                                <p>2024-06-01</p>
                                <label>Stock In (Pcs)</label>
                                <p class="in">+50</p>
                            </div>
                            <div class="history-item">
                                <label>Date</label>
                                <p>2024-06-10</p>
                                <label>Stock Out (Pcs)</label>
                                <p class="out">-30</p>
                            </div>
                            <div class="history-item">
                                <label>Date</label>
                                <p>2024-06-15</p>
                                <label>Stock In (Pcs)</label>
                                <p class="in">+20</p>
                            </div>
                            <div class="history-item">
                                <label>Date</label>
                                <p>2024-06-20</p>
                                <label>Stock Out (Pcs)</label>
                                <p class="out">-40</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            fetchInventoryViewDetails();
            var chart;


            function fetchInventoryViewDetails() {

                $.ajax({
                    url: '<?= MODULES . '/inventory/ajax/inventory_history.php' ?>',
                    type: 'POST',
                    data: {
                        inventory_id: <?= $inventory_id ?>,
                        stock_period: $("#stock-period").val()
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status == 'success') {
                            const productDetails = response.product_details;
                            const inventoryHistory = response.inventory_history;
                            const stockUsageChartData = response.stock_usage_chart_data;

                            // Update product details
                            $('#product-name').text(productDetails.product_name);
                            $('#product-code').text(productDetails.product_code);
                            $('#product-unit').text(productDetails.unit_of_measure);
                            $('#product-stock').text(productDetails.quantity_in_stock);

                            // Update stock usage chart
                            const categories = stockUsageChartData.labels;
                            const series = [{
                                name: 'Stock In',
                                data: stockUsageChartData.stock_in
                            }, {
                                name: 'Stock Out',
                                data: stockUsageChartData.stock_out
                            }];

                            if (chart) {
                                chart.updateOptions({
                                    xaxis: {
                                        categories: categories
                                    },
                                    series: series
                                });
                            } else {
                                chart = new ApexCharts(document.querySelector("#chart"), {
                                    chart: {
                                        type: 'bar',
                                        stacked: true,
                                        height: 350
                                    },
                                    series: series,
                                    xaxis: {
                                        categories: categories
                                    },
                                    colors: ['#00E396', '#FEB019'],
                                    legend: {
                                        position: 'top'
                                    },
                                    plotOptions: {
                                        bar: {
                                            horizontal: false,
                                        },
                                    },
                                    fill: {
                                        opacity: 1
                                    },
                                    title: {
                                        text: 'Stock Usage of Product ' + productDetails.product_name,
                                        align: 'left'
                                    }
                                });
                                chart.render();
                            }

                            // Update inventory history
                            let historyHtml = '';
                            inventoryHistory.forEach(history => {
                                historyHtml += `<div class="history-item">
                                                    <label>Date</label>
                                                    <p>${history.created_on}</p>
                                                    <label>Stock ${history.inventory_history_status == 0 ? 'In' : 'Out'} (Pcs)</label>
                                                    <p class="${history.inventory_history_status == 0 ? 'In' : 'Out'}">${history.inventory_history_status == 0 ? '+' : '-'}${history.quantity}</p>
                                                </div>`;
                            });
                            $('.inventory-history').html(historyHtml);
                        } else {
                            showToastr('error', response.message);
                        }
                    }
                });
            }
        </script>
<?php }
} ?>