function salesReportChart() {
    var options = {
        chart: {
            type: 'line', // Base chart type
            height: 400
        },
        series: [
            {
                name: 'Sales',
                type: 'line',
                data: [450, 470, 510, 490, 520, 580, 600] // Example data
            },
            {
                name: 'Quantity Sold',
                type: 'line',
                data: [50, 600, 700, 652, 444, 556, 692] // Example data
            },
            {
                name: 'Revenue',
                type: 'column',
                data: [5000, 5200, 5500, 5300, 5700, 6100, 6300] // Example data
            }
        ],
        stroke: {
            width: [3, 3, 0]
        },
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        yaxis: [
            {
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
        xaxis: {
            title: {
                text: 'Months'
            }
        },
        title: {
            text: 'Sales Report for Product Dairy Milk',
            align: 'left'
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
}