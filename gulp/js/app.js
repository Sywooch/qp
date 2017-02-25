var App = (function(){
    "use strict";

    var stage = [];
    var currentStage = $('#app[data-stage]').data('stage') || 'product';

    var debug = true;

    $.widget( "custom.catcomplete", $.ui.autocomplete, {
        _renderMenu: function( ul, items ) {
            var that = this,
                currentCategory = "";
            $.each( items, function( index, item ) {
                if ( item.category != currentCategory ) {
                    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
                that._renderItemData( ul, item );
            });
        }
    });

    //public API
    return {

        /** @type {function(...*)} */
        log: function() {
            if(debug)
                console.log.apply(console, arguments)
        },
        init: function() {

            stage['cart'] = Cart;
            stage['product'] = Product;

            this.reinit();
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
        },

        reinit: function () {

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
        }
    }
})();

App.init();

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});