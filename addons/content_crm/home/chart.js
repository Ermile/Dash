function chartDrawer()
{
  if($("#identifyChart").length == 1){identifyChart();}
  if($("#genderchart").length == 1){gender_chart();}
  if($("#statuschart").length == 1){status_chart();}
  if($("#logChart").length == 1){log_chart();}

}



function identifyChart()
{

  Highcharts.chart('identifyChart',
  {
    chart: {
      zoomType: 'x',
      style: {
        fontFamily: 'IRANSans, Tahoma, sans-serif'
      }
    },
    title: {
      text: '{%trans "Users group by identify"%}'
    },
    xAxis: [{
      categories: {{dashboardDetail.chart.identify.categories | raw}},
      crosshair: true
    }],
    yAxis: [{ // Primary yAxis
      labels: {
        format: '{value}',
        style: {
          color: Highcharts.getOptions().colors[0]
        }
      },
      title: {
        text: '{%trans "Person"%}',
        useHTML: Highcharts.hasBidiBug,
        style: {
          color: Highcharts.getOptions().colors[0]
        }
      }
    },
    { // Secondary yAxis
      title: {
        text: '{%trans "Person"%}',
        useHTML: Highcharts.hasBidiBug,
        style: {
          color: Highcharts.getOptions().colors[1]
        }
      },
      labels: {
        format: '{value}',
        style: {
          color: Highcharts.getOptions().colors[1]
        }
      },
      opposite: true
    }],
    tooltip: {
      useHTML: true,
      borderWidth: 0,
      shared: true
    },
    exporting:
    {
      enabled: false
    },
    credits:
    {
        text: '{{service.title}}',
        href: '{{service.url}}',
        position:
        {
            x: -35,
            y: -7
        },
        style: {
            fontWeight: 'bold'
        }
    },
    legend: {
      layout: 'vertical',
      align: 'left',
      x: 120,
      verticalAlign: 'top',
      y: 100,
      floating: true,
      backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
    },
    series: [
    {
      name: '{%trans "Count"%}',
      type: 'column',
      data: {{dashboardDetail.chart.identify.value | raw}},
      tooltip: {
        valueSuffix: ' {%trans "Person"%}'
      }

    }
    ]
  }, function(_chart)
    {
      _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
    }
  );
}







function gender_chart()
{
  Highcharts.chart('genderchart',
  {
    chart: {
      zoomType: 'x',
      style: {
        fontFamily: 'IRANSans, Tahoma, sans-serif'
      },
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
    },
    title: {
      text: '{%trans "Users group by"%} {%trans "gender"%}'
    },
    tooltip: {
      useHTML: true,
      borderWidth: 0,
      shared: true,
      pointFormat: '{series.name} <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true,
          // format: '<b>{point.name}</b><br> {point.percentage:.1f} %',
          useHTML: true,
          style: {
            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
          }
        }
      }
    },
    exporting:
    {
      enabled: false
    },
    credits:
    {
        text: '{{service.title}}',
        href: '{{service.url}}',
        position:
        {
            x: -35,
            y: -7
        },
        style: {
            fontWeight: 'bold'
        }
    },
    series:
    [
    {
      name: '{%trans "User Status"%}',
      allowPointSelect: true,
      data: {{dashboardDetail.chart.gender | raw}},
      tooltip: {
        valueSuffix: ' {%trans "Person"%}'
      },
      showInLegend: true
    }]
  }, function(_chart)
  {
    _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
  });
}


function status_chart()
{
  Highcharts.chart('statuschart',
  {
    chart: {
      zoomType: 'x',
      style: {
        fontFamily: 'IRANSans, Tahoma, sans-serif'
      },
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
    },
    title: {
      text: '{%trans "Users group by"%} {%trans "Status"%}'
    },
    tooltip: {
      useHTML: true,
      borderWidth: 0,
      shared: true,
      pointFormat: '{series.name} <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true,
          // format: '<b>{point.name}</b><br> {point.percentage:.1f} %',
          useHTML: true,
          style: {
            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
          }
        }
      }
    },
    exporting:
    {
      enabled: false
    },
    credits:
    {
        text: '{{service.title}}',
        href: '{{service.url}}',
        position:
        {
            x: -35,
            y: -7
        },
        style: {
            fontWeight: 'bold'
        }
    },
    series:
    [
    {
      name: '{%trans "User Status"%}',
      allowPointSelect: true,
      data: {{dashboardDetail.chart.status | raw}},
      tooltip: {
        valueSuffix: ' {%trans "Person"%}'
      },
      showInLegend: true
    }]
  }, function(_chart)
  {
    _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
  });
}



function log_chart()
{

  Highcharts.chart('logChart', {
    chart: {
      type: 'areaspline',
      zoomType: 'x',
      style: {
        fontFamily: 'IRANSans, Tahoma, sans-serif'
      }
    },
    title: {
      text: 'Logs group by date'
    },
    yAxis: {
      title: {
        text: 'Record'
      }
    },
    tooltip: {
      useHTML: true,
      borderWidth: 0,
      shared: true,
      valueSuffix: ' {%trans "record"%}'
    },
    legend:{
      enabled: false
    },
    exporting:
    {
      enabled: false
    },
    credits:
    {
        text: '{{service.title}}',
        href: '{{service.url}}',
        position:
        {
            x: -35,
            y: -7
        },
        style: {
            fontWeight: 'bold'
        }
    },
    plotOptions: {
      areaspline: {
        fillOpacity: 0.5
      }
    },
    series: [{
      name: 'John',
      data: [3, 4, 3, 5, 4, 10, 12]
    }]
  }, function(_chart)
    {
      _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
    }
  );
}



