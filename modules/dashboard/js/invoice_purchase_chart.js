var options = {
    series: [{
        name: 'Invoices',
        data: [44, 55, 57, 56, 61, 58, 63, 60, 66, 52, 77, 83]
    }, {
        name: 'Purchases',
        data: [76, 85, 101, 98, 87, 105, 91, 114, 94, 102, 110, 97]
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
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
            formatter: function (val) {
                return "$ " + val + " thousands"
            }
        }
    },
    title: {
        text: 'Invoice - Purchase Comparison - Last 12 Months',
        align: 'center'
    },
};

var chart = new ApexCharts(document.querySelector("#invoice-purchase-chart"), options);
chart.render();