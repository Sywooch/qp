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
                    App.message('Произошла ошибка', false);
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
            App.message('Корзина обновленна', true);
        }

    };

})(jQuery);
