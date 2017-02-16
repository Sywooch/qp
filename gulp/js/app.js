var App = (function(){
    "use strict";

    //public API
    return {
        init: function() {

            var stage = [];

            var currentStage = $('#app[data-stage]').data('stage') || 'product';

            stage['cart'] = Cart;
            stage['product'] = Product;

            stage[currentStage].init();

            $('input[type=number]').stepper({
                type: 'int',       // Allow floating point numbers
                wheel_step:1,       // Wheel increment is 1
                arrow_step: 1,    // Up/Down arrows increment is 0.5
                limit: [1, 100],
                incrementButton: '<i class="fa fa-plus"></i>',
                decrementButton: '<i class="fa fa-minus"></i>',

                onStep: function( val, up )
                {
                    stage[currentStage].update(this, val);
                }
            });
        },

        /*
         * Notify
         *
         * @param {string} msg
         * @param {string} t default "success"
         */
        message: function (msg, t) {
            var type = t ? 'success' : 'danger',
                icon = t ? '<i class="fa fa-check fa-lg" aria-hidden="true"></i> ' : '<i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> ';
            $.notify({
                // options
                message: icon + msg
            },{
                // settings
                type: type,
                delay: 2000,
                animate: {
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                }
            });
        }
    }
})();

App.init();

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});