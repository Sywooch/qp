(function($){

    var $el = $('.transform'),
        $catalog = $('.catalog');

    var Catalog = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $el.on('click', function () {
                self.shown();
            });
            $catalog.mousemove(function (event) {
                self.dropMove($(this), true);
            });
            $catalog.mouseout(function (event) {
                self.dropMove($(this), false);
            });
        },
        shown: function () {
            if($el.hasClass('shown')){
                $el.removeClass('shown');
            } else {
                $el.addClass('shown');
            }
        },
        /**
         * Visible drop menu
         *
         * @param {object} dom
         * @param {boolean} action
         */
        dropMove: function (dom, action) {
            if(action) {
                dom.addClass('active');
            } else {
                dom.removeClass('active');
            }
        }
    };

    Catalog.init();

})(jQuery);