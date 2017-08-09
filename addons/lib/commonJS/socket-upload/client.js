var fm;

$('#upload').click(function() {
  fm = new FileManager({
    file: $('#a').prop('files')[0],
    ajax: {
      url: 'http://localhost:8000/'
    }
  });

  fm.upload();

  var $progress = $('#progress');

  fm.on('upload:progress', function(e, data) {
    $progress.css('width', data.percentage + '%').text(Math.floor(data.percentage) + '%');
  });
});

$('#pause').click(function() {
  fm.pause();
});

$('#resume').click(function() {
  fm.resume();
});
