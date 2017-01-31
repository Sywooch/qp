(function($){

    const $inputCount = $('.product_count'),
        $compare = $('.btn-compare'),
        $cart = $('.shopping');

    // TODO: Сделать функцию, которая будет предотвращать множественное отправление ajax запросов

    // Time in milliseconds between Ajax requests
    const interval = 200;

    var Cart = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $inputCount.on('change', function () {
                self.changeCount($(this));
            });

            $compare.on('click', function () {
                self.addToCart($(this));
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

            this.getData('/catalog/add', {
                id: id,
                count: count
            });

            // Animation goods movement to cart
            var $imgToFly = $('img[data-product-id=' + id + ']');
            if ($imgToFly) {
                var $imgClone = $imgToFly.clone()
                    .offset($imgToFly.offset())
                    .css({'opacity':'0.7', 'position':'absolute', 'height':'150px', 'width':'150px', 'z-index':'1000'})
                    .appendTo($('body'))
                    .animate({
                        'top': $cart.offset().top + 10,
                        'left': $cart.offset().left + 50,
                        'width':35,
                        'height':35
                    }, 'slow');

                $imgClone.animate({'width':0, 'height':0}, function(){ $(this).detach() });
            }

        },

        /**
         * Get data using ajax
         *
         * @param {string} url example:"/controller/action"
         * @param {object} options
         */
        getData: function(url, options){
            var self = this;
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: {
                    product_id: options.id,
                    product_count: options.count
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
        changeCount: function (element) {
            var id = element.data('productId'),
                count = element.val();

            $compare.each(function (index, el) {

                if($(el).data('productId') === id) {
                    $(el).attr('data-product-count', count);
                }
            });
        },
    };

    Cart.init();

})(jQuery);