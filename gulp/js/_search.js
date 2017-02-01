(function($){

    "use strict";

    var $open = $('.btn-search-modal'),
        $input = $('#search-input-mobile');

    var Search = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $open.on('click', function () {
                setTimeout(self.openSearch, 800);
            });
        },
        openSearch: function () {
            $input.focus();
        }
    };

    Search.init();

})(jQuery);