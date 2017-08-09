// Used to work with localStorage. LocalStorage API doesn't allow for setting/getting
// objects/arrays directly
(function(root) {
  function stringify(something) {
    if(typeof something === 'object')
      return JSON.stringify(something);

    return something;
  }

  function parse(something) {
    try {
      var object = JSON.parse(something);
      return object;
    } catch(e) {}
    var num = +something;
    if(!isNaN(num))
      return num;

    return something;
  }

  var LS = {
    set: function(a, b) {
      return localStorage.setItem(a, stringify(b));
    },
    get: function(a) {
      return parse(localStorage.getItem(a));
    },
    remove: function(a) {
      return localStorage.removeItem(a);
    },
    has: function(a) {
      return typeof localStorage.getItem(a) !== 'undefined';
    },
    push: function(a) {
      var items = [].slice.call(arguments, 1);
      var target = LS.get(a) || [];
      return LS.set(a, target.concat(items));
    },
    extend: function(a, b) {
      var item = LS.get(a) || {};
      _.extend(item, b);
      return LS.set(a, item);
    }
  }

  root.LS = LS;
})(this);