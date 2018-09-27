function chartDrawer()
{
  if($("#chartdiv").length == 1){highChart();}
}



function highChart()
{

Highcharts.chart('chartdiv',
{
  chart: {
    zoomType: 'x'
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



function myChart1()
{
  am4core.useTheme(am4themes_animated);

  var chart = am4core.create("chartdiv", am4charts.XYChart);

{%if visitor.chart%}
  chart.data = {{dashboardDetail.visitorchart | raw}};
{%else%}
  return;
{%endif%}

  var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
  categoryAxis.renderer.grid.template.location = 0;
  categoryAxis.renderer.ticks.template.disabled = true;
  categoryAxis.renderer.line.opacity = 0;
  categoryAxis.renderer.grid.template.disabled = true;
  categoryAxis.renderer.minGridDistance = 40;
  categoryAxis.dataFields.category = "date";


  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
  valueAxis.tooltip.disabled = true;
  valueAxis.renderer.line.opacity = 0;
  valueAxis.renderer.ticks.template.disabled = true;
  valueAxis.min = 0;

  var columnSeries = chart.series.push(new am4charts.ColumnSeries());
  columnSeries.dataFields.categoryX = "date";
  columnSeries.dataFields.valueY = "visit";
  columnSeries.tooltipText = '{%trans "visit"%}' +": {valueY.value}";
  columnSeries.name = '{%trans "visit"%}';
  columnSeries.sequencedInterpolation = true;
  columnSeries.defaultState.transitionDuration = 1500;
  columnSeries.fill = chart.colors.getIndex(4);

  var columnTemplate = columnSeries.columns.template;
  columnTemplate.column.cornerRadiusTopLeft = 10;
  columnTemplate.column.cornerRadiusTopRight = 10;
  columnTemplate.strokeWidth = 1;
  columnTemplate.strokeOpacity = 1;
  columnTemplate.stroke = columnSeries.fill;

  var desaturateFilter = new am4core.DesaturateFilter();
  desaturateFilter.saturation = 0.5;

  columnTemplate.filters.push(desaturateFilter);

  // first way - get properties from data. but can only be done with columns, as they are separate objects.
  columnTemplate.propertyFields.strokeDasharray = "stroke";
  columnTemplate.propertyFields.fillOpacity = "opacity";

  // add some cool saturation effect on hover
  var desaturateFilterHover = new am4core.DesaturateFilter();
  desaturateFilterHover.saturation = 1;

  var hoverState = columnTemplate.states.create("hover");
  hoverState.transitionDuration = 2000;
  hoverState.filters.push(desaturateFilterHover);

  var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
  valueAxis2.tooltip.disabled = true;
  valueAxis2.renderer.opposite = true;
  valueAxis2.renderer.grid.template.disabled = true;

  var lineSeries = chart.series.push(new am4charts.LineSeries());
  lineSeries.dataFields.categoryX = "date";
  lineSeries.dataFields.valueY = "visitor";
  lineSeries.tooltipText = '{%trans "visitor"%}' +": {valueY.value}";
  lineSeries.name = '{%trans "visitor"%}';
  lineSeries.sequencedInterpolation = true;
  lineSeries.defaultState.transitionDuration = 1500;
  lineSeries.stroke = chart.colors.getIndex(11);
  lineSeries.fill = lineSeries.stroke;
  lineSeries.strokeWidth = 2;
  lineSeries.yAxis = valueAxis2;



  var dropShadow = new am4core.DropShadowFilter();
  dropShadow.opacity = 0.25;
  lineSeries.filters.push(dropShadow);

  var bullet = lineSeries.bullets.push(new am4charts.CircleBullet());
  bullet.fill = lineSeries.stroke;
  bullet.circle.radius = 4;

  chart.cursor = new am4charts.XYCursor();
  chart.cursor.behavior = "none";
  chart.cursor.lineX.opacity = 0;
  chart.cursor.lineY.opacity = 0;


  chart.legend = new am4charts.Legend();
  chart.legend.parent = chart.plotContainer;
  chart.legend.zIndex = 100;
  chart.legend.valueLabels.template.text = "{valueY.value.formatNumber('#.')}";
}

