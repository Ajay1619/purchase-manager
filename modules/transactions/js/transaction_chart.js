// Sample data for the chart
var data = {
    daily: {
        labels: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
        income: [150, 170, 160, 180, 220, 200, 240, 210, 250, 270, 230, 190],
        expense: [100, 130, 120, 150, 160, 180, 200, 170, 190, 200, 210, 150]
    },
    monthly: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        income: [4500, 4700, 5100, 4900, 5200, 5800, 6000, 5900, 6200, 6700, 6300, 5800],
        expense: [3000, 3200, 3100, 3400, 3600, 3800, 4000, 3700, 3900, 4000, 4100, 3500]
    },
    yearly: {
        labels: ['2019', '2020', '2021', '2022', '2023', '2024'],
        income: [54000, 57000, 62000, 65000, 70000, 75000],
        expense: [36000, 38000, 40000, 43000, 46000, 48000]
    }
};

var options = {
    chart: {
        type: 'area', // Change to area chart
        height: 400
    },
    series: [{
        name: 'Income',
        data: data['monthly'].income
    }, {
        name: 'Expense',
        data: data['monthly'].expense
    }],
    xaxis: {
        categories: data['monthly'].labels
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

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();

function updateChart() {
    var timeframe = document.getElementById('timeframe').value;
    chart.updateOptions({
        series: [{
            name: 'Income',
            data: data[timeframe].income
        }, {
            name: 'Expense',
            data: data[timeframe].expense
        }],
        xaxis: {
            categories: data[timeframe].labels
        }
    });
}