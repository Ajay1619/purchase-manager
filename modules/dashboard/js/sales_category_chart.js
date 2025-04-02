var options = {
    series: [35, 45, 28, 20, 50], // Replace with actual sales data
    chart: {
        type: 'polarArea',
        height: 350
    },
    labels: ['Category A', 'Category B', 'Category C', 'Category D', 'Category E'], // Replace with actual category names
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

var chart = new ApexCharts(document.querySelector("#sales-category-chart"), options);
chart.render();