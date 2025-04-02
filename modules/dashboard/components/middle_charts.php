<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div class="chart-box">
        <div id="chart"></div>
    </div>

    <div class="top-products">
        <div class="product-card">
            <img src="<?= GLOBAL_PATH . '/images/1st.png' ?>" alt="">
            <div class="product-details">
                <div class="product-title-1"></div>
                <div class="product-sales-1"></div>
                <div class="product-revenue-1"></div>
            </div>
        </div>
        <div class="product-card">
            <img src="<?= GLOBAL_PATH . '/images/2nd.png' ?>" alt="">
            <div class="product-details">
                <div class="product-title-2"></div>
                <div class="product-sales-2"></div>
                <div class="product-revenue-2"></div>
            </div>
        </div>
        <div class="product-card">
            <img src="<?= GLOBAL_PATH . '/images/3rd.png' ?>" alt="">
            <div class="product-details">
                <div class="product-title-3"></div>
                <div class="product-sales-3"></div>
                <div class="product-revenue-3"></div>
            </div>
        </div>
        <!-- Customers -->
        <div class="card">
            <h2>Top 5 Customers</h2>
            <div class="customer-container">
                <div class="customer" id="customer-1">
                    <span id="customer-name" class="customer-name-1">Customer A</span>
                    <div class="progress-bar">
                        <div id="progress-1" class="progress top-customer"></div>
                    </div>
                    <span class="percentage" id="percentage-1">75%</span>
                </div>
                <div class="customer" id="customer-2">
                    <span id="customer-name" class="customer-name-2">Customer B</span>
                    <div class="progress-bar">
                        <div id="progress-2" class="progress second-customer"></div>
                    </div>
                    <span class="percentage" id="percentage-2">60%</span>
                </div>
                <div class="customer" id="customer-3">
                    <span id="customer-name" class="customer-name-3">Customer C</span>
                    <div class="progress-bar">
                        <div id="progress-3" class="progress third-customer"></div>
                    </div>
                    <span class="percentage" id="percentage-3">50%</span>
                </div>
                <div class="customer" id="customer-4">
                    <span id="customer-name" class="customer-name-4">Customer D</span>
                    <div class="progress-bar">
                        <div id="progress-4" class="progress fourth-customer"></div>
                    </div>
                    <span class="percentage" id="percentage-4">40%</span>
                </div>
                <div class="customer" id="customer-5">
                    <span id="customer-name" class="customer-name-5">Customer E</span>
                    <div class="progress-bar">
                        <div id="progress-5" class="progress fifth-customer"></div>
                    </div>
                    <span class="percentage" id="percentage-5">30%</span>
                </div>
            </div>
        </div>

    </div>
    <script>
        var chart;
        $.ajax({
            url: '<?= MODULES . '/transactions/json/fetch_transactions_chart_details.php' ?>',
            type: 'GET',
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {
                    var labels = [];
                    var incomeData = [];
                    var expenseData = [];
                    response.data.forEach(function(item) {
                        labels.push(item.period_label);
                        incomeData.push(parseFloat(item.income));
                        expenseData.push(parseFloat(item.expense));
                    });
                    var options = {
                        chart: {
                            type: 'area',
                            height: 400
                        },
                        series: [{
                            name: 'Income',
                            data: incomeData
                        }, {
                            name: 'Expense',
                            data: expenseData
                        }],
                        xaxis: {
                            categories: labels
                        },
                        title: {
                            text: 'Income and Expense Over Time',
                            align: 'left'
                        },
                        yaxis: {
                            title: {
                                text: 'Amount (₹)'
                            }
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                inverseColors: false,
                                opacityFrom: 0.7,
                                opacityTo: 0.9,
                                stops: [0, 90, 100]
                            }
                        },
                        colors: ['#00A86B', '#FF6347'], // Greenish for income, reddish for expense
                        stroke: {
                            curve: 'smooth'
                        }
                    };
                    chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();
                } else {
                    showToast(response.status, response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching purchase history: ' + error);
            }
        });

        $.ajax({
            url: '<?= MODULES . '/dashboard/json/top_three_products.php' ?>',
            type: 'GET',
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {
                    const data = response.data;
                    $.each(data, function(index, product) {
                        const newIndex = index + 1;
                        $('.product-title-' + newIndex).text(product.product_name);
                        $('.product-sales-' + newIndex).text(product.total_invoices + ' Sold');
                        $('.product-revenue-' + newIndex).text('₹ ' + product.total_revenue);
                    });
                } else {
                    showToast(response.status, response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching purchase history: ' + error);
            }
        });

        $.ajax({
            url: '<?= MODULES . '/dashboard/json/top_five_customers.php' ?>',
            type: 'GET',
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {
                    const data = response.data;
                    // Loop through the customers and display them
                    $.each(data, function(index, customer) {
                        const newIndex = index + 1;
                        $('.customer-name-' + newIndex).text(customer.full_customer_name);
                        $('#progress-' + newIndex).css('width', customer.purchase_percentage + '%');
                        $('#percentage-' + newIndex).text(customer.purchase_percentage + '%');

                        // Show the customer element
                        $('#customer-' + newIndex).show();
                    });

                    // Hide remaining customer elements if there are less than 5 customers
                    for (let i = data.length + 1; i <= 5; i++) {
                        $('#customer-' + i).hide();
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
<?php } ?>