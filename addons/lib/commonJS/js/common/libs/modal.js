$(function () {
    $(document.body).click(function (e) {
        var $target = $(e.target);
        if ($target.is('[data-modal]')
            || $target.parents('[data-modal]').length
            || $target.is('.modal-dialog')
            || $target.parents('.modal-dialog').length
            || $('.modal.visible').hasAttr('data-always'))
            return;
        $('.modal').trigger('close');
    });

    $(window).keydown(function (e) {
        if (e.which === 27)
            $('.modal').trigger('close');
    });

    // Open modals by clicking on elements with data-modal attribute
    $(document).on('click', '[data-modal]', function (e) {
        var $this = $(this);
        if ($this.hasClass('modal') || $this.parents('.modal').length > 0) return;

        e.preventDefault();
        var $modal = $('#' + $this.attr('data-modal'));
        $modal.copyData($this, ['modal']);
        $modal.trigger('open', $this);
    });

    // Close modals and exit events
    $(document).on('click', '[data-cancel]', function (e) {
        $('.modal').trigger('close').trigger('cancel');
    });

    $(document).on('click', '[data-ok]', function (e) {
        $('.modal').trigger('close').trigger('ok');
    });
});



