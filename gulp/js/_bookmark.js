(function($){

    "use strict";

    var $el = $('.bookmark');
    var $btn = $('.btn-bookmark');

    const
        BOOKMARK_ADD = true,
        BOOKMARK_REMOVE = false;

    var action = BOOKMARK_ADD;

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    var Bookmark = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $el.on('click', function () {

                var url = (function(el) {
                    action = BOOKMARK_ADD;
                    // Get url and change current title
                    if(el.hasClass('active')) {
                        el.attr("title", "В избранное");
                        action = BOOKMARK_REMOVE;
                        return '/catalog/delete-bookmark';
                    }
                    el.attr("title", "В избранном");
                    return '/catalog/add-bookmark';

                })($(this));

                var id = $(this).data('productId');
                self.getData(url, {
                    id: id
                }, $(this));
            });
            $btn.on('click', function () {
                var id = $(this).data('productId');
                action = BOOKMARK_REMOVE;
                var that = this;
                self.getData('/catalog/delete-bookmark', {
                    id: id,
                    remover: $(that).parent().parent()
                }, $(this));

            });
        },

        /**
         * Get data using ajax
         *
         * @param {string} url example:"/controller/action"
         * @param {object} options
         * @param {object} el
         */
        getData: function (url, options, el) {
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: {
                    product_id: options.id,
                    _csrf: csrfToken
                },
                success: function(result) {
                    var count = parseInt(result) ? parseInt(result) : "";
                    if(action === BOOKMARK_ADD) {
                        App.message('Товар добавлен в избранное', true);
                        el.addClass('active');
                    } else {
                        App.message('Товар удалён из избранного', true);
                        el.removeClass('active');
                    }
                    el.find('.counter').html(count);
                    if(options.remover) {
                        //Remove dom element from /profile/bookmark
                        options.remover.fadeOut(400, function () {
                            $(this).remove();
                        });
                    }
                },
                error: function () {
                    App.message('Произошла ошибка', false);
                }

            });
        }
    };

    Bookmark.init();

})(jQuery);