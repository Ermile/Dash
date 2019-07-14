function chartDrawer()
{

  if($("#postchart").length == 1){post_chart();}
  if($("#wordcloud").length == 1){wordcloud();}
}




function wordcloud()
{


  Highcharts.chart('wordcloud',
  {
    chart:
    {
      zoomType: 'x',
      style:
      {
        fontFamily: 'IRANSans, Tahoma, sans-serif'
      }
    },
    series: [{
      type: 'wordcloud',
      data: {{allWordCloud | raw}},
      name: '{%trans "Count"%}'
    }],
    plotOptions: {
      series : {
        // turboThreshold: 1000
      },
        wordcloud: {
            style : {
              fontFamily: "IRANSans"
            }
        }
    },
    tooltip: {
      useHTML: true,
      borderWidth: 0
    },
    credits:
    {
        text: '{{service.title}}',
        href: '{{service.url}}',
        position:
        {
            align: 'left',
            x: 45,
            verticalAlign: 'top',
            y: 25
        },
        style: {
            color: '#15677b',
            fontWeight: 'bold'
        }
    },
    title: {
      text: '{%trans "Word cloud"%}'
    }
  }, function(_chart)
  {
    _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).add();
  }
  );
}

function post_chart()
{
  Highcharts.chart('postchart',
  {
    chart: {
      type: 'area',
      zoomType: 'x',
      style: {
        fontFamily: 'IRANSans, Tahoma, sans-serif'
      }
    },
    title: {
      text: '{%trans "Post count"%}'
    },
    xAxis: {
      categories: {{dashboardDetail.chart.post.categories | raw}},
      tickmarkPlacement: 'on',
      title: {
        enabled: false
      }
    },
    yAxis: {
      title: {
        text: '{%trans "Post"%}'
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
      valueSuffix: ' {%trans "post"%}'
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
    series: {{dashboardDetail.chart.post.data | raw}}
  }, function(_chart)
    {
      _chart.renderer.image('{{service.logo}}', 10, 5, 30, 30).attr({class: 'chartServiceLogo'}).add();
    }
  );
}

