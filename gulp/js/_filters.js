(function($){

    "use strict";

    var $checkbox = $('.filter input:checkbox'),
        $fullApply = $('.btn-apply'),
        $header = $('header.header'),
        $content = $('.pjax-result'),
        $loader = $('.filter-loader'),
        $sort = $('#sort'),
        filterApply = 'filter-apply-btn',
        $filterApply = $('.' + filterApply),
        $showMore = $('.js-show-more'),
        catalogID = $('.products-view').data("catalogId"),
        $preloader = $('.preloader');

    //Параметры для ajax запроса. Очищаются после каждого запроса
    var ajaxParams = '',
        //с какого элемента выводить товары. По умолчанию 24, т.к. это число товаров уже выведенно.
        offset = 24;

    var Data = function () {
        this.m = []; // filter for products [{id: 1, values: [1,2,3...n]}, ...]

        this.price = {
            key: 'p',
            value: [0, 999999],
            active: false,
            getUrl: function () {
                return this.active ?
                    this.key + ":" + (this.value[0] * 100) + "-" + (this.value[1] * 100) + ";" : "";
            }
        };

        this.order = {
            key: 'o',
            value: -1,
            getUrl: function () {
                return  this.value == -1 ?
                    "" : this.key + ":" + (this.value) + ";";
            }
        };

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
         * Set price, order, products
         *
         * @param {string} s
         */
        this.set = function(s) {
            var par = location.search.split('f=')[1];
            if(par)
                s = par;
            var d = s.split(';'),
                item, _t;
            for(var i = 0 ; i < d.length - 1; i++) {
                item = d[i].split(':');
                if(item[0] == this.price.key) {
                    this.setPrice(item);
                } else if(item[0] == this.order.key) {
                    this.setOrder(item[1]);
                } else {
                    _t = item[1].split(',');
                    for(var k = 0 ; k < _t.length; k++) {
                        this.add({id: item[0], value: _t[k]});
                    }
                }
            }
        };
        /**
         * @param {array} item - ["p", "100-250"]
         */
        this.setPrice = function (item) {
            var _t = item[1].split('-');
            this.price.value[0] = _t[0];
            this.price.value[1] = _t[1];
        };
        /**
         * @param {number} order
         */
        this.setOrder = function (order) {
            this.order.value = order;
        };

        this.getUrl = function () {
            var url = "";
            url += this.price.getUrl();
            url += data.serialize();
            url += this.order.getUrl();
            return url ? "f=" + url : "";
        };
    };

    var data = new Data();

    var Price = function () {
        var $slider = $( ".slider-range" ),
            $from =  $( "#price_from"),
            $to =  $( "#price_to");

        var min, max;

        return {
            init: function () {
                var self = this;

                min = parseInt(arguments[0]/100 || $from.data('min'));
                max = parseInt(arguments[1]/100 || $to.data('max'));

                self.setValue(min, max);
                $slider.slider({
                    range: true,
                    min: $from.data('min'),
                    max: $to.data('max'),
                    values: [ min, max ],
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
                data.price.value[0] = min;
                data.price.value[1] = max;
                data.price.active = this.isActive();
            },

            isActive: function () {
                return !(min == data.price.value[0] && max == data.price.value[1]);
            },

            getInterval: function () {
                return ($from.val() * 100) + "-" + ($to.val() * 100);
            }

        };
    };

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
       // ddd.set("p:16-500;954:1945166136,903495414;358:1829271172,796428635;");
        ddd.set("?p:90-445;864:353509045,69753746;");
        log(ddd);
        log(ddd.serialize());
    }

    var Filters = {
        init: function() {
            this.event();
            this.setFilter();
            offset = this.getOffset();
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
                self.getFilteredData();
            });
            $filterApply.on('click', function () {
                self.getFilteredData();
            });
            $showMore.on('click', function () {
                self.showMore();
            });
            $sort.on('change', function () {
                self.sort($(this).val());
            });
        },
        /*
         * @param {number} value
         */
        sort: function (value) {
            App.log("Select order: " + value);
            data.setOrder(value);
            this.getFilteredData();
        },

        /*
         * Setup filter
         */
        setFilter: function () {
            data.set(window.location.search);
            if(data.m.length > 0 || data.price.value[0] > 0) {
                Price().init(data.price.value[0], data.price.value[1]);
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
            if(data.order.value >= 0) {
                $sort.val(data.order.value);
            }
        },

        getFilteredData: function (append) {
            var url = '/catalog/view/'+catalogID+'?' + data.getUrl();

            $loader.css('opacity', 1);

            this.getData(url, function (data) {
                $content.html(data);
                App.reinit();
                setTimeout(function() {
                    $loader.css('opacity', 0);
                }, 150);
            });

            offset = this.getOffset();

            setTimeout("jQuery('#preloader').animate({'opacity' : '0'},300,function(){jQuery('#preloader').hide()})",800);
            setTimeout("jQuery('.preloader_hide, .selector_open').animate({'opacity' : '1'},500)",800);

            // Change url
            if(url != window.location){
                window.history.pushState(null, null, url);
            }
        },

        /*
         * @param {string} url
         * @param {function} callback(resultData)
         */
        getData: function (url, handler) {
            $.ajax({
                url:     url + '&ajax=1',
                success: handler,
                error: function () {
                    App.log('Error');
                    App.message('Произошла ошибка', false);
                }
            });
        },

        showMore: function () {
            var url = '/catalog/view/'+catalogID+'?' + data.getUrl() + '&offset=' + offset;
            var self = this;
            $showMore.hide()
            $preloader.show();
            self.getData(url, function (data) {
                $content.append(data);
                App.reinit();
                setTimeout(function() {
                    $showMore.show();
                    $preloader.hide();
                }, 150);
                $('html, body').animate({
                    scrollTop: self.getLastListProduct().offset().top - 20
                }, 500);
            });

            offset = this.getOffset();
        },

        /*
         * @returns {object} - DOM элемент
         */
        getLastListProduct: function () {
            return $('.products-list').last();
        },

        /*
         * Находит offset последнего элемента.
         *
         * @returns integer
         */
        getOffset: function () {
            var _offset = this.getLastListProduct().data('offset');
            return _offset || 1;
        }
    };

    Filters.init();

})(jQuery);