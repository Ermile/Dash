// Used to work with localStorage. LocalStorage API doesn't allow for setting/getting
// objects/arrays directly
(function (root) {
    function stringify(something) {
        return typeof something === 'object' ? JSON.stringify(something) : something;
    }

    function parse(something) {
        try { return JSON.parse(something); }
        catch (e) {}
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
})(this);
