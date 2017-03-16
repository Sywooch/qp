(function($){

    "use strict";

    var $handler = $('.qp-collapse-handler'),
        isActive = false;


    var CollapseFilter = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $handler.on('click', function () {
                self.toggle($('#' + $(this).data('toggle')));
            });
        },

        toggle: function ( el) {
            if(window.innerWidth < 769) {
                if(el.hasClass('shown')){
                    el.removeClass('shown');
                    el.addClass('closed');
                    el.fadeOut(200);
                    $handler.removeClass('active');
                    isActive = false;
                } else {
                    el.addClass('shown');
                    el.removeClass('closed');
                    el.fadeIn(100);
                    $handler.addClass('active');
                    isActive = true;
                }
            }
        }
    };

    CollapseFilter.init();

})(jQuery);