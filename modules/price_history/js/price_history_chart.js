function priceHistoryChart(vendorData, xAxisLabels,productName) {
    const options = {
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
                formatter: function (value) {
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
            text: 'Price History of Product '+productName,
            align: 'left'
        }
    };

    // Render the chart
    const chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
}
