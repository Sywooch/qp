(function($){

    "use strict";

    var $el = $('.bookmark');
    var $btn = $('.btn-bookmark');

    var action = true; // true - add to bookmark

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
                        action = false;
                        return '/catalog/delete-bookmark';
                    }
                    action = true;
                    el.attr("title", "В избранном");
                    return '/catalog/add-bookmark';

                })($(this));

                var id = $(this).data('productId');
                self.getData(url, {
                    id: id
                });
            });
            $btn.on('click', function () {
                var id = $(this).data('productId');
                action = false;
                var fn = this;

                self.getData('/catalog/delete-bookmark', {
                    id: id,
                    remover: $(fn).parent().parent()
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
                success: function(result) {
                    if(status) {
                        App.message('Товар добавлен в избранное', true);
                    } else {
                        App.message('Товар удалён из избранного', true);
                    }
                    if(options.remover) {
                        options.remover.fadeOut(400, function () {
                            $(this).remove();
                        });
                    }
                },
                error: function () {
                    App.message('Произошла ошибка', false);
                    status = false;
                }

            });
        }
    };

    Bookmark.init();

})(jQuery);