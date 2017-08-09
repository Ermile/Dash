var app = require('express')(),
    bp = require('body-parser'),
    fs = require('fs'),
    _ = require('underscore'),
    multiparty = require('connect-multiparty');

app.use(bp.urlencoded());
app.use(multiparty());

app.use(function(req, res, next) {
  res.header("Access-Control-Allow-Origin", "*");
  res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
  next();
})

app.post('/file', function(req, res) {
  if(!req.files.file) {
    res.status(400);
    res.end('No file specified');
    return;
  }
  var f = fs.readFileSync(req.files.file.path);
  fs.appendFileSync(__dirname + '/' + req.files.file.originalFilename, f)
  res.end('true');
});

app.post('/check', function(req, res) {
  if(!req.files.file) {
    res.status(400);
    res.end('No file specified');
    return;
  }

  var buff = new Buffer(100);

  var f = fs.openSync(__dirname + '/' + req.files.file.originalFilename, 'r');
  var stats = fs.fstatSync(f);
  fs.readSync(f, buff, 0, 100, stats.size - 100);

  var target = fs.readFileSync(req.files.file.path);

  if(target.toString() === buff.toString()) {
    res.end('Check successful');
  } else {
    res.status(400);
    res.end('Check unsucessful');
  }
})

app.post('/resume', function(req, res) {
  try {
    var stats = fs.statSync(__dirname + '/' + req.body.fileName);
  } catch(e) {
    return res.end('0');
  }

  res.end(stats.size + '');
})


app.post('/form-validate', function(req, res) {
  if(!req.body.password) {
    res.json({
      "title": "خطا در ثبت فرم",
      "status": 0,
      "messages": {
        "error": [{
          "title": "شما پسورد وارد نکردید!",
          "group": "forms",
          "element": "password"
        }, {
          "title": "پسورد شما کوتاه تر از ۴ کاراکتر است",
          "group": "forms",
          "element": "password"
        }],
        "warn": [{
          "title": "ما پیشنهاد میکنیم پسوردی طولانی تر از ۱۰ حرف انتخاب کنید",
          "group": "forms",
          "element": "password"
        }]
      },
      "info": {
          "id": 20,
          "username": "baravak",
          "url": "http://ermile.com/baravak/t65332"
      }
    });
  } else if (req.body.password.length < 4) {
    res.json({
      "title": "خطا در ثبت فرم",
      "status": 0,
      "messages": {
        "error": [{
          "title": "پسورد شما کوتاه تر از ۴ کاراکتر است",
          "group": "forms",
          "element": "password"
        }],
        "warn": [{
          "title": "ما پیشنهاد میکنیم پسوردی طولانی تر از ۱۰ حرف انتخاب کنید",
          "group": "forms",
          "element": "password"
        }]
      },
      "info": {
          "id": 20,
          "username": "baravak",
          "url": "http://ermile.com/baravak/t65332"
      }
    })
  } else if (req.body.password.length < 10) {
    res.json({
      status: 1,
      "messages": {
        "warn": [{
          "title": "ما پیشنهاد میکنیم پسوردی طولانی تر از ۱۰ حرف انتخاب کنید",
          "group": "forms",
          "element": "password"
        }]
      },
      title: 'شما با موفقیت ثبت نام شدید'
    })
  } else {
    res.json({
      status: 2,
      title: 'شما با موفقیت ثبت نام شدید'
    })
  }
})

var folders = [
  {id: 0, name: 'New Folder 1', subfiles: [3, 4, 5]},
  {id: 1, name: 'New Folder 2'}
];
var files = [
  {id: 2, name: 'New File', content: 'Salam'},
  {id: 3, name: 'File 3', content: 'Yo'},
  {id: 4, name: 'File 4', content: 'Chetori'},
  {id: 5, name: 'File 5', content: 'test'}
];

function getFiles(id) {
  if(!_.isNumber(id) || isNaN(id)) return files;
  return folders[id].subfiles || [];
}

function getFolders(id) {
  if(!_.isNumber(id) || isNaN(id)) return folders;
  return folders[id].subfolders || [];
}

/*app.get('/folders/:id?', function(req, res) {
  res.json(getFolders(+req.params.id));
});

app.get('/files/:id?', function(req, res) {
  res.json(getFiles(+req.params.id));
})
*/
app.get('/json/file/:id', function(req, res) {
  res.json(_.findWhere(files, {id: +req.params.id}));
})

app.get('/json/folder/:id', function(req, res) {
  res.json(_.findWhere(folders, {id: +req.params.id}));
})

var filetemplate = '<% for(var i = 0; i < files.length; i++) { %> \
                    <a href="#file/<%= files[i].id %>" id="file<%= files[i].id %>" class="file"><%= files[i].name %></a> \
                <% } %>';

var foldertemplate = filetemplate.replace(/file/g, 'folder');

app.get('/view/files/:id?', function(req, res) {
  var html = '';
  html += _.template(foldertemplate)({folders: getFolders(+req.params.id)});
  html += _.template(filetemplate)({files: getFiles(+req.params.id)});

  res.end(html);
})

app.listen(8000);
