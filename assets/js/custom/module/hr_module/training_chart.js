$(document).ready(function () {
    // alert('et');
    generateApexChart(trainingId);
});

function generateApexChart(training_id) {

    ajaxPostRequest(pageURL + 'training_attendence_chart', {'training_id': training_id}, function(data){
        var options = {
            series: data.data.series,
            chart: {
                width: 380,
                type: 'pie',
            },
            labels: data.data.lable,
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
    
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    });
    
}