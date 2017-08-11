// Morede estefade baraye kar ba HTML5 History

(function(root)
{
  "use strict";

  var $window  = $(window);
  var defaults =
  {
    html: '',
    title: null,
    url: '/',
    replace: false,
    filter: null,
    fake: false,
    data: false,
    nostate: false,
    abort: true,
    ajax:
    {
      type: 'get'
    }
  };

  // add ajax poll to allow to cancel ajax requests before sending new one
  $.xhrPool = [];
  $.xhrPool.abortAll = function()
  {
     $(this).each(function(idx, jqXHR)
     {
        jqXHR.abort();
     });
     $.xhrPool.length = 0
  };

  $.ajaxSetup(
  {
    beforeSend: function(jqXHR)
    {
        $.xhrPool.push(jqXHR);
    },
    complete: function(jqXHR)
    {
       var index = $.xhrPool.indexOf(jqXHR);
       if (index > -1)
       {
          $.xhrPool.splice(index, 1);
       }
    }
 });


  function exec(src)
  {
    var r = src.slice(src.lastIndexOf('/')+1);
    $(document).sroute(r);
  }

  function render(obj)
  {
    var focusBeforeChange  = $(':focus');
    var pageContentChanged = null;
    $window.trigger('navigate:render:start', obj);

    var html = obj.html,
        $html = $(html);

    if(obj.id) $('body').attr('id', obj.id);
    if(obj.id) $('body').attr('data-page', obj.id);

    $window.trigger('navigate:render:filter:before', obj.filter);

    var filter = _.isArray(obj.filter) ?
        '[data-xhr="' + obj.filter.join('"], [data-xhr="') + '"]'
      : obj.filter ? '[data-xhr="' + obj.filter + '"]' : null;

    (filter ? $html.filter(filter).add($html.find(filter)) : $html).each(function()
    {
      var target = $(this).attr('data-xhr');

      var $target = $('[data-xhr="'+target+'"]');

      var newPage = this;

      // if new content is different from current content replace it
      // its added by javad, if has bug report this condition
      if($(this).html() !== $target.html())
      {
        pageContentChanged = true;
        // add content after old one - simple replace
        $target.after(this);
        // remove old one
        $target.remove();

        // ----------------------------------- all below ways is not good.
        // // replace with fade effect
        // $target.fadeOut("slow", function()
        // {
        //   $(this).replaceWith(newPage);
        //   $target.fadeIn("slow").show();
        // });

        // // replace with slide effect
        // $target.slideUp("slow", function()
        // {
        //   $(this).replaceWith(newPage);
        //   $target.slideDown("slow");
        // });

        // // replace with fade effect
        // $target.fadeOut(100, function()
        // {
        //   $(this).replaceWith(newPage).fadeIn(500);
        // });

        // $target.fadeTo("normal", 0.4, function()
        // {
        //   $(this).replaceWith(newPage);
        //   $target.fadeIn("slow").show();
        // });

      }
    });

    $window.trigger('navigate:render:filter:done', filter);

    var $title = $html.find('title');

    if($title.length)
    {
      $('head title').text($title.text());
    }

    if(obj.js)
    {
      var scripts = obj.js;
      $window.trigger('navigate:render:scripts:before', obj.js);

      scripts.forEach(function(src)
      {
        var $script = $('script[src="' + src + '"]');

        if(!$script.length)
        {
          $script = $('<script></script>');
          $script.prop('async', true);
          $script.prop('src', src);
          $window.trigger('navigate:render:script:created', $script);

          $(document.body).append($script);

          $window.trigger('navigate:render:script:appended', $script);
        }
      });
      $window.trigger('navigate:render:scripts:done');
    }

    $html.sroute(null, true);

    if(obj.title) document.title = obj.title;
    $window.trigger('navigate:render:done');


    // on navigate if in new page we have autofocus field, set focus to it
    if(!pageContentChanged)
    {
      // if page content is not changed, do nothing...
      // console.log(10);
    }
    // if we have input with autofocus, set focus to first of it
    else if($('input[autofocus]').length)
    {
      // console.log(20);
      if(focusBeforeChange.is($('input[autofocus]')[0]))
      {
        // if this and old input is equal skip
        // check later
        // console.log(21);
      }
      else
      {
        $('input[autofocus]')[0].focus();
        // console.log(24);
      }
    }
    else
    {
      // we dont have autofocus input, skip it
      // console.log(30);
    }
  }


  function fetch(props, md5)
  {
    $window.trigger('navigate:fetch:start', props, md5);
    // add loading
    $(document.body).addClass('loading-page');
    callFunc('loading_page', true);

    var options = $.extend(true, {}, props.ajax,
    {
      url: props.url,
      // headers: { 'Cached-MD5': props.md5 }
    });

    var deferred = new jQuery.Deferred();

    if(props.abort)
    {
      $.xhrPool.abortAll();
    }

    var myXhr = $.ajax(options)
    .done(function(res)
    {
      $window.trigger('navigate:fetch:ajax:start', options);

      var json,
          html;

      var jsonExpected = res[0] === '{';
      try
      {
        var n = res.indexOf('\n');
        n = n === -1 ? undefined : n;
        json = JSON.parse(res.slice(0, n));
        var debug = json.debug;

        var $div = $('<div></div>');

        var hasError = false;

        for(var i in debug.messages)
        {
          var grp = debug.messages[i];

          var type;

          switch(i)
          {
            case 'true':
              type = 'success';
              break;

            case 'warn':
              type = 'warning';
              break;

            case 'error':
              type = 'error';
              hasError = true;
              break;

            default:
              type = 'info';
              break;
          }

          // Generate Notification HTML
          var $ul = $('<ul class="' + i + ' unselectable"></ul>');


          for(var j = 0, len = grp.length; j < len; j++)
          {
            var msg = grp[j];
            // generate message text
            var msgText = '<li class="notify-' + msg.group + ' ' + msg.redirect + '">';
            if(debug.title)
            {
              msgText += "<b>" + debug.title + "</b><br />";
            }
            msgText += msg.title + '</li>';

            var $msg = $ul.append(msgText);

            if(msg.element)
            {
              try
              {
                var parsed = JSON.parse(msg.element);
                msg.element = parsed;
              } catch(e) {}
            }
          }
          $div.append($ul);
        }
        notify(
        {
          html: $div,
          delay: 7000,
          sticky: debug.msg && debug.msg.redirect
        });



        // if(json.getFromCache) {
          // json = LS.get(props.md5);
        // } else {
          html = res.slice(n);
          // if(json.md5) {
            // LS.set(props.url, json.md5);
            // LS.set(json.md5, _.extend(json, {html: html}));
            _.extend(json, {html: html});
          // }
        // }

        if(json.options)
        {
          var $options = $('#options-meta');
          $options.putData(json.options);
        }
      }
      catch(e) {
        if (jsonExpected)
        {
          notify(
          {
            html: '<ul class="error unselectable">'
                 +'<li class="notify-json">There was an error in parsing JSON</li>'
                 +'</ul>'
          });
        }
        deferred.reject();
        return location.replace(props.url);
      }

      $window.trigger('navigate:fetch:ajax:done', json)
             .trigger('navigate:fetch:done', json);
      deferred.resolve(json);
      // remove loading
      $(document.body).removeClass('loading-page');
      callFunc('loading_page', false);


    }).error(function(a, b, c)
    {
      $window.trigger('navigate:fetch:ajax:error', a, b, c);
    });

    return deferred.promise();
  }

  function Navigate(obj)
  {
    // console.log(obj);
    var deferred = new jQuery.Deferred();
    var props    = $.extend(true, {}, defaults, obj);

    $window.trigger('navigate:start', props);

    if(obj.fake)
    {
      deferred.resolve();
      if(!obj.nostate)
      {
        root.history[props.replace ? 'replaceState' : 'pushState'](props, props.title, props.url);
      }
      $window.trigger('statechange');

      return deferred.promise();
    }

    if(obj.html)
    {
      render(props);
      deferred.resolve();
      if(!obj.nostate)
      {
        root.history[props.replace ? 'replaceState' : 'pushState'](props, props.title, props.url);
      }
      $window.trigger('statechange');

      return deferred.promise();
    }

    var md5 = LS.get(props.url);
    props.md5 = md5;
    fetch(props).then(function(data)
    {
      _.extend(props, data);

      if(!obj.nostate)
      {
        root.history[props.replace ? 'replaceState' : 'pushState'](props, props.title, props.url);
      }
      if(!props.data)
      {
        render(_.extend({}, props, {html: data.html}));
      }

      $window.trigger('statechange');
      // remove loading
      $('body').removeClass('loading-page');
      callFunc('loading_page', false);

      deferred.resolve(props);
    });

    return deferred.promise();
  }

  window.onpopstate = function(e)
  {
    var state = e.state;

    if(!state) return true;
    e.preventDefault();

    if(!state.html)
    {
      fetch(state).then(function(data)
      {
        var props = _.extend(true, {}, state, data.json);

        render(_.extend({}, props, {html: data.html}));

        $window.trigger('statechange');
      });
    }
    else
    {
      render(state);
      $window.trigger('statechange');
    }

    return false;
  };

  if(!history.state)
  {
    Navigate(
    {
      url: location.href,
      fake: true,
      replace: true
    });
  }

  root.Navigate = Navigate;
})(this);