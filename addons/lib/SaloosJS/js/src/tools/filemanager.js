// FileManager
// estefade baraye khandan va upload e file ha

(function(root, $) {
  "use strict";

  var socket = io('http://localhost:8000');
  setTimeout(function() {
    socket.disconnect();
  }, 1000);

  var defaults = {
    url: 'localhost:8000',
    type: 'BinaryString',
    originalFile: null,
    file: null,
    result: null,
    range: [0, 0],
    parts: 200,
    rest: 50,
    restDuration: 500,
    stop: false,
    panic: false,
    ready: false,
    data: null // {}
  };

  var FileManager = function FileManager(options) {
    _.extend(this, defaults, options);

    this.originalFile = this.file;
    this.size = this.originalFile.size;
    this.fileType = this.originalFile.type;
    this.range = [0, this.size];
    this.data = {};

    this.$ = $(this);

    this._connect();
  };

  FileManager.prototype = {
    load: function() {
      var deferred = new jQuery.Deferred();

      this.fileReader = new FileReader();
      var self = this;
      this.fileReader.onload = function(e) {
        self.result = e.target.result;
        deferred.resolve(e);
        self.$.trigger('file:load', e);
      };
      this.fileReader.onerror = this.fileReader.onabort = function(e) {
        deferred.reject(e);
        self.$.trigger('file:error', e);
      };
      this.fileReader['readAs' + this.type](this.file);

      return deferred.promise();
    },
    slice: function(start, end) {
      this.file = this.originalFile.slice(start, end);
      this.range = [start, end];
      this.$.trigger('file:slice', this.range);
      return this;
    },
    full: function() {
      this.slice(0, this.size);
      return this;
    },
    _disconnect: function() {
      _super.ready = false;
      _super.panic = true;
      _super.stream = null;
      _super.paused = true;
      _super.$.trigger('socket:disconnect');

      socket.once('connect', function() {
        _super.panic = false;
      })
    },
    _connect: function() {
      var _super = this;

      socket.on('disconnect', this._disconnect);
    },
    createStream: function() {
      var deferred = new jQuery.Deferred();
      var _super = this;

      var meta = _.extend({
        file: this.originalFile.name,
        size: this.size,
      }, this.data);

      socket.emit('meta', meta);

      socket.once('ready', function(id) {
        _super.fileID = id;
        _super.data.fileID = id;
        _super.ready = true;
        deferred.resolve(id);
      });

      return deferred.promise();
    },
    send: function() {
      var deferred = new jQuery.Deferred();

      socket.emit('data', this.file);
      socket.once('data-answer', function() {
        deferred.resolve();
      });

      return deferred.promise();
    },
    upload: function(start, end, options) {
      var deferred = new jQuery.Deferred();

      var _super = this;

      if(!socket.connected) {
        socket.once('connect', function() {
          _super.$.trigger('socket:connect');
          _super.upload(start, end, options);
        });
        return;
      }

      if(!this.ready) {
        this.createStream().then(function() {
          _super.upload(start, end, options);
        });
        return;
      }

      this.$.trigger('upload:start');

      var from = _.isNumber(start) ? start : 0,
          to = _.isNumber(end) ? end : this.size,
          iteration = 0,
          chunkSize = this.parts*1000,
          rest = 0;

      this.lastSession = {
        from: from,
        to: to
      };

      (function loop() {
        if(_super.panic) {
          _super.panic = false;
          deferred.reject();
          return;
        }
        var chunk = [from + iteration*chunkSize, from + (iteration+1)*chunkSize];

        if(chunk[1] > to) {
          chunk[1] = to;
          _super.stop = true;
          deferred.resolve();
        }

        _super.slice(chunk[0], chunk[1]);
        _super.send(options).then(function() {
          iteration++;
          rest++;

          var percentage = chunk[1] * 100 / to;
          _super.$.trigger('upload:progress', {
            min: from,
            max: to,
            sent: chunk[1],
            percentage: percentage,
          });

          if(_super.stop) {
            _super.stop = false;
            if (_super.paused) _super.$.trigger('upload:pause');
            return;
          }

          if(rest === _super.rest) {
            rest = 0;
            setTimeout(loop, _super.restDuration);
          } else {
            loop();
          }
        });
      })();

      this.$.trigger('upload:done');
      return deferred.promise();
    },
    pause: function() {
      this.stop = true;
      this.paused = true;
    },
    resume: function() {
      if(!this.paused || !socket.connected) return;
      console.log('resume');

      var _super = this;
      socket.emit('resume-status', this.data);
      socket.once('resume-status', function(from) {
        _super.stop = false;
        _super.paused = false;
        _super.$.trigger('upload:resume');
        _super.upload(from);
      });
    },
    on: function() {
      this.$.on.apply(this.$, arguments);
    },
    destroy: function() {
      socket.off('disconnect', this._disconnect);
      this.$.off();
      this.$ = null;
      this.originalFile = this.file = null;
      this.range = null;
      this.data = null;
      this.result = null;
    }
  };

  root.FileManager = FileManager;
  root.socket = socket;
})(this, $);