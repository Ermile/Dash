(function(root) {
  "use strict";


  var defaults = {
    ajax: {
      type: 'post',
      contentType: false,
      processData: false,
      dataType: 'json',
      // mimeType: 'text/plain',
      url: 'http://localhost:8000'
    },
    socket: true,
    type: 'BinaryString',
    originalFile: null,
    file: null,
    result: null,
    range: [0, 0],
    parts: 200,
    rest: 20,
    restDuration: 500,

    // Events

    beforeStart: _.noop,
    afterCheck: _.noop,
    progress: _.noop,
    done: _.noop
  };

  var FileManager = function(options) {
    var ajax = _.extend({}, defaults.ajax, options.ajax);
    _.extend(this, defaults, options);
    this.ajax = ajax;

    this.originalFile = this.file;
    this.size = this.originalFile.size;
    this.fileType = this.originalFile.type;
    this.range = [0, this.size];
    this.sessionID = 0;
    this.fileID = 0;
    this.client = new BinaryClient(this.ajax.url.replace('http://', 'ws://'), {
      chunkSize: options.parts*1000
    });

    var _super = this;

    this.client.on('open', function() {
      _super.clientOpen = true;
    });
  };

  FileManager.prototype = {
    _load: function() {
      var deferred = new jQuery.Deferred();

      this.fileReader = new FileReader();
      var self = this;
      this.fileReader.onload = function(e) {
        self.result = e.target.result;
        deferred.resolve(self);
      };
      this.fileReader.onerror = this.fileReader.onabort = function(e) {
        deferred.reject(e);
      };
      this.fileReader['readAs' + this.type](this.file);

      return deferred.promise();
    },
    load: function() {
      return this._load.apply(this, arguments);
    },
    _slice: function(start, end) {
      this.file = this.originalFile.slice(start, end);
      this.range = [start, end];
      return this;
    },
    slice: function() {
      return this._slice.apply(this, arguments);
    },
    _full: function() {
      this.file = this.originalFile;
      this.range = [0, this.size];
      return this;
    },
    full: function() {
      return this._full.apply(this, arguments);
    },
    _check: function(from, to, options) {
      var lastbits = this.originalFile.slice(from, to);

      var fd = new FormData();
      fd.append('file', lastbits, this.originalFile.name);
      fd.append('range', from +'-'+to);

      var opts = _.extend({}, this.ajax, options || {}, {
        data: fd,
      });

      return $.ajax(this.ajax.url + '/check', opts);
    },
    check: function() {
      return this._check.apply(this, arguments);
    },
    _send: function(options) {
      var fd = new FormData();
      fd.append('file', this.file, this.fileID || this.originalFile.name);
      if(!this.fileID) fd.append('size', this.size);
      fd.append('session', this.sessionID);

      var opts = _.extend({}, this.ajax, options || {}, {
        data: fd,
      });

      var _self = this;

      return $.ajax(this.ajax.url + '/upload', opts).done(function(response) {
        if(!_self.sessionID && (!response || !response.session)) {
          _self._abort = true;
        }
        if(!_self.sessionID && response) {
          _self.sessionID = response.session;
          _self.fileID = response.file;
        }

        if(response && response.status === 0) _self._abort = true;
      });
    },
    send: function() {
      return this._send.apply(this, arguments);
    },
    _upload: function(from, to, options) {
      this.beforeStart(this);

      var deferred = new jQuery.Deferred();

      var opts = _.extend({}, this, options);

      from = _.isNumber(from) ? from : 0;
      to = _.isNumber(to) ? to : this.size;
      var parts = (parts || opts.parts)*1000;

      var i = from,
          max = false,
          _super = this,
          rest = 0,
          percentage = i * 100 / to;

      if(this.socket) {
        if(!this.clientOpened) {
          this.client.on('open', function() {
            _super.upload(from, to, options);
          });
        }

        this.slice(from, to).load().then(function() {
          var stream = _super.client.send(_super.file, {
            file: _super.originalFile.name,
            size: _super.size
          });
        });
        return true;
      }

      function loop() {
        if(_super._abort) {
          _super._abort = false;
          deferred.reject(_super);
          return;
        }
        if(max) {
          _super.pause(true);
          deferred.resolve(_super);
          _super.done(_super);
          return;
        }
        if(i + parts > to) {
          max = true;
        }
        _super.slice(i, max ? to : i+parts).load().then(function() {

          function done(d, status) {
            i = max ? to : i + parts;
            percentage = i * 100 / to;
            _super.progress({
              min: from,
              max: to,
              sent: i,
              percentage: percentage,
              _super: _super});

            rest++;
            if(rest >= _super.rest) {
              setTimeout(loop, _super.restDuration);
              rest = 0;
            } else {
              loop();
            }
          }
          function err(xhr, status, error) {
            deferred.reject.apply(deferred, arguments);
            _super._abort = true;
          }

          _super.send(opts.ajax).then(done, err);
        }, function(err) {
          deferred.reject(err);
          _super._abort = true;
        });
      }


      if(i > 0) {
        var f = from - 100 < 0 ? 0 : from - 100;
        this.check(f, from).then(function(res, status) {
          _super.afterCheck(_super);
          loop();
        }, deferred.reject);
      } else {
        loop();
      }

      return deferred.promise();
    },
    upload: function() {
      return this._upload.apply(this, arguments);
    },
    _pause: function(finished) {
      this._abort = true;
      $.ajax(this.ajax.url + '/killSession', {
        data: {session: this.sessionID, finished: finished || false}
      });
      return this;
    },
    pause: function() {
      return this._pause.apply(this, arguments);
    },
    _resume: function(options) {
      var _super = this;

      var opts = _.extend({
        type: 'post',
        data: {file: this.fileID},
        dataType: 'text',
        mimeType: 'text/plain'},
        options || {});

      return $.ajax(this.ajax.url + '/resume', opts)
              .then(function(response) {
                _super.upload(+response);
              });
    },
    resume: function() {
      return this._resume.apply(this, arguments); 
    }
  };

  root.FileManager = FileManager;
})(this);