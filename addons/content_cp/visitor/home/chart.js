function chartDrawer()
{
  if($("#chartdiv").length == 1){highChart();}
}



function highChart()
{

Highcharts.chart('chartdiv',
{
  chart: {
    zoomType: 'x',
    style: {
      fontFamily: 'IRANSans, Tahoma, sans-serif'
    }
  },
  title: {
    text: '{%trans "Website analytics"%}'
  },
  xAxis: [{
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
      text: '{%trans "Page"%}',
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
    shared: true
  },
  exporting:
  {
    buttons:
    {
      contextButton:
      {
        menuItems:
        [
         'printChart',
         'separator',
         'downloadPNG',
         'downloadJPEG',
         'downloadSVG'
        ]
      }
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
    name: '{%trans "Page view"%}',
    type: 'column',
    data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
    tooltip: {
      valueSuffix: ' {%trans "page"%}'
    }

  },
  {
    name: '{%trans "Visit"%}',
    type: 'spline',
    yAxis: 1,
    data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
    tooltip: {
      valueSuffix: ' {%trans "person"%}'
    }
  }
  ]
});
}