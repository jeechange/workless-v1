+function ($) {
    var ajaxRequest = function () {

    };

    $.fn.ajaxRequest = function () {
        return this.each(function () {
            if (XHR.status == 200) {
                return;
            }
        });
    };
}(jQuery);

