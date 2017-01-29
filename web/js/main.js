$(document).ready(function () {
    /**
     * Search panel
     */
    var
        $productCount = $('.product_count'),
        $transform = $('.transform');

    function event() {
        $transform.on('click', function () {

            if($(this).hasClass('shown')){
                $(this).removeClass('shown');
            } else {
                $(this).addClass('shown');
            }

        });
        $productCount.on('change', function () {
            var id = $(this).data('productId'),
                count = $(this).val();

            $('.btn-compare').each(function (indx, el) {
                if($(el).data('productId') === id) {
                    $(el).attr('data-product-count', count);
                }
            });
        });
    }

    function init() {
        event();
    }
    init();
    
    
});