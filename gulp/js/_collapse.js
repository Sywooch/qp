(function($){

    "use strict";

    var $handler = $('.qp-collapse-handler');


    var Collapse = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $handler.on('click', function () {
                self.shown($(this), $('#' + $(this).data('toggle')));
            });
        },

        shown: function (handler, el) {
            if(window.innerWidth < 769) {
                if(el.hasClass('shown')){
                    el.removeClass('shown');
                    el.addClass('closed');
                    el.fadeOut(200);
                    handler.removeClass('active');
                } else {
                    el.addClass('shown');
                    el.removeClass('closed');
                    el.fadeIn(100);
                    handler.addClass('active');
                }
            }
        },
    };

    Collapse.init();

})(jQuery);