(function ($) {
    $.fn.autoComplete = function (options) {
        var o = $.extend({}, $.fn.autoComplete.defaults, options);
        if (typeof options == 'string') {
            this.each(function () {
                var that = $(this);
                if (options == 'destroy') {
                    $(window).off('resize.autocomplete', that.updateSC);
                    that.off('blur.autocomplete focus.autocomplete keydown.autocomplete keyup.autocomplete');
                    if (that.data('autocomplete'))
                        that.attr('autocomplete', that.data('autocomplete'));
                    else
                        that.removeAttr('autocomplete');
                    $(that.data('sc')).remove();
                    that.removeData('sc').removeData('autocomplete');
                }
            });
            return this;
        }
        return this.each(function () {
            var that = $(this);
            that.sc = $('<div class="autocomplete-suggestions ' + o.menuClass + '"></div>');
            that.data('sc', that.sc).data('autocomplete', that.attr('autocomplete'));
            that.attr('autocomplete', 'off');
            that.cache = {};
            that.last_val = '';
            that.updateSC = function (resize, next) {
                that.sc.css({ width: that.outerWidth() });
                if (!resize) {
                    that.sc.show();
                    if (!that.sc.maxHeight)
                        that.sc.maxHeight = parseInt(that.sc.css('max-height'));
                    if (!that.sc.suggestionHeight)
                        that.sc.suggestionHeight = $('.autocomplete-suggestion', that.sc).first().outerHeight();
                    if (that.sc.suggestionHeight)
                        if (!next)
                            that.sc.scrollTop(0);
                        else {
                            var scrTop = that.sc.scrollTop(), selTop = next.offset().top - that.sc.offset().top;
                            if (selTop + that.sc.suggestionHeight - that.sc.maxHeight > 0)
                                that.sc.scrollTop(selTop + that.sc.suggestionHeight + scrTop - that.sc.maxHeight);
                            else if (selTop < 0)
                                that.sc.scrollTop(selTop + scrTop);
                        }
                }
            };
            $(window).on('resize.autocomplete', that.updateSC);
            that.sc.insertAfter(that);
            that.sc.on('mouseleave', '.autocomplete-suggestion', function () {
                $('.autocomplete-suggestion.selected').removeClass('selected');
            });
            that.sc.on('mouseenter', '.autocomplete-suggestion', function () {
                $('.autocomplete-suggestion.selected').removeClass('selected');
                $(this).addClass('selected');
            });
            that.sc.on('mousedown', '.autocomplete-suggestion', function (e) {
                var item = $(this), v = item.data('val');
                if (v || item.hasClass('autocomplete-suggestion')) {
                    that.val(v);
                    o.onSelect(e, v, item, that);
                    that.sc.hide();
                }
            });
            that.on('blur.autocomplete', function () {
                try {
                    over_sb = $('.autocomplete-suggestions:hover').length;
                } catch (e) {
                    over_sb = 0;
                }
                if (!over_sb) {
                    that.last_val = that.val();
                    that.sc.hide();
                    setTimeout(function () {
                        that.sc.hide();
                    }, 350);
                } else if (!that.is(':focus'))
                    setTimeout(function () {
                        that.focus();
                    }, 20);
            });
            if (!o.minChars)
                that.on('focus.autocomplete', function () {
                    that.last_val = '\n';
                    that.trigger('keyup.autocomplete');
                });
            function suggest(data) {
                var val = that.val();
                that.cache[val] = data;
                if (data.length && val.length >= o.minChars) {
                    var s = '';
                    for (var i = 0; i < data.length; i++)
                        s += o.renderItem(data[i], val);
                    that.sc.html(s);
                    that.updateSC(0);
                } else
                    that.sc.hide();
            }
            that.on('keydown.autocomplete', function (e) {
                if ((e.which == 40 || e.which == 38) && that.sc.html()) {
                    var next, sel = $('.autocomplete-suggestion.selected', that.sc);
                    if (!sel.length) {
                        next = e.which == 40 ? $('.autocomplete-suggestion', that.sc).first() : $('.autocomplete-suggestion', that.sc).last();
                        that.val(next.addClass('selected').data('val'));
                    } else {
                        next = e.which == 40 ? sel.next('.autocomplete-suggestion') : sel.prev('.autocomplete-suggestion');
                        if (next.length) {
                            sel.removeClass('selected');
                            that.val(next.addClass('selected').data('val'));
                        } else {
                            sel.removeClass('selected');
                            that.val(that.last_val);
                            next = 0;
                        }
                    }
                    that.updateSC(0, next);
                    return false;
                } else if (e.which == 27)
                    that.val(that.last_val).sc.hide();
                else if (e.which == 13 || e.which == 9) {
                    sel = $('.autocomplete-suggestion.selected', that.sc);
                    if (sel.length && that.sc.is(':visible')) {
                        o.onSelect(e, sel.data('val'), sel);
                        setTimeout(function () {
                            that.sc.hide();
                        }, 20);
                    }
                }
            });
            that.on('keyup.autocomplete', function (e) {
                if (!~$.inArray(e.which, [13, 27, 35, 36, 37, 38, 39, 40])) {
                    var val = that.val();
                    if (val.length >= o.minChars) {
                        if (val != that.last_val) {
                            that.last_val = val;
                            clearTimeout(that.timer);
                            if (o.cache) {
                                if (val in that.cache) {
                                    suggest(that.cache[val]);
                                    return;
                                }
                                for (var i = 1; i < val.length - o.minChars; i++) {
                                    var part = val.slice(0, val.length - i);
                                    if (part in that.cache && !that.cache[part].length) {
                                        suggest([]);
                                        return;
                                    }
                                }
                            }
                            that.timer = setTimeout(function () {
                                o.source(val, suggest, that);
                            }, o.delay);
                        }
                    } else {
                        that.last_val = val;
                        that.sc.hide();
                    }
                }
            });
        });
    };
    $.fn.autoComplete.defaults = {
        source: 0,
        minChars: 3,
        delay: 150,
        cache: 1,
        menuClass: '',
        renderItem: function (item, search) {
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
            return '<div class="autocomplete-suggestion" data-val="' + item + '">' + item.replace(re, '<b>$1</b>') + '</div>';
        },
        onSelect: function (e, term, item) {
        },
        onClick: function () {
            return true;
        }
    };
}(jQuery));
(function (root) {
    function stringify(something) {
        return typeof something === 'object' ? JSON.stringify(something) : something;
    }
    function parse(something) {
        try {
            return JSON.parse(something);
        } catch (e) {
        }
        var num = +something;
        return isNaN(num) ? something : num;
    }
    var LS = root.LS = {
        set: function (a, b) {
            return localStorage.setItem(a, stringify(b));
        },
        get: function (a) {
            return parse(localStorage.getItem(a));
        },
        remove: function (a) {
            return localStorage.removeItem(a);
        },
        has: function (a) {
            return typeof localStorage.getItem(a) !== 'undefined';
        },
        push: function (a) {
            var items = [].slice.call(arguments, 1);
            var target = LS.get(a) || [];
            return LS.set(a, target.concat(items));
        },
        extend: function (a, b) {
            var item = LS.get(a) || {};
            _.extend(item, b);
            return LS.set(a, item);
        }
    };
}(this));
$(function () {
    $(document.body).click(function (e) {
        var $target = $(e.target);
        if ($target.is('[data-modal]') || $target.parents('[data-modal]').length || $target.is('.modal-dialog') || $target.parents('.modal-dialog').length || $('.modal.visible').hasAttr('data-always'))
            return;
        $('.modal').trigger('close');
    });
    $(window).keydown(function (e) {
        if (e.which === 27)
            $('.modal').trigger('close');
    });
    $(document).on('click', '[data-modal]', function (e) {
        var $this = $(this);
        if ($this.hasClass('modal') || $this.parents('.modal').length > 0)
            return;
        e.preventDefault();
        var $modal = $('#' + $this.attr('data-modal'));
        $modal.copyData($this, ['modal']);
        $modal.trigger('open', $this);
    });
    $(document).on('click', '[data-cancel]', function (e) {
        $('.modal').trigger('close').trigger('cancel');
    });
    $(document).on('click', '[data-ok]', function (e) {
        $('.modal').trigger('close').trigger('ok');
    });
});
var global = this;
function extend(protoProps, staticProps) {
    var parent = this, child;
    if (protoProps && _.has(protoProps, 'constructor'))
        child = protoProps.constructor;
    else
        child = function () {
            return parent.apply(this, arguments);
        };
    _.extend(child, parent, staticProps);
    var Surrogate = function () {
        this.constructor = child;
    };
    Surrogate.prototype = parent.prototype;
    child.prototype = new Surrogate();
    if (protoProps)
        _.extend(child.prototype, protoProps);
    child.__super__ = parent.prototype;
    return child;
}
$.fn.getData = function () {
    var d = {};
    $(this).find('input').each(function () {
        d[this.name] = this.value;
    });
    return d;
};
$.fn.putData = function (d) {
    $(this).find('input').each(function () {
        var k = d[this.name];
        if (k)
            this.value = k;
    });
};
String.prototype.toEnglish = function () {
    return this.split('').map(function (a) {
        var code = a.charCodeAt(0);
        return code > 1785 || code < 1776 ? a : String.fromCharCode(code - 1728);
    }).join('');
};
String.prototype.toFarsi = function () {
    return this.split('').map(function (a) {
        var code = a.charCodeAt(0);
        if (code > 57 || code < 48)
            return a;
        return String.fromCharCode(code + 1728);
    }).join('');
};
function launchFullscreen(element) {
    if (element.requestFullscreen)
        element.requestFullscreen();
    else if (element.mozRequestFullScreen)
        element.mozRequestFullScreen();
    else if (element.webkitRequestFullscreen)
        element.webkitRequestFullscreen();
    else if (element.msRequestFullscreen)
        element.msRequestFullscreen();
}
global.extend = extend;
global.launchFullscreen = launchFullscreen;
(function () {
    var _routes = {};
    _routes.length = 0;
    function _route() {
        var fn, index;
        var args = arguments;
        index = _routes.length++;
        if (typeof args[args.length - 1] != 'function')
            return;
        fn = args[args.length - 1];
        for (var i = 0; i < args.length - 1; i++) {
            if (!_routes[index]) {
                _routes[index] = {};
                _routes[index].routes = [];
                _routes[index].fn = fn;
            }
            _routes[index].routes.push(args[i]);
            if (args[i] === '*' || location.pathname.match(args[i])) {
                $(document).ready(fn.call(document));
            }
        }
    }
    function main(base, args) {
        url = args[0];
        for (var i = 0; i < _routes.length; i++) {
            var oRoute = _routes[i];
            for (var iRoute in oRoute.routes) {
                var condition = oRoute.routes[iRoute];
                if (condition == '*') {
                    _call(base, oRoute.fn, args);
                } else if (typeof condition == 'string' && condition === url) {
                    _call(base, oRoute.fn, args);
                } else if (typeof condition == 'object' && condition.test(url)) {
                    _call(base, oRoute.fn, args);
                }
            }
        }
    }
    function _call(base, fn, args) {
        fn.apply(base, args);
    }
    route = _route;
    route_main = main;
}());
(function ($) {
    $.fn.sroute = function (url) {
        var args = arguments;
        url = url ? url : location.pathname;
        url = url.replace(/^\//, '').replace(/\/$/, '');
        args[0] = url;
        $(this).each(function () {
            route_main(this, args);
        });
    };
}(jQuery));
(function (root) {
    'use strict';
    var $window = $(window);
    var defaults = {
        html: '',
        title: null,
        url: '/',
        replace: false,
        filter: null,
        fake: false,
        data: false,
        nostate: false,
        ajax: { type: 'get' }
    };
    function exec(src) {
        var r = src.slice(src.lastIndexOf('/') + 1);
        $(document).sroute(r);
    }
    function render(obj) {
        $window.trigger('navigate:render:start', obj);
        var html = obj.html, $html = $(html);
        if (obj.id)
            $('body').attr('id', obj.id);
        $window.trigger('navigate:render:filter:before', obj.filter);
        var filter = _.isArray(obj.filter) ? '[data-xhr="' + obj.filter.join('"], [data-xhr="') + '"]' : obj.filter ? '[data-xhr="' + obj.filter + '"]' : null;
        (filter ? $html.filter(filter).add($html.find(filter)) : $html).each(function () {
            var target = $(this).attr('data-xhr');
            var $target = $('[data-xhr="' + target + '"]');
            $target.after(this);
            $target.remove();
        });
        $window.trigger('navigate:render:filter:done', filter);
        var $title = $html.find('title');
        if ($title.length) {
            $('head title').text($title.text());
        }
        if (obj.js) {
            var scripts = obj.js;
            $window.trigger('navigate:render:scripts:before', obj.js);
            scripts.forEach(function (src) {
                var $script = $('script[src="' + src + '"]');
                if (!$script.length) {
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
        $html.eq(0).sroute();
        if (obj.title)
            document.title = obj.title;
        $window.trigger('navigate:render:done');
    }
    function fetch(props, md5) {
        $window.trigger('navigate:fetch:start', props, md5);
        $(document.body).addClass('loading-page');
        var options = $.extend(true, {}, props.ajax, { url: props.url });
        var deferred = new jQuery.Deferred();
        $.ajax(options).done(function (res) {
            $window.trigger('navigate:fetch:ajax:start', options);
            var json, html;
            var jsonExpected = res[0] === '{';
            try {
                var n = res.indexOf('\n');
                n = n === -1 ? undefined : n;
                json = JSON.parse(res.slice(0, n));
                var debug = json.debug;
                var $div = $('<div></div>');
                var hasError = false;
                for (var i in debug.messages) {
                    var grp = debug.messages[i];
                    var type;
                    switch (i) {
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
                    }
                    var $ul = $('<ul class="' + i + ' unselectable"></ul>');
                    for (var j = 0, len = grp.length; j < len; j++) {
                        var msg = grp[j];
                        var $msg = $ul.append('<li class="notify-' + msg.group + ' ' + msg.redirect + '">' + (debug.title || '') + msg.title + '</li>');
                        if (msg.element) {
                            try {
                                var parsed = JSON.parse(msg.element);
                                msg.element = parsed;
                            } catch (e) {
                            }
                        }
                    }
                    $div.append($ul);
                }
                notify({
                    html: $div,
                    delay: 7000,
                    sticky: debug.msg && debug.msg.redirect
                });
                html = res.slice(n);
                _.extend(json, { html: html });
                if (json.options) {
                    var $options = $('#options-meta');
                    $options.putData(json.options);
                }
            } catch (e) {
                if (jsonExpected) {
                    notify({ html: '<ul class="error unselectable">' + '<li class="notify-json">There was an error in parsing JSON</li>' + '</ul>' });
                }
                deferred.reject();
                return location.replace(props.url);
            }
            $window.trigger('navigate:fetch:ajax:done', json).trigger('navigate:fetch:done', json);
            deferred.resolve(json);
            $(document.body).removeClass('loading-page');
        }).error(function (a, b, c) {
            $window.trigger('navigate:fetch:ajax:error', a, b, c);
        });
        return deferred.promise();
    }
    function Navigate(obj) {
        console.log(obj);
        var deferred = new jQuery.Deferred();
        var props = $.extend(true, {}, defaults, obj);
        $window.trigger('navigate:start', props);
        if (obj.fake) {
            deferred.resolve();
            if (!obj.nostate) {
                root.history[props.replace ? 'replaceState' : 'pushState'](props, props.title, props.url);
            }
            $window.trigger('statechange');
            return deferred.promise();
        }
        if (obj.html) {
            render(props);
            deferred.resolve();
            if (!obj.nostate) {
                root.history[props.replace ? 'replaceState' : 'pushState'](props, props.title, props.url);
            }
            $window.trigger('statechange');
            return deferred.promise();
        }
        var md5 = LS.get(props.url);
        props.md5 = md5;
        fetch(props).then(function (data) {
            _.extend(props, data);
            if (!obj.nostate) {
                root.history[props.replace ? 'replaceState' : 'pushState'](props, props.title, props.url);
            }
            if (!props.data) {
                render(_.extend({}, props, { html: data.html }));
            }
            $window.trigger('statechange');
            $('body').removeClass('loading-page');
            deferred.resolve(props);
        });
        return deferred.promise();
    }
    window.onpopstate = function (e) {
        var state = e.state;
        if (!state)
            return true;
        e.preventDefault();
        if (!state.html) {
            fetch(state).then(function (data) {
                var props = _.extend(true, {}, state, data.json);
                render(_.extend({}, props, { html: data.html }));
                $window.trigger('statechange');
            });
        } else {
            render(state);
            $window.trigger('statechange');
        }
        return false;
    };
    if (!history.state) {
        Navigate({
            url: location.href,
            fake: true,
            replace: true
        });
    }
    root.Navigate = Navigate;
}(this));
(function (root) {
    var timeout = 0;
    var $window = $(window);
    function Notification(options) {
        $window.trigger('notify:before', options);
        var $f = $('#formError');
        var $notif = $f.length ? $f : $('<div id="formError"></div>');
        $(document.body).append($notif);
        if (timeout)
            clearTimeout(timeout);
        if (options === false) {
            $notif.fadeOutAndRemove();
            $window.trigger('notify:close:force').trigger('notify:done');
            return;
        } else {
            $notif.fadeIn();
            $window.trigger('notify:shown');
        }
        if (options.html)
            $notif.html(options.html);
        else
            $notif.html('<p>' + options.text + '</p>').addClass(options.type);
        $window.trigger('notify:html', $notif);
        if (!options.sticky)
            $notif.prop('sticky', false);
        timeout = setTimeout(function () {
            $notif.fadeOutAndRemove();
            $window.trigger('notify:close:timeout', $notif);
        }, options.delay || 7000);
    }
    $(document).on('click', '#formError li', function () {
        var $this = $(this);
        if ($this.parents('#formError').prop('sticky'))
            return;
        $this.fadeOutAndRemove();
        $window.trigger('notify:close:click');
    });
    root.notify = Notification;
}(this));
(function ($) {
    'use strict';
    var requests = [], defaults = {
            ajax: {
                type: undefined,
                url: undefined,
                processData: false,
                contentType: false,
                dataType: 'json',
                cache: false,
                abort: false,
                beforeSend: function (request) {
                    request.setRequestHeader('accept', 'application/json');
                }
            },
            noLoading: false
        };
    $.fn.ajaxify = function Ajaxify(options) {
        var $form = $(this);
        $.extend(true, this, defaults, options);
        $form.trigger('ajaxify:init', this);
        var _super = this;
        function send($this) {
            $form.trigger('ajaxify:send:before', _super);
            var elementOptions = {
                type: _super.link ? $this.attr('data-method') || 'get' : $this.prop('method') || $this.attr('data-method'),
                url: (_super.link ? $this.prop('href') : $this.prop('action') || $this.attr('data-action')) || location.href
            };
            var ajax = _.extend(_super.ajax, elementOptions);
            if (!_super.link) {
                var fd = new FormData($this.get(0));
                $this.find('[contenteditable]').each(function () {
                    fd.append(this.getAttribute('name'), this.innerHTML);
                });
                for (var formName in ajax.data)
                    fd.append(formName, ajax.data[formName]);
                var ajaxOptions = _.extend(ajax, { data: fd });
                $this.find('input, [contenteditable]').attr('disabled', '');
            } else {
                try {
                    ajaxOptions = _.extend(ajax, {
                        data: JSON.parse($this.attr('data-data')),
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                        processData: true
                    });
                } catch (e) {
                    ajaxOptions = ajax;
                }
                $('[data-ajaxify]').attr('disabled', '');
            }
            var refresh = ajaxOptions.refresh || $this.attr('data-refresh') !== undefined;
            if (!_super.noLoading)
                $('body').addClass('loading-form');
            $form.trigger('ajaxify:send:ajax:start', ajaxOptions);
            var myXhr = $.ajax(ajaxOptions).done(function (data, status, xhr) {
                _super.results = data;
                $.fn.ajaxify.showResults(data, $this, _super);
                if (data.msg && data.msg.redirect) {
                    var a = $('<a href="' + data.msg.redirect + '"></a>');
                    if (a.isAbsoluteURL() || data.msg.direct)
                        location.replace(data.msg.redirect);
                    else
                        Navigate({ url: data.msg.redirect });
                    return;
                }
                if (refresh)
                    Navigate({
                        url: location.href,
                        replace: true
                    });
                $form.trigger('ajaxify:success', data, status, xhr);
            }).fail(function (xhr, status, error) {
                $form.trigger('ajaxify:fail', xhr, status, error);
            }).always(function (a1, a2, a3) {
                $form.trigger('ajaxify:complete', a1, a2, a3);
                if (_super.noLoading)
                    return;
                $('input, [contenteditable], [data-ajaxify]').removeAttr('disabled');
                $('body').removeClass('loading-form');
            });
            if (ajaxOptions.abort) {
                requests.push(myXhr);
                for (var i = 0; i < requests.length - 1; i++)
                    requests[i].abort();
            }
        }
        send.call(_super, $form.first());
    };
    $.fn.ajaxify.showResults = function (data, $form, _super) {
        $form.trigger('ajaxify:render:start', data, $form, _super);
        $form.find('input').removeClass('error warn');
        var $div = $('<div></div>');
        var hasError = false;
        for (var i in data.messages) {
            var grp = data.messages[i], type;
            switch (i) {
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
            }
            var $ul = $('<ul class="' + i + ' unselectable"></ul>');
            $form.find('input').removeClass('invalid');
            for (var j = 0, len = grp.length; j < len; j++) {
                var msg = grp[j];
                $ul.append('<li class="notify-' + msg.group + ' ' + msg.redirect + '">' + (data.title || '') + msg.title + '</li>');
                if (msg.element) {
                    try {
                        msg.element = JSON.parse(msg.element);
                    } catch (e) {
                    }
                    (_.isArray(msg.element) ? msg.element : [msg.element]).forEach(function (e) {
                        var $el = $form.find('input[name="' + e + '"]');
                        $el.addClass('invalid');
                    });
                }
            }
            $div.append($ul);
        }
        $form.trigger('ajaxify:render:done', data, $form, _super);
        if (!hasError && $form.attr('data-clear') !== undefined)
            $form.find('input, select, textarea, [contenteditable]').not('[data-unclear]').val('');
        $form.trigger('ajaxify:render:clear', data, $form, _super);
        if (!hasError)
            setTimeout(function () {
                if ($form.find('input').get(0))
                    $form.find('input').get(0).select();
            }, 100);
        $form.trigger('ajaxify:render:focus', data, $form, _super);
        notify({
            html: $div,
            delay: parseInt($form.attr('data-delay'), 10),
            sticky: data.msg && data.msg.redirect
        });
        $form.trigger('ajaxify:notify', data, $form, _super);
    };
}(jQuery));
$(document).ready(function () {
    var regex = /(?:\?|&)?lang=\w*/g;
    if (regex.test(location.search))
        Navigate({
            url: location.pathname + location.search.replace(regex, '') + location.hash,
            replace: true
        });
    $(document).keydown(function (e) {
        if (e.keyCode === 27)
            $('input').blur();
    });
    $(document).on('submit', 'form', function (e) {
        if ($(this).hasAttr('data-action'))
            return;
        e.preventDefault();
        $(this).ajaxify();
    });
    $(document).on('click', '[data-ajaxify]', function (e) {
        e.preventDefault();
        $(this).ajaxify({ link: true });
    });
    $(document).on('keypress', 'input[type="date"],                            input[type="datetime-local"],                            input[type="number"],                            input[type="tel"],                            input#mobile', function (e) {
        if (this.getAttribute('data-allowpersian') !== null || e.which < 32)
            return;
        e.preventDefault();
        if (e.which === 32)
            return;
        var key = String.fromCharCode(e.which), val = '';
        if (e.which <= 1785 && e.which >= 1776)
            try {
                var start = this.selectionStart, end = this.selectionEnd;
                val = this.value.slice(0, start) + key.toEnglish() + this.value.slice(end);
            } catch (e) {
                val = this.value + key.toEnglish();
            }
        else
            val = this.value + key;
        if (isNaN(+val)) {
            $this = $(this);
            $this.addClass('invalid');
            setTimeout(function () {
                $this.removeClass('invalid');
            }, 500);
        } else
            this.value = val;
        return false;
    });
    $(document.body).on('click', 'a', function (e) {
        var $this = $(this);
        if ($this.attr('target') === '_blank' || $this.hasAttr('data-ajaxify') || $this.hasAttr('data-action') || $this.hasAttr('data-direct') || $this.hasAttr('data-modal') || $this.isAbsoluteURL())
            return;
        e.preventDefault();
        if (!$this.attr('href') || $this.attr('href').indexOf('#') > -1)
            return;
        var href = $this.attr('href');
        if (href.indexOf('lang=') > -1)
            return location.replace(href);
        Navigate({
            url: href,
            fake: !!$this.attr('data-fake')
        });
    });
});
route('*', function () {
    $('.modal', this).on('close', function () {
        var $this = $(this);
        $this.removeClass('visible');
        $.each($this.data(), function (key) {
            if (key === 'modal')
                return;
            $(this).removeAttr(key);
        });
    });
    $('.modal', this).on('open', function () {
        $(this).addClass('visible');
        var $send = $('[data-ajaxify]', this);
        if (!$send.length)
            return;
        $.each($send.data(), function (key) {
            if (key === 'modal')
                return;
            $send.removeAttr(key);
        });
        $send.copyData(this, ['modal']);
    });
    $('.panel .panel-heading', this).click(function () {
        var el = $(this).parent();
        if (el.hasClass('closed')) {
            el.children('.panel-footer').slideDown(300);
            el.children('.panel-body').slideDown(600, function () {
                el.removeClass('closed');
            });
        } else {
            el.children('.panel-footer').slideUp(300);
            el.children('.panel-body').slideUp(500, function () {
                el.addClass('closed');
            });
        }
    });
});