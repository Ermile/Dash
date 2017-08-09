// Universal Tools used by other files

(function(root) {
  function extend(protoProps, staticProps) {
    var parent = this;
    var child;

    if (protoProps && _.has(protoProps, 'constructor')) {
      child = protoProps.constructor;
    } else {
      child = function(){ return parent.apply(this, arguments); };
    }

    _.extend(child, parent, staticProps);

    var Surrogate = function(){ this.constructor = child; };
    Surrogate.prototype = parent.prototype;
    child.prototype = new Surrogate();

    if (protoProps) _.extend(child.prototype, protoProps);

    child.__super__ = parent.prototype;

    return child;
  }

  $.fn.getData = function() {
    var d = {};
    $(this).find('input').each(function() {
      d[this.name] = this.value;
    });

    return d;
  };

  $.fn.putData = function(d) {
    $(this).find('input').each(function() {
      var k = d[this.name];
      if(k) this.value = k;
    });
  };

  String.prototype.toEnglish = function() {
    var result = this.split('').map(function(a) {
      var code = a.charCodeAt(0);
      if(code > 1785 || code < 1776) return a;

      return String.fromCharCode(code-1728);
    }).join('');
    return result;
  };

  String.prototype.toFarsi = function() {
    var result = this.split('').map(function(a) {
      var code = a.charCodeAt(0);
      if(code > 57 || code < 48) return a;

      return String.fromCharCode(code+1728);
    }).join('');
    return result;
  };


  // Find the right method, call on correct element
  function launchFullscreen(element) {
    if(element.requestFullscreen) {element.requestFullscreen();
    } else if(element.mozRequestFullScreen) {element.mozRequestFullScreen();
    } else if(element.webkitRequestFullscreen) {element.webkitRequestFullscreen();
    } else if(element.msRequestFullscreen) {element.msRequestFullscreen();
    }
  }

  root.extend = extend;
  root.launchFullscreen = launchFullscreen;
})(this);
