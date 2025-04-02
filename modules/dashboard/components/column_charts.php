<?php
require_once('../../../config/sparrow.php');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <div id="inventory-chart" class="column-chart-container"></div>
    <div id="invoice-purchase-chart" class="column-chart-container"></div>
    <div id="sales-category-chart" class="column-chart-container"></div>

    <script>
        $.ajax({
            url: '<?= MODULES . '/dashboard/json/dashboard_inventory_chart.php' ?>',
            type: 'GET',
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {
                    const data = response.data[0]; // Access the first object in the data array
                    const inStockPercentage = parseFloat(data.in_stock_percentage);
                    const outOfStockPercentage = parseFloat(data.out_of_stock_percentage);
                    const stockProductPercentage = parseFloat(data.stock_product_percentage);
                    const nonStockProductPercentage = parseFloat(data.non_stock_product_percentage);

                    var options = {
                        series: [inStockPercentage, outOfStockPercentage, stockProductPercentage, nonStockProductPercentage], // Populate with AJAX data
                        chart: {
                            height: 350,
                            type: 'radialBar',
                        },
                        plotOptions: {
                            radialBar: {
                                dataLabels: {
                                    name: {
                                        fontSize: '22px',
                                        color: '#304463',
                                        offsetY: -10,
                                    },
                                    value: {
                                        fontSize: '16px',
                                        color: '#7D8ABC',
                                        offsetY: 5,
                                    },
                                    total: {
                                        show: true,
                                        label: 'Total',
                                        formatter: function(w) {
                                            // Custom formatter, you can change this based on your needs
                                            return '100%'; // Adjust based on calculation
                                        }
                                    }
                                }
                            }
                        },
                        labels: ['Items in Stock', 'Out of Stock', 'Stock Product', 'End Product'],
                        colors: ['#00A86B', '#FF6347', '#FFA500', '#4B0082'], // Colors for each segment

                        legend: {
                            show: true,
                            position: 'bottom',
                            fontSize: '14px',
                            markers: {
                                width: 12,
                                height: 12,
                                radius: 12,
                                strokeWidth: 0,
                                fillColors: ['#304463', '#00A86B', '#FF6347'],
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 5,
                            },
                        },
                        title: {
                            text: 'Inventory Data',
                            align: 'center'
                        },
                    };

                    // Create the chart
                    var chart = new ApexCharts(document.querySelector("#inventory-chart"), options);
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
            url: '<?= MODULES . '/dashboard/json/dashboard_bills_count_chart.php' ?>',
            type: 'GET',
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {

                    // Split the response data into arrays and limit to last 12 months
                    var invoice_counts = response.data[0].invoice_counts.split(',').map(Number).slice(0, 12).reverse(); // Reverse the array
                    var purchase_order_counts = response.data[0].purchase_order_counts.split(',').map(Number).slice(0, 12).reverse(); // Reverse the array
                    var month_names = response.data[0].month_names.split(',').slice(0, 12).reverse(); // Reverse the array

                    // Chart options with dynamic data from the response
                    var options = {
                        series: [{
                            name: 'Invoices',
                            data: invoice_counts
                        }, {
                            name: 'Purchases',
                            data: purchase_order_counts
                        }],
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: month_names,
                        },
                        yaxis: {
                            title: {
                                text: 'Amount'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val + " Bills"
                                }
                            }
                        },
                        title: {
                            text: 'Invoice - Purchase Comparison - Last 12 Months',
                            align: 'center'
                        },
                    };

                    // Create and render the chart
                    var chart = new ApexCharts(document.querySelector("#invoice-purchase-chart"), options);
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
            url: '<?= MODULES . '/dashboard/json/dashboard_sales_category_chart.php' ?>',
            type: 'GET',
            success: function(response) {
                response = JSON.parse(response);
                if (response.status === 'success') {

                    // Extract the data for the chart
                    var seriesData = response.data.map(item => item.product_count); // Extract product_count
                    var categoryLabels = response.data.map(item => item.product_category); // Extract product_category

                    // Chart options with dynamic data from the response
                    var options = {
                        series: seriesData, // Use the extracted series data
                        chart: {
                            type: 'polarArea',
                            height: 350
                        },
                        labels: categoryLabels, // Use the extracted category labels
                        stroke: {
                            colors: ['#fff']
                        },
                        fill: {
                            opacity: 0.8
                        },
                        colors: ['#FF4560', '#00E396', '#FEB019', '#775DD0', '#008FFB'], // Appropriate colors for the categories
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 300
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }],
                        title: {
                            text: 'Sales by Category',
                            align: 'center'
                        },
                        dataLabels: {
                            enabled: true,
                        },
                        legend: {
                            position: 'bottom'
                        }
                    };

                    // Create and render the chart
                    var chart = new ApexCharts(document.querySelector("#sales-category-chart"), options);
                    chart.render();

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