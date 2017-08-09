// Ajaxify: Ersale Form va Link ha ba estefade az AJAX
(function($)
{
  'use strict';

  var defaults =
  {
    ajax:
    {
      type: undefined,
      url: undefined,
      processData: false,
      contentType: false,
      dataType: 'json',
      cache: false,
      abort: false,
      async: true,
      beforeSend: function (request)
      {
        request.setRequestHeader("accept", "application/json");
      }
    },
    noLoading: false,
    lockForm: true,
  };
  var requests = [];

  $.fn.ajaxify = function Ajaxify(options)
  {
    var $form = $(this);
    $.extend(true, this, defaults, options);
    $form.trigger('ajaxify:init', this);
    var _super = this;

    // save focus position if wanna disable inputs
    if(_super.lockForm)
    {
      $(":focus").attr('data-hasFocus', true);
    }


    function send($this)
    {
      $form.trigger('ajaxify:send:before', _super);

      var elementOptions =
      {
        type: _super.link ? $this.attr('data-method') || 'get' : $this.prop('method') || $this.attr('data-method'),
        url: (_super.link ? $this.prop('href') : $this.prop('action')  || $this.attr('data-action')) || location.href
      };

      var ajax = _.extend(_super.ajax, elementOptions);

      var ajaxOptions;
      if(!_super.link)
      {
        // if we are in form use it, else use empty param
        if($this.is('form'))
        {
          var fd = new FormData($this.get(0));
        }
        else
        {
          var fd = new FormData();
        }

        $this.find('[contenteditable]').each(function()
        {
          fd.append(this.getAttribute('name'), this.innerHTML);
        });
        for(var formName in ajax.data)
        {
          fd.append(formName, ajax.data[formName]);
        }
        ajaxOptions = _.extend(ajax,
        {
          data: fd
        });
        if(_super.lockForm)
        {
          $this.find('input, [contenteditable]').attr('disabled', '');
        }
      }
      else
      {
        try
        {
          var data = JSON.parse($this.attr('data-data'));
          ajaxOptions = _.extend(ajax,
          {
            data: data,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            processData: true
          });
        }
        catch(e)
        {
          ajaxOptions = ajax;
        }
        if(_super.lockForm)
        {
          $('[data-ajaxify]').attr('disabled', '');
        }
      }

      var refresh = ajaxOptions.refresh || $this.attr('data-refresh') !== undefined;

      if(!_super.noLoading)
      {
        $('body').addClass('loading-form');
        callFunc('loading_form', true);
      }


      $form.trigger('ajaxify:send:ajax:start', ajaxOptions);


      var myXhr = $.ajax(ajaxOptions)
      .done(function(data, status, xhr)
      {
        _super.results = data;

        $.fn.ajaxify.showResults(data, $this, _super);

        if(data.msg && data.msg.redirect)
        {
          var a = $('<a href="' + data.msg.redirect + '"></a>');
          if(a.isAbsoluteURL() || data.msg.direct)
          {
            location.replace(data.msg.redirect);
          }
          else
          {
            Navigate({
              url: data.msg.redirect
            });
          }
          return;
        }

        if(refresh)
        {
          Navigate({
            url: location.href,
            replace: true
          });
        }

        $form.trigger('ajaxify:success', data, status, xhr);
      })
      .fail(function(xhr, status, error)
      {
        $form.trigger('ajaxify:fail', xhr, status, error);
      })
      .always(function(a1, a2, a3)
      {
        $form.trigger('ajaxify:complete', a1, a2, a3);

        // if(_super.noLoading) return;
        if(_super.lockForm)
        {
          $('input, [contenteditable], [data-ajaxify]').removeAttr('disabled');
        }

        $('body').removeClass('loading-form');
        callFunc('loading_form', false);
      });

      // console.log(ajaxOptions.abort);
      if(ajaxOptions.abort)
      {
        requests.push(myXhr);
        for(var i = 0; i < requests.length-1; i++)
        {
          requests[i].abort();
        }
      }

    }

    send.call(_super, $form.first());
  };

  // Used to show notification
  $.fn.ajaxify.showResults = function(data, $form, _super)
  {
    $form.trigger('ajaxify:render:start', data, $form, _super);

    $form.find('input').removeClass('error warn');

    var $div = $('<div></div>');

    var hasError = false;

    for(var i in data.messages)
    {
      var grp = data.messages[i];

      var type;

      switch(i) {
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

      $form.find('input').removeClass('invalid');

      for(var j = 0, len = grp.length; j < len; j++)
      {
        var msg = grp[j];

        // generate message text
        var msgText = '<li class="notify-' + msg.group + ' ' + msg.redirect + '">';
        if(data.title)
        {
          msgText += "<b>" + data.title + "</b><br />";
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

          (_.isArray(msg.element) ? msg.element : [msg.element]).forEach(function(e)
          {
            var $el = $form.find('input[name="' + e + '"]');
            $el.addClass('invalid');
          });
        }
      }
      $div.append($ul);
    }

    $form.trigger('ajaxify:render:done', data, $form, _super);

    if (!hasError && $form.attr('data-clear') !== undefined)
    {
      $form.find('input, select, textarea, [contenteditable]').not('[data-unclear]').val('');
    }

    $form.trigger('ajaxify:render:clear', data, $form, _super);

    if(!hasError)
    {
      setTimeout(function()
      {
        if($form.find('input[data-get-focus]').get(0))
        {
          $form.find('input[data-get-focus]').get(0).select();
        }
        else if(_super.lockForm)
        {
          $('[data-hasFocus]').focus();
          $('[data-hasFocus]').attr('data-hasFocus', null);
        }
      }, 100);
    }

    $form.trigger('ajaxify:render:focus', data, $form, _super);

    notify(
    {
      html: $div,
      delay: parseInt($form.attr('data-delay'), 10),
      sticky: data.msg && data.msg.redirect
    });

    $form.trigger('ajaxify:notify', data, $form, _super);
  };
})(jQuery);

