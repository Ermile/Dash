// Morede estefade baraye kar ba HTML5 History

(function (root) {
    "use strict";

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
        ajax: {
            type: 'get'
        }
    };

    function exec(src) {
        var r = src.slice(src.lastIndexOf('/') + 1);
        $(document).sroute(r);
    }

    function render(obj) {
        $window.trigger('navigate:render:start', obj);

        var html = obj.html,
            $html = $(html);

        if (obj.id) $('body').attr('id', obj.id);

        $window.trigger('navigate:render:filter:before', obj.filter);

        var filter = _.isArray(obj.filter) ?
        '[data-xhr="' + obj.filter.join('"], [data-xhr="') + '"]'
            : obj.filter ? '[data-xhr="' + obj.filter + '"]' : null;

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

        if (obj.title) document.title = obj.title;
        $window.trigger('navigate:render:done');
    }

    function fetch(props, md5) {
        $window.trigger('navigate:fetch:start', props, md5);

        $(document.body).addClass('loading-page');

        var options = $.extend(true, {}, props.ajax,
            {
                url: props.url,
                // headers: { 'Cached-MD5': props.md5 }
            });

        var deferred = new jQuery.Deferred();

        $.ajax(options).done(function (res) {
            $window.trigger('navigate:fetch:ajax:start', options);

            var json,
                html;

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

                    // Generate Notification HTML
                    var $ul = $('<ul class="' + i + ' unselectable"></ul>');


                    for (var j = 0, len = grp.length; j < len; j++) {
                        var msg = grp[j];
                        var $msg = $ul.append('<li class="notify-' + msg.group + ' ' + msg.redirect + '">' +
                            (debug.title || '') + msg.title + '</li>');

                        if (msg.element) {
                            try {
                                var parsed = JSON.parse(msg.element);
                                msg.element = parsed;
                            } catch (e) {}
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

                if (json.options) {
                    var $options = $('#options-meta');
                    $options.putData(json.options);
                }
            } catch (e) {
                if (jsonExpected) {
                    notify({
                        html: '<ul class="error unselectable">'
                        + '<li class="notify-json">There was an error in parsing JSON</li>'
                        + '</ul>'
                    });
                }
                deferred.reject();
                return location.replace(props.url);
            }

            $window.trigger('navigate:fetch:ajax:done', json)
                .trigger('navigate:fetch:done', json);
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
                render(_.extend({}, props, {html: data.html}));
            }

            $window.trigger('statechange');
            $('body').removeClass('loading-page');

            deferred.resolve(props);
        });

        return deferred.promise();
    }

    window.onpopstate = function (e) {
        var state = e.state;

        if (!state) return true;
        e.preventDefault();

        if (!state.html) {
            fetch(state).then(function (data) {
                var props = _.extend(true, {}, state, data.json);

                render(_.extend({}, props, {html: data.html}));

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
})(this);
