si4.initChart = function() {
    var mdDataEl = document.getElementById("chartData");
    var mdData = JSON.parse(mdDataEl.innerText);
    var chartData = mdData.chartData;
    //console.log(mdData);

    var canvas = document.getElementById("chartCanvas");
    var ctx = canvas.getContext('2d');
    var myChart = new Chart(ctx, {
        type: mdData.qType || 'bar',
        data: {
            labels: Object.keys(chartData),
            datasets: [{
                label: si4.translate("chart_num_deaths"),
                data: Object.values(chartData),
                backgroundColor: Object.keys(chartData).map(function(x) { return 'rgba(255, 99, 132, 0.2)' }),
                borderColor: Object.keys(chartData).map(function(x) { return 'rgba(255,99,132,1)' }),
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
};