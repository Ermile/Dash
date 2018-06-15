function getCpu() {
  $.ajax({
    url: 'cpu.json.php',
    success: function (response)
    {
      console.log(response);
      setTimeout(function ()
      {
        getCpu();
      }, 1000);
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
          newDate(2),
          newDate(3),
          newDate(4),
          newDate(5),
          newDate(6)
        ],
        datasets: [
        {
          label: '{%trans "CPU Usage"%}',
          backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
          borderColor: window.chartColors.red,
          fill: false,
          data:
          [
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor()
          ],
        },
        {
          label: '{%trans "Memory"%}',
          backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
          borderColor: window.chartColors.blue,
          fill: false,
          data:
          [
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor(),
            randomScalingFactor()
          ],
        },
        {
          label: '{%trans "Disk usage"%}',
          backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
          borderColor: window.chartColors.green,
          fill: false,
          data: [
          {
            x: newDateString(0),
            y: randomScalingFactor()
          }, {
            x: newDateString(5),
            y: randomScalingFactor()
          }, {
            x: newDateString(7),
            y: randomScalingFactor()
          }, {
            x: newDateString(15),
            y: randomScalingFactor()
          }],
        }]
      },
      options: {
        title: {
          text: 'Server usage'
        },
        scales: {
          xAxes: [{
            type: 'time',
            time: {
              format: timeFormat,
              // round: 'day'
              tooltipFormat: 'll HH:mm'
            },
          }],
          yAxes: [{
            scaleLabel: {
              display: true,
              labelString: '{%trans "percentage"%}'
            }
          }]
        },
      }
    };

    window.onload = function() {
      var ctx = document.getElementById('canvas').getContext('2d');
      window.myLine = new Chart(ctx, config);

    };

    // document.getElementById('randomizeData').addEventListener('click', function() {
    //   config.data.datasets.forEach(function(dataset) {
    //     dataset.data.forEach(function(dataObj, j) {
    //       if (typeof dataObj === 'object') {
    //         dataObj.y = randomScalingFactor();
    //       } else {
    //         dataset.data[j] = randomScalingFactor();
    //       }
    //     });
    //   });

    //   window.myLine.update();
    // });


    document.getElementById('addData').addEventListener('click', function()
    {
      if (config.data.datasets.length > 0)
      {
        config.data.labels.push(newDate(config.data.labels.length));

        for (var index = 0; index < config.data.datasets.length; ++index)
        {
          if (typeof config.data.datasets[index].data[0] === 'object')
          {
            config.data.datasets[index].data.push(
            {
              x: newDate(config.data.datasets[index].data.length),
              y: randomScalingFactor(),
            });
          }
          else
          {
            config.data.datasets[index].data.push(randomScalingFactor());
          }
        }

        window.myLine.update();
      }
    });


    // document.getElementById('removeData').addEventListener('click', function()
    // {
    //   config.data.labels.splice(-1, 1); // remove the label first

    //   config.data.datasets.forEach(function(dataset)
    //   {
    //     dataset.data.pop();
    //   });

    //   window.myLine.update();
    // });
}


