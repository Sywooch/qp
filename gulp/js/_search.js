String.prototype.score=function(e,f){if(this===e)return 1;if(""===e)return 0;var d=0,a,g=this.toLowerCase(),n=this.length,h=e.toLowerCase(),k=e.length,b;a=0;var l=1,m,c;f&&(m=1-f);if(f)for(c=0;c<k;c+=1)b=g.indexOf(h[c],a),-1===b?l+=m:(a===b?a=.7:(a=.1," "===this[b-1]&&(a+=.8)),this[b]===e[c]&&(a+=.1),d+=a,a=b+1);else for(c=0;c<k;c+=1){b=g.indexOf(h[c],a);if(-1===b)return 0;a===b?a=.7:(a=.1," "===this[b-1]&&(a+=.8));this[b]===e[c]&&(a+=.1);d+=a;a=b+1}d=.5*(d/n+d/k)/l;h[0]===g[0]&&.85>d&&(d+=.15);return d};

(function($){

    "use strict";

    var $open = $('.btn-search-modal'),
        $input = $('#search-input'),
        $closeSearch = $('.js-search-close'),
        $modal = $('#search-modal'),
        $searchHeaderWrap = $('#js-search-wrap'),
        $searchHeaderInput = $('#js-search-input'),
        $searchOverlay = $('.search-overlay');

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    var dataProducts;
    var dataCategories;

    function lightwell(request, response) {
        function fuzzy_match(text, query) {
            if (text === null) {
                return null
            }
            var search = query.replace(/\s/g, '').toLowerCase(),
                tokens = '',
                textLen = text.length,
                searchLen = search.length,
                search_position = 0;

            for (var n = 0; n < textLen; n++) {
                var text_char = text[n];
                if (search_position < searchLen &&
                    text_char.toLowerCase() === search[search_position]) {
                    text_char = '<b>' + text_char + '</b>';
                    search_position += 1;
                }
                tokens += text_char;
            }
            if (search_position !== searchLen) {
                return null;
            }
            return tokens;
        }
        var matchesProd = [], matchesCat = [];

        if (request.term==="") {
            response([]);
            return;
        }
        function add(dataList, matches) {
            dataList.map(function(item) {
                if (item.label === null) {
                    return false;
                }
                var rating = item.label.score(request.term, 0.5);
                if (rating > 0.5) {
                    var result = fuzzy_match(item.label, request.term);
                    if (result !== null) {
                        matches.push({
                            id: item.id,
                            rating: rating,
                            label: result
                        });
                    }
                }
            });
        }
        
        function sortAndSlice(arr, label) {
            if (arr.length === 0) {
                return;
            }
            arr.push({
                label: label,
                rating: 2
            });
            arr.sort(function (a, b) {
                return a.rating < b.rating
            }).slice(0, 20);
        }

        add(dataProducts, matchesProd);
        add(dataCategories, matchesCat);

        sortAndSlice(matchesProd, 0);
        sortAndSlice(matchesCat, 1);

        response(matchesProd.concat(matchesCat));
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
                    if(ui.item.label === 0 || ui.item.label === 1) {
                        return false;
                    }
                    $input.val( ui.item.label );
                    return false;
                }
            })
            .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                if(item.label === 0) {
                    return $( "<li class='ui-autocomplete-category'>" )
                        .append( "Товары" )
                        .appendTo( ul );
                }
                if(item.label === 1) {
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
            });
            $closeSearch.on('click', function () {
                self.hideOverlay();
            });
            $modal.on('shown.bs.modal', function () {
                self.openSearch();
            });
            $searchHeaderInput.on('focus', function () {
                $modal.modal('show');
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
            $input.focus();
        }
    };

    Search.init();

})(jQuery);