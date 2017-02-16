(function($){

    "use strict";

    var debug = true;

    function log(s) {
        if(debug) {
            console.log(s);
        }
    }

    var Price = function () {
        var $slider = $( ".slider-range" ),
            $from =  $( "#price_from"),
            $to =  $( "#price_to");

        return {
            init: function () {
                var min = parseInt(arguments[0]/100 || $from.data('min')),
                    max = parseInt(arguments[1]/100 || $to.data('max'));
                var self = this;
                self.setValue(min, max);
                $slider.slider({
                    range: true,
                    min: $from.data('min'),
                    max: $to.data('max'),
                    values: [ min, max],
                    slide: function( event, ui ) {
                        self.setValue(ui.values[0], ui.values[1]);
                        var h = $from.offset().top - $header.height();
                        $filterApply.css({top: h}).fadeIn(250);
                    }
                });
            },

            setValue: function (min, max) {
                $from.val(min);
                $to.val(max);
            },

            isActive: function () {
                return !!$from.val();
            },

            getInterval: function () {
                return ($from.val() * 100) + "-" + ($to.val() * 100);
            }

        };
    };

    var Data = function () {
        this.m = []; // [{id: 1, values: [1,2,3...n]}, ...]

        /**
         * Add object to this.m
         *
         * @param {Object} obj
         * @param {number} obj.id
         * @param {number} obj.value
         * @returns 0
         */
        this.add = function (obj) {
            for(var i = 0; i < this.m.length; i++) {
                if(this.m[i].id == obj.id) {
                    this.m[i].values.push(obj.value);
                    return 0;
                }
            }
            this.m.push({id: obj.id, values: [obj.value]});
            return 0;
        };

        /**
         * Remove object from this.m
         *
         * @param {Object} obj
         * @param {number} obj.id
         * @param {number} obj.value
         * @returns {boolean}
         */
        this.remove = function (obj) {
            for(var i = 0; i < this.m.length; i++) {
                if(this.m[i].id == obj.id) {
                    var index = this.m[i].values.indexOf(obj.value);
                    if (index > -1) {
                        this.m[i].values.splice(index, 1);
                    }
                    if(this.m[i].values.length == 0) {
                        this.m.splice(i, 1);
                    }
                    return true;
                }
            }
            return false;
        };
        this.serialize = function () {
            var s = "";
            for(var i = 0; i < this.m.length; i++) {
                s += this.m[i].id;
                s += ":";
                for(var k = 0; k < this.m[i].values.length; k++) {
                    s += this.m[i].values[k];
                    if(k < this.m[i].values.length - 1)
                        s += ",";
                }
                s += ";";
            }
            return s;
        };
        /**
         * Unserialize and get price
         *
         * @param {string} s
         * @returns {Object} price
         * @returns {integer} price.from
         * @returns {integer} price.to
         */
        this.get = function(s) {
            var par = location.search.split('f=')[1];
            if(par)
                s = par;
            var d = s.split(';');
            var price = {
                from: 0, to:0
            };
            for(var i = 0 ; i < d.length - 1; i++) {
                var item = d[i].split(':');
                if(item[0] == 'p') {
                    var t = item[1].split('-');
                    price.from = t[0];
                    price.to = t[1];
                } else {
                    var t = item[1].split(',');
                    for(var k = 0 ; k < t.length; k++) {
                        this.add({id: item[0], value: t[k]});
                    }
                }
            }
            return price;
        }
    };

    var $checkbox = $('.filter input:checkbox'),
        $fullApply = $('.btn-apply'),
        $header = $('header.header'),
        $content = $('.pjax-result'),
        $loader = $('.filter-loader'),
        filterApply = 'filter-apply-btn',
        $filterApply = $('.' + filterApply);

    var data = new Data(),
        catalogID = $('.products-view').data("catalogId");

    function test1() {
        var test = [{id: 1, value: 2},{id: 2, value: 2},{id: 3, value: 2},{id: 1, value: 4},{id: 1, value: 5}];
        var ddd = new Data();
        for(var i = 0; i < test.length; i++) {
            ddd.add(test[i]);
        }
        log(ddd.serialize(ddd));
        // log(data.remove({id: 2, value: 2}));
    }

    function test2() {
        var ddd = new Data();
       // ddd.get("p:16-500;954:1945166136,903495414;358:1829271172,796428635;");
        ddd.get("?p:90-445;864:353509045,69753746;");
        log(ddd);
        log(ddd.serialize());
    }

    var Filters = {
        init: function() {
            this.event();
            this.setFilter();
        },
        event: function() {
            var self = this;
            $checkbox.on('change', function (e) {
                var cur = $(this),
                    obj = {id: cur.data('name'), value: cur.val() };
                if(cur.prop( "checked" )) {
                    data.add(obj);
                } else {
                    data.remove(obj);
                }
                var h = cur.offset().top - $header.height() - $filterApply.height();
                $filterApply.css({top: h}).fadeIn(250);
            });
            $("html, body").on("click", function(e) {
                $(e.target).hasClass(filterApply) || $filterApply.fadeOut(250);
            });
            $fullApply.on('click', function () {
                self.getData();
            });
            $filterApply.on('click', function () {
                self.getData();
            });
        },

        /*
         * Setup filter
         */
        setFilter: function () {
            var price = data.get(window.location.search) || {from: 0, to: 0};
            if(data.m.length > 0 || price.to > 0) {
                Price().init(price.from, price.to);
                $checkbox.each(function () {
                    var cur = $(this),
                        curVal = cur.val(),
                        curName = cur.data('name');
                    for(var i = 0; i < data.m.length; i++) {
                        if(data.m[i].id == curName) {
                            for(var k = 0; k < data.m[i].values.length; k++) {
                                if(data.m[i].values[k] == curVal) {
                                    cur.prop("checked", true);
                                }
                            }
                        }

                    }
                });
            } else {
                Price().init();
            }
        },
        getUrl: function () {
            var url = "f=";
            if(Price().isActive()) {
                url += "p:" + Price().getInterval() + ";";
            }
            url += data.serialize();
            return url;
        },

        getData: function () {
            var url = '/catalog/view/'+catalogID+'?' + this.getUrl();
            $loader.css('opacity', 1);
            $.ajax({
                url:     url + '&ajax=1',
                success: function(data){
                    $content.html(data);
                    App.reinit();
                    setTimeout(function() {
                        $loader.css('opacity', 0);
                    }, 150);
                },
                error: function () {
                    console.log('Error');
                    App.message('Произошла ошибка', false);
                }
            });

            // Change url
            if(url != window.location){
                window.history.pushState(null, null, url);
            }
        },
    };

    Filters.init();

})(jQuery);