var Cart = (function(){

    var inputCount = $('.product_count'),
        compare = $('.btn-compare'),
        cart = $('.shopping');

    //todo: Сделать функцию, которая будет предотвращать множественное отправление ajax запросов

    //Time in milliseconds between Ajax requests
    var interval = 200;

    var __ = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            inputCount.on('change', function () {
                self.changeCount($(this));
            });

            compare.on('click', function () {
                self.addToCart($(this));
            });
        },
        addToCart: function (el) {
            var id = el.data('productId'),
                count = el.attr('data-product-count');

            this.getData('/catalog/add', {
                id: id,
                count: count
            });

            var imgtofly = $('img[data-product-id=' + id + ']');
            if (imgtofly) {
                var imgclone = imgtofly.clone()
                    .offset(imgtofly.offset())
                    .css({'opacity':'0.7', 'position':'absolute', 'height':'150px', 'width':'150px', 'z-index':'1000'})
                    .appendTo($('body'))
                    .animate({
                        'top':cart.offset().top + 10,
                        'left':cart.offset().left + 50,
                        'width':35,
                        'height':35
                    }, 'slow');
                imgclone.animate({'width':0, 'height':0}, function(){ $(this).detach() });
            }


        },
        getData: function(url, options){
            var self = this;
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: "product-id="+options.id+"&product-count="+options.count,
                success: function(result){
                    self.render(result);
                },
                error: function () {
                    console.log('Error');
                }

            });
        },
        render: function (result) {
            setTimeout(function() {cart.html(result)}, 600);
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
                Search.init;
                CatalogMenu.init;
                Cart.init;
            }
        }
    })();

    App.init();
});