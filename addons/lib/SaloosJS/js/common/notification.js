// Morede estefade baraye namayesh Notification ha

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
            $window.trigger('notify:close:force')
                .trigger('notify:done');
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

        // if (options.sticky) {
        //   $notif.prop('sticky', true);
        //   return;
        // }

        timeout = setTimeout(function () {
            $notif.fadeOutAndRemove();
            $window.trigger('notify:close:timeout', $notif);
        }, options.delay || 7000);
    }

    $(document).on('click', '#formError li', function () {
        var $this = $(this);
        if ($this.parents('#formError').prop('sticky')) return;

        $this.fadeOutAndRemove();
        $window.trigger('notify:close:click');
    });

    root.notify = Notification;
})(this);
