// Code haye marboot be bakhshe files.talambar.dev
var fileList = new Cortex([]);
var filesTree = new Cortex({});

var $breadcrumbs = React.render(BreadcrumbView({items: []}), document.getElementById('breadcrumb-wrapper'));
var $tree = React.render(TreeView({items:{}}), document.getElementById('tree-wrapper'));
var $files = $('#files');

var trigger = true,
    time = Date.now();

fileList.on('update', function(d) {
  if (Date.now() - time > 200) trigger = true;
  if (!trigger) return;
  var parent = filterByURL(fileList);

  trigger = false;
  time = Date.now();
  setChildren(fileList, parent);

  component.setProps({items: d.getValue()});

  filesTree.set(objectifyFiles(fileList, getChildren(fileList, "")));

  $(document).trigger('navigate:done');
});

filesTree.on('update', function(d) {
  $tree.setProps({items: d.val()});
});

var component = React.render(FileList({items: []}), $files.get(0));

var userVisited = location.href;
Navigate({
  url: 'files/list', // files/list
  data: true
}).done(function() {
  var obj = JSON.parse(history.state.tree);
  if(!obj) obj = LS.get("files");

  fileList.set(obj);
  LS.set("files", obj);
  var parent = filterByURL(fileList);

  setChildren(fileList, parent);

  component.setProps({
    parent: parent
  });

  // files/list
  Navigate({
    url: userVisited,
    fake: true
  });
});


$(window).on('statechange', function() {
  var parent = filterByURL(fileList);

  if (parent) {
    var target = fileList.filterObject({id: parent});
    if (!target.length || target[0].type.val() !== 'folder') return;
  }
  setChildren(fileList, parent);

  component.setProps({
    parent: parent
  });

  $breadcrumbs.setProps({
    'items': location.pathname.slice(1).split('/')
  });
});

$(document).ready(function() {
  var filemanager = $('.filemanager');

  var fm;

  var $umodal = $('#upload_modal'),
      $pause = $('#pause'),
      $resume = $('#resume'),
      $upload = $('#upload'),
      $flabel = $umodal.find('label'),
      $fpreview = $('#file_preview'),
      $finput = $('#file_input'),
      $progress = $('#progress');

  $umodal.on('open', function() {
    $pause.hide();
    $resume.hide();
    $finput.val('').show();
    $progress.css('width', '0').text('');
    $flabel.text('Select your file');
    $fpreview.addClass('hidden').prop('src', '');
  })

  $finput.on('change', function() {
    showPreview($finput.get(0).files[0]);
  });

  $('#upload_form').submit(function(e) {
    e.preventDefault();

    if ($finput.prop('dropfiles')) {
      startUpload($finput.prop('dropfiles')[0]);
    } else {
      startUpload($finput.get(0).files[0])
    }
  });

  $('#resume').click(function(e) {
    e.preventDefault();
    fm.resume();
  });

  $('#pause').click(function(e) {
    e.preventDefault();
    fm.pause();
  });

  $('#newfolder').click(function(e) {
    e.preventDefault();
    $('#newform').removeClass('hidden');
  });

  $('#select_all').click(function(e) {
    e.preventDefault();

    component.selectAll();
  });

  $body = $(document.body);

  $body.on('keydown', function(e) {
    if (e.which === 27) {
      $('#newform').addClass('hidden').find('[name="name"]').val('');
      selected = [];
      $('.selected').removeClass('selected');
    }

    if (e.which === 65 && (e.ctrlKey || e.metaKey)) {
      component.selectAll();
    }
  });

  $body.on('dragover', function(e) {
    e.preventDefault();
    if (!selected.dragging)
      $body.addClass('dragover');
    else
      $body.removeClass('dragover').addClass('filedrag');
  });
  $body.on('dragend dragleave drop', function() {
    $body.removeClass('dragover filedrag');
  });
  $body.on('drop', function(e) {
    e.preventDefault();
    var files = e.originalEvent.dataTransfer.files;
    if (!files.length) return;

    $umodal.trigger('open');
    $finput.hide();
    $flabel.text(files[0].name)

    $finput.prop('dropfiles', files);
    showPreview(files[0]);
  })

  function startUpload(file) {
    if (fm) fm.destroy();

    fm = new FileManager({
      file: file,
    });

    fm.on('upload:start', function() {
      $pause.show();
    });

    fm.on('upload:pause socket:disconnect', function() {
      $pause.hide()
      $resume.show();
    })

    fm.on('upload:resume', function() {
      $pause.show();
      $resume.hide()
    })

    fm.on('upload:progress', function(e, data) {
      $progress.css('width', data.percentage + '%').text(Math.floor(data.percentage) + '%');
    });

    fm.upload();
  }

  function showPreview(file) {
    var type = file.type.split('/')[0];
    if (type === "image") {
      var preview = new FileManager({
        type: 'DataURL',
        file: file
      });
      preview.load().then(function(e) {
        $fpreview.prop('src', e.target.result).removeClass('hidden');
      });
    } else {
      $fpreview.addClass('hidden').prop('src', '');
    }
  }
});

function filterByURL(files) {
  var url = location.pathname.slice(1).split('/');

  if(url[0] === '') return '';

  var current = files.findObject({name: decodeURIComponent(url[0]), parent: ''});
  (function() {
    if(!current) return;

    for(var i = 1, len = url.length; i < len; i++) {
      current = files.findObject({name: decodeURIComponent(url[i]), parent: current.id.getValue()});
    }
  })();

  var parent = current ? current.id.getValue() : null;

  return parent;
}

function objectifyFiles(files, filtered, url) {
  var tree = {};

  filtered.forEach(function(file) {
    var obj = file.val();
    obj.url = (url || '') + '/' + obj.name;
    obj.children = objectifyFiles(files, getChildren(files, obj.id), obj.url);

    tree[obj.id] = obj;
  });

  return tree;
}

function setChildren(files, parent) {
  var children = getChildren(files, parent);

  children.forEach(function(child, i) {
    if(child.type.val() !== 'folder' || child.hasKey('disabled')) return;

    var subchilds = getChildren(files, child.id.val()).count();

    var ref = files.findObject({id: child.id.val()});

    ref.add('children', subchilds);
  });
}

function getChildren(files, id) {
  return new Cortex(files.filterObject({parent: id}).map(function(a) {
    return a.val();
  }));
}