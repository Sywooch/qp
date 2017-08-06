(function($){

    "use strict";

    var $open = $('.btn-search-modal'),
        $inputMobile = $('#search-input-mobile'),
        $input = $('#search-input'),
        $closeSearch = $('.js-search-close'),
        $searchOverlay = $('.search-overlay');

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    var dataProducts;
    var dataCategories;

    function lightwell(request, response) {
        function hasMatch (needle, haystack) {
            var hlen = haystack.length;
            var nlen = needle.length;
            if (nlen > hlen) {
                return false;
            }
            if (nlen === hlen) {
                return needle === haystack;
            }
            outer: for (var i = 0, j = 0; i < nlen; i++) {
                var nch = needle.charCodeAt(i);
                while (j < hlen) {
                    if (haystack.charCodeAt(j++) === nch) {
                        continue outer;
                    }
                }
                return false;
            }
            return true;
        }
        var i, l, obj, matches = [];

        var hasProduct = true, hasCategory = true;

        if (request.term==="") {
            response([]);
            return;
        }

        for  (i = 0, l = dataProducts.length; i<l; i++) {
            obj = dataProducts[i];
            if (hasMatch(request.term.toLowerCase(), obj.label.toLowerCase())) {
                if(hasProduct) {
                    matches.push({label: 0});
                    hasProduct = false;
                }
                matches.push(obj);
                if(matches.length > 9)
                    break;
            }
        }
        for  (i = 0, l = dataCategories.length; i<l; i++) {
            obj = dataCategories[i];
            if (hasMatch(request.term.toLowerCase(), obj.label.toLowerCase())) {
                if(hasCategory) {
                    matches.push({label: 1});
                    hasCategory = false;
                }
                matches.push(obj);
            }
        }
        response(matches);
    }

    var Search = {
        init: function() {
            this.event();
            this.getData();

            $input.autocomplete({
                delay: 0,
                source: lightwell,
                minLength: 2,
                messages: {
                    noResults: '',
                    results: function() {}
                },
                focus: function( event, ui ) {
                    if(ui.item.label == 0 || ui.item.label == 1) {
                        return false;
                    }
                    $input.val( ui.item.label );
                    return false;
                }
            })
            .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                if(item.label == 0) {
                    return $( "<li class='ui-autocomplete-category'>" )
                        .append( "Товары" )
                        .appendTo( ul );
                }
                if(item.label == 1) {
                    return $( "<li class='ui-autocomplete-category'>" )
                        .append( "Категории" )
                        .appendTo( ul );
                }
                return $( "<li>" )
                    .append( "<a href='" + item.url + "'>" + item.label + "</a>" )
                    .appendTo( ul );
            };
        },
        getData: function () {
            $.ajax( {
                url: "/catalog/search-data",
                dataType: "json",
                type: "POST",
                data: {
                    _csrf: csrfToken
                },
                success: function( data ) {
                    dataProducts = data.products;
                    dataCategories = data.categories;
                    var i;
                    for(i = 0; i < dataProducts.length; i++) {
                        dataProducts[i].url = '/product/view/' + dataProducts[i].id;
                    }
                    for(i = 0; i < dataCategories.length; i++) {
                        dataCategories[i].url = '/catalog/view/' + dataCategories[i].id;
                    }
                },
                error: function () {
                    App.log('Error #10');
                    App.message('Произошла ошибка', false);
                }
            } );
        },
        event: function() {
            var self = this;
            $open.on('click', function () {
                setTimeout(self.openSearch, 800);
            });
            $closeSearch.on('click', function () {
                self.hideOverlay();
            });
        },
        hideOverlay: function () {
            $searchOverlay.addClass('hide');
            setTimeout(function () {
                //$searchOverlay.css('display', 'none');
                $searchOverlay.removeClass('hide');
            }, 200);
        },
        openSearch: function () {
            $inputMobile.focus();
        }
    };

    Search.init();

})(jQuery);