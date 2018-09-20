function chartDrawer()
{
  if($("#chartdiv").length == 1){myChart1();}
}


//-------------------------------------------------------------------------------------------------------
function myChart1()
{
  /**
 * --------------------------------------------------------
 * This demo was created using amCharts V4 preview release.
 *
 * V4 is the latest installement in amCharts data viz
 * library family, to be released in the first half of
 * 2018.
 *
 * For more information and documentation visit:
 * https://www.amcharts.com/docs/v4/
 * --------------------------------------------------------
 */

am4core.useTheme(am4themes_animated);

var chart = am4core.create("chartdiv", am4charts.XYChart);


var data = [];

chart.data = {{dashboardDetail.visitorchart | raw}};

var dateAxis = chart.xAxes.push(new am4charts.DateAxis());

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.tooltip.disabled = true;

var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.dateX = "date";
series.dataFields.visitValueY = "visit";
series.dataFields.valueY = "visitor";
series.tooltipText = "visit: {visitValueY.value} visitor: {valueY.value}";
series.sequencedInterpolation = true;
series.fillOpacity = 0.3;
series.defaultState.transitionDuration = 1000;
series.tensionX = 0.8;

var series2 = chart.series.push(new am4charts.LineSeries());
series2.dataFields.dateX = "date";
series2.dataFields.valueY = "visit";
series2.sequencedInterpolation = true;
series2.defaultState.transitionDuration = 1500;
series2.stroke = chart.colors.getIndex(6);
series2.tensionX = 0.8;

chart.cursor = new am4charts.XYCursor();
chart.cursor.xAxis = dateAxis;
chart.scrollbarX = new am4core.Scrollbar();
}

