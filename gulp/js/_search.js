(function($){

    "use strict";

    $.widget( "custom.catcomplete", $.ui.autocomplete, {
        _renderMenu: function( ul, items ) {
            var that = this,
                currentCategory = "";
            $.each( items, function( index, item ) {
                if ( item.category != currentCategory ) {
                    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
                that._renderItemData( ul, item );
            });
        }
    });

    var $open = $('.btn-search-modal'),
        $inputMobile = $('#search-input-mobile'),
        $input = $('#search-input');

    var dataTestProduct = [
        { label: "Бананы", url: "test"},
        { label: "Кофе Максим", url: "test"},
        { label: "Бараны", url: "test"},
        { label: "Китикет", url: "test"},
        { label: "Булочка", url: "test"},
        { label: "Бублик", url: "test"},
        { label: "Тест продукта 1", url: "test"},
        { label: "Тест продукта 2", url: "test"},
        { label: "Тест продукта 3", url: "test"},
        { label: "Тест продукта 4", url: "test"}
    ];

    var dataTestCategory = [
        { label: "Овощи", url: "category"},
        { label: "Фрукты", url: "category"},
        { label: "Выпечка", url: "category"},
        { label: "Алкоголь", url: "category"},
        { label: "Выемка", url: "category"},
        { label: "Тест категории 1", url: "category"},
        { label: "Тест категории 2", url: "category"},
        { label: "Тест категории 3", url: "category"},
        { label: "Тест категории 4", url: "category"}
    ];

    function lightwell(request, response) {
        function fuzzysearch (needle, haystack) {
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
        function hasMatch(str) {
            var d = Levenshtein.get(str.toLowerCase(), request.term.toLowerCase());
            console.log(d);
            return d > 2;
            //return srt.toLowerCase().indexOf(request.term.toLowerCase())!==-1;
        }
        var i, l, obj, matches = [];

        var hasProduct = true, hasCategory = true;

        if (request.term==="") {
            response([]);
            return;
        }

        for  (i = 0, l = dataTestProduct.length; i<l; i++) {
            obj = dataTestProduct[i];
            if (fuzzysearch(request.term.toLowerCase(), obj.label.toLowerCase())) {
                if(hasProduct) {
                    matches.push({label: 0});
                    hasProduct = false;
                }
                if(matches.length > 10)
                    break;
                matches.push(obj);
            }
        }
        for  (i = 0, l = dataTestCategory.length; i<l; i++) {
            obj = dataTestCategory[i];
            if (fuzzysearch(request.term.toLowerCase(), obj.label.toLowerCase())) {
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
                },
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
        event: function() {
            var self = this;
            $open.on('click', function () {
                setTimeout(self.openSearch, 800);
            });
        },
        openSearch: function () {
            $inputMobile.focus();
        }
    };

    Search.init();

})(jQuery);