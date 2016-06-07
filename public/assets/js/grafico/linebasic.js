function geraGraficoLineBasic(id, variavel, min, max, series) {
    $('.' + id).highcharts({
        title: {
            text: variavel
            },
        subtitle: {
            text: 'Termos da variável ' + variavel
        },
        xAxis: {
            min: min,
            max: max
        },
        yAxis: {
            min:0,
            max: 1,
            title: {
                text: 'Pertinência'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px"><b>Valor:</b> {point.key}</span><table>',
            pointFormat:  '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                          '<td style="padding:0"><b>{point.y}</b> pertinência</td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            series: {
                marker: {
                    enabled: false
                }
            }
        },
        series: series
    });
}