function getServerStat() {
  $.ajax({
    url: '{{url.here}}?server=status',
    success: function (response)
    {
      console.log(response);
      setTimeout(function ()
      {
        getCpu();
      }, 1000);
      return response;
    }
  });
}




function chartDrawer()
{
    var timeFormat = 'MM/DD/YYYY HH:mm';

    function newDate(days) {
      return moment().add(days, 'd').toDate();
    }

    function newDateString(days) {
      return moment().add(days, 'd').format(timeFormat);
    }

    var color = Chart.helpers.color;
    var config =
    {
      type: 'line',
      data:
      {
        labels:
        [ // Date Objects
          newDate(0),
          newDate(1),
          newDate(2)
        ],
        datasets: [
        {
          label: '{%trans "CPU Usage"%}',
          backgroundColor: color(window.chartColors.red).alpha(0.7).rgbString(),
          borderColor: window.chartColors.red,
          fill: false,
          data:
          [
            74,
            58,
            94
          ],
        },
        {
          label: '{%trans "Memory"%}',
          backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
          borderColor: window.chartColors.blue,
          fill: false,
          data:
          [
            25,
            49,
            30
          ],
        },
        {
          label: '{%trans "Disk usage"%}',
          backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
          borderColor: window.chartColors.green,
          fill: false,
          data:
          [
            10,
            11,
            13
          ],
        }]
      },
      options:
      {
        title:
        {
          text: 'Server usage'
        },
        scales:
        {
          yAxes: [
          {
            ticks:
            {
              // the data minimum used for determining the ticks is Math.min(dataMin, suggestedMin)
              suggestedMin: 0,
              // the data maximum used for determining the ticks is Math.max(dataMax, suggestedMax)
              suggestedMax: 100
            }
          }]
        },
        scales:
        {
          xAxes: [
          {
            type: 'time',
            time:
            {
              format: timeFormat,
              // round: 'day'
              tooltipFormat: 'll HH:mm'
            },
          }],
          yAxes: [
          {
            scaleLabel:
            {
              display: true,
              labelString: '{%trans "percentage"%}'
            }
          }]
        },
      }
    };

    window.onload = function()
    {
      var ctx = document.getElementById('canvas').getContext('2d');
      window.myLine = new Chart(ctx, config);

    };

    document.getElementById('addData').addEventListener('click', function()
    {
      if (config.data.datasets.length > 0)
      {
        config.data.labels.push(newDate(config.data.labels.length));
        newVal =
        {
          cpu:10,
          memory:80,
          disk:30
        };
        // newVal = getServerStat;
        if(newVal)
        {
          config.data.datasets[0].data.push(newVal.cpu);
          config.data.datasets[1].data.push(newVal.memory);
          config.data.datasets[2].data.push(newVal.disk);
        }

        window.myLine.update();
      }
    });
}

function addNewServerData()
{

}

