(function($){

    "use strict";
    var price = function () {
        var $slider = $( ".slider-range" ),
            $from =  $( "#price_from"),
            $to =  $( "#price_to");

        return {
            init: function () {
                $slider.slider({
                    range: true,
                    min: $from.data('min'),
                    max: $to.data('max'),
                    values: [ $from.data('min'), $to.data('max')],
                    slide: function( event, ui ) {
                        $from.val(ui.values[0]);
                        $to.val(ui.values[1]);
                    }
                });
            }

        };
    };



    var Filters = {
        init: function() {
            this.event();
            price().init();
        },
        event: function() {
            var self = this;
        }
    };

    Filters.init();

})(jQuery);