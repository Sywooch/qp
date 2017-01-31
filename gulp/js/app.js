(function($){
    var App = (function(){
        "use strict";

        //public API
        return {
            init: function() {
                $('input[type=number]').stepper({
                    type: 'int',       // Allow floating point numbers
                    wheel_step:1,       // Wheel increment is 1
                    arrow_step: 1,    // Up/Down arrows increment is 0.5
                    limit: [1, 20],
                    incrementButton: '<i class="fa fa-plus"></i>',
                    decrementButton: '<i class="fa fa-minus"></i>',

                    onStep: function( val, up )
                    {

                    }
                });
            }
        }
    })();

    App.init();
})(jQuery);