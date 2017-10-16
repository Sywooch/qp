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
                if ( item.category !== currentCategory ) {
                    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
                that._renderItemData( ul, item );
            });
        }
    });

    // Skel.
    skel.breakpoints({
        xlarge: '(max-width: 1680px)',
        large: '(max-width: 1280px)',
        medium: '(max-width: 980px)',
        small: '(max-width: 736px)',
        xsmall: '(max-width: 480px)',
        xxsmall: '(max-width: 360px)',
        short: '(min-aspect-ratio: 16/7)',
        xshort: '(min-aspect-ratio: 16/6)'
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

            $('.button-collapse').sideNav({'edge': 'left'});

            $('*[data-route]').on("click", function () {
                var route = $(this).data('route');
                window.location = route;
            });

            $('[data-toggle="tooltip"]').tooltip();

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
                    enter: 'animated slideInDown',
                    exit: 'animated slideOutRight'
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
