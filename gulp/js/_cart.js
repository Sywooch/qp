var Cart = (function(){

    var inputCount = $('.product_count'),
        compare = $('.btn-compare'),
        el = $('.shopping');

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
                var id = $(this).data('productId'),
                    count = $(this).attr('data-product-count');
                self.getData('/catalog/add', {
                    id: id,
                    count: count
                });
            });
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
            el.html(result);
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