function geraGraficoLineBasic(id, variavel, min, max, series, centroide) {
    var header = centroide !== true ? '<span style="font-size:10px"><b>Valor:</b> {point.key} ' + variavel.sigla_unidade_medida + '</span><table>' : '';
    var legenda = centroide === true ? '<b>{point.x}</b> ' + variavel.sigla_unidade_medida : '<b>{point.y}</b> pertinência';
    $('.' + id).highcharts({
        title: {
            text: variavel.nome
            },
        subtitle: {
            text: 'Termos da variável ' + variavel.nome
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
            }
        },
        tooltip: {
            headerFormat: header,
            pointFormat:  '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                          '<td style="padding:0">' + legenda + '</td></tr>',
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