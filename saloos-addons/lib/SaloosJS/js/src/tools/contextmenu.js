// Sakhte Menu haye click rast

(function($) {
  "use strict";

  var id = 1;

  var list = [];

  var ContextMenu = function ContextMenu(options) {
    _.extend(this, _.omit(defaults, 'item'), _.omit(options, 'item'));
    this.status = 0;
    this.init(options);
    list.push(this);
  };

  ContextMenu.prototype = {
    clone: function(element) {
      var ops = {items: this.items, jquery: $(element)};
      return new ContextMenu(ops);
    },
    init: function(options) {
      var _super = this;
      var $wrapper = this.$wrapper = this.createWrapper();
      var $elements = this.$elements = this.jquery;

      this.$wrapper.trigger('contextmenu:init');

      options = options || {};

      this.items = this.items.map(function(item) {
        return _.defaults(item, defaults.item, options.item);
      });

      $elements.contextmenu(function(e) {
        e.preventDefault();
        e.stopPropagation();

        list.forEach(function(cm) {
          cm.close();
        });
        _super.open(e);
      });

      $wrapper.on('close', this.close.bind(this));

      $(window).on('keydown', function(e) {
        if (!_super.status) return;
        var $li = $wrapper.find('li');
        var active = $li.filter('.active').index();

        if (e.which === 38) {
          $li.removeClass('active')
             .eq(active-1)
             .addClass('active');

          $wrapper.trigger('contextmenu:selection:change', active-1)
        } else if (e.which === 40) {
          if (active === $li.length-1) active = -1;
          $li.removeClass('active')
             .eq(active+1)
             .addClass('active');

          $wrapper.trigger('contextmenu:selection:change', active+1)
        } else if (e.which === 13) {
          var $target =
                $li.removeClass('active')
                .eq(active)
                .addClass('active')
                .get(0);

          $target.children[0].click();
          $wrapper.trigger('contextmenu:selection:trigger', active)
                  .trigger('contextmenu:selection:trigger:'+active);
        } else {
          _super.items.forEach(function(item, i) {
            if (item.hotkey && e.which === item.hotkey.toUpperCase().charCodeAt(0)) {
              var $target =
                    $li.removeClass('active')
                    .eq(i)
                    .addClass('active')
                    .get(0);

              $target.children[0].click();
              $wrapper.trigger('contextmenu:selection:trigger', i)
                      .trigger('contextmenu:selection:trigger:'+i);
            }
          });
        }
      });

      $(document).ready(function() {
        $(document.body).append($wrapper);
        _super.createItems();

        $wrapper.trigger('contextmenu:appended');
      });
    },
    add: function(item) {
      var index = this.items.push(item);
      this.createItems();

      this.$wrapper.trigger('contextmenu:update');
      return index-1;
    },
    remove: function(item) {
      if(_.isNumber(item)) {
        this.items.splice(item, 1);
      } else {
        this.items = _.without(this.items, _.omit(item, 'events'));
      }
      this.createItems();

      this.$wrapper.trigger('contextmenu:update');
      return this.items.length;
    },
    setItems: function(items) {
      this.items = items;
      this.createItems();

      this.$wrapper.trigger('contextmenu:update');
      return items.length;
    },
    open: function(e) {
      var $wrapper = this.$wrapper;
      $wrapper
        .css({
          position: 'absolute',
          left: e.pageX + 5,
          top: e.pageY + 5,
          display: 'block',
          visibility: 'hidden'
        });

      var offset = $wrapper.offset(),
          width = $wrapper.width(),
          height = $wrapper.height();
      if(offset.left + width > $(window).width()) {
        $wrapper.css({
          left: e.pageX - width - 5
        });
      }
      if(offset.top + height > $(window).height()) {
        $wrapper.css({
          top: e.pageY - height - 5
        });
      }
      this.status = 1;
      $wrapper.css('visibility', 'visible').show();
      $wrapper.trigger('contextmenu:open');
    },
    close: function() {
      this.status = 0;
      this.$wrapper.find('li.active').removeClass('active');
      this.$wrapper.css('visibility', 'hidden').hide();
      this.$wrapper.trigger('contextmenu:close');
    },
    createItems: function() {
      var $stack = $('<ul></ul>');
      for(var i = 0, len = this.items.length; i < len; i++) {
        var item = _.extend({}, defaults.item, this.items[i]);
        var $el = $('<li data-id="' + i + '"><a ' + (item.link ? 'href="' + item.link + '"' : '') + '>' + item.text + '</a></li>');
        if (item.hotkey) {
          var $a = $el.find('a');
          var html = $a.html();
          var regex = new RegExp(item.hotkey, 'i');
          var underlined = '<span style="text-decoration: underline">$&</span>';
          $a.html(html.replace(regex, underlined));
        }
        $stack = $stack.append($el);
      }

      this.$wrapper.empty().append($stack);
      return $stack;
    },
    createWrapper: function() {
      var $wrapper = $('<div class="ctx-menu modal" id="ctx-' + (id++) + '" data-modal></div>');
      $wrapper.css({
        display: 'none',
        position: 'absolute'
      });

      $wrapper.find('ul').css({
        'list-style': 'none',
        margin: '0'
      });

      return $wrapper;
    },
    on: function() {
      this.$wrapper.on.apply(this.$wrapper, arguments);
    }
  };

  ContextMenu.extend = extend;

  $.fn.ctxMenu = function(options) {
    return new ContextMenu(_.extend({}, options, {jquery: this}));
  };

  var defaults = $.fn.ctxMenu.defaults = {
    item: {
      link: false
    }
  };
})(jQuery);