function chartDrawer()
{
  if($("#identifyChart").length == 1){identifyChart();}
  if($("#genderchart").length == 1){gender_chart();}
  if($("#statuschart").length == 1){status_chart();}
  if($("#logChart").length == 1){log_chart();}
  if($("#UsersChart").length == 1){users_chart();}
  if($("#userGuage").length == 1){users_guage();}

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
      text: '{%trans "User logs group by date"%}'
    },
    yAxis: {
      title: {
        text: '{%trans "Records"%}'
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

function users_chart()
{
  Highcharts.chart('UsersChart',
  {
    chart: {
      type: 'area',
      zoomType: 'x',
      style: {
        fontFamily: 'IRANSans, Tahoma, sans-serif'
      }
    },
    title: {
      text: 'Historic and Estimated Worldwide Population Growth by Region'
    },
    xAxis: {
      categories: ['1750', '1800', '1850', '1900', '1950', '1999', '2050'],
      tickmarkPlacement: 'on',
      title: {
        enabled: false
      }
    },
    yAxis: {
      title: {
        text: '{%trans "Members"%}'
      },
      labels: {
        formatter: function () {
          return this.value / 1000;
        }
      }
    },
    tooltip: {
      useHTML: true,
      borderWidth: 0,
      shared: true,
      valueSuffix: ' {%trans "member"%}'
    },
    plotOptions: {
      area: {
        stacking: 'normal',
        lineColor: '#666666',
        lineWidth: 1,
        marker: {
          lineWidth: 1,
          lineColor: '#666666'
        }
      }
    },
    legend: {
      layout: 'vertical',
      align: 'right',
      verticalAlign: 'middle'
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
    series: [{
      name: 'Asia',
      data: [502, 635, 809, 947, 1402, 3634, 5268]
    }, {
      name: 'Africa',
      data: [106, 107, 111, 133, 221, 767, 1766]
    }, {
      name: 'Europe',
      data: [163, 203, 276, 408, 547, 729, 628]
    }, {
      name: 'America',
      data: [18, 31, 54, 156, 339, 818, 1201]
    }, {
      name: 'Oceania',
      data: [2, 2, 2, 6, 13, 30, 46]
    }]
  }, function(_chart)
    {
      _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
    }
  );
}


function users_guage()
{
  /**
   * In the chart render event, add icons on top of the circular shapes
   */
  function renderIcons() {

    // Move icon
    if (!this.series[0].icon) {
      this.series[0].icon = this.renderer.path(['M', -8, 0, 'L', 8, 0, 'M', 0, -8, 'L', 8, 0, 0, 8])
        .attr({
          'stroke': '#303030',
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round',
          'stroke-width': 2,
          'zIndex': 10
        })
        .add(this.series[2].group);
    }
    this.series[0].icon.translate(
      this.chartWidth / 2 - 10,
      this.plotHeight / 2 - this.series[0].points[0].shapeArgs.innerR -
        (this.series[0].points[0].shapeArgs.r - this.series[0].points[0].shapeArgs.innerR) / 2
    );

    // Exercise icon
    if (!this.series[1].icon) {
      this.series[1].icon = this.renderer.path(
        ['M', -8, 0, 'L', 8, 0, 'M', 0, -8, 'L', 8, 0, 0, 8,
          'M', 8, -8, 'L', 16, 0, 8, 8]
        )
        .attr({
          'stroke': '#ffffff',
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round',
          'stroke-width': 2,
          'zIndex': 10
        })
        .add(this.series[2].group);
    }
    this.series[1].icon.translate(
      this.chartWidth / 2 - 10,
      this.plotHeight / 2 - this.series[1].points[0].shapeArgs.innerR -
        (this.series[1].points[0].shapeArgs.r - this.series[1].points[0].shapeArgs.innerR) / 2
    );

    // Stand icon
    if (!this.series[2].icon) {
      this.series[2].icon = this.renderer.path(['M', 0, 8, 'L', 0, -8, 'M', -8, 0, 'L', 0, -8, 8, 0])
        .attr({
          'stroke': '#303030',
          'stroke-linecap': 'round',
          'stroke-linejoin': 'round',
          'stroke-width': 2,
          'zIndex': 10
        })
        .add(this.series[2].group);
    }

    this.series[2].icon.translate(
      this.chartWidth / 2 - 10,
      this.plotHeight / 2 - this.series[2].points[0].shapeArgs.innerR -
        (this.series[2].points[0].shapeArgs.r - this.series[2].points[0].shapeArgs.innerR) / 2
    );
  }

  Highcharts.chart('userGuage', {

    chart: {
      type: 'solidgauge',
      height: '110%',
      events: {
        render: renderIcons
      }
    },

    title: {
      text: '{%trans "Special user report"%}',
    },

    tooltip: {
      borderWidth: 0,
      backgroundColor: 'none',
      useHTML: true,
      shadow: false,
      style: {
        fontSize: '12px'
      },
      pointFormat: '{series.name}<br><span style="font-size:2em; color: {point.color}; font-weight: bold">{point.y}%</span>',
      positioner: function (labelWidth) {
        return {
          x: (this.chart.chartWidth - labelWidth) / 2,
          y: (this.chart.plotHeight / 2) + 15
        };
      }
    },
    pane: {
      startAngle: 0,
      endAngle: 360,
      background: [{ // Track for Move
        outerRadius: '112%',
        innerRadius: '88%',
        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[0])
          .setOpacity(0.3)
          .get(),
        borderWidth: 0
      }, { // Track for Exercise
        outerRadius: '87%',
        innerRadius: '63%',
        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[1])
          .setOpacity(0.3)
          .get(),
        borderWidth: 0
      }, { // Track for Stand
        outerRadius: '62%',
        innerRadius: '38%',
        backgroundColor: Highcharts.Color(Highcharts.getOptions().colors[2])
          .setOpacity(0.3)
          .get(),
        borderWidth: 0
      }]
    },

    yAxis: {
      min: 0,
      max: 100,
      lineWidth: 0,
      tickPositions: []
    },

    plotOptions: {
      solidgauge: {
        dataLabels: {
          enabled: false
        },
        linecap: 'round',
        stickyTracking: false,
        rounded: true
      }
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
    series: [{
      name: 'Asia',
      data: [502
    series: [{
      name: 'Mobile',
      data: [{
        color: Highcharts.getOptions().colors[0],
        radius: '112%',
        innerRadius: '88%',
        y: 90
      }]
    }, {
      name: 'Telegram',
      data: [{
        color: Highcharts.getOptions().colors[1],
        radius: '87%',
        innerRadius: '63%',
        y: 25
      }]
    }, {
      name: 'Android',
      data: [{
        color: Highcharts.getOptions().colors[2],
        radius: '62%',
        innerRadius: '38%',
        y: 5
      }]
    }]
  }, function(_chart)
    {
      _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
    }
  );



}

