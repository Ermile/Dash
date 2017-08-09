var BinaryServer = require('binaryjs').BinaryServer,
    fs = require('fs'),
    asyn = require('async'),
    db = require('./db');

var server = BinaryServer({port: 8000});

server.on('connection', function(client) {
  client.on('stream', function(stream, meta) {
    var hash = Math.random().toString(16).slice(2);
    var name = hash + Date.now() + meta.file;
    var fileStream = fs.createWriteStream(__dirname + '/uploads/' + name);

    stream.pipe(fileStream);

    addFile(meta, client.id, function(err, file, filepart, attachment) {
      if(err) return console.log('Error: ', err);

      stream.on('end', function() {
        console.log('end');
        filepart.destroy();
        moveFile(name, file.id);
        file.update({file_code: null});
      });
    });

    stream.on('error', function(e) {
      console.log('Error', e);
    });
  });
});

function addFile(meta, clientId, cb) {
  asyn.waterfall([
    function(next) {
      db.File.create({
        file_folder: meta.parent,
        file_code: clientId,
        file_size: meta.size,
        file_status: 'inprogress',
        file_server: 1
      }).then(function(file) {
        next(null, file);
      }, next);
    },
    function(file, next) {
      db.FilePart.create({
        file_id: file.id,
        filepart_part: 0,
        filepart_code: clientId,
        filepart_status: 'inprogress'
      }).then(function(filepart) {
        next(null, file, filepart);
      }, next);
    },
    function(file, filepart, next) {
      db.Attachment.create({
        file_id: file.id,
        attachment_title: meta.file,
        attachment_type: 'file',
        attachment_addr: meta.addr,
        attachment_size: meta.size,
        attachment_parent: meta.parent,
        attachment_count: 0,
        attachment_order: 0,
        user_id: meta.user || 190
      }).then(function(attachment) {
        next(null, file, filepart, attachment);
      }, next);
    }
  ], cb);
}

function moveFile(name, id) {
  fs.renameSync(__dirname + '/uploads/' + name, __dirname + '/uploads/' + id);
}
