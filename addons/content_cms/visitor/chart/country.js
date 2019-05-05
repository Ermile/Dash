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

var chart = am4core.create("chartdiv", am4maps.MapChart);
chart.geodata = am4geodata_worldLow;
chart.projection = new am4maps.projections.Miller();

var title = chart.chartContainer.createChild(am4core.Label);
title.text = "Life expectancy in the World";
title.fontSize = 20;
title.paddingTop = 30;
title.align = "center";

var polygonSeries = chart.series.push(new am4maps.MapPolygonSeries());
var polygonTemplate = polygonSeries.mapPolygons.template;
polygonTemplate.tooltipText = "{name}: {value.value.formatNumber('#.0')}";
polygonSeries.heatRules.push({
  property: "fill",
  target: polygonSeries.mapPolygons.template,
  min: am4core.color("#ffffff"),
  max: am4core.color("#AAAA00")
});
polygonSeries.useGeodata = true;

// add heat legend
var heatLegend = chart.chartContainer.createChild(am4maps.HeatLegend);
heatLegend.valign = "bottom";
heatLegend.align = "left";
heatLegend.width = am4core.percent(100);
heatLegend.series = polygonSeries;
heatLegend.orientation = "horizontal";
heatLegend.padding(20, 20, 20, 20);
heatLegend.valueAxis.renderer.labels.template.fontSize = 10;
heatLegend.valueAxis.renderer.minGridDistance = 40;

polygonSeries.mapPolygons.template.events.on("over", event => {
  handleHover(event.target);
});

polygonSeries.mapPolygons.template.events.on("hit", event => {
  handleHover(event.target);
});

function handleHover(mapPolygon) {
  if (!isNaN(mapPolygon.dataItem.value)) {
    heatLegend.valueAxis.showTooltipAt(mapPolygon.dataItem.value);
  } else {
    heatLegend.valueAxis.hideTooltip();
  }
}

polygonSeries.mapPolygons.template.strokeOpacity = 0.4;
polygonSeries.mapPolygons.template.events.on("out", event => {
  heatLegend.valueAxis.hideTooltip();
});

chart.zoomControl = new am4maps.ZoomControl();
chart.zoomControl.valign = "top";

// life expectancy data
polygonSeries.data = {{chartDetail.data | raw}};

// excludes Antarctica
polygonSeries.exclude = ["AQ"];


}
