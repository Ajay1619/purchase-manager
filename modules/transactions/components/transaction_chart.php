<?php
require_once('../../../config/sparrow.php');
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
?>
    <!-- Transaction Chart -->
    <section id="transaction-chart">
        <div id="time-frame">
            <label for="timeframe">Select Timeframe: </label>
            <select id="timeframe" onchange="transactionChartDetail()">
                <option value="daily">Daily</option>
                <option value="monthly" selected>Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
        <div id="chart"></div>

        <script>
            var chart;

            function transactionChartDetail() {
                var timeframe = $('#timeframe').val();
                $.ajax({
                    type: 'POST',
                    url: '<?= MODULES . '/transactions/json/fetch_transactions_chart_details.php' ?>',
                    data: {
                        timeframe: timeframe
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status == 'success') {
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

                            if (chart) {
                                chart.updateOptions(options);
                            } else {
                                chart = new ApexCharts(document.querySelector("#chart"), options);
                                chart.render();
                            }
                        } else {
                            alert('Failed to fetch transaction details: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Error fetching transaction details: ' + error);
                    }
                });
            }

            $(document).ready(function() {
                $('#timeframe').change(transactionChartDetail);

                // Initialize the chart with the default timeframe
                transactionChartDetail();
            });
        </script>
    <?php } ?>