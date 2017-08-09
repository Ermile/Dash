(function () {
    var _routes = {};
    _routes.length = 0;
    function _route() {
        var fn, index;
        var args = arguments;
        index = _routes.length++;

        if (typeof args[args.length - 1] != 'function') return;
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
                }
                else if (typeof condition == 'string' && condition === url) {
                    _call(base, oRoute.fn, args);
                }
                else if (typeof condition == 'object' && condition.test(url)) {
                    _call(base, oRoute.fn, args);
                }
            }
        }
    }

    function _call(base, fn, args) {
        fn.apply(base, args);
    }

    // $(document).ready(function()
    // {
    //   $(document).sroute();
    // });
    route = _route;
    route_main = main;
})();

(function ($) {
    $.fn.sroute = function (url) {
        var args = arguments;
        url = (url) ? url : location.pathname;
        url = (url.replace(/^\//, "")).replace(/\/$/, "");
        args[0] = url;
        $(this).each(function () {
            route_main(this, args);
        });
    };

    // $(document).ready(function() {
    //   $(document.body).sroute();
    // })
})(jQuery);
