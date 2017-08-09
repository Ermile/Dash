jQuery.fn.tagName = function() {
  return (this.prop('nodeName') || '').toLowerCase();
}

jQuery.fn.isAbsoluteURL = function() {
  var base = location.protocol + '//' + location.host;
  var prop = this.prop('href');
  var attr = this.attr('href');

  return prop.indexOf(base) !== 0 && (prop === attr || prop === attr + '/')
}

jQuery.fn.copyData = function(el, dont) {
  var $target = $(el),
      $this = $(this);

  $.each($target.data(), function(key, val) {
    if(dont && dont.indexOf(key) > -1) return;

    $this.attr('data-' + key, typeof val === 'object' ? JSON.stringify(val) : val);
  });

  return this;
}

jQuery.fn.hasAttr = function(attr) {
  return this.attr(attr) !== undefined;
}

jQuery.fn.fadeOutAndRemove = function(speed) {
  $(this).stop().fadeOut({
    duration: speed,
    complete: function() {
      $(this).remove();
    }
  });
};

jQuery.fn.moveAndRemove = function(speed) {
  var duration = speed || 1000;
  $(this).stop().css('position', 'relative')
  .animate({
    'transformtranslateX': '-500px',
    opacity: -0.2
  }, {
    duration: duration,
    step: function(now, fx) {
      if(fx.prop.indexOf('transform') > -1) {
        var fn = fx.prop.slice(9);
        fx.elem.style.transform = fn + '(' + Math.round(now) + 'px)';
      }
    },
    complete: function() {
      $(this).remove();
    }
  });
}