<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
    if (isset($_GET['type']) && $_GET['type'] == 'view') {
        $product_id = isset($_POST['product_id']) ? sanitizeInput($_POST['product_id'], 'int')  : 1;
?>
        <!-- The Modal -->
        <div id="myModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="modal-title">Sales Report</h2>
                <div class="modal-body">
                    <h2 id="product-name">Dairy Milk</h2>
                    <select id="sales-period" name="sales-period" onchange="fetchSalesReportData()" required>
                        <option value="" disabled>Select a Period</option>
                        <option value="daily" selected>Daily</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <!-- Sales Report chart -->
                    <div id="chart"></div>

                    <div id="customer-dropdown">
                        <label for="customer-select">Select Customer:</label>
                        <select id="customer-select" onchange="fetchSalesReportData()">
                            <option value="customerA">Customer A</option>
                            <option value="customerB">customer B</option>
                            <option value="customerC">customer C</option>
                        </select>
                    </div>
                    <div id="sales-report-history-table" class="sales-report-history-table">
                        <h2>Sales History</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th>Unit of Measure</th>
                                    <th>Quantity Sold</th>
                                    <th>Unit Price</th>

                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <script>
            var salesChart;

            function fetchSalesReportData() {
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/sales_report/json/view_sales_report_popup_data.php' ?>',
                    data: {
                        product_id: <?= $product_id ?>,
                        sales_period: $('#sales-period').val(),
                        customer_id: $('#customer-select').val()
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {

                            // Update product details
                            $('#product-name').text(data.product_details.product_name + ' | ' + data.product_details.product_code);

                            // Populate Customer Dropdown
                            var customerSelect = $('#customer-select');
                            customerSelect.empty(); // Clear the current options
                            customerSelect.append('<option value="" disabled>Select Customer</option>'); // Default option

                            $.each(data.customer_data, function(customerId, customer) {
                                customerSelect.append('<option value="' + customer.customer_id + '"' +
                                    (customer.customer_id == data.selected_customer_id ? ' selected' : '') + '>' + customer.name + '</option>');
                            });

                            // Populate the Sales History Table
                            const salesHistoryTableBody = $('#sales-report-history-table tbody');
                            salesHistoryTableBody.empty();
                            let serialNo = 1;

                            if (data.sales_history.length > 0) {
                                data.sales_history.forEach(item => {
                                    salesHistoryTableBody.append(`
                                <tr>
                                    <td>${serialNo++}</td>
                                    <td>${item.date}</td>
                                    <td>${item.invoice_no}</td>
                                    <td>${item.unit_of_measure}</td>
                                    <td>${item.quantity_sold}</td>
                                    <td>${item.unit_price}</td>
                                </tr>
                            `);
                                });
                            }

                            // Chart Data
                            const chartData = data.chartData;
                            const xAxisLabels = data.xAxisLabels;
                            const productName = data.product_details.product_name;

                            if (salesChart) {
                                // Update chart if it already exists
                                salesChart.updateOptions({
                                    series: [{
                                            name: 'Sales',
                                            type: 'line',
                                            data: chartData.sales || []
                                        },
                                        {
                                            name: 'Quantity Sold',
                                            type: 'line',
                                            data: chartData.quantity || []
                                        },
                                        {
                                            name: 'Revenue',
                                            type: 'column',
                                            data: chartData.revenue || []
                                        }
                                    ],
                                    xaxis: {
                                        categories: xAxisLabels || []
                                    },
                                    title: {
                                        text: 'Sales Report for Product ' + productName,
                                    }
                                });
                            } else {
                                // Create a new chart if it doesn't exist
                                salesChart = new ApexCharts(document.querySelector("#chart"), {
                                    chart: {
                                        height: 400,
                                        type: "line",
                                        stacked: false
                                    },
                                    dataLabels: {
                                        enabled: false
                                    },
                                    series: [{
                                            name: 'Sales',
                                            type: 'line',
                                            data: chartData.sales || []
                                        },
                                        {
                                            name: 'Quantity Sold',
                                            type: 'line',
                                            data: chartData.quantity || []
                                        },
                                        {
                                            name: 'Revenue',
                                            type: 'column',
                                            data: chartData.revenue || []
                                        }
                                    ],
                                    stroke: {
                                        width: [3, 3, 0]
                                    },
                                    xaxis: {
                                        title: {
                                            text: "Date"
                                        },
                                        categories: xAxisLabels || []
                                    },
                                    yaxis: [{
                                            title: {
                                                text: 'Sales & Quantity Sold'
                                            }
                                        },
                                        {
                                            opposite: true,
                                            title: {
                                                text: 'Revenue'
                                            }
                                        }
                                    ],
                                    title: {
                                        text: 'Sales Report for Product ' + productName,
                                        align: 'left'
                                    },
                                    markers: {
                                        size: 4
                                    }
                                });
                                salesChart.render();
                            }

                        } else {
                            showToaster('error', 'Error fetching sales report data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading sales report chart data:', error);
                    }
                });
            }

            $(document).ready(function() {
                fetchSalesReportData();
            });
        </script>


<?php }
} ?>