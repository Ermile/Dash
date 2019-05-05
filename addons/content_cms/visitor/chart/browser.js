function chartDrawer()
{
  if($("#chartdiv").length == 1){myChart1();}
}



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

var chart = am4core.create("chartdiv", am4charts.PieChart3D);

chart.legend = new am4charts.Legend();

chart.data = chart.data = {{chartDetail.data | raw}};

chart.innerRadius = am4core.percent(40);

var series = chart.series.push(new am4charts.PieSeries3D());
series.dataFields.value = "value";
series.dataFields.category = "key";
}
