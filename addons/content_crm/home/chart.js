function chartDrawer()
{
  if($("#identifyChart").length == 1){identifyChart();}
  if($("#genderchart").length == 1){gender_chart();}
  if($("#statuschart").length == 1){status_chart();}
  if($("#syncedChart").length == 1){syncedChart();}
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




/**
 * Synchronize zooming through the setExtremes event handler.
 */
function syncExtremes(e) {
  var thisChart = this.chart;

  if (e.trigger !== 'syncExtremes') { // Prevent feedback loop
    Highcharts.each(Highcharts.charts, function (chart) {
      if (chart !== thisChart) {
        if (chart.xAxis[0].setExtremes) { // It is null while updating
          chart.xAxis[0].setExtremes(
            e.min,
            e.max,
            undefined,
            false,
            { trigger: 'syncExtremes' }
          );
        }
      }
    });
  }
}


function syncedChart()
{
  /*
  The purpose of this demo is to demonstrate how multiple charts on the same page
  can be linked through DOM and Highcharts events and API methods. It takes a
  standard Highcharts config with a small variation for each data set, and a
  mouse/touch event handler to bind the charts together.
  */



  /**
   * In order to synchronize tooltips and crosshairs, override the
   * built-in events with handlers defined on the parent element.
   */
  ['mousemove', 'touchmove', 'touchstart'].forEach(function (eventType)
  {
    document.getElementById('syncedChart').addEventListener(
      eventType,
      function (e) {
        var chart,
          point,
          i,
          event;

        for (i = 0; i < Highcharts.charts.length; i = i + 1) {
          chart = Highcharts.charts[i];
          // Find coordinates within the chart
          event = chart.pointer.normalize(e);
          // Get the hovered point
          point = chart.series[0].searchPoint(event, true);

          if (point) {
            point.highlight(e);
          }
        }
      }
    );
  });

  /**
   * Override the reset function, we don't need to hide the tooltips and
   * crosshairs.
   */
  Highcharts.Pointer.prototype.reset = function ()
  {
    return undefined;
  };

  /**
   * Highlight a point by showing tooltip, setting hover state and draw crosshair
   */
  Highcharts.Point.prototype.highlight = function (event)
  {
    event = this.series.chart.pointer.normalize(event);
    this.onMouseOver(); // Show the hover marker
    this.series.chart.tooltip.refresh(this); // Show the tooltip
    this.series.chart.xAxis[0].drawCrosshair(event, this); // Show the crosshair
  };



  // Get the data. The contents of the data file can be viewed at
  Highcharts.ajax(
  {
    url: '{{url.kingdom}}/support/ticket/report?ajaxreport=json',
    dataType: 'text',
    success: function (activity)
    {

      activity = JSON.parse(activity);
      activity.datasets.forEach(function (dataset, i)
      {

        // Add X values
        dataset.data = Highcharts.map(dataset.data, function (val, j)
        {
          return [activity.xData[j], val];
        });

        var syncedChart = document.createElement('div');
        syncedChart.className = 'chart';
        document.getElementById('syncedChart').appendChild(syncedChart);

        Highcharts.chart(syncedChart,
        {
          chart: {
            marginLeft: 40, // Keep all charts left aligned
            spacingTop: 20,
            spacingBottom: 20,
            height: 220,
            zoomType: 'x',
            style: {
              fontFamily: 'IRANSans, Tahoma, sans-serif'
            }
          },
          title: {
            text: dataset.name,
            align: 'left',
            margin: 0,
            x: 30
          },
          credits: {
            enabled: false
          },
          exporting: {
            enabled: false
          },
          legend: {
            enabled: false
          },
          xAxis: {
            crosshair: true,
            events: {
              setExtremes: syncExtremes
            },
            labels: {
              format: '{value}'
            }
          },
          yAxis: {
            allowDecimals: false,
            title: {
              text: null
            }
          },
          tooltip: {
            positioner: function () {
              return {
                // right aligned
                x: this.chart.chartWidth - this.label.width,
                y: 10 // align to title
              };
            },
            borderWidth: 0,
            backgroundColor: 'none',
            pointFormat: '{point.y}',
            headerFormat: '',
            shadow: false,
            style: {
              fontSize: '18px'
            },
            valueDecimals: dataset.valueDecimals
          },
          series: [{
            data: dataset.data,
            name: dataset.name,
            type: dataset.type,
            color: Highcharts.getOptions().colors[i],
            fillOpacity: 0.3,
            tooltip: {
              valueSuffix: ' ' + dataset.unit
            }
          }]
        });
      });
    }
  });

}


