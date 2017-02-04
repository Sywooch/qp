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