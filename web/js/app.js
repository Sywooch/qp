(function($){

    "use strict";

    var $el = $('.bookmark');

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    var Bookmark = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $el.on('click', function () {

                var url = (function(el) {

                    // Get url and change current title
                    if(el.hasClass('active')) {
                        el.attr("title", "В избранное");
                        return '/catalog/delete-bookmark';
                    }
                    el.attr("title", "В избранном");
                    return '/catalog/add-bookmark';

                })($(this));

                var id = $(this).data('productId');
                self.getData(url, {
                    id: id
                });
            });
        },

        /**
         * Get data using ajax
         *
         * @param {string} url example:"/controller/action"
         * @param {object} options
         */
        getData: function (url, options) {
            var self = this;
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: {
                    product_id: options.id,
                    _csrf: csrfToken
                },
                success: function(result){
                    console.log(result);
                },
                error: function () {
                    console.log('Error');
                }

            });
        }
    };

    Bookmark.init();

})(jQuery);
var Cart = (function($){

    var $cart = $('.shopping');

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // These variables are used to limit Ajax requests
    var presentOperation = 0, delayOperation = 0,

        products = [];

    var total = function () {
        var $total = $("#total span");
        var $sum = $('.cart-item__sum span');

        return {
            get: function () {
                return $total.text();
            },
            update: function () {
                var result = 0;
                $sum.each(function (index, el) {
                    result += parseFloat($(el).text());
                });
                $total.text(result);
            }
        };
    };

    return {
        /**
         * @access public
         */
        init: function() {
            this.event();
        },
        event: function() {

        },

        /**
         * Change event input number
         *
         * @access public
         * @param {object} element
         * @param {number} val
         */
        update: function (element, val) {
            var id = element.data('productId'),
                $item = $('.cart-item[data-product-id="' + id + '"]'),
                quantity = $item.find('.cart-item__quantity'),
                sum = $item.find('.cart-item__sum span'),
                price = parseFloat($item.data('productPrice'));

            quantity.text(val);
            sum.text(parseInt(val) * price);
            total().update();

            this.addProduct(id, val);

            this.addToCart();
        },

        /**
         * Add products to cart. Creates an array changes, then all changes are sent in the request
         *
         */
        addToCart: function () {
            var self = this;
            presentOperation++;
            setTimeout(function () {
                delayOperation++;
                if(delayOperation == presentOperation) {
                    self.saveData('/cart/add-multiple', products);
                    products = [];
                }
            }, 1000);
        },

        /**
         * Get data using ajax
         *
         * @param {string} url example:"/controller/action"
         * @param {object} options
         */
        saveData: function(url, options) {
            var self = this;
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: {
                    products: options,
                    _csrf: csrfToken
                },
                success: function(result){
                    self.render(result);
                },
                error: function () {
                    console.log('Error');
                }

            });
        },

        /**
         * Check for existence. If exist, it adds an element to the array, otherwise change the count
         *
         * @param {number} id Product ID
         * @param {number} count
         * @return {boolean}
         */
        addProduct: function (id, count) {
            var i;
            for(i = 0; i < products.length; i++) {
                if(products[i].id == id) {
                    products[i].count = count;
                    return true;
                }
            }
            products.push({
                id: id,
                count: count
            });
            return false;
        },

        /**
         * Render html in cart element
         *
         * @param {string} result Result from server
         */
        render: function (result) {
            $cart.html(result);
        }

    };

})(jQuery);

