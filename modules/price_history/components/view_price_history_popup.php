<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $product_id = isset($_POST['product_id']) ? sanitizeInput($_POST['product_id'], 'int') : '';
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Price History</h2>
                <div class="modal-body">
                    <h2 id="product-name"></h2>
                    <select id="price-period" name="price-period" onchange="price_history_data()" required>
                        <option value="" disabled>Select a Period</option>
                        <option value="daily" selected>Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <!-- Price history chart -->
                    <div id="chart"></div>

                    <!-- Dropdown and Table HTML -->
                    <div id="vendor-dropdown">
                        <label for="vendor-select">Select Vendor:</label>
                        <select id="vendor-select" name="vendor-select" onchange="price_history_data()">
                            <option value="" selected disabled>Select a Vendor</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>

                    <div id="price-history-view-table" class="price-history-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Purchase Order ID</th>
                                    <th>Unit of Measure</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script src="<?= MODULES . '/price_history/js/price_history_chart.js' ?>"></script>
        <script src="<?= PACKAGES . '/apexchart/apexchart.js' ?>"></script>
        <script>
            price_history_data();
            var chart;

            function price_history_data() {

                $.ajax({
                    url: '<?= MODULES . '/price_history/json/view_price_history_popup_data.php' ?>',
                    type: 'POST',
                    data: {
                        product_id: <?= $product_id ?>,
                        price_period: $('#price-period').val(),
                        vendor_id: $('#vendor-select').val() || '',
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            $('#product-name').text(response.product_details.product_name + ' | ' + response.product_details.product_code);
                            // Populate Vendor Dropdown
                            const vendorSelect = $('#vendor-select');
                            vendorSelect.empty();
                            vendorSelect.append('<option value="" disabled>Select a Vendor</option>');

                            let selectedVendorId = response.price_history.length > 0 ? response.price_history[0].vendor_id : null;

                            response.vendor_data.forEach(vendor => {
                                vendorSelect.append('<option value="' + vendor.vendor_id + '"' + (vendor.vendor_id === selectedVendorId ? ' selected' : '') + '>' + vendor.name + '</option>');
                            });

                            // Populate Price History Table
                            const priceHistoryTableBody = $('#price-history-view-table tbody');
                            priceHistoryTableBody.empty();
                            let serialNo = 1;

                            if (response.price_history.length > 0) {
                                const history = response.price_history[0]; // Assuming price_history has only one item due to selected vendor

                                history.items.forEach(item => {
                                    priceHistoryTableBody.append(`
                                            <tr>
                                                <td>${serialNo++}</td>
                                                <td>${item.date}</td>
                                                <td>${item.purchase_order_number}</td>
                                                <td>${item.unit_of_measure}</td>
                                                <td>${item.unit_price}</td>
                                            </tr>
                                        `);
                                });
                            }

                            // Populate the Chart
                            const vendorData = response.vendor_data;
                            const xAxisLabels = response.x_axis_labels;
                            const productName = response.product_details.product_name;

                            if (chart) {
                                // Update the chart if it already exists
                                chart.updateOptions({
                                    series: vendorData,
                                    xaxis: {
                                        categories: xAxisLabels
                                    },
                                    title: {
                                        text: 'Price History of Product ' + productName,
                                    }
                                });
                            } else {
                                // Create a new chart if it doesn't exist
                                chart = new ApexCharts(document.querySelector("#chart"), {
                                    chart: {
                                        height: 350,
                                        type: "line",
                                        stacked: false
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    series: vendorData,
                                    stroke: {
                                        width: 5
                                    },
                                    xaxis: {
                                        title: {
                                            text: "Date"
                                        },
                                        categories: xAxisLabels
                                    },
                                    yaxis: {
                                        labels: {
                                            formatter: function(value) {
                                                return "₹" + value; // Example: Add currency symbol
                                            }
                                        },
                                        title: {
                                            text: "Price"
                                        }
                                    },
                                    tooltip: {
                                        shared: false,
                                        intersect: true,
                                        x: {
                                            show: false
                                        }
                                    },
                                    legend: {
                                        horizontalAlign: "left",
                                        offsetX: 40
                                    },
                                    markers: {
                                        size: 4
                                    },
                                    title: {
                                        text: 'Price History of Product ' + productName,
                                        align: 'left'
                                    }
                                });
                                chart.render();
                            }

                        } else {
                            showToast('error', response.message);
                        }
                    },
                    error: function() {
                        showToast('error', 'Failed to fetch price history.');
                    }
                });
            }
        </script>
<?php }
} ?>