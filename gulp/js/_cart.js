var Cart = (function(){
    "use strict";

    var inputCount = $('.product_count'),
        compare = $('.btn-compare');

    var __ = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            inputCount.on('change', function () {
                self.changeCount($(this));
            });
        },
        changeCount: function (element) {
            var id = element.data('productId'),
                count = element.val();

            compare.each(function (index, el) {

                if($(el).data('productId') === id) {
                    $(el).attr('data-product-count', count);
                }
            });
        },
    };

    return {
        init: __.init()
    }
})();