(function($){

    var $el = $('.transform');

    var Catalog = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $el.on('click', function () {
                self.shown();
            });
        },
        shown: function () {
            if($el.hasClass('shown')){
                $el.removeClass('shown');
            } else {
                $el.addClass('shown');
            }
        },
    };

    Catalog.init();

})(jQuery);
(function($)
{
    // This plugin is changed for project qpvl.ru
    /*
     Numeric Stepper jQuery plugin

     Licensed under MIT:

     Copyright (c) Luciano Longo

     Permission is hereby granted, free of charge, to any person obtaining a copy of
     this software and associated documentation files (the "Software"), to deal in
     the Software without restriction, including without limitation the rights to
     use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
     the Software, and to permit persons to whom the Software is furnished to do so,
     subject to the following conditions:

     The above copyright notice and this permission notice shall be included in all
     copies or substantial portions of the Software.

     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
     FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
     COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
     IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
     CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
     */
    $.fn.stepper = function( options )
    {
        var _defaults = {
            type: 'float',                  // or 'int'
            floatPrecission: 2,             // decimal precission
            ui: true,                       // +/- buttons
            allowWheel: true,               // mouse wheel
            allowArrows: true,              // keyboar arrows (up, down)
            arrowStep: 1,                   // ammount to increment with arrow keys
            wheelStep: 1,                   // ammount to increment with mouse wheel
            limit: [null, null],            // [min, max] limit
            preventWheelAcceleration: true, // In some systems, like OS X, the wheel has acceleration, enable this option to prevent it
            incrementButton: '&blacktriangle;',
            decrementButton: '&blacktriangledown;',

            // Events
            onStep: null,   // fn( [number] val, [bool] up )
            onWheel: null,  // fn( [number] val, [bool] up )
            onArrow: null,  // fn( [number] val, [bool] up )
            onButton: null, // fn( [number] val, [bool] up )
            onKeyUp: null   // fn( [number] val )
        };

        return $(this).each(function()
        {
            var $data = $(this).data();
            delete $data.stepper;

            var _options = $.extend({}, _defaults, options, $data),
                $this = $(this),
                $wrap = $('<div class="product-counter"/>');

            if( $this.data('stepper') )
                return;

            $wrap.insertAfter( $this );
            $this.appendTo( $wrap );

            /* API */

            $this.stepper = (function()
            {
                return {
                    limit: _limit,
                    decimalRound: _decimal_round,
                    onStep: function( callback ) { _options.onStep = callback; },
                    onWheel: function( callback ) { _options.onWheel = callback; },
                    onArrow: function( callback ) { _options.onArrow = callback; },
                    onButton: function( callback ) { _options.onButton = callback; },
                    onKeyUp: function( callback ) { _options.onKeyUp = callback; }
                };
            })();

            $this.data('stepper', $this.stepper);

            /* UI */

            if( _options.ui )
            {
                var $btnDown = $('<a class="btn btn-down">'+_options.decrementButton+'</a>').appendTo( $wrap ),
                    $btnUp   = $('<a class="btn btn-up">'+_options.incrementButton+'</a>').appendTo( $wrap );

                var stepInterval;

                $btnUp.mousedown(function(e)
                {
                    e.preventDefault();

                    var val = _step( _options.arrowStep );
                    _evt('Button', [val, true]);
                });

                $btnDown.mousedown(function(e)
                {
                    e.preventDefault();

                    var val = _step( -_options.arrowStep );
                    _evt('Button', [val, false]);
                });

                $(document).mouseup(function()
                {
                    clearInterval( stepInterval );
                });
            }


            /* Events */

            if( _options.allowWheel )
            {
                $wrap.bind('DOMMouseScroll', _handleWheel);
                $wrap.bind('mousewheel', _handleWheel);
            }

            $wrap.keydown(function(e)
            {
                var key = e.which,
                    val = $this.val();

                if( _options.allowArrows )
                    switch( key )
                    {
                        // Up arrow
                        case 38:
                            val = _step( _options.arrowStep );
                            _evt('Arrow', [val, true]);
                            break;

                        // Down arrow
                        case 40:
                            val = _step( -_options.arrowStep );
                            _evt('Arrow', [val, false]);
                            break;
                    }

                // Only arrow keys, misc modifier chars and numbers and period (including keypad)
                if( ( key < 37 && key > 40 ) || ( key > 57 && key < 91 ) || ( key > 105 && key != 110 && key != 190 ) )
                    e.preventDefault();

                // Allow only one peroid and only if float is enabled
                if( _options.type == 'float' && $.inArray( key, [ 110, 190 ] ) != -1 && val.indexOf('.') != -1 )
                    e.preventDefault();
            }).keyup(function(e)
            {
                _evt('KeyUp', [$this.val()] );
            });

            function _handleWheel(e)
            {
                // Prevent actual page scrolling
                e.preventDefault();

                var d,
                    evt = e.originalEvent;

                if( evt.wheelDelta )
                    d = evt.wheelDelta / 120;
                else if( evt.detail )
                    d = -evt.detail / 3;

                if( d )
                {
                    if( _options.preventWheelAcceleration )
                        d = d < 0 ? -1 : 1;

                    var val = _step( _options.wheelStep * d );

                    _evt('Wheel', [val, d > 0]);
                }
            }

            function _step( step )
            {
                if( ! $this.val() )
                    $this.val( 0 );

                var typeCast = _options.type == 'int' ? parseInt : parseFloat,
                    val      = _limit( typeCast( $this.val() ) + step );

                $this.val( val );

                _evt('Step', [val, step > 0]);

                return val;
            }

            function _evt( name, args )
            {
                var callback = _options['on'+name];

                if( typeof callback == 'function' )
                    callback.apply( $this, args );
            }

            function _limit( num )
            {
                var min = _options.limit[0],
                    max = _options.limit[1];

                if( min !== null && num < min )
                    num = min;
                else if( max !== null && num > max )
                    num = max;

                return _decimal_round( num );
            }

            function _decimal_round( num, precission )
            {
                if( typeof precission == 'undefined' )
                    precission =  _options.floatPrecission;

                var pow = Math.pow(10, precission);
                num = Math.round( num * pow ) / pow;

                return num;
            }
        });
    }
})(jQuery);
var Product = (function($){

    const $inputCount = $('.product-count'),
        $compare = $('.btn-compare'),
        $cart = $('.shopping');

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Time in milliseconds between Ajax requests
    const interval = 900;
    var timer = true;

    var __ = {
        /**
         * @access public
         */
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;

            $compare.on('click', function () {
                if(timer) {
                    self.addToCart($(this));
                    timer = false;
                    setTimeout(function() {
                        timer = true;
                    }, interval);
                }
            });
        },

        /**
         * Add product to cart
         *
         * @param {object} el dom element (button)
         */
        addToCart: function (el) {
            var id = el.data('productId') || 0,
                count = el.attr('data-product-count') || 0;

            this.getData('/cart/add', {
                id: id,
                count: count
            });

            // Animation goods movement to cart
            var $imgToFly = $('img[data-product-id=' + id + ']');
            if ($imgToFly) {
                var $imgClone = $imgToFly.clone()
                    .offset($imgToFly.offset())
                    .css({
                        'opacity': '0.7',
                        'position': 'absolute',
                        'height': '150px',
                        'width': '150px',
                        'z-index': '1000'
                    })
                    .appendTo($('body'))
                    .animate({
                        'top': $cart.offset().top + 10,
                        'left': $cart.offset().left + 50,
                        'width': 35,
                        'height': 35
                    }, 'slow');

                $imgClone.animate({'width': 0, 'height': 0}, function () {
                    $(this).detach();
                });
            }
        },

        /**
         * Get data using ajax
         *
         * @param {string} url example:"/controller/action"
         * @param {object} options
         */
        getData: function(url, options) {
            var self = this;
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: {
                    product_id: options.id,
                    product_count: options.count,
                    _csrf: csrfToken
                },
                success: function(result){
                    self.render(result);
                },
                error: function () {
                    console.log('Error');
                }

            });
        },

        /**
         * Render html in cart element
         *
         * @param {string} result Result from server
         */
        render: function (result) {
            setTimeout(function() {$cart.html(result)}, 600);
        },

        /**
         * Change event input number
         *
         * @access public
         * @param {object} element
         * @param {number} val
         */
        changeCount: function (element, val) {
            var id = element.data('productId');

            $compare.each(function (index, el) {

                if($(el).data('productId') === id) {
                    $(el).attr('data-product-count', val);
                }
            });
        },
    };

    return {
        init: __.init(),
        changeCount: __.changeCount
    };



})(jQuery);
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
var App = (function(){
    "use strict";

    //public API
    return {
        init: function() {

            Product.init;
            Cart.init;

            $('input[type=number]').stepper({
                type: 'int',       // Allow floating point numbers
                wheel_step:1,       // Wheel increment is 1
                arrow_step: 1,    // Up/Down arrows increment is 0.5
                limit: [1, 100],
                incrementButton: '<i class="fa fa-plus"></i>',
                decrementButton: '<i class="fa fa-minus"></i>',

                onStep: function( val, up )
                {
                    Product.changeCount(this, val);
                    Cart.update(this, val);
                }
            });
        }
    }
})();

App.init();