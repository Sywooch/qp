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
var CatalogMenu = (function(){
    "use strict";

    var el = $('.transform');

    var __ = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            el.on('click', function () {
                self.shown();
            });
        },
        shown: function () {
            if(el.hasClass('shown')){
                el.removeClass('shown');
            } else {
                el.addClass('shown');
            }
        },
    }

    return {
        init: __.init()
    }
})();
var Search = (function(){
    "use strict";

    var open = $('.btn-search-modal'),
        input = $('#search-input-mobile');

    var __ = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            open.on('click', function () {
                setTimeout(self.openSearch, 800);
            });
        },
        openSearch: function () {
            input.focus();
        },

    }

    return {
        init: __.init()
    }
})();
$(document).ready(function () {
    var App = (function(){
        "use strict";

        //public API
        return {
            init: function() {
                Search.init();
                CatalogMenu.init();
                Cart.init();
            }
        }
    })();

    App.init();
});