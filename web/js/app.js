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
var Cart = (function($){

    var $cart = $('.shopping');

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // These variables are used to limit Ajax requests
    var presentOperation = 0, delayOperation = 0,

        products = [];

    var total = function () {
        var $total = $("#total span");
        var $sum = $('.cart-item__sum span');

        return {
            get: function () {
                return $total.text();
            },
            update: function () {
                var result = 0;
                $sum.each(function (index, el) {
                    result += parseFloat($(el).text());
                });
                $total.text(result);
            }
        };
    };

    return {
        /**
         * @access public
         */
        init: function() {
            this.event();
        },
        event: function() {

        },

        /**
         * Change event input number
         *
         * @access public
         * @param {object} element
         * @param {number} val
         */
        update: function (element, val) {
            var id = element.data('productId'),
                $item = $('.cart-item[data-product-id="' + id + '"]'),
                quantity = $item.find('.cart-item__quantity'),
                sum = $item.find('.cart-item__sum span'),
                price = parseFloat($item.data('productPrice'));

            quantity.text(val);
            sum.text(Math.ceil( parseInt(val) * price * 100 ) / 100);
            total().update();

            this.addProduct(id, val);

            this.addToCart();
        },

        /**
         * Add products to cart. Creates an array changes, then all changes are sent in the request
         *
         */
        addToCart: function () {
            var self = this;
            presentOperation++;
            setTimeout(function () {
                delayOperation++;
                if(delayOperation == presentOperation) {
                    self.saveData('/cart/add-multiple', products);
                    products = [];
                }
            }, 1000);
        },

        /**
         * Get data using ajax
         *
         * @param {string} url example:"/controller/action"
         * @param {object} options
         */
        saveData: function(url, options) {
            var self = this;
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: {
                    products: options,
                    _csrf: csrfToken
                },
                success: function(result){
                    self.render(result);
                },
                error: function () {
                    console.log('Error');
                    App.message('Произошла ошибка', false);
                }
            });
        },

        /**
         * Check for existence. If exist, it adds an element to the array, otherwise change the count
         *
         * @param {number} id Product ID
         * @param {number} count
         * @return {boolean}
         */
        addProduct: function (id, count) {
            var i;
            for(i = 0; i < products.length; i++) {
                if(products[i].id == id) {
                    products[i].count = count;
                    return true;
                }
            }
            products.push({
                id: id,
                count: count
            });
            return false;
        },

        /**
         * Render html in cart element
         *
         * @param {string} result Result from server
         */
        render: function (result) {
            $cart.html(result);
            App.message('Корзина обновленна', true);
        }

    };

})(jQuery);

(function($){

    var $el = $('.transform'),
        $catalog = $('.catalog');

    var Catalog = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $el.on('click', function () {
                self.shown();
            });
            $catalog.mousemove(function (event) {
                self.dropMove($(this), true);
            });
            $catalog.mouseout(function (event) {
                self.dropMove($(this), false);
            });
        },
        shown: function () {
            if($el.hasClass('shown')){
                $el.removeClass('shown');
            } else {
                $el.addClass('shown');
            }
        },
        /**
         * Visible drop menu
         *
         * @param {object} dom
         * @param {boolean} action
         */
        dropMove: function (dom, action) {
            if(action) {
                dom.addClass('active');
            } else {
                dom.removeClass('active');
            }
        }
    };

    Catalog.init();

})(jQuery);
(function($){

    "use strict";

    var $handler = $('.qp-collapse-handler');


    var CollapseFilter = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $handler.on('click', function () {
                self.toggle($(this), $('#' + $(this).data('toggle')));
            });
        },

        toggle: function (handler, el) {
            if(window.innerWidth < 769) {
                if(el.hasClass('shown')){
                    el.removeClass('shown');
                    el.addClass('closed');
                    el.fadeOut(200);
                    handler.removeClass('activated');
                } else {
                    el.addClass('shown');
                    el.removeClass('closed');
                    el.fadeIn(100);
                    handler.addClass('activated');
                }
            }
        }
    };

    CollapseFilter.init();

})(jQuery);
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
        $preloader = $('.preloader'),
        $limit = $('#limit');

    //Параметры для ajax запроса. Очищаются после каждого запроса
    var ajaxParams = '',
        //с какого элемента выводить товары. По умолчанию 24, т.к. это число товаров уже выведенно.
        offset = 24,
        productCount = $showMore.data('productCount'),
        appliedFilters = [];

    var Data = function () {
        this.m = []; // filter for products [{id: 1, values: [1,2,3...n]}, ...]

        this.limit = {
            value: 0,
            active: false,
            getUrl: function (urlLength) {
                var url = this.active ? "limit=" + this.value : "";
                if (urlLength > 0 && url.length > 0) {
                    return '&' + url;
                }
                return url;
            }
        };

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
                return  this.value === -1 ?
                    "" : this.key + ":" + (this.value) + ";";
            }
        };

        /**
         * Add object to this.m
         *
         * @param {Object} obj
         * @param {number} obj.id
         * @param {number} obj.value
         * @return number
         */
        this.add = function (obj) {
            for(var i = 0; i < this.m.length; i++) {
                if(this.m[i].id === obj.id) {
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
        /**
         * Порядок имеет значение
         * @returns {string}
         */
        this.getUrl = function () {
            var url = "";
            url += this.price.getUrl();
            url += data.serialize();
            url += this.order.getUrl();
            url += this.limit.getUrl(url.length);
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
            this.visibleShowMoreBtn();
            this.setOffset();
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
            $limit.on('change', function () {
                self.limit($(this).val());
            });
        },
        /*
         * @param {number} value
         */
        sort: function (value) {
            data.setOrder(value);
            this.getFilteredData();
        },
        /*
         * @param {number} value
         */
        limit: function (value) {
            data.limit.active = true;
            data.limit.value = value;
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
                        curName = cur.data('name'),
                        cutTitle = cur.data('title');
                    var applies = {
                        title: cutTitle,
                        value: []
                    };
                    for(var i = 0; i < data.m.length; i++) {
                        if(data.m[i].id == curName) {

                            for(var k = 0; k < data.m[i].values.length; k++) {
                                if(data.m[i].values[k] == curVal) {
                                    applies.value.push(cur.parent().find('label').text().replace(/\s{2,}/g, ' '));
                                    cur.prop("checked", true);
                                }
                            }

                        }
                    }
                    if (applies.value.length > 0) {
                        appliedFilters.push(applies);
                    }
                });
                console.log(appliedFilters);
            } else {
                Price().init();
            }
            if(data.order.value >= 0) {
                $sort.val(data.order.value);
            }
        },

        setAppliedFilters: function () {
            appliedFilters.map(function (item) {

            });
        },

        getFilteredData: function () {
            var url = '/catalog/view/'+catalogID+'?' + data.getUrl();

            $loader.css('opacity', 1);
            var self = this;

            this.getData(url, function (data) {
                $content.html(data);
                App.reinit();
                setTimeout(function() {
                    $loader.css('opacity', 0);
                }, 150);
                self.setOffset();
            });

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

        visibleShowMoreBtn: function () {
            if (offset >= productCount) {
                $showMore.hide();
            } else {
                $showMore.show();
            }
        },

        showMore: function () {
            var url = '/catalog/view/'+catalogID+'?' + data.getUrl() + '&offset=' + offset;
            var self = this;
            $showMore.hide();
            $preloader.show();
            self.getData(url, function (data) {
                $content.append(data);
                App.reinit();
                setTimeout(function() {
                    self.visibleShowMoreBtn();
                    $preloader.hide();
                }, 150);
                $('html, body').animate({
                    scrollTop: self.getLastListProduct().offset().top - 20
                }, 500);
                self.setOffset();
            });
        },

        /*
         * @returns {object} - DOM элемент
         */
        getLastListProduct: function () {
            return $('.products-list').last();
        },

        /*
         * Находит offset последнего элемента.
         */
        setOffset: function () {
            offset = this.getLastListProduct().data('offset');
            this.visibleShowMoreBtn();
        }
    };

    Filters.init();

})(jQuery);
/*
 * Project: Bootstrap Notify = v3.1.5
 * Description: Turns standard Bootstrap alerts into "Growl-like" notifications.
 * Author: Mouse0270 aka Robert McIntosh
 * License: MIT License
 * Website: https://github.com/mouse0270/bootstrap-growl
 */

/* global define:false, require: false, jQuery:false */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS
        factory(require('jquery'));
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {
    // Create the defaults once
    var defaults = {
        element: 'body',
        position: null,
        type: "info",
        allow_dismiss: true,
        allow_duplicates: true,
        newest_on_top: false,
        showProgressbar: false,
        placement: {
            from: "top",
            align: "right"
        },
        offset: 20,
        spacing: 10,
        z_index: 1031,
        delay: 5000,
        timer: 1000,
        url_target: '_blank',
        mouse_over: null,
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        onShow: null,
        onShown: null,
        onClose: null,
        onClosed: null,
        onClick: null,
        icon_type: 'class',
        template: '<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'
    };

    String.format = function () {
        var args = arguments;
        var str = arguments[0];
        return str.replace(/(\{\{\d\}\}|\{\d\})/g, function (str) {
            if (str.substring(0, 2) === "{{") return str;
            var num = parseInt(str.match(/\d/)[0]);
            return args[num + 1];
        });
    };

    function isDuplicateNotification(notification) {
        var isDupe = false;

        $('[data-notify="container"]').each(function (i, el) {
            var $el = $(el);
            var title = $el.find('[data-notify="title"]').html().trim();
            var message = $el.find('[data-notify="message"]').html().trim();

            // The input string might be different than the actual parsed HTML string!
            // (<br> vs <br /> for example)
            // So we have to force-parse this as HTML here!
            var isSameTitle = title === $("<div>" + notification.settings.content.title + "</div>").html().trim();
            var isSameMsg = message === $("<div>" + notification.settings.content.message + "</div>").html().trim();
            var isSameType = $el.hasClass('alert-' + notification.settings.type);

            if (isSameTitle && isSameMsg && isSameType) {
                //we found the dupe. Set the var and stop checking.
                isDupe = true;
            }
            return !isDupe;
        });

        return isDupe;
    }

    function Notify(element, content, options) {
        // Setup Content of Notify
        var contentObj = {
            content: {
                message: typeof content === 'object' ? content.message : content,
                title: content.title ? content.title : '',
                icon: content.icon ? content.icon : '',
                url: content.url ? content.url : '#',
                target: content.target ? content.target : '-'
            }
        };

        options = $.extend(true, {}, contentObj, options);
        this.settings = $.extend(true, {}, defaults, options);
        this._defaults = defaults;
        if (this.settings.content.target === "-") {
            this.settings.content.target = this.settings.url_target;
        }
        this.animations = {
            start: 'webkitAnimationStart oanimationstart MSAnimationStart animationstart',
            end: 'webkitAnimationEnd oanimationend MSAnimationEnd animationend'
        };

        if (typeof this.settings.offset === 'number') {
            this.settings.offset = {
                x: this.settings.offset,
                y: this.settings.offset
            };
        }

        //if duplicate messages are not allowed, then only continue if this new message is not a duplicate of one that it already showing
        if (this.settings.allow_duplicates || (!this.settings.allow_duplicates && !isDuplicateNotification(this))) {
            this.init();
        }
    }

    $.extend(Notify.prototype, {
        init: function () {
            var self = this;

            this.buildNotify();
            if (this.settings.content.icon) {
                this.setIcon();
            }
            if (this.settings.content.url != "#") {
                this.styleURL();
            }
            this.styleDismiss();
            this.placement();
            this.bind();

            this.notify = {
                $ele: this.$ele,
                update: function (command, update) {
                    var commands = {};
                    if (typeof command === "string") {
                        commands[command] = update;
                    } else {
                        commands = command;
                    }
                    for (var cmd in commands) {
                        switch (cmd) {
                            case "type":
                                this.$ele.removeClass('alert-' + self.settings.type);
                                this.$ele.find('[data-notify="progressbar"] > .progress-bar').removeClass('progress-bar-' + self.settings.type);
                                self.settings.type = commands[cmd];
                                this.$ele.addClass('alert-' + commands[cmd]).find('[data-notify="progressbar"] > .progress-bar').addClass('progress-bar-' + commands[cmd]);
                                break;
                            case "icon":
                                var $icon = this.$ele.find('[data-notify="icon"]');
                                if (self.settings.icon_type.toLowerCase() === 'class') {
                                    $icon.removeClass(self.settings.content.icon).addClass(commands[cmd]);
                                } else {
                                    if (!$icon.is('img')) {
                                        $icon.find('img');
                                    }
                                    $icon.attr('src', commands[cmd]);
                                }
                                self.settings.content.icon = commands[command];
                                break;
                            case "progress":
                                var newDelay = self.settings.delay - (self.settings.delay * (commands[cmd] / 100));
                                this.$ele.data('notify-delay', newDelay);
                                this.$ele.find('[data-notify="progressbar"] > div').attr('aria-valuenow', commands[cmd]).css('width', commands[cmd] + '%');
                                break;
                            case "url":
                                this.$ele.find('[data-notify="url"]').attr('href', commands[cmd]);
                                break;
                            case "target":
                                this.$ele.find('[data-notify="url"]').attr('target', commands[cmd]);
                                break;
                            default:
                                this.$ele.find('[data-notify="' + cmd + '"]').html(commands[cmd]);
                        }
                    }
                    var posX = this.$ele.outerHeight() + parseInt(self.settings.spacing) + parseInt(self.settings.offset.y);
                    self.reposition(posX);
                },
                close: function () {
                    self.close();
                }
            };

        },
        buildNotify: function () {
            var content = this.settings.content;
            this.$ele = $(String.format(this.settings.template, this.settings.type, content.title, content.message, content.url, content.target));
            this.$ele.attr('data-notify-position', this.settings.placement.from + '-' + this.settings.placement.align);
            if (!this.settings.allow_dismiss) {
                this.$ele.find('[data-notify="dismiss"]').css('display', 'none');
            }
            if ((this.settings.delay <= 0 && !this.settings.showProgressbar) || !this.settings.showProgressbar) {
                this.$ele.find('[data-notify="progressbar"]').remove();
            }
        },
        setIcon: function () {
            if (this.settings.icon_type.toLowerCase() === 'class') {
                this.$ele.find('[data-notify="icon"]').addClass(this.settings.content.icon);
            } else {
                if (this.$ele.find('[data-notify="icon"]').is('img')) {
                    this.$ele.find('[data-notify="icon"]').attr('src', this.settings.content.icon);
                } else {
                    this.$ele.find('[data-notify="icon"]').append('<img src="' + this.settings.content.icon + '" alt="Notify Icon" />');
                }
            }
        },
        styleDismiss: function () {
            this.$ele.find('[data-notify="dismiss"]').css({
                position: 'absolute',
                right: '10px',
                top: '5px',
                zIndex: this.settings.z_index + 2
            });
        },
        styleURL: function () {
            this.$ele.find('[data-notify="url"]').css({
                backgroundImage: 'url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)',
                height: '100%',
                left: 0,
                position: 'absolute',
                top: 0,
                width: '100%',
                zIndex: this.settings.z_index + 1
            });
        },
        placement: function () {
            var self = this,
                offsetAmt = this.settings.offset.y,
                css = {
                    display: 'inline-block',
                    margin: '0px auto',
                    position: this.settings.position ? this.settings.position : (this.settings.element === 'body' ? 'fixed' : 'absolute'),
                    transition: 'all .5s ease-in-out',
                    zIndex: this.settings.z_index
                },
                hasAnimation = false,
                settings = this.settings;

            $('[data-notify-position="' + this.settings.placement.from + '-' + this.settings.placement.align + '"]:not([data-closing="true"])').each(function () {
                offsetAmt = Math.max(offsetAmt, parseInt($(this).css(settings.placement.from)) + parseInt($(this).outerHeight()) + parseInt(settings.spacing));
            });
            if (this.settings.newest_on_top === true) {
                offsetAmt = this.settings.offset.y;
            }
            css[this.settings.placement.from] = offsetAmt + 'px';

            switch (this.settings.placement.align) {
                case "left":
                case "right":
                    css[this.settings.placement.align] = this.settings.offset.x + 'px';
                    break;
                case "center":
                    css.left = 0;
                    css.right = 0;
                    break;
            }
            this.$ele.css(css).addClass(this.settings.animate.enter);
            $.each(Array('webkit-', 'moz-', 'o-', 'ms-', ''), function (index, prefix) {
                self.$ele[0].style[prefix + 'AnimationIterationCount'] = 1;
            });

            $(this.settings.element).append(this.$ele);

            if (this.settings.newest_on_top === true) {
                offsetAmt = (parseInt(offsetAmt) + parseInt(this.settings.spacing)) + this.$ele.outerHeight();
                this.reposition(offsetAmt);
            }

            if ($.isFunction(self.settings.onShow)) {
                self.settings.onShow.call(this.$ele);
            }

            this.$ele.one(this.animations.start, function () {
                hasAnimation = true;
            }).one(this.animations.end, function () {
                self.$ele.removeClass(self.settings.animate.enter);
                if ($.isFunction(self.settings.onShown)) {
                    self.settings.onShown.call(this);
                }
            });

            setTimeout(function () {
                if (!hasAnimation) {
                    if ($.isFunction(self.settings.onShown)) {
                        self.settings.onShown.call(this);
                    }
                }
            }, 600);
        },
        bind: function () {
            var self = this;

            this.$ele.find('[data-notify="dismiss"]').on('click', function () {
                self.close();
            });

            if ($.isFunction(self.settings.onClick)) {
                this.$ele.on('click', function (event) {
                    if (event.target != self.$ele.find('[data-notify="dismiss"]')[0]) {
                        self.settings.onClick.call(this, event);
                    }
                });
            }

            this.$ele.mouseover(function () {
                $(this).data('data-hover', "true");
            }).mouseout(function () {
                $(this).data('data-hover', "false");
            });
            this.$ele.data('data-hover', "false");

            if (this.settings.delay > 0) {
                self.$ele.data('notify-delay', self.settings.delay);
                var timer = setInterval(function () {
                    var delay = parseInt(self.$ele.data('notify-delay')) - self.settings.timer;
                    if ((self.$ele.data('data-hover') === 'false' && self.settings.mouse_over === "pause") || self.settings.mouse_over != "pause") {
                        var percent = ((self.settings.delay - delay) / self.settings.delay) * 100;
                        self.$ele.data('notify-delay', delay);
                        self.$ele.find('[data-notify="progressbar"] > div').attr('aria-valuenow', percent).css('width', percent + '%');
                    }
                    if (delay <= -(self.settings.timer)) {
                        clearInterval(timer);
                        self.close();
                    }
                }, self.settings.timer);
            }
        },
        close: function () {
            var self = this,
                posX = parseInt(this.$ele.css(this.settings.placement.from)),
                hasAnimation = false;

            this.$ele.attr('data-closing', 'true').addClass(this.settings.animate.exit);
            self.reposition(posX);

            if ($.isFunction(self.settings.onClose)) {
                self.settings.onClose.call(this.$ele);
            }

            this.$ele.one(this.animations.start, function () {
                hasAnimation = true;
            }).one(this.animations.end, function () {
                $(this).remove();
                if ($.isFunction(self.settings.onClosed)) {
                    self.settings.onClosed.call(this);
                }
            });

            setTimeout(function () {
                if (!hasAnimation) {
                    self.$ele.remove();
                    if (self.settings.onClosed) {
                        self.settings.onClosed(self.$ele);
                    }
                }
            }, 600);
        },
        reposition: function (posX) {
            var self = this,
                notifies = '[data-notify-position="' + this.settings.placement.from + '-' + this.settings.placement.align + '"]:not([data-closing="true"])',
                $elements = this.$ele.nextAll(notifies);
            if (this.settings.newest_on_top === true) {
                $elements = this.$ele.prevAll(notifies);
            }
            $elements.each(function () {
                $(this).css(self.settings.placement.from, posX);
                posX = (parseInt(posX) + parseInt(self.settings.spacing)) + $(this).outerHeight();
            });
        }
    });

    $.notify = function (content, options) {
        var plugin = new Notify(this, content, options);
        return plugin.notify;
    };
    $.notifyDefaults = function (options) {
        defaults = $.extend(true, {}, defaults, options);
        return defaults;
    };

    $.notifyClose = function (selector) {

        if (typeof selector === "undefined" || selector === "all") {
            $('[data-notify]').find('[data-notify="dismiss"]').trigger('click');
        }else if(selector === 'success' || selector === 'info' || selector === 'warning' || selector === 'danger'){
            $('.alert-' + selector + '[data-notify]').find('[data-notify="dismiss"]').trigger('click');
        } else if(selector){
            $(selector + '[data-notify]').find('[data-notify="dismiss"]').trigger('click');
        }
        else {
            $('[data-notify-position="' + selector + '"]').find('[data-notify="dismiss"]').trigger('click');
        }
    };

    $.notifyCloseExcept = function (selector) {

        if(selector === 'success' || selector === 'info' || selector === 'warning' || selector === 'danger'){
            $('[data-notify]').not('.alert-' + selector).find('[data-notify="dismiss"]').trigger('click');
        } else{
            $('[data-notify]').not(selector).find('[data-notify="dismiss"]').trigger('click');
        }
    };


}));
(function($)
{
    // This plugin is changed for project qpvl.ru
    /*
     Numeric Stepper jQuery plugin

     Licensed under MIT:

     Copyright (c) Luciano Longo

     Permission is hereby granted, free of charge, to any person obtaining a copy of
     this software and associated documentation files (the "Software"), to deal in
     the Software without restriction, including without limitation the rights to
     use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
     the Software, and to permit persons to whom the Software is furnished to do so,
     subject to the following conditions:

     The above copyright notice and this permission notice shall be included in all
     copies or substantial portions of the Software.

     THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
     FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
     COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
     IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
     CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
     */
    $.fn.stepper = function( options )
    {
        var _defaults = {
            type: 'float',                  // or 'int'
            floatPrecission: 2,             // decimal precission
            ui: true,                       // +/- buttons
            allowWheel: true,               // mouse wheel
            allowArrows: true,              // keyboar arrows (up, down)
            arrowStep: 1,                   // ammount to increment with arrow keys
            wheelStep: 1,                   // ammount to increment with mouse wheel
            limit: [null, null],            // [min, max] limit
            preventWheelAcceleration: true, // In some systems, like OS X, the wheel has acceleration, enable this option to prevent it
            incrementButton: '&blacktriangle;',
            decrementButton: '&blacktriangledown;',

            // Events
            onStep: null,   // fn( [number] val, [bool] up )
            onWheel: null,  // fn( [number] val, [bool] up )
            onArrow: null,  // fn( [number] val, [bool] up )
            onButton: null, // fn( [number] val, [bool] up )
            onKeyUp: null   // fn( [number] val )
        };

        return $(this).each(function()
        {
            var $data = $(this).data();
            delete $data.stepper;

            var _options = $.extend({}, _defaults, options, $data),
                $this = $(this),
                $wrap = $('<div class="product-counter"/>');

            if( $this.data('stepper') )
                return;

            $wrap.insertAfter( $this );
            $this.appendTo( $wrap );

            /* API */

            $this.stepper = (function()
            {
                return {
                    limit: _limit,
                    decimalRound: _decimal_round,
                    onStep: function( callback ) { _options.onStep = callback; },
                    onWheel: function( callback ) { _options.onWheel = callback; },
                    onArrow: function( callback ) { _options.onArrow = callback; },
                    onButton: function( callback ) { _options.onButton = callback; },
                    onKeyUp: function( callback ) { _options.onKeyUp = callback; }
                };
            })();

            $this.data('stepper', $this.stepper);

            /* UI */

            if( _options.ui )
            {
                var $btnDown = $('<a class="btn btn-down">'+_options.decrementButton+'</a>').appendTo( $wrap ),
                    $btnUp   = $('<a class="btn btn-up">'+_options.incrementButton+'</a>').appendTo( $wrap );

                var stepInterval;

                $btnUp.mousedown(function(e)
                {
                    e.preventDefault();

                    var val = _step( _options.arrowStep );
                    _evt('Button', [val, true]);
                });

                $btnDown.mousedown(function(e)
                {
                    e.preventDefault();

                    var val = _step( -_options.arrowStep );
                    _evt('Button', [val, false]);
                });

                $(document).mouseup(function()
                {
                    clearInterval( stepInterval );
                });
            }


            /* Events */

            if( _options.allowWheel )
            {
                $wrap.bind('DOMMouseScroll', _handleWheel);
                $wrap.bind('mousewheel', _handleWheel);
            }

            $wrap.keydown(function(e)
            {
                var key = e.which,
                    val = $this.val();

                if( _options.allowArrows )
                    switch( key )
                    {
                        // Up arrow
                        case 38:
                            val = _step( _options.arrowStep );
                            _evt('Arrow', [val, true]);
                            break;

                        // Down arrow
                        case 40:
                            val = _step( -_options.arrowStep );
                            _evt('Arrow', [val, false]);
                            break;
                    }

                // Only arrow keys, misc modifier chars and numbers and period (including keypad)
                if( ( key < 37 && key > 40 ) || ( key > 57 && key < 91 ) || ( key > 105 && key != 110 && key != 190 ) )
                    e.preventDefault();

                // Allow only one peroid and only if float is enabled
                if( _options.type == 'float' && $.inArray( key, [ 110, 190 ] ) != -1 && val.indexOf('.') != -1 )
                    e.preventDefault();
            }).keyup(function(e)
            {
                _evt('KeyUp', [$this.val()] );
            });

            function _handleWheel(e)
            {
                // Prevent actual page scrolling
                e.preventDefault();

                var d,
                    evt = e.originalEvent;

                if( evt.wheelDelta )
                    d = evt.wheelDelta / 120;
                else if( evt.detail )
                    d = -evt.detail / 3;

                if( d )
                {
                    if( _options.preventWheelAcceleration )
                        d = d < 0 ? -1 : 1;

                    var val = _step( _options.wheelStep * d );

                    _evt('Wheel', [val, d > 0]);
                }
            }

            function _step( step )
            {
                if( ! $this.val() )
                    $this.val( 0 );

                var typeCast = _options.type == 'int' ? parseInt : parseFloat,
                    val      = _limit( typeCast( $this.val() ) + step );

                $this.val( val );

                _evt('Step', [val, step > 0]);

                return val;
            }

            function _evt( name, args )
            {
                var callback = _options['on'+name];

                if( typeof callback == 'function' )
                    callback.apply( $this, args );
            }

            function _limit( num )
            {
                var min = _options.limit[0],
                    max = _options.limit[1];

                if( min !== null && num < min )
                    num = min;
                else if( max !== null && num > max )
                    num = max;

                return _decimal_round( num );
            }

            function _decimal_round( num, precission )
            {
                if( typeof precission == 'undefined' )
                    precission =  _options.floatPrecission;

                var pow = Math.pow(10, precission);
                num = Math.round( num * pow ) / pow;

                return num;
            }
        });
    }
})(jQuery);
var Product = (function($){

    var $inputCount = $('.product-count'),
        $compare,
        $cart = $('.shopping');

    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Time in milliseconds between Ajax requests
    const interval = 900;
    var timer = true;

    return {
        /**
         * @access public
         */
        init: function() {
            $compare = $('.btn-compare');
            this.event();
        },
        event: function() {
            var self = this;

            $compare.on('click', function () {
                if(timer) {
                    self.addToCart($(this));
                    timer = false;
                    setTimeout(function() {
                        timer = true;
                    }, interval);
                }
            });
        },

        /**
         * Add product to cart
         *
         * @param {object} el dom element (button)
         */
        addToCart: function (el) {
            var id = el.data('productId') || 0,
                count = el.attr('data-product-count') || 0;

            this.getData('/cart/add', {
                id: id,
                count: count
            });
            //this.fly(id);
        },

        fly: function (id) {
            // Animation goods movement to cart
            var $imgToFly = $('img[data-product-id=' + id + ']');
            if ($imgToFly) {
                var $imgClone = $imgToFly.clone()
                    .offset($imgToFly.offset())
                    .css({
                        'opacity': '0.7',
                        'position': 'absolute',
                        'height': '150px',
                        'width': '150px',
                        'z-index': '1000'
                    })
                    .appendTo($('body'))
                    .animate({
                        'top': $cart.offset().top + 10,
                        'left': $cart.offset().left + 50,
                        'width': 35,
                        'height': 35
                    }, 'slow');

                $imgClone.animate({'width': 0, 'height': 0}, function () {
                    $(this).detach();
                });
            }
        },

        /**
         * Get data using ajax
         *
         * @param {string} url example:"/controller/action"
         * @param {object} options
         */
        getData: function(url, options) {
            var self = this;
            $.ajax({
                url: url,
                dataType: "html",
                type: "POST",
                data: {
                    product_id: options.id,
                    product_count: options.count,
                    _csrf: csrfToken
                },
                success: function(result){
                    self.render(result);
                    App.message('Товар успешно добавлен в корзину', true);
                },
                error: function () {
                    console.log('Error');
                    App.message('Произошла ошибка', false);
                }

            });
        },

        /**
         * Render html in cart element
         *
         * @param {string} result Result from server
         */
        render: function (result) {
            $cart.html(result);
        },

        /**
         * Change event input number
         *
         * @access public
         * @param {object} element
         * @param {number} val
         */
        update: function (element, val) {
            var id = element.data('productId');

            $compare.each(function (index, el) {

                if($(el).data('productId') === id) {
                    $(el).attr('data-product-count', val);
                }
            });
        },
    };


})(jQuery);
String.prototype.score=function(e,f){if(this===e)return 1;if(""===e)return 0;var d=0,a,g=this.toLowerCase(),n=this.length,h=e.toLowerCase(),k=e.length,b;a=0;var l=1,m,c;f&&(m=1-f);if(f)for(c=0;c<k;c+=1)b=g.indexOf(h[c],a),-1===b?l+=m:(a===b?a=.7:(a=.1," "===this[b-1]&&(a+=.8)),this[b]===e[c]&&(a+=.1),d+=a,a=b+1);else for(c=0;c<k;c+=1){b=g.indexOf(h[c],a);if(-1===b)return 0;a===b?a=.7:(a=.1," "===this[b-1]&&(a+=.8));this[b]===e[c]&&(a+=.1);d+=a;a=b+1}d=.5*(d/n+d/k)/l;h[0]===g[0]&&.85>d&&(d+=.15);return d};

(function($){

    "use strict";

    var $open = $('.btn-search-modal'),
        $input = $('#search-input'),
        $closeSearch = $('.js-search-close'),
        $modal = $('#search-modal'),
        $mobileFooter  = $('.mobile-footer'),
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
            var search = query.toLowerCase(),
                tokens = '',
                textLen = text.length,
                searchLen = search.length,
                search_position = 0;

            for (var n = 0; n < textLen; n++) {
                var text_char = text[n];
                if (search_position < searchLen &&
                    text_char === search[search_position]) {
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

        if (request.term === "") {
            response([]);
            return;
        }
        function addMatches(dataList, matches) {
            var counter = 0;
            dataList.map(function(item) {
                if (item.label === null || counter > 100) {
                    return false;
                }
                var rating = item.label.score(request.term, 0.5);
                if (rating > 0.4) {
                    var result = fuzzy_match(item.label.toLowerCase(), request.term);
                    if (result !== null) {
                        matches.push({
                            id: item.id,
                            rating: rating,
                            label: result,
                            url: item.url
                        });
                        counter++;
                    }
                }
            });
        }
        
        function sort(arr) {
            if (arr.length === 0) {
                return;
            }
            arr = arr.sort(function (a, b) {
                return a.rating < b.rating
            });
        }

        addMatches(dataProducts, matchesProd);
        addMatches(dataCategories, matchesCat);

        sort(matchesProd);
        sort(matchesCat);

        const LENGTH = 10;
        var _len1 = matchesProd.length,
            _len2 = matchesCat.length;

        if (_len1 > LENGTH) {
            matchesProd = matchesProd.slice(0, 10);
        }
        if (_len2 > LENGTH) {
            matchesCat = matchesCat.slice(0, 10);
        }
        if (_len1 > 0) {
            matchesProd.unshift({
                label: 0
            });
        }
        if (_len2 > 0) {
            matchesCat.unshift({
                label: 1
            });
        }

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
                    $input.val( ui.item.label.replace(/(<([^>]+)>)/ig, '') );
                    return false;
                }
            });
            jQuery.ui.autocomplete.prototype._renderItem = function( ul, item ) {
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

            jQuery.ui.autocomplete.prototype._resizeMenu = function () {
                if (skel.vars.mobile) {
                    this.menu.element.outerWidth( $input.parent().width() + 50 );
                } else {
                    this.menu.element.outerWidth( $input.parent().width() );
                }
                this.menu.element.outerHeight( $(window).height() - 110);
                var left = $input.parent().offset().left;
                this.menu.element.css({left: left + 'px'});
            }

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
            $modal.on('hidden.bs.modal', function () {
                $mobileFooter.removeClass('mini');
            });
            $searchHeaderInput.on('focus', function () {
                $modal.modal('show');
            });
            $searchHeaderInput.on('focus', function () {
                $modal.modal('show');
            });
            $(window).resize(function () {
                // jQuery.ui.autocomplete.prototype._resizeMenu();
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
            $mobileFooter.addClass('mini');
        }
    };

    Search.init();

})(jQuery);
!function(a,b,c,d){"use strict";function k(a,b,c){return setTimeout(q(a,c),b)}function l(a,b,c){return Array.isArray(a)?(m(a,c[b],c),!0):!1}function m(a,b,c){var e;if(a)if(a.forEach)a.forEach(b,c);else if(a.length!==d)for(e=0;e<a.length;)b.call(c,a[e],e,a),e++;else for(e in a)a.hasOwnProperty(e)&&b.call(c,a[e],e,a)}function n(a,b,c){for(var e=Object.keys(b),f=0;f<e.length;)(!c||c&&a[e[f]]===d)&&(a[e[f]]=b[e[f]]),f++;return a}function o(a,b){return n(a,b,!0)}function p(a,b,c){var e,d=b.prototype;e=a.prototype=Object.create(d),e.constructor=a,e._super=d,c&&n(e,c)}function q(a,b){return function(){return a.apply(b,arguments)}}function r(a,b){return typeof a==g?a.apply(b?b[0]||d:d,b):a}function s(a,b){return a===d?b:a}function t(a,b,c){m(x(b),function(b){a.addEventListener(b,c,!1)})}function u(a,b,c){m(x(b),function(b){a.removeEventListener(b,c,!1)})}function v(a,b){for(;a;){if(a==b)return!0;a=a.parentNode}return!1}function w(a,b){return a.indexOf(b)>-1}function x(a){return a.trim().split(/\s+/g)}function y(a,b,c){if(a.indexOf&&!c)return a.indexOf(b);for(var d=0;d<a.length;){if(c&&a[d][c]==b||!c&&a[d]===b)return d;d++}return-1}function z(a){return Array.prototype.slice.call(a,0)}function A(a,b,c){for(var d=[],e=[],f=0;f<a.length;){var g=b?a[f][b]:a[f];y(e,g)<0&&d.push(a[f]),e[f]=g,f++}return c&&(d=b?d.sort(function(a,c){return a[b]>c[b]}):d.sort()),d}function B(a,b){for(var c,f,g=b[0].toUpperCase()+b.slice(1),h=0;h<e.length;){if(c=e[h],f=c?c+g:b,f in a)return f;h++}return d}function D(){return C++}function E(a){var b=a.ownerDocument;return b.defaultView||b.parentWindow}function ab(a,b){var c=this;this.manager=a,this.callback=b,this.element=a.element,this.target=a.options.inputTarget,this.domHandler=function(b){r(a.options.enable,[a])&&c.handler(b)},this.init()}function bb(a){var b,c=a.options.inputClass;return b=c?c:H?wb:I?Eb:G?Gb:rb,new b(a,cb)}function cb(a,b,c){var d=c.pointers.length,e=c.changedPointers.length,f=b&O&&0===d-e,g=b&(Q|R)&&0===d-e;c.isFirst=!!f,c.isFinal=!!g,f&&(a.session={}),c.eventType=b,db(a,c),a.emit("hammer.input",c),a.recognize(c),a.session.prevInput=c}function db(a,b){var c=a.session,d=b.pointers,e=d.length;c.firstInput||(c.firstInput=gb(b)),e>1&&!c.firstMultiple?c.firstMultiple=gb(b):1===e&&(c.firstMultiple=!1);var f=c.firstInput,g=c.firstMultiple,h=g?g.center:f.center,i=b.center=hb(d);b.timeStamp=j(),b.deltaTime=b.timeStamp-f.timeStamp,b.angle=lb(h,i),b.distance=kb(h,i),eb(c,b),b.offsetDirection=jb(b.deltaX,b.deltaY),b.scale=g?nb(g.pointers,d):1,b.rotation=g?mb(g.pointers,d):0,fb(c,b);var k=a.element;v(b.srcEvent.target,k)&&(k=b.srcEvent.target),b.target=k}function eb(a,b){var c=b.center,d=a.offsetDelta||{},e=a.prevDelta||{},f=a.prevInput||{};(b.eventType===O||f.eventType===Q)&&(e=a.prevDelta={x:f.deltaX||0,y:f.deltaY||0},d=a.offsetDelta={x:c.x,y:c.y}),b.deltaX=e.x+(c.x-d.x),b.deltaY=e.y+(c.y-d.y)}function fb(a,b){var f,g,h,j,c=a.lastInterval||b,e=b.timeStamp-c.timeStamp;if(b.eventType!=R&&(e>N||c.velocity===d)){var k=c.deltaX-b.deltaX,l=c.deltaY-b.deltaY,m=ib(e,k,l);g=m.x,h=m.y,f=i(m.x)>i(m.y)?m.x:m.y,j=jb(k,l),a.lastInterval=b}else f=c.velocity,g=c.velocityX,h=c.velocityY,j=c.direction;b.velocity=f,b.velocityX=g,b.velocityY=h,b.direction=j}function gb(a){for(var b=[],c=0;c<a.pointers.length;)b[c]={clientX:h(a.pointers[c].clientX),clientY:h(a.pointers[c].clientY)},c++;return{timeStamp:j(),pointers:b,center:hb(b),deltaX:a.deltaX,deltaY:a.deltaY}}function hb(a){var b=a.length;if(1===b)return{x:h(a[0].clientX),y:h(a[0].clientY)};for(var c=0,d=0,e=0;b>e;)c+=a[e].clientX,d+=a[e].clientY,e++;return{x:h(c/b),y:h(d/b)}}function ib(a,b,c){return{x:b/a||0,y:c/a||0}}function jb(a,b){return a===b?S:i(a)>=i(b)?a>0?T:U:b>0?V:W}function kb(a,b,c){c||(c=$);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return Math.sqrt(d*d+e*e)}function lb(a,b,c){c||(c=$);var d=b[c[0]]-a[c[0]],e=b[c[1]]-a[c[1]];return 180*Math.atan2(e,d)/Math.PI}function mb(a,b){return lb(b[1],b[0],_)-lb(a[1],a[0],_)}function nb(a,b){return kb(b[0],b[1],_)/kb(a[0],a[1],_)}function rb(){this.evEl=pb,this.evWin=qb,this.allow=!0,this.pressed=!1,ab.apply(this,arguments)}function wb(){this.evEl=ub,this.evWin=vb,ab.apply(this,arguments),this.store=this.manager.session.pointerEvents=[]}function Ab(){this.evTarget=yb,this.evWin=zb,this.started=!1,ab.apply(this,arguments)}function Bb(a,b){var c=z(a.touches),d=z(a.changedTouches);return b&(Q|R)&&(c=A(c.concat(d),"identifier",!0)),[c,d]}function Eb(){this.evTarget=Db,this.targetIds={},ab.apply(this,arguments)}function Fb(a,b){var c=z(a.touches),d=this.targetIds;if(b&(O|P)&&1===c.length)return d[c[0].identifier]=!0,[c,c];var e,f,g=z(a.changedTouches),h=[],i=this.target;if(f=c.filter(function(a){return v(a.target,i)}),b===O)for(e=0;e<f.length;)d[f[e].identifier]=!0,e++;for(e=0;e<g.length;)d[g[e].identifier]&&h.push(g[e]),b&(Q|R)&&delete d[g[e].identifier],e++;return h.length?[A(f.concat(h),"identifier",!0),h]:void 0}function Gb(){ab.apply(this,arguments);var a=q(this.handler,this);this.touch=new Eb(this.manager,a),this.mouse=new rb(this.manager,a)}function Pb(a,b){this.manager=a,this.set(b)}function Qb(a){if(w(a,Mb))return Mb;var b=w(a,Nb),c=w(a,Ob);return b&&c?Nb+" "+Ob:b||c?b?Nb:Ob:w(a,Lb)?Lb:Kb}function Yb(a){this.id=D(),this.manager=null,this.options=o(a||{},this.defaults),this.options.enable=s(this.options.enable,!0),this.state=Rb,this.simultaneous={},this.requireFail=[]}function Zb(a){return a&Wb?"cancel":a&Ub?"end":a&Tb?"move":a&Sb?"start":""}function $b(a){return a==W?"down":a==V?"up":a==T?"left":a==U?"right":""}function _b(a,b){var c=b.manager;return c?c.get(a):a}function ac(){Yb.apply(this,arguments)}function bc(){ac.apply(this,arguments),this.pX=null,this.pY=null}function cc(){ac.apply(this,arguments)}function dc(){Yb.apply(this,arguments),this._timer=null,this._input=null}function ec(){ac.apply(this,arguments)}function fc(){ac.apply(this,arguments)}function gc(){Yb.apply(this,arguments),this.pTime=!1,this.pCenter=!1,this._timer=null,this._input=null,this.count=0}function hc(a,b){return b=b||{},b.recognizers=s(b.recognizers,hc.defaults.preset),new kc(a,b)}function kc(a,b){b=b||{},this.options=o(b,hc.defaults),this.options.inputTarget=this.options.inputTarget||a,this.handlers={},this.session={},this.recognizers=[],this.element=a,this.input=bb(this),this.touchAction=new Pb(this,this.options.touchAction),lc(this,!0),m(b.recognizers,function(a){var b=this.add(new a[0](a[1]));a[2]&&b.recognizeWith(a[2]),a[3]&&b.requireFailure(a[3])},this)}function lc(a,b){var c=a.element;m(a.options.cssProps,function(a,d){c.style[B(c.style,d)]=b?a:""})}function mc(a,c){var d=b.createEvent("Event");d.initEvent(a,!0,!0),d.gesture=c,c.target.dispatchEvent(d)}var e=["","webkit","moz","MS","ms","o"],f=b.createElement("div"),g="function",h=Math.round,i=Math.abs,j=Date.now,C=1,F=/mobile|tablet|ip(ad|hone|od)|android/i,G="ontouchstart"in a,H=B(a,"PointerEvent")!==d,I=G&&F.test(navigator.userAgent),J="touch",K="pen",L="mouse",M="kinect",N=25,O=1,P=2,Q=4,R=8,S=1,T=2,U=4,V=8,W=16,X=T|U,Y=V|W,Z=X|Y,$=["x","y"],_=["clientX","clientY"];ab.prototype={handler:function(){},init:function(){this.evEl&&t(this.element,this.evEl,this.domHandler),this.evTarget&&t(this.target,this.evTarget,this.domHandler),this.evWin&&t(E(this.element),this.evWin,this.domHandler)},destroy:function(){this.evEl&&u(this.element,this.evEl,this.domHandler),this.evTarget&&u(this.target,this.evTarget,this.domHandler),this.evWin&&u(E(this.element),this.evWin,this.domHandler)}};var ob={mousedown:O,mousemove:P,mouseup:Q},pb="mousedown",qb="mousemove mouseup";p(rb,ab,{handler:function(a){var b=ob[a.type];b&O&&0===a.button&&(this.pressed=!0),b&P&&1!==a.which&&(b=Q),this.pressed&&this.allow&&(b&Q&&(this.pressed=!1),this.callback(this.manager,b,{pointers:[a],changedPointers:[a],pointerType:L,srcEvent:a}))}});var sb={pointerdown:O,pointermove:P,pointerup:Q,pointercancel:R,pointerout:R},tb={2:J,3:K,4:L,5:M},ub="pointerdown",vb="pointermove pointerup pointercancel";a.MSPointerEvent&&(ub="MSPointerDown",vb="MSPointerMove MSPointerUp MSPointerCancel"),p(wb,ab,{handler:function(a){var b=this.store,c=!1,d=a.type.toLowerCase().replace("ms",""),e=sb[d],f=tb[a.pointerType]||a.pointerType,g=f==J,h=y(b,a.pointerId,"pointerId");e&O&&(0===a.button||g)?0>h&&(b.push(a),h=b.length-1):e&(Q|R)&&(c=!0),0>h||(b[h]=a,this.callback(this.manager,e,{pointers:b,changedPointers:[a],pointerType:f,srcEvent:a}),c&&b.splice(h,1))}});var xb={touchstart:O,touchmove:P,touchend:Q,touchcancel:R},yb="touchstart",zb="touchstart touchmove touchend touchcancel";p(Ab,ab,{handler:function(a){var b=xb[a.type];if(b===O&&(this.started=!0),this.started){var c=Bb.call(this,a,b);b&(Q|R)&&0===c[0].length-c[1].length&&(this.started=!1),this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:J,srcEvent:a})}}});var Cb={touchstart:O,touchmove:P,touchend:Q,touchcancel:R},Db="touchstart touchmove touchend touchcancel";p(Eb,ab,{handler:function(a){var b=Cb[a.type],c=Fb.call(this,a,b);c&&this.callback(this.manager,b,{pointers:c[0],changedPointers:c[1],pointerType:J,srcEvent:a})}}),p(Gb,ab,{handler:function(a,b,c){var d=c.pointerType==J,e=c.pointerType==L;if(d)this.mouse.allow=!1;else if(e&&!this.mouse.allow)return;b&(Q|R)&&(this.mouse.allow=!0),this.callback(a,b,c)},destroy:function(){this.touch.destroy(),this.mouse.destroy()}});var Hb=B(f.style,"touchAction"),Ib=Hb!==d,Jb="compute",Kb="auto",Lb="manipulation",Mb="none",Nb="pan-x",Ob="pan-y";Pb.prototype={set:function(a){a==Jb&&(a=this.compute()),Ib&&(this.manager.element.style[Hb]=a),this.actions=a.toLowerCase().trim()},update:function(){this.set(this.manager.options.touchAction)},compute:function(){var a=[];return m(this.manager.recognizers,function(b){r(b.options.enable,[b])&&(a=a.concat(b.getTouchAction()))}),Qb(a.join(" "))},preventDefaults:function(a){if(!Ib){var b=a.srcEvent,c=a.offsetDirection;if(this.manager.session.prevented)return b.preventDefault(),void 0;var d=this.actions,e=w(d,Mb),f=w(d,Ob),g=w(d,Nb);return e||f&&c&X||g&&c&Y?this.preventSrc(b):void 0}},preventSrc:function(a){this.manager.session.prevented=!0,a.preventDefault()}};var Rb=1,Sb=2,Tb=4,Ub=8,Vb=Ub,Wb=16,Xb=32;Yb.prototype={defaults:{},set:function(a){return n(this.options,a),this.manager&&this.manager.touchAction.update(),this},recognizeWith:function(a){if(l(a,"recognizeWith",this))return this;var b=this.simultaneous;return a=_b(a,this),b[a.id]||(b[a.id]=a,a.recognizeWith(this)),this},dropRecognizeWith:function(a){return l(a,"dropRecognizeWith",this)?this:(a=_b(a,this),delete this.simultaneous[a.id],this)},requireFailure:function(a){if(l(a,"requireFailure",this))return this;var b=this.requireFail;return a=_b(a,this),-1===y(b,a)&&(b.push(a),a.requireFailure(this)),this},dropRequireFailure:function(a){if(l(a,"dropRequireFailure",this))return this;a=_b(a,this);var b=y(this.requireFail,a);return b>-1&&this.requireFail.splice(b,1),this},hasRequireFailures:function(){return this.requireFail.length>0},canRecognizeWith:function(a){return!!this.simultaneous[a.id]},emit:function(a){function d(d){b.manager.emit(b.options.event+(d?Zb(c):""),a)}var b=this,c=this.state;Ub>c&&d(!0),d(),c>=Ub&&d(!0)},tryEmit:function(a){return this.canEmit()?this.emit(a):(this.state=Xb,void 0)},canEmit:function(){for(var a=0;a<this.requireFail.length;){if(!(this.requireFail[a].state&(Xb|Rb)))return!1;a++}return!0},recognize:function(a){var b=n({},a);return r(this.options.enable,[this,b])?(this.state&(Vb|Wb|Xb)&&(this.state=Rb),this.state=this.process(b),this.state&(Sb|Tb|Ub|Wb)&&this.tryEmit(b),void 0):(this.reset(),this.state=Xb,void 0)},process:function(){},getTouchAction:function(){},reset:function(){}},p(ac,Yb,{defaults:{pointers:1},attrTest:function(a){var b=this.options.pointers;return 0===b||a.pointers.length===b},process:function(a){var b=this.state,c=a.eventType,d=b&(Sb|Tb),e=this.attrTest(a);return d&&(c&R||!e)?b|Wb:d||e?c&Q?b|Ub:b&Sb?b|Tb:Sb:Xb}}),p(bc,ac,{defaults:{event:"pan",threshold:10,pointers:1,direction:Z},getTouchAction:function(){var a=this.options.direction,b=[];return a&X&&b.push(Ob),a&Y&&b.push(Nb),b},directionTest:function(a){var b=this.options,c=!0,d=a.distance,e=a.direction,f=a.deltaX,g=a.deltaY;return e&b.direction||(b.direction&X?(e=0===f?S:0>f?T:U,c=f!=this.pX,d=Math.abs(a.deltaX)):(e=0===g?S:0>g?V:W,c=g!=this.pY,d=Math.abs(a.deltaY))),a.direction=e,c&&d>b.threshold&&e&b.direction},attrTest:function(a){return ac.prototype.attrTest.call(this,a)&&(this.state&Sb||!(this.state&Sb)&&this.directionTest(a))},emit:function(a){this.pX=a.deltaX,this.pY=a.deltaY;var b=$b(a.direction);b&&this.manager.emit(this.options.event+b,a),this._super.emit.call(this,a)}}),p(cc,ac,{defaults:{event:"pinch",threshold:0,pointers:2},getTouchAction:function(){return[Mb]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.scale-1)>this.options.threshold||this.state&Sb)},emit:function(a){if(this._super.emit.call(this,a),1!==a.scale){var b=a.scale<1?"in":"out";this.manager.emit(this.options.event+b,a)}}}),p(dc,Yb,{defaults:{event:"press",pointers:1,time:500,threshold:5},getTouchAction:function(){return[Kb]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,e=a.deltaTime>b.time;if(this._input=a,!d||!c||a.eventType&(Q|R)&&!e)this.reset();else if(a.eventType&O)this.reset(),this._timer=k(function(){this.state=Vb,this.tryEmit()},b.time,this);else if(a.eventType&Q)return Vb;return Xb},reset:function(){clearTimeout(this._timer)},emit:function(a){this.state===Vb&&(a&&a.eventType&Q?this.manager.emit(this.options.event+"up",a):(this._input.timeStamp=j(),this.manager.emit(this.options.event,this._input)))}}),p(ec,ac,{defaults:{event:"rotate",threshold:0,pointers:2},getTouchAction:function(){return[Mb]},attrTest:function(a){return this._super.attrTest.call(this,a)&&(Math.abs(a.rotation)>this.options.threshold||this.state&Sb)}}),p(fc,ac,{defaults:{event:"swipe",threshold:10,velocity:.65,direction:X|Y,pointers:1},getTouchAction:function(){return bc.prototype.getTouchAction.call(this)},attrTest:function(a){var c,b=this.options.direction;return b&(X|Y)?c=a.velocity:b&X?c=a.velocityX:b&Y&&(c=a.velocityY),this._super.attrTest.call(this,a)&&b&a.direction&&a.distance>this.options.threshold&&i(c)>this.options.velocity&&a.eventType&Q},emit:function(a){var b=$b(a.direction);b&&this.manager.emit(this.options.event+b,a),this.manager.emit(this.options.event,a)}}),p(gc,Yb,{defaults:{event:"tap",pointers:1,taps:1,interval:300,time:250,threshold:2,posThreshold:10},getTouchAction:function(){return[Lb]},process:function(a){var b=this.options,c=a.pointers.length===b.pointers,d=a.distance<b.threshold,e=a.deltaTime<b.time;if(this.reset(),a.eventType&O&&0===this.count)return this.failTimeout();if(d&&e&&c){if(a.eventType!=Q)return this.failTimeout();var f=this.pTime?a.timeStamp-this.pTime<b.interval:!0,g=!this.pCenter||kb(this.pCenter,a.center)<b.posThreshold;this.pTime=a.timeStamp,this.pCenter=a.center,g&&f?this.count+=1:this.count=1,this._input=a;var h=this.count%b.taps;if(0===h)return this.hasRequireFailures()?(this._timer=k(function(){this.state=Vb,this.tryEmit()},b.interval,this),Sb):Vb}return Xb},failTimeout:function(){return this._timer=k(function(){this.state=Xb},this.options.interval,this),Xb},reset:function(){clearTimeout(this._timer)},emit:function(){this.state==Vb&&(this._input.tapCount=this.count,this.manager.emit(this.options.event,this._input))}}),hc.VERSION="2.0.4",hc.defaults={domEvents:!1,touchAction:Jb,enable:!0,inputTarget:null,inputClass:null,preset:[[ec,{enable:!1}],[cc,{enable:!1},["rotate"]],[fc,{direction:X}],[bc,{direction:X},["swipe"]],[gc],[gc,{event:"doubletap",taps:2},["tap"]],[dc]],cssProps:{userSelect:"default",touchSelect:"none",touchCallout:"none",contentZooming:"none",userDrag:"none",tapHighlightColor:"rgba(0,0,0,0)"}};var ic=1,jc=2;kc.prototype={set:function(a){return n(this.options,a),a.touchAction&&this.touchAction.update(),a.inputTarget&&(this.input.destroy(),this.input.target=a.inputTarget,this.input.init()),this},stop:function(a){this.session.stopped=a?jc:ic},recognize:function(a){var b=this.session;if(!b.stopped){this.touchAction.preventDefaults(a);var c,d=this.recognizers,e=b.curRecognizer;(!e||e&&e.state&Vb)&&(e=b.curRecognizer=null);for(var f=0;f<d.length;)c=d[f],b.stopped===jc||e&&c!=e&&!c.canRecognizeWith(e)?c.reset():c.recognize(a),!e&&c.state&(Sb|Tb|Ub)&&(e=b.curRecognizer=c),f++}},get:function(a){if(a instanceof Yb)return a;for(var b=this.recognizers,c=0;c<b.length;c++)if(b[c].options.event==a)return b[c];return null},add:function(a){if(l(a,"add",this))return this;var b=this.get(a.options.event);return b&&this.remove(b),this.recognizers.push(a),a.manager=this,this.touchAction.update(),a},remove:function(a){if(l(a,"remove",this))return this;var b=this.recognizers;return a=this.get(a),b.splice(y(b,a),1),this.touchAction.update(),this},on:function(a,b){var c=this.handlers;return m(x(a),function(a){c[a]=c[a]||[],c[a].push(b)}),this},off:function(a,b){var c=this.handlers;return m(x(a),function(a){b?c[a].splice(y(c[a],b),1):delete c[a]}),this},emit:function(a,b){this.options.domEvents&&mc(a,b);var c=this.handlers[a]&&this.handlers[a].slice();if(c&&c.length){b.type=a,b.preventDefault=function(){b.srcEvent.preventDefault()};for(var d=0;d<c.length;)c[d](b),d++}},destroy:function(){this.element&&lc(this,!1),this.handlers={},this.session={},this.input.destroy(),this.element=null}},n(hc,{INPUT_START:O,INPUT_MOVE:P,INPUT_END:Q,INPUT_CANCEL:R,STATE_POSSIBLE:Rb,STATE_BEGAN:Sb,STATE_CHANGED:Tb,STATE_ENDED:Ub,STATE_RECOGNIZED:Vb,STATE_CANCELLED:Wb,STATE_FAILED:Xb,DIRECTION_NONE:S,DIRECTION_LEFT:T,DIRECTION_RIGHT:U,DIRECTION_UP:V,DIRECTION_DOWN:W,DIRECTION_HORIZONTAL:X,DIRECTION_VERTICAL:Y,DIRECTION_ALL:Z,Manager:kc,Input:ab,TouchAction:Pb,TouchInput:Eb,MouseInput:rb,PointerEventInput:wb,TouchMouseInput:Gb,SingleTouchInput:Ab,Recognizer:Yb,AttrRecognizer:ac,Tap:gc,Pan:bc,Swipe:fc,Pinch:cc,Rotate:ec,Press:dc,on:t,off:u,each:m,merge:o,extend:n,inherit:p,bindFn:q,prefixed:B}),typeof define==g&&define.amd?define(function(){return hc}):"undefined"!=typeof module&&module.exports?module.exports=hc:a[c]=hc}(window,document,"Hammer");

(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery', 'hammerjs'], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'), require('hammerjs'));
    } else {
        factory(jQuery, Hammer);
    }
}(function($, Hammer) {
    function hammerify(el, options) {
        var $el = $(el);
        if(!$el.data("hammer")) {
            $el.data("hammer", new Hammer($el[0], options));
        }
    }

    $.fn.hammer = function(options) {
        return this.each(function() {
            hammerify(this, options);
        });
    };

    // extend the emit method to also trigger jQuery events
    Hammer.Manager.prototype.emit = (function(originalEmit) {
        return function(type, data) {
            originalEmit.call(this, type, data);
            $(this.element).trigger({
                type: type,
                gesture: data
            });
        };
    })(Hammer.Manager.prototype.emit);
}));

(function ($) {

    var methods = {
        init : function(options) {
            var defaults = {
                menuWidth: 300,
                edge: 'left',
                closeOnClick: false,
                draggable: true
            };
            options = $.extend(defaults, options);

            $(this).each(function(){
                var $this = $(this);
                var menuId = $this.attr('data-activates');
                var menu = $("#"+ menuId);

                // Set to width
                if (options.menuWidth != 300) {
                    menu.css('width', options.menuWidth);
                }

                // Add Touch Area
                var $dragTarget = $('.drag-target[data-sidenav="' + menuId + '"]');
                if (options.draggable) {
                    // Regenerate dragTarget
                    if ($dragTarget.length) {
                        $dragTarget.remove();
                    }

                    $dragTarget = $('<div class="drag-target"></div>').attr('data-sidenav', menuId);
                    $('body').append($dragTarget);
                } else {
                    $dragTarget = $();
                }

                if (options.edge == 'left') {
                    menu.css('transform', 'translateX(-100%)');
                    $dragTarget.css({'left': 0}); // Add Touch Area
                }
                else {
                    menu.addClass('right-aligned') // Change text-alignment to right
                        .css('transform', 'translateX(100%)');
                    $dragTarget.css({'right': 0}); // Add Touch Area
                }

                // If fixed sidenav, bring menu out
                if (menu.hasClass('fixed')) {
                    if (window.innerWidth > 992) {
                        menu.css('transform', 'translateX(0)');
                    }
                }

                // Window resize to reset on large screens fixed
                if (menu.hasClass('fixed')) {
                    $(window).resize( function() {
                        if (window.innerWidth > 992) {
                            // Close menu if window is resized bigger than 992 and user has fixed sidenav
                            if ($('#sidenav-overlay').length !== 0 && menuOut) {
                                removeMenu(true);
                            }
                            else {
                                // menu.removeAttr('style');
                                menu.css('transform', 'translateX(0%)');
                                // menu.css('width', options.menuWidth);
                            }
                        }
                        else if (menuOut === false){
                            if (options.edge === 'left') {
                                menu.css('transform', 'translateX(-100%)');
                            } else {
                                menu.css('transform', 'translateX(100%)');
                            }

                        }

                    });
                }

                // if closeOnClick, then add close event for all a tags in side sideNav
                if (options.closeOnClick === true) {
                    menu.on("click.itemclick", "a:not(.collapsible-header)", function(){
                        removeMenu();
                    });
                }

                var removeMenu = function(restoreNav) {
                    panning = false;
                    menuOut = false;
                    // Reenable scrolling
                    $('body').css({
                        overflow: '',
                        width: ''
                    });

                    $('#sidenav-overlay').velocity({opacity: 0}, {duration: 200,
                        queue: false, easing: 'easeOutQuad',
                        complete: function() {
                            $(this).remove();
                        } });
                    if (options.edge === 'left') {
                        // Reset phantom div
                        $dragTarget.css({width: '', right: '', left: '0'});
                        menu.velocity(
                            {'translateX': '-100%'},
                            { duration: 200,
                                queue: false,
                                easing: 'easeOutCubic',
                                complete: function() {
                                    if (restoreNav === true) {
                                        // Restore Fixed sidenav
                                        menu.removeAttr('style');
                                        menu.css('width', options.menuWidth);
                                    }
                                }

                            });
                    }
                    else {
                        // Reset phantom div
                        $dragTarget.css({width: '', right: '0', left: ''});
                        menu.velocity(
                            {'translateX': '100%'},
                            { duration: 200,
                                queue: false,
                                easing: 'easeOutCubic',
                                complete: function() {
                                    if (restoreNav === true) {
                                        // Restore Fixed sidenav
                                        menu.removeAttr('style');
                                        menu.css('width', options.menuWidth);
                                    }
                                }
                            });
                    }
                };



                // Touch Event
                var panning = false;
                var menuOut = false;

                if (options.draggable) {
                    $dragTarget.on('click', function(){
                        if (menuOut) {
                            removeMenu();
                        }
                    });

                    $dragTarget.hammer({
                        prevent_default: false
                    }).bind('pan', function(e) {

                        if (e.gesture.pointerType == "touch") {

                            var direction = e.gesture.direction;
                            var x = e.gesture.center.x;
                            var y = e.gesture.center.y;
                            var velocityX = e.gesture.velocityX;

                            // Disable Scrolling
                            var $body = $('body');
                            var $overlay = $('#sidenav-overlay');
                            var oldWidth = $body.innerWidth();
                            $body.css('overflow', 'hidden');
                            $body.width(oldWidth);

                            // If overlay does not exist, create one and if it is clicked, close menu
                            if ($overlay.length === 0) {
                                $overlay = $('<div id="sidenav-overlay"></div>');
                                $overlay.css('opacity', 0).click( function(){
                                    removeMenu();
                                });
                                $('body').append($overlay);
                            }

                            // Keep within boundaries
                            if (options.edge === 'left') {
                                if (x > options.menuWidth) { x = options.menuWidth; }
                                else if (x < 0) { x = 0; }
                            }

                            if (options.edge === 'left') {
                                // Left Direction
                                if (x < (options.menuWidth / 2)) { menuOut = false; }
                                // Right Direction
                                else if (x >= (options.menuWidth / 2)) { menuOut = true; }
                                menu.css('transform', 'translateX(' + (x - options.menuWidth) + 'px)');
                            }
                            else {
                                // Left Direction
                                if (x < (window.innerWidth - options.menuWidth / 2)) {
                                    menuOut = true;
                                }
                                // Right Direction
                                else if (x >= (window.innerWidth - options.menuWidth / 2)) {
                                    menuOut = false;
                                }
                                var rightPos = (x - options.menuWidth / 2);
                                if (rightPos < 0) {
                                    rightPos = 0;
                                }

                                menu.css('transform', 'translateX(' + rightPos + 'px)');
                            }


                            // Percentage overlay
                            var overlayPerc;
                            if (options.edge === 'left') {
                                overlayPerc = x / options.menuWidth;
                                $overlay.velocity({opacity: overlayPerc }, {duration: 10, queue: false, easing: 'easeOutQuad'});
                            }
                            else {
                                overlayPerc = Math.abs((x - window.innerWidth) / options.menuWidth);
                                $overlay.velocity({opacity: overlayPerc }, {duration: 10, queue: false, easing: 'easeOutQuad'});
                            }
                        }

                    }).bind('panend', function(e) {

                        if (e.gesture.pointerType == "touch") {
                            var $overlay = $('#sidenav-overlay');
                            var velocityX = e.gesture.velocityX;
                            var x = e.gesture.center.x;
                            var leftPos = x - options.menuWidth;
                            var rightPos = x - options.menuWidth / 2;
                            if (leftPos > 0 ) {
                                leftPos = 0;
                            }
                            if (rightPos < 0) {
                                rightPos = 0;
                            }
                            panning = false;

                            if (options.edge === 'left') {
                                // If velocityX <= 0.3 then the user is flinging the menu closed so ignore menuOut
                                if ((menuOut && velocityX <= 0.3) || velocityX < -0.5) {
                                    // Return menu to open
                                    if (leftPos !== 0) {
                                        menu.velocity({'translateX': [0, leftPos]}, {duration: 300, queue: false, easing: 'easeOutQuad'});
                                    }

                                    $overlay.velocity({opacity: 1 }, {duration: 50, queue: false, easing: 'easeOutQuad'});
                                    $dragTarget.css({width: '50%', right: 0, left: ''});
                                    menuOut = true;
                                }
                                else if (!menuOut || velocityX > 0.3) {
                                    // Enable Scrolling
                                    $('body').css({
                                        overflow: '',
                                        width: ''
                                    });
                                    // Slide menu closed
                                    menu.velocity({'translateX': [-1 * options.menuWidth - 10, leftPos]}, {duration: 200, queue: false, easing: 'easeOutQuad'});
                                    $overlay.velocity({opacity: 0 }, {duration: 200, queue: false, easing: 'easeOutQuad',
                                        complete: function () {
                                            $(this).remove();
                                        }});
                                    $dragTarget.css({width: '10px', right: '', left: 0});
                                }
                            }
                            else {
                                if ((menuOut && velocityX >= -0.3) || velocityX > 0.5) {
                                    // Return menu to open
                                    if (rightPos !== 0) {
                                        menu.velocity({'translateX': [0, rightPos]}, {duration: 300, queue: false, easing: 'easeOutQuad'});
                                    }

                                    $overlay.velocity({opacity: 1 }, {duration: 50, queue: false, easing: 'easeOutQuad'});
                                    $dragTarget.css({width: '50%', right: '', left: 0});
                                    menuOut = true;
                                }
                                else if (!menuOut || velocityX < -0.3) {
                                    // Enable Scrolling
                                    $('body').css({
                                        overflow: '',
                                        width: ''
                                    });

                                    // Slide menu closed
                                    menu.velocity({'translateX': [options.menuWidth + 10, rightPos]}, {duration: 200, queue: false, easing: 'easeOutQuad'});
                                    $overlay.velocity({opacity: 0 }, {duration: 200, queue: false, easing: 'easeOutQuad',
                                        complete: function () {
                                            $(this).remove();
                                        }});
                                    $dragTarget.css({width: '10px', right: 0, left: ''});
                                }
                            }

                        }
                    });
                }

                $this.off('click.sidenav').on('click.sidenav', function() {
                    if (menuOut === true) {
                        menuOut = false;
                        panning = false;
                        removeMenu();
                    }
                    else {

                        // Disable Scrolling
                        var $body = $('body');
                        var $overlay = $('<div id="sidenav-overlay"></div>');
                        var oldWidth = $body.innerWidth();
                        $body.css('overflow', 'hidden');
                        $body.width(oldWidth);

                        // Push current drag target on top of DOM tree
                        $('body').append($dragTarget);

                        if (options.edge === 'left') {
                            $dragTarget.css({width: '50%', right: 0, left: ''});
                            menu.velocity({'translateX': [0, -1 * options.menuWidth]}, {duration: 300, queue: false, easing: 'easeOutQuad'});
                        }
                        else {
                            $dragTarget.css({width: '50%', right: '', left: 0});
                            menu.velocity({'translateX': [0, options.menuWidth]}, {duration: 300, queue: false, easing: 'easeOutQuad'});
                        }

                        $overlay.css('opacity', 0)
                            .click(function(){
                                menuOut = false;
                                panning = false;
                                removeMenu();
                                $overlay.velocity({opacity: 0}, {duration: 300, queue: false, easing: 'easeOutQuad',
                                    complete: function() {
                                        $(this).remove();
                                    } });

                            });
                        $('body').append($overlay);
                        $overlay.velocity({opacity: 1}, {duration: 300, queue: false, easing: 'easeOutQuad',
                            complete: function () {
                                menuOut = true;
                                panning = false;
                            }
                        });
                    }

                    return false;
                });
            });


        },
        destroy: function () {
            var $overlay = $('#sidenav-overlay');
            var $dragTarget = $('.drag-target[data-sidenav="' + $(this).attr('data-activates') + '"]');
            $overlay.trigger('click');
            $dragTarget.remove();
            $(this).off('click');
            $overlay.remove();
        },
        show : function() {
            this.trigger('click');
        },
        hide : function() {
            $('#sidenav-overlay').trigger('click');
        }
    };


    $.fn.sideNav = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.sideNav' );
        }
    }; // Plugin end
}( jQuery ));
/* skel.js v3.0.1 | (c) skel.io | MIT licensed */
var skel = function() {
    "use strict";
    var t = {
        breakpointIds: null,
        events: {},
        isInit: !1,
        obj: {
            attachments: {},
            breakpoints: {},
            head: null,
            states: {}
        },
        sd: "/",
        state: null,
        stateHandlers: {},
        stateId: "",
        vars: {},
        DOMReady: null,
        indexOf: null,
        isArray: null,
        iterate: null,
        matchesMedia: null,
        extend: function(e, n) {
            t.iterate(n, function(i) {
                t.isArray(n[i]) ? (t.isArray(e[i]) || (e[i] = []), t.extend(e[i], n[i])) : "object" == typeof n[i] ? ("object" != typeof e[i] && (e[i] = {}), t.extend(e[i], n[i])) : e[i] = n[i]
            })
        },
        newStyle: function(t) {
            var e = document.createElement("style");
            return e.type = "text/css", e.innerHTML = t, e
        },
        _canUse: null,
        canUse: function(e) {
            t._canUse || (t._canUse = document.createElement("div"));
            var n = t._canUse.style,
                i = e.charAt(0).toUpperCase() + e.slice(1);
            return e in n || "Moz" + i in n || "Webkit" + i in n || "O" + i in n || "ms" + i in n
        },
        on: function(e, n) {
            var i = e.split(/[\s]+/);
            return t.iterate(i, function(e) {
                var a = i[e];
                if (t.isInit) {
                    if ("init" == a) return void n();
                    if ("change" == a) n();
                    else {
                        var r = a.charAt(0);
                        if ("+" == r || "!" == r) {
                            var o = a.substring(1);
                            if (o in t.obj.breakpoints) if ("+" == r && t.obj.breakpoints[o].active) n();
                            else if ("!" == r && !t.obj.breakpoints[o].active) return void n()
                        }
                    }
                }
                t.events[a] || (t.events[a] = []), t.events[a].push(n)
            }), t
        },
        trigger: function(e) {
            return t.events[e] && 0 != t.events[e].length ? (t.iterate(t.events[e], function(n) {
                t.events[e][n]()
            }), t) : void 0
        },
        breakpoint: function(e) {
            return t.obj.breakpoints[e]
        },
        breakpoints: function(e) {
            function n(t, e) {
                this.name = this.id = t, this.media = e, this.active = !1, this.wasActive = !1
            }
            return n.prototype.matches = function() {
                return t.matchesMedia(this.media)
            }, n.prototype.sync = function() {
                this.wasActive = this.active, this.active = this.matches()
            }, t.iterate(e, function(i) {
                t.obj.breakpoints[i] = new n(i, e[i])
            }), window.setTimeout(function() {
                t.poll()
            }, 0), t
        },
        addStateHandler: function(e, n) {
            t.stateHandlers[e] = n
        },
        callStateHandler: function(e) {
            var n = t.stateHandlers[e]();
            t.iterate(n, function(e) {
                t.state.attachments.push(n[e])
            })
        },
        changeState: function(e) {
            t.iterate(t.obj.breakpoints, function(e) {
                t.obj.breakpoints[e].sync()
            }), t.vars.lastStateId = t.stateId, t.stateId = e, t.breakpointIds = t.stateId === t.sd ? [] : t.stateId.substring(1).split(t.sd), t.obj.states[t.stateId] ? t.state = t.obj.states[t.stateId] : (t.obj.states[t.stateId] = {
                attachments: []
            }, t.state = t.obj.states[t.stateId], t.iterate(t.stateHandlers, t.callStateHandler)), t.detachAll(t.state.attachments), t.attachAll(t.state.attachments), t.vars.stateId = t.stateId, t.vars.state = t.state, t.trigger("change"), t.iterate(t.obj.breakpoints, function(e) {
                t.obj.breakpoints[e].active ? t.obj.breakpoints[e].wasActive || t.trigger("+" + e) : t.obj.breakpoints[e].wasActive && t.trigger("-" + e)
            })
        },
        generateStateConfig: function(e, n) {
            var i = {};
            return t.extend(i, e), t.iterate(t.breakpointIds, function(e) {
                t.extend(i, n[t.breakpointIds[e]])
            }), i
        },
        getStateId: function() {
            var e = "";
            return t.iterate(t.obj.breakpoints, function(n) {
                var i = t.obj.breakpoints[n];
                i.matches() && (e += t.sd + i.id)
            }), e
        },
        poll: function() {
            var e = "";
            e = t.getStateId(), "" === e && (e = t.sd), e !== t.stateId && t.changeState(e)
        },
        _attach: null,
        attach: function(e) {
            var n = t.obj.head,
                i = e.element;
            return i.parentNode && i.parentNode.tagName ? !1 : (t._attach || (t._attach = n.firstChild), n.insertBefore(i, t._attach.nextSibling), e.permanent && (t._attach = i), !0)
        },
        attachAll: function(e) {
            var n = [];
            t.iterate(e, function(t) {
                n[e[t].priority] || (n[e[t].priority] = []), n[e[t].priority].push(e[t])
            }), n.reverse(), t.iterate(n, function(e) {
                t.iterate(n[e], function(i) {
                    t.attach(n[e][i])
                })
            })
        },
        detach: function(t) {
            var e = t.element;
            return t.permanent || !e.parentNode || e.parentNode && !e.parentNode.tagName ? !1 : (e.parentNode.removeChild(e), !0)
        },
        detachAll: function(e) {
            var n = {};
            t.iterate(e, function(t) {
                n[e[t].id] = !0
            }), t.iterate(t.obj.attachments, function(e) {
                e in n || t.detach(t.obj.attachments[e])
            })
        },
        attachment: function(e) {
            return e in t.obj.attachments ? t.obj.attachments[e] : null
        },
        newAttachment: function(e, n, i, a) {
            return t.obj.attachments[e] = {
                id: e,
                element: n,
                priority: i,
                permanent: a
            }
        },
        init: function() {
            t.initMethods(), t.initVars(), t.initEvents(), t.obj.head = document.getElementsByTagName("head")[0], t.isInit = !0, t.trigger("init")
        },
        initEvents: function() {
            t.on("resize", function() {
                t.poll()
            }), t.on("orientationChange", function() {
                t.poll()
            }), t.DOMReady(function() {
                t.trigger("ready")
            }), window.onload && t.on("load", window.onload), window.onload = function() {
                t.trigger("load")
            }, window.onresize && t.on("resize", window.onresize), window.onresize = function() {
                t.trigger("resize")
            }, window.onorientationchange && t.on("orientationChange", window.onorientationchange), window.onorientationchange = function() {
                t.trigger("orientationChange")
            }
        },
        initMethods: function() {
            document.addEventListener ? ! function(e, n) {
                t.DOMReady = n()
            }("domready", function() {
                function t(t) {
                    for (r = 1; t = n.shift();) t()
                }
                var e, n = [],
                    i = document,
                    a = "DOMContentLoaded",
                    r = /^loaded|^c/.test(i.readyState);
                return i.addEventListener(a, e = function() {
                    i.removeEventListener(a, e), t()
                }),
                    function(t) {
                        r ? t() : n.push(t)
                    }
            }) : ! function(e, n) {
                t.DOMReady = n()
            }("domready", function(t) {
                function e(t) {
                    for (h = 1; t = i.shift();) t()
                }
                var n, i = [],
                    a = !1,
                    r = document,
                    o = r.documentElement,
                    s = o.doScroll,
                    c = "DOMContentLoaded",
                    d = "addEventListener",
                    u = "onreadystatechange",
                    l = "readyState",
                    f = s ? /^loaded|^c/ : /^loaded|c/,
                    h = f.test(r[l]);
                return r[d] && r[d](c, n = function() {
                    r.removeEventListener(c, n, a), e()
                }, a), s && r.attachEvent(u, n = function() {
                    /^c/.test(r[l]) && (r.detachEvent(u, n), e())
                }), t = s ? function(e) {
                    self != top ? h ? e() : i.push(e) : function() {
                        try {
                            o.doScroll("left")
                        } catch (n) {
                            return setTimeout(function() {
                                t(e)
                            }, 50)
                        }
                        e()
                    }()
                } : function(t) {
                    h ? t() : i.push(t)
                }
            }), Array.prototype.indexOf ? t.indexOf = function(t, e) {
                return t.indexOf(e)
            } : t.indexOf = function(t, e) {
                if ("string" == typeof t) return t.indexOf(e);
                var n, i, a = e ? e : 0;
                if (!this) throw new TypeError;
                if (i = this.length, 0 === i || a >= i) return -1;
                for (0 > a && (a = i - Math.abs(a)), n = a; i > n; n++) if (this[n] === t) return n;
                return -1
            }, Array.isArray ? t.isArray = function(t) {
                return Array.isArray(t)
            } : t.isArray = function(t) {
                return "[object Array]" === Object.prototype.toString.call(t)
            }, Object.keys ? t.iterate = function(t, e) {
                if (!t) return [];
                var n, i = Object.keys(t);
                for (n = 0; i[n] && e(i[n], t[i[n]]) !== !1; n++);
            } : t.iterate = function(t, e) {
                if (!t) return [];
                var n;
                for (n in t) if (Object.prototype.hasOwnProperty.call(t, n) && e(n, t[n]) === !1) break
            }, window.matchMedia ? t.matchesMedia = function(t) {
                return "" == t ? !0 : window.matchMedia(t).matches
            } : window.styleMedia || window.media ? t.matchesMedia = function(t) {
                if ("" == t) return !0;
                var e = window.styleMedia || window.media;
                return e.matchMedium(t || "all")
            } : window.getComputedStyle ? t.matchesMedia = function(t) {
                if ("" == t) return !0;
                var e = document.createElement("style"),
                    n = document.getElementsByTagName("script")[0],
                    i = null;
                e.type = "text/css", e.id = "matchmediajs-test", n.parentNode.insertBefore(e, n), i = "getComputedStyle" in window && window.getComputedStyle(e, null) || e.currentStyle;
                var a = "@media " + t + "{ #matchmediajs-test { width: 1px; } }";
                return e.styleSheet ? e.styleSheet.cssText = a : e.textContent = a, "1px" === i.width
            } : t.matchesMedia = function(t) {
                if ("" == t) return !0;
                var e, n, i, a, r = {
                    "min-width": null,
                    "max-width": null
                }, o = !1;
                for (i = t.split(/\s+and\s+/), e = 0; e < i.length; e++) n = i[e], "(" == n.charAt(0) && (n = n.substring(1, n.length - 1), a = n.split(/:\s+/), 2 == a.length && (r[a[0].replace(/^\s+|\s+$/g, "")] = parseInt(a[1]), o = !0));
                if (!o) return !1;
                var s = document.documentElement.clientWidth,
                    c = document.documentElement.clientHeight;
                return null !== r["min-width"] && s < r["min-width"] || null !== r["max-width"] && s > r["max-width"] || null !== r["min-height"] && c < r["min-height"] || null !== r["max-height"] && c > r["max-height"] ? !1 : !0
            }, navigator.userAgent.match(/MSIE ([0-9]+)/) && RegExp.$1 < 9 && (t.newStyle = function(t) {
                var e = document.createElement("span");
                return e.innerHTML = ' <style type="text/css">' + t + "</style>", e
            })
        },
        initVars: function() {
            var e, n, i, a = navigator.userAgent;
            e = "other", n = 0, i = [
                ["firefox", /Firefox\/([0-9\.]+)/],
                ["bb", /BlackBerry.+Version\/([0-9\.]+)/],
                ["bb", /BB[0-9]+.+Version\/([0-9\.]+)/],
                ["opera", /OPR\/([0-9\.]+)/],
                ["opera", /Opera\/([0-9\.]+)/],
                ["edge", /Edge\/([0-9\.]+)/],
                ["safari", /Version\/([0-9\.]+).+Safari/],
                ["chrome", /Chrome\/([0-9\.]+)/],
                ["ie", /MSIE ([0-9]+)/],
                ["ie", /Trident\/.+rv:([0-9]+)/]
            ], t.iterate(i, function(t, i) {
                return a.match(i[1]) ? (e = i[0], n = parseFloat(RegExp.$1), !1) : void 0
            }), t.vars.browser = e, t.vars.browserVersion = n, e = "other", n = 0, i = [
                ["ios", /([0-9_]+) like Mac OS X/, function(t) {
                    return t.replace("_", ".").replace("_", "")
                }],
                ["ios", /CPU like Mac OS X/, function(t) {
                    return 0
                }],
                ["wp", /Windows Phone ([0-9\.]+)/, null],
                ["android", /Android ([0-9\.]+)/, null],
                ["mac", /Macintosh.+Mac OS X ([0-9_]+)/, function(t) {
                    return t.replace("_", ".").replace("_", "")
                }],
                ["windows", /Windows NT ([0-9\.]+)/, null],
                ["bb", /BlackBerry.+Version\/([0-9\.]+)/, null],
                ["bb", /BB[0-9]+.+Version\/([0-9\.]+)/, null]
            ], t.iterate(i, function(t, i) {
                return a.match(i[1]) ? (e = i[0], n = parseFloat(i[2] ? i[2](RegExp.$1) : RegExp.$1), !1) : void 0
            }), t.vars.os = e, t.vars.osVersion = n, t.vars.IEVersion = "ie" == t.vars.browser ? t.vars.browserVersion : 99, t.vars.touch = "wp" == t.vars.os ? navigator.msMaxTouchPoints > 0 : !! ("ontouchstart" in window), t.vars.mobile = "wp" == t.vars.os || "android" == t.vars.os || "ios" == t.vars.os || "bb" == t.vars.os
        }
    };
    return t.init(), t
}();
! function(t, e) {
    "function" == typeof define && define.amd ? define([], e) : "object" == typeof exports ? module.exports = e() : t.skel = e()
}(this, function() {
    return skel
});
/*! VelocityJS.org (1.2.3). (C) 2014 Julian Shapiro. MIT @license: en.wikipedia.org/wiki/MIT_License */
/*! VelocityJS.org jQuery Shim (1.0.1). (C) 2014 The jQuery Foundation. MIT @license: en.wikipedia.org/wiki/MIT_License. */
/*! Note that this has been modified by Materialize to confirm that Velocity is not already being imported. */
jQuery.Velocity?console.log("Velocity is already loaded. You may be needlessly importing Velocity again; note that Materialize includes Velocity."):(!function(e){function t(e){var t=e.length,a=r.type(e);return"function"===a||r.isWindow(e)?!1:1===e.nodeType&&t?!0:"array"===a||0===t||"number"==typeof t&&t>0&&t-1 in e}if(!e.jQuery){var r=function(e,t){return new r.fn.init(e,t)};r.isWindow=function(e){return null!=e&&e==e.window},r.type=function(e){return null==e?e+"":"object"==typeof e||"function"==typeof e?n[i.call(e)]||"object":typeof e},r.isArray=Array.isArray||function(e){return"array"===r.type(e)},r.isPlainObject=function(e){var t;if(!e||"object"!==r.type(e)||e.nodeType||r.isWindow(e))return!1;try{if(e.constructor&&!o.call(e,"constructor")&&!o.call(e.constructor.prototype,"isPrototypeOf"))return!1}catch(a){return!1}for(t in e);return void 0===t||o.call(e,t)},r.each=function(e,r,a){var n,o=0,i=e.length,s=t(e);if(a){if(s)for(;i>o&&(n=r.apply(e[o],a),n!==!1);o++);else for(o in e)if(n=r.apply(e[o],a),n===!1)break}else if(s)for(;i>o&&(n=r.call(e[o],o,e[o]),n!==!1);o++);else for(o in e)if(n=r.call(e[o],o,e[o]),n===!1)break;return e},r.data=function(e,t,n){if(void 0===n){var o=e[r.expando],i=o&&a[o];if(void 0===t)return i;if(i&&t in i)return i[t]}else if(void 0!==t){var o=e[r.expando]||(e[r.expando]=++r.uuid);return a[o]=a[o]||{},a[o][t]=n,n}},r.removeData=function(e,t){var n=e[r.expando],o=n&&a[n];o&&r.each(t,function(e,t){delete o[t]})},r.extend=function(){var e,t,a,n,o,i,s=arguments[0]||{},l=1,u=arguments.length,c=!1;for("boolean"==typeof s&&(c=s,s=arguments[l]||{},l++),"object"!=typeof s&&"function"!==r.type(s)&&(s={}),l===u&&(s=this,l--);u>l;l++)if(null!=(o=arguments[l]))for(n in o)e=s[n],a=o[n],s!==a&&(c&&a&&(r.isPlainObject(a)||(t=r.isArray(a)))?(t?(t=!1,i=e&&r.isArray(e)?e:[]):i=e&&r.isPlainObject(e)?e:{},s[n]=r.extend(c,i,a)):void 0!==a&&(s[n]=a));return s},r.queue=function(e,a,n){function o(e,r){var a=r||[];return null!=e&&(t(Object(e))?!function(e,t){for(var r=+t.length,a=0,n=e.length;r>a;)e[n++]=t[a++];if(r!==r)for(;void 0!==t[a];)e[n++]=t[a++];return e.length=n,e}(a,"string"==typeof e?[e]:e):[].push.call(a,e)),a}if(e){a=(a||"fx")+"queue";var i=r.data(e,a);return n?(!i||r.isArray(n)?i=r.data(e,a,o(n)):i.push(n),i):i||[]}},r.dequeue=function(e,t){r.each(e.nodeType?[e]:e,function(e,a){t=t||"fx";var n=r.queue(a,t),o=n.shift();"inprogress"===o&&(o=n.shift()),o&&("fx"===t&&n.unshift("inprogress"),o.call(a,function(){r.dequeue(a,t)}))})},r.fn=r.prototype={init:function(e){if(e.nodeType)return this[0]=e,this;throw new Error("Not a DOM node.")},offset:function(){var t=this[0].getBoundingClientRect?this[0].getBoundingClientRect():{top:0,left:0};return{top:t.top+(e.pageYOffset||document.scrollTop||0)-(document.clientTop||0),left:t.left+(e.pageXOffset||document.scrollLeft||0)-(document.clientLeft||0)}},position:function(){function e(){for(var e=this.offsetParent||document;e&&"html"===!e.nodeType.toLowerCase&&"static"===e.style.position;)e=e.offsetParent;return e||document}var t=this[0],e=e.apply(t),a=this.offset(),n=/^(?:body|html)$/i.test(e.nodeName)?{top:0,left:0}:r(e).offset();return a.top-=parseFloat(t.style.marginTop)||0,a.left-=parseFloat(t.style.marginLeft)||0,e.style&&(n.top+=parseFloat(e.style.borderTopWidth)||0,n.left+=parseFloat(e.style.borderLeftWidth)||0),{top:a.top-n.top,left:a.left-n.left}}};var a={};r.expando="velocity"+(new Date).getTime(),r.uuid=0;for(var n={},o=n.hasOwnProperty,i=n.toString,s="Boolean Number String Function Array Date RegExp Object Error".split(" "),l=0;l<s.length;l++)n["[object "+s[l]+"]"]=s[l].toLowerCase();r.fn.init.prototype=r.fn,e.Velocity={Utilities:r}}}(window),function(e){"object"==typeof module&&"object"==typeof module.exports?module.exports=e():"function"==typeof define&&define.amd?define(e):e()}(function(){return function(e,t,r,a){function n(e){for(var t=-1,r=e?e.length:0,a=[];++t<r;){var n=e[t];n&&a.push(n)}return a}function o(e){return m.isWrapped(e)?e=[].slice.call(e):m.isNode(e)&&(e=[e]),e}function i(e){var t=f.data(e,"velocity");return null===t?a:t}function s(e){return function(t){return Math.round(t*e)*(1/e)}}function l(e,r,a,n){function o(e,t){return 1-3*t+3*e}function i(e,t){return 3*t-6*e}function s(e){return 3*e}function l(e,t,r){return((o(t,r)*e+i(t,r))*e+s(t))*e}function u(e,t,r){return 3*o(t,r)*e*e+2*i(t,r)*e+s(t)}function c(t,r){for(var n=0;m>n;++n){var o=u(r,e,a);if(0===o)return r;var i=l(r,e,a)-t;r-=i/o}return r}function p(){for(var t=0;b>t;++t)w[t]=l(t*x,e,a)}function f(t,r,n){var o,i,s=0;do i=r+(n-r)/2,o=l(i,e,a)-t,o>0?n=i:r=i;while(Math.abs(o)>h&&++s<v);return i}function d(t){for(var r=0,n=1,o=b-1;n!=o&&w[n]<=t;++n)r+=x;--n;var i=(t-w[n])/(w[n+1]-w[n]),s=r+i*x,l=u(s,e,a);return l>=y?c(t,s):0==l?s:f(t,r,r+x)}function g(){V=!0,(e!=r||a!=n)&&p()}var m=4,y=.001,h=1e-7,v=10,b=11,x=1/(b-1),S="Float32Array"in t;if(4!==arguments.length)return!1;for(var P=0;4>P;++P)if("number"!=typeof arguments[P]||isNaN(arguments[P])||!isFinite(arguments[P]))return!1;e=Math.min(e,1),a=Math.min(a,1),e=Math.max(e,0),a=Math.max(a,0);var w=S?new Float32Array(b):new Array(b),V=!1,C=function(t){return V||g(),e===r&&a===n?t:0===t?0:1===t?1:l(d(t),r,n)};C.getControlPoints=function(){return[{x:e,y:r},{x:a,y:n}]};var T="generateBezier("+[e,r,a,n]+")";return C.toString=function(){return T},C}function u(e,t){var r=e;return m.isString(e)?b.Easings[e]||(r=!1):r=m.isArray(e)&&1===e.length?s.apply(null,e):m.isArray(e)&&2===e.length?x.apply(null,e.concat([t])):m.isArray(e)&&4===e.length?l.apply(null,e):!1,r===!1&&(r=b.Easings[b.defaults.easing]?b.defaults.easing:v),r}function c(e){if(e){var t=(new Date).getTime(),r=b.State.calls.length;r>1e4&&(b.State.calls=n(b.State.calls));for(var o=0;r>o;o++)if(b.State.calls[o]){var s=b.State.calls[o],l=s[0],u=s[2],d=s[3],g=!!d,y=null;d||(d=b.State.calls[o][3]=t-16);for(var h=Math.min((t-d)/u.duration,1),v=0,x=l.length;x>v;v++){var P=l[v],V=P.element;if(i(V)){var C=!1;if(u.display!==a&&null!==u.display&&"none"!==u.display){if("flex"===u.display){var T=["-webkit-box","-moz-box","-ms-flexbox","-webkit-flex"];f.each(T,function(e,t){S.setPropertyValue(V,"display",t)})}S.setPropertyValue(V,"display",u.display)}u.visibility!==a&&"hidden"!==u.visibility&&S.setPropertyValue(V,"visibility",u.visibility);for(var k in P)if("element"!==k){var A,F=P[k],j=m.isString(F.easing)?b.Easings[F.easing]:F.easing;if(1===h)A=F.endValue;else{var E=F.endValue-F.startValue;if(A=F.startValue+E*j(h,u,E),!g&&A===F.currentValue)continue}if(F.currentValue=A,"tween"===k)y=A;else{if(S.Hooks.registered[k]){var H=S.Hooks.getRoot(k),N=i(V).rootPropertyValueCache[H];N&&(F.rootPropertyValue=N)}var L=S.setPropertyValue(V,k,F.currentValue+(0===parseFloat(A)?"":F.unitType),F.rootPropertyValue,F.scrollData);S.Hooks.registered[k]&&(i(V).rootPropertyValueCache[H]=S.Normalizations.registered[H]?S.Normalizations.registered[H]("extract",null,L[1]):L[1]),"transform"===L[0]&&(C=!0)}}u.mobileHA&&i(V).transformCache.translate3d===a&&(i(V).transformCache.translate3d="(0px, 0px, 0px)",C=!0),C&&S.flushTransformCache(V)}}u.display!==a&&"none"!==u.display&&(b.State.calls[o][2].display=!1),u.visibility!==a&&"hidden"!==u.visibility&&(b.State.calls[o][2].visibility=!1),u.progress&&u.progress.call(s[1],s[1],h,Math.max(0,d+u.duration-t),d,y),1===h&&p(o)}}b.State.isTicking&&w(c)}function p(e,t){if(!b.State.calls[e])return!1;for(var r=b.State.calls[e][0],n=b.State.calls[e][1],o=b.State.calls[e][2],s=b.State.calls[e][4],l=!1,u=0,c=r.length;c>u;u++){var p=r[u].element;if(t||o.loop||("none"===o.display&&S.setPropertyValue(p,"display",o.display),"hidden"===o.visibility&&S.setPropertyValue(p,"visibility",o.visibility)),o.loop!==!0&&(f.queue(p)[1]===a||!/\.velocityQueueEntryFlag/i.test(f.queue(p)[1]))&&i(p)){i(p).isAnimating=!1,i(p).rootPropertyValueCache={};var d=!1;f.each(S.Lists.transforms3D,function(e,t){var r=/^scale/.test(t)?1:0,n=i(p).transformCache[t];i(p).transformCache[t]!==a&&new RegExp("^\\("+r+"[^.]").test(n)&&(d=!0,delete i(p).transformCache[t])}),o.mobileHA&&(d=!0,delete i(p).transformCache.translate3d),d&&S.flushTransformCache(p),S.Values.removeClass(p,"velocity-animating")}if(!t&&o.complete&&!o.loop&&u===c-1)try{o.complete.call(n,n)}catch(g){setTimeout(function(){throw g},1)}s&&o.loop!==!0&&s(n),i(p)&&o.loop===!0&&!t&&(f.each(i(p).tweensContainer,function(e,t){/^rotate/.test(e)&&360===parseFloat(t.endValue)&&(t.endValue=0,t.startValue=360),/^backgroundPosition/.test(e)&&100===parseFloat(t.endValue)&&"%"===t.unitType&&(t.endValue=0,t.startValue=100)}),b(p,"reverse",{loop:!0,delay:o.delay})),o.queue!==!1&&f.dequeue(p,o.queue)}b.State.calls[e]=!1;for(var m=0,y=b.State.calls.length;y>m;m++)if(b.State.calls[m]!==!1){l=!0;break}l===!1&&(b.State.isTicking=!1,delete b.State.calls,b.State.calls=[])}var f,d=function(){if(r.documentMode)return r.documentMode;for(var e=7;e>4;e--){var t=r.createElement("div");if(t.innerHTML="<!--[if IE "+e+"]><span></span><![endif]-->",t.getElementsByTagName("span").length)return t=null,e}return a}(),g=function(){var e=0;return t.webkitRequestAnimationFrame||t.mozRequestAnimationFrame||function(t){var r,a=(new Date).getTime();return r=Math.max(0,16-(a-e)),e=a+r,setTimeout(function(){t(a+r)},r)}}(),m={isString:function(e){return"string"==typeof e},isArray:Array.isArray||function(e){return"[object Array]"===Object.prototype.toString.call(e)},isFunction:function(e){return"[object Function]"===Object.prototype.toString.call(e)},isNode:function(e){return e&&e.nodeType},isNodeList:function(e){return"object"==typeof e&&/^\[object (HTMLCollection|NodeList|Object)\]$/.test(Object.prototype.toString.call(e))&&e.length!==a&&(0===e.length||"object"==typeof e[0]&&e[0].nodeType>0)},isWrapped:function(e){return e&&(e.jquery||t.Zepto&&t.Zepto.zepto.isZ(e))},isSVG:function(e){return t.SVGElement&&e instanceof t.SVGElement},isEmptyObject:function(e){for(var t in e)return!1;return!0}},y=!1;if(e.fn&&e.fn.jquery?(f=e,y=!0):f=t.Velocity.Utilities,8>=d&&!y)throw new Error("Velocity: IE8 and below require jQuery to be loaded before Velocity.");if(7>=d)return void(jQuery.fn.velocity=jQuery.fn.animate);var h=400,v="swing",b={State:{isMobile:/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),isAndroid:/Android/i.test(navigator.userAgent),isGingerbread:/Android 2\.3\.[3-7]/i.test(navigator.userAgent),isChrome:t.chrome,isFirefox:/Firefox/i.test(navigator.userAgent),prefixElement:r.createElement("div"),prefixMatches:{},scrollAnchor:null,scrollPropertyLeft:null,scrollPropertyTop:null,isTicking:!1,calls:[]},CSS:{},Utilities:f,Redirects:{},Easings:{},Promise:t.Promise,defaults:{queue:"",duration:h,easing:v,begin:a,complete:a,progress:a,display:a,visibility:a,loop:!1,delay:!1,mobileHA:!0,_cacheValues:!0},init:function(e){f.data(e,"velocity",{isSVG:m.isSVG(e),isAnimating:!1,computedStyle:null,tweensContainer:null,rootPropertyValueCache:{},transformCache:{}})},hook:null,mock:!1,version:{major:1,minor:2,patch:2},debug:!1};t.pageYOffset!==a?(b.State.scrollAnchor=t,b.State.scrollPropertyLeft="pageXOffset",b.State.scrollPropertyTop="pageYOffset"):(b.State.scrollAnchor=r.documentElement||r.body.parentNode||r.body,b.State.scrollPropertyLeft="scrollLeft",b.State.scrollPropertyTop="scrollTop");var x=function(){function e(e){return-e.tension*e.x-e.friction*e.v}function t(t,r,a){var n={x:t.x+a.dx*r,v:t.v+a.dv*r,tension:t.tension,friction:t.friction};return{dx:n.v,dv:e(n)}}function r(r,a){var n={dx:r.v,dv:e(r)},o=t(r,.5*a,n),i=t(r,.5*a,o),s=t(r,a,i),l=1/6*(n.dx+2*(o.dx+i.dx)+s.dx),u=1/6*(n.dv+2*(o.dv+i.dv)+s.dv);return r.x=r.x+l*a,r.v=r.v+u*a,r}return function a(e,t,n){var o,i,s,l={x:-1,v:0,tension:null,friction:null},u=[0],c=0,p=1e-4,f=.016;for(e=parseFloat(e)||500,t=parseFloat(t)||20,n=n||null,l.tension=e,l.friction=t,o=null!==n,o?(c=a(e,t),i=c/n*f):i=f;s=r(s||l,i),u.push(1+s.x),c+=16,Math.abs(s.x)>p&&Math.abs(s.v)>p;);return o?function(e){return u[e*(u.length-1)|0]}:c}}();b.Easings={linear:function(e){return e},swing:function(e){return.5-Math.cos(e*Math.PI)/2},spring:function(e){return 1-Math.cos(4.5*e*Math.PI)*Math.exp(6*-e)}},f.each([["ease",[.25,.1,.25,1]],["ease-in",[.42,0,1,1]],["ease-out",[0,0,.58,1]],["ease-in-out",[.42,0,.58,1]],["easeInSine",[.47,0,.745,.715]],["easeOutSine",[.39,.575,.565,1]],["easeInOutSine",[.445,.05,.55,.95]],["easeInQuad",[.55,.085,.68,.53]],["easeOutQuad",[.25,.46,.45,.94]],["easeInOutQuad",[.455,.03,.515,.955]],["easeInCubic",[.55,.055,.675,.19]],["easeOutCubic",[.215,.61,.355,1]],["easeInOutCubic",[.645,.045,.355,1]],["easeInQuart",[.895,.03,.685,.22]],["easeOutQuart",[.165,.84,.44,1]],["easeInOutQuart",[.77,0,.175,1]],["easeInQuint",[.755,.05,.855,.06]],["easeOutQuint",[.23,1,.32,1]],["easeInOutQuint",[.86,0,.07,1]],["easeInExpo",[.95,.05,.795,.035]],["easeOutExpo",[.19,1,.22,1]],["easeInOutExpo",[1,0,0,1]],["easeInCirc",[.6,.04,.98,.335]],["easeOutCirc",[.075,.82,.165,1]],["easeInOutCirc",[.785,.135,.15,.86]]],function(e,t){b.Easings[t[0]]=l.apply(null,t[1])});var S=b.CSS={RegEx:{isHex:/^#([A-f\d]{3}){1,2}$/i,valueUnwrap:/^[A-z]+\((.*)\)$/i,wrappedValueAlreadyExtracted:/[0-9.]+ [0-9.]+ [0-9.]+( [0-9.]+)?/,valueSplit:/([A-z]+\(.+\))|(([A-z0-9#-.]+?)(?=\s|$))/gi},Lists:{colors:["fill","stroke","stopColor","color","backgroundColor","borderColor","borderTopColor","borderRightColor","borderBottomColor","borderLeftColor","outlineColor"],transformsBase:["translateX","translateY","scale","scaleX","scaleY","skewX","skewY","rotateZ"],transforms3D:["transformPerspective","translateZ","scaleZ","rotateX","rotateY"]},Hooks:{templates:{textShadow:["Color X Y Blur","black 0px 0px 0px"],boxShadow:["Color X Y Blur Spread","black 0px 0px 0px 0px"],clip:["Top Right Bottom Left","0px 0px 0px 0px"],backgroundPosition:["X Y","0% 0%"],transformOrigin:["X Y Z","50% 50% 0px"],perspectiveOrigin:["X Y","50% 50%"]},registered:{},register:function(){for(var e=0;e<S.Lists.colors.length;e++){var t="color"===S.Lists.colors[e]?"0 0 0 1":"255 255 255 1";S.Hooks.templates[S.Lists.colors[e]]=["Red Green Blue Alpha",t]}var r,a,n;if(d)for(r in S.Hooks.templates){a=S.Hooks.templates[r],n=a[0].split(" ");var o=a[1].match(S.RegEx.valueSplit);"Color"===n[0]&&(n.push(n.shift()),o.push(o.shift()),S.Hooks.templates[r]=[n.join(" "),o.join(" ")])}for(r in S.Hooks.templates){a=S.Hooks.templates[r],n=a[0].split(" ");for(var e in n){var i=r+n[e],s=e;S.Hooks.registered[i]=[r,s]}}},getRoot:function(e){var t=S.Hooks.registered[e];return t?t[0]:e},cleanRootPropertyValue:function(e,t){return S.RegEx.valueUnwrap.test(t)&&(t=t.match(S.RegEx.valueUnwrap)[1]),S.Values.isCSSNullValue(t)&&(t=S.Hooks.templates[e][1]),t},extractValue:function(e,t){var r=S.Hooks.registered[e];if(r){var a=r[0],n=r[1];return t=S.Hooks.cleanRootPropertyValue(a,t),t.toString().match(S.RegEx.valueSplit)[n]}return t},injectValue:function(e,t,r){var a=S.Hooks.registered[e];if(a){var n,o,i=a[0],s=a[1];return r=S.Hooks.cleanRootPropertyValue(i,r),n=r.toString().match(S.RegEx.valueSplit),n[s]=t,o=n.join(" ")}return r}},Normalizations:{registered:{clip:function(e,t,r){switch(e){case"name":return"clip";case"extract":var a;return S.RegEx.wrappedValueAlreadyExtracted.test(r)?a=r:(a=r.toString().match(S.RegEx.valueUnwrap),a=a?a[1].replace(/,(\s+)?/g," "):r),a;case"inject":return"rect("+r+")"}},blur:function(e,t,r){switch(e){case"name":return b.State.isFirefox?"filter":"-webkit-filter";case"extract":var a=parseFloat(r);if(!a&&0!==a){var n=r.toString().match(/blur\(([0-9]+[A-z]+)\)/i);a=n?n[1]:0}return a;case"inject":return parseFloat(r)?"blur("+r+")":"none"}},opacity:function(e,t,r){if(8>=d)switch(e){case"name":return"filter";case"extract":var a=r.toString().match(/alpha\(opacity=(.*)\)/i);return r=a?a[1]/100:1;case"inject":return t.style.zoom=1,parseFloat(r)>=1?"":"alpha(opacity="+parseInt(100*parseFloat(r),10)+")"}else switch(e){case"name":return"opacity";case"extract":return r;case"inject":return r}}},register:function(){9>=d||b.State.isGingerbread||(S.Lists.transformsBase=S.Lists.transformsBase.concat(S.Lists.transforms3D));for(var e=0;e<S.Lists.transformsBase.length;e++)!function(){var t=S.Lists.transformsBase[e];S.Normalizations.registered[t]=function(e,r,n){switch(e){case"name":return"transform";case"extract":return i(r)===a||i(r).transformCache[t]===a?/^scale/i.test(t)?1:0:i(r).transformCache[t].replace(/[()]/g,"");case"inject":var o=!1;switch(t.substr(0,t.length-1)){case"translate":o=!/(%|px|em|rem|vw|vh|\d)$/i.test(n);break;case"scal":case"scale":b.State.isAndroid&&i(r).transformCache[t]===a&&1>n&&(n=1),o=!/(\d)$/i.test(n);break;case"skew":o=!/(deg|\d)$/i.test(n);break;case"rotate":o=!/(deg|\d)$/i.test(n)}return o||(i(r).transformCache[t]="("+n+")"),i(r).transformCache[t]}}}();for(var e=0;e<S.Lists.colors.length;e++)!function(){var t=S.Lists.colors[e];S.Normalizations.registered[t]=function(e,r,n){switch(e){case"name":return t;case"extract":var o;if(S.RegEx.wrappedValueAlreadyExtracted.test(n))o=n;else{var i,s={black:"rgb(0, 0, 0)",blue:"rgb(0, 0, 255)",gray:"rgb(128, 128, 128)",green:"rgb(0, 128, 0)",red:"rgb(255, 0, 0)",white:"rgb(255, 255, 255)"};/^[A-z]+$/i.test(n)?i=s[n]!==a?s[n]:s.black:S.RegEx.isHex.test(n)?i="rgb("+S.Values.hexToRgb(n).join(" ")+")":/^rgba?\(/i.test(n)||(i=s.black),o=(i||n).toString().match(S.RegEx.valueUnwrap)[1].replace(/,(\s+)?/g," ")}return 8>=d||3!==o.split(" ").length||(o+=" 1"),o;case"inject":return 8>=d?4===n.split(" ").length&&(n=n.split(/\s+/).slice(0,3).join(" ")):3===n.split(" ").length&&(n+=" 1"),(8>=d?"rgb":"rgba")+"("+n.replace(/\s+/g,",").replace(/\.(\d)+(?=,)/g,"")+")"}}}()}},Names:{camelCase:function(e){return e.replace(/-(\w)/g,function(e,t){return t.toUpperCase()})},SVGAttribute:function(e){var t="width|height|x|y|cx|cy|r|rx|ry|x1|x2|y1|y2";return(d||b.State.isAndroid&&!b.State.isChrome)&&(t+="|transform"),new RegExp("^("+t+")$","i").test(e)},prefixCheck:function(e){if(b.State.prefixMatches[e])return[b.State.prefixMatches[e],!0];for(var t=["","Webkit","Moz","ms","O"],r=0,a=t.length;a>r;r++){var n;if(n=0===r?e:t[r]+e.replace(/^\w/,function(e){return e.toUpperCase()}),m.isString(b.State.prefixElement.style[n]))return b.State.prefixMatches[e]=n,[n,!0]}return[e,!1]}},Values:{hexToRgb:function(e){var t,r=/^#?([a-f\d])([a-f\d])([a-f\d])$/i,a=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i;return e=e.replace(r,function(e,t,r,a){return t+t+r+r+a+a}),t=a.exec(e),t?[parseInt(t[1],16),parseInt(t[2],16),parseInt(t[3],16)]:[0,0,0]},isCSSNullValue:function(e){return 0==e||/^(none|auto|transparent|(rgba\(0, ?0, ?0, ?0\)))$/i.test(e)},getUnitType:function(e){return/^(rotate|skew)/i.test(e)?"deg":/(^(scale|scaleX|scaleY|scaleZ|alpha|flexGrow|flexHeight|zIndex|fontWeight)$)|((opacity|red|green|blue|alpha)$)/i.test(e)?"":"px"},getDisplayType:function(e){var t=e&&e.tagName.toString().toLowerCase();return/^(b|big|i|small|tt|abbr|acronym|cite|code|dfn|em|kbd|strong|samp|var|a|bdo|br|img|map|object|q|script|span|sub|sup|button|input|label|select|textarea)$/i.test(t)?"inline":/^(li)$/i.test(t)?"list-item":/^(tr)$/i.test(t)?"table-row":/^(table)$/i.test(t)?"table":/^(tbody)$/i.test(t)?"table-row-group":"block"},addClass:function(e,t){e.classList?e.classList.add(t):e.className+=(e.className.length?" ":"")+t},removeClass:function(e,t){e.classList?e.classList.remove(t):e.className=e.className.toString().replace(new RegExp("(^|\\s)"+t.split(" ").join("|")+"(\\s|$)","gi")," ")}},getPropertyValue:function(e,r,n,o){function s(e,r){function n(){u&&S.setPropertyValue(e,"display","none")}var l=0;if(8>=d)l=f.css(e,r);else{var u=!1;if(/^(width|height)$/.test(r)&&0===S.getPropertyValue(e,"display")&&(u=!0,S.setPropertyValue(e,"display",S.Values.getDisplayType(e))),!o){if("height"===r&&"border-box"!==S.getPropertyValue(e,"boxSizing").toString().toLowerCase()){var c=e.offsetHeight-(parseFloat(S.getPropertyValue(e,"borderTopWidth"))||0)-(parseFloat(S.getPropertyValue(e,"borderBottomWidth"))||0)-(parseFloat(S.getPropertyValue(e,"paddingTop"))||0)-(parseFloat(S.getPropertyValue(e,"paddingBottom"))||0);return n(),c}if("width"===r&&"border-box"!==S.getPropertyValue(e,"boxSizing").toString().toLowerCase()){var p=e.offsetWidth-(parseFloat(S.getPropertyValue(e,"borderLeftWidth"))||0)-(parseFloat(S.getPropertyValue(e,"borderRightWidth"))||0)-(parseFloat(S.getPropertyValue(e,"paddingLeft"))||0)-(parseFloat(S.getPropertyValue(e,"paddingRight"))||0);return n(),p}}var g;g=i(e)===a?t.getComputedStyle(e,null):i(e).computedStyle?i(e).computedStyle:i(e).computedStyle=t.getComputedStyle(e,null),"borderColor"===r&&(r="borderTopColor"),l=9===d&&"filter"===r?g.getPropertyValue(r):g[r],(""===l||null===l)&&(l=e.style[r]),n()}if("auto"===l&&/^(top|right|bottom|left)$/i.test(r)){var m=s(e,"position");("fixed"===m||"absolute"===m&&/top|left/i.test(r))&&(l=f(e).position()[r]+"px")}return l}var l;if(S.Hooks.registered[r]){var u=r,c=S.Hooks.getRoot(u);n===a&&(n=S.getPropertyValue(e,S.Names.prefixCheck(c)[0])),S.Normalizations.registered[c]&&(n=S.Normalizations.registered[c]("extract",e,n)),l=S.Hooks.extractValue(u,n)}else if(S.Normalizations.registered[r]){var p,g;p=S.Normalizations.registered[r]("name",e),"transform"!==p&&(g=s(e,S.Names.prefixCheck(p)[0]),S.Values.isCSSNullValue(g)&&S.Hooks.templates[r]&&(g=S.Hooks.templates[r][1])),l=S.Normalizations.registered[r]("extract",e,g)}if(!/^[\d-]/.test(l))if(i(e)&&i(e).isSVG&&S.Names.SVGAttribute(r))if(/^(height|width)$/i.test(r))try{l=e.getBBox()[r]}catch(m){l=0}else l=e.getAttribute(r);else l=s(e,S.Names.prefixCheck(r)[0]);return S.Values.isCSSNullValue(l)&&(l=0),b.debug>=2&&console.log("Get "+r+": "+l),l},setPropertyValue:function(e,r,a,n,o){var s=r;if("scroll"===r)o.container?o.container["scroll"+o.direction]=a:"Left"===o.direction?t.scrollTo(a,o.alternateValue):t.scrollTo(o.alternateValue,a);else if(S.Normalizations.registered[r]&&"transform"===S.Normalizations.registered[r]("name",e))S.Normalizations.registered[r]("inject",e,a),s="transform",a=i(e).transformCache[r];else{if(S.Hooks.registered[r]){var l=r,u=S.Hooks.getRoot(r);n=n||S.getPropertyValue(e,u),a=S.Hooks.injectValue(l,a,n),r=u}if(S.Normalizations.registered[r]&&(a=S.Normalizations.registered[r]("inject",e,a),r=S.Normalizations.registered[r]("name",e)),s=S.Names.prefixCheck(r)[0],8>=d)try{e.style[s]=a}catch(c){b.debug&&console.log("Browser does not support ["+a+"] for ["+s+"]")}else i(e)&&i(e).isSVG&&S.Names.SVGAttribute(r)?e.setAttribute(r,a):e.style[s]=a;b.debug>=2&&console.log("Set "+r+" ("+s+"): "+a)}return[s,a]},flushTransformCache:function(e){function t(t){return parseFloat(S.getPropertyValue(e,t))}var r="";if((d||b.State.isAndroid&&!b.State.isChrome)&&i(e).isSVG){var a={translate:[t("translateX"),t("translateY")],skewX:[t("skewX")],skewY:[t("skewY")],scale:1!==t("scale")?[t("scale"),t("scale")]:[t("scaleX"),t("scaleY")],rotate:[t("rotateZ"),0,0]};f.each(i(e).transformCache,function(e){/^translate/i.test(e)?e="translate":/^scale/i.test(e)?e="scale":/^rotate/i.test(e)&&(e="rotate"),a[e]&&(r+=e+"("+a[e].join(" ")+") ",delete a[e])})}else{var n,o;f.each(i(e).transformCache,function(t){return n=i(e).transformCache[t],"transformPerspective"===t?(o=n,!0):(9===d&&"rotateZ"===t&&(t="rotate"),void(r+=t+n+" "))}),o&&(r="perspective"+o+" "+r)}S.setPropertyValue(e,"transform",r)}};S.Hooks.register(),S.Normalizations.register(),b.hook=function(e,t,r){var n=a;return e=o(e),f.each(e,function(e,o){if(i(o)===a&&b.init(o),r===a)n===a&&(n=b.CSS.getPropertyValue(o,t));else{var s=b.CSS.setPropertyValue(o,t,r);"transform"===s[0]&&b.CSS.flushTransformCache(o),n=s}}),n};var P=function(){function e(){return s?k.promise||null:l}function n(){function e(e){function p(e,t){var r=a,n=a,i=a;return m.isArray(e)?(r=e[0],!m.isArray(e[1])&&/^[\d-]/.test(e[1])||m.isFunction(e[1])||S.RegEx.isHex.test(e[1])?i=e[1]:(m.isString(e[1])&&!S.RegEx.isHex.test(e[1])||m.isArray(e[1]))&&(n=t?e[1]:u(e[1],s.duration),e[2]!==a&&(i=e[2]))):r=e,t||(n=n||s.easing),m.isFunction(r)&&(r=r.call(o,V,w)),m.isFunction(i)&&(i=i.call(o,V,w)),[r||0,n,i]}function d(e,t){var r,a;return a=(t||"0").toString().toLowerCase().replace(/[%A-z]+$/,function(e){return r=e,""}),r||(r=S.Values.getUnitType(e)),[a,r]}function h(){var e={myParent:o.parentNode||r.body,position:S.getPropertyValue(o,"position"),fontSize:S.getPropertyValue(o,"fontSize")},a=e.position===L.lastPosition&&e.myParent===L.lastParent,n=e.fontSize===L.lastFontSize;L.lastParent=e.myParent,L.lastPosition=e.position,L.lastFontSize=e.fontSize;var s=100,l={};if(n&&a)l.emToPx=L.lastEmToPx,l.percentToPxWidth=L.lastPercentToPxWidth,l.percentToPxHeight=L.lastPercentToPxHeight;else{var u=i(o).isSVG?r.createElementNS("http://www.w3.org/2000/svg","rect"):r.createElement("div");b.init(u),e.myParent.appendChild(u),f.each(["overflow","overflowX","overflowY"],function(e,t){b.CSS.setPropertyValue(u,t,"hidden")}),b.CSS.setPropertyValue(u,"position",e.position),b.CSS.setPropertyValue(u,"fontSize",e.fontSize),b.CSS.setPropertyValue(u,"boxSizing","content-box"),f.each(["minWidth","maxWidth","width","minHeight","maxHeight","height"],function(e,t){b.CSS.setPropertyValue(u,t,s+"%")}),b.CSS.setPropertyValue(u,"paddingLeft",s+"em"),l.percentToPxWidth=L.lastPercentToPxWidth=(parseFloat(S.getPropertyValue(u,"width",null,!0))||1)/s,l.percentToPxHeight=L.lastPercentToPxHeight=(parseFloat(S.getPropertyValue(u,"height",null,!0))||1)/s,l.emToPx=L.lastEmToPx=(parseFloat(S.getPropertyValue(u,"paddingLeft"))||1)/s,e.myParent.removeChild(u)}return null===L.remToPx&&(L.remToPx=parseFloat(S.getPropertyValue(r.body,"fontSize"))||16),null===L.vwToPx&&(L.vwToPx=parseFloat(t.innerWidth)/100,L.vhToPx=parseFloat(t.innerHeight)/100),l.remToPx=L.remToPx,l.vwToPx=L.vwToPx,l.vhToPx=L.vhToPx,b.debug>=1&&console.log("Unit ratios: "+JSON.stringify(l),o),l}if(s.begin&&0===V)try{s.begin.call(g,g)}catch(x){setTimeout(function(){throw x},1)}if("scroll"===A){var P,C,T,F=/^x$/i.test(s.axis)?"Left":"Top",j=parseFloat(s.offset)||0;s.container?m.isWrapped(s.container)||m.isNode(s.container)?(s.container=s.container[0]||s.container,P=s.container["scroll"+F],T=P+f(o).position()[F.toLowerCase()]+j):s.container=null:(P=b.State.scrollAnchor[b.State["scrollProperty"+F]],C=b.State.scrollAnchor[b.State["scrollProperty"+("Left"===F?"Top":"Left")]],T=f(o).offset()[F.toLowerCase()]+j),l={scroll:{rootPropertyValue:!1,startValue:P,currentValue:P,endValue:T,unitType:"",easing:s.easing,scrollData:{container:s.container,direction:F,alternateValue:C}},element:o},b.debug&&console.log("tweensContainer (scroll): ",l.scroll,o)}else if("reverse"===A){if(!i(o).tweensContainer)return void f.dequeue(o,s.queue);"none"===i(o).opts.display&&(i(o).opts.display="auto"),"hidden"===i(o).opts.visibility&&(i(o).opts.visibility="visible"),i(o).opts.loop=!1,i(o).opts.begin=null,i(o).opts.complete=null,v.easing||delete s.easing,v.duration||delete s.duration,s=f.extend({},i(o).opts,s);var E=f.extend(!0,{},i(o).tweensContainer);for(var H in E)if("element"!==H){var N=E[H].startValue;E[H].startValue=E[H].currentValue=E[H].endValue,E[H].endValue=N,m.isEmptyObject(v)||(E[H].easing=s.easing),b.debug&&console.log("reverse tweensContainer ("+H+"): "+JSON.stringify(E[H]),o)}l=E}else if("start"===A){var E;i(o).tweensContainer&&i(o).isAnimating===!0&&(E=i(o).tweensContainer),f.each(y,function(e,t){if(RegExp("^"+S.Lists.colors.join("$|^")+"$").test(e)){var r=p(t,!0),n=r[0],o=r[1],i=r[2];if(S.RegEx.isHex.test(n)){for(var s=["Red","Green","Blue"],l=S.Values.hexToRgb(n),u=i?S.Values.hexToRgb(i):a,c=0;c<s.length;c++){var f=[l[c]];o&&f.push(o),u!==a&&f.push(u[c]),y[e+s[c]]=f}delete y[e]}}});for(var z in y){var O=p(y[z]),q=O[0],$=O[1],M=O[2];z=S.Names.camelCase(z);var I=S.Hooks.getRoot(z),B=!1;if(i(o).isSVG||"tween"===I||S.Names.prefixCheck(I)[1]!==!1||S.Normalizations.registered[I]!==a){(s.display!==a&&null!==s.display&&"none"!==s.display||s.visibility!==a&&"hidden"!==s.visibility)&&/opacity|filter/.test(z)&&!M&&0!==q&&(M=0),s._cacheValues&&E&&E[z]?(M===a&&(M=E[z].endValue+E[z].unitType),B=i(o).rootPropertyValueCache[I]):S.Hooks.registered[z]?M===a?(B=S.getPropertyValue(o,I),M=S.getPropertyValue(o,z,B)):B=S.Hooks.templates[I][1]:M===a&&(M=S.getPropertyValue(o,z));var W,G,Y,D=!1;if(W=d(z,M),M=W[0],Y=W[1],W=d(z,q),q=W[0].replace(/^([+-\/*])=/,function(e,t){return D=t,""}),G=W[1],M=parseFloat(M)||0,q=parseFloat(q)||0,"%"===G&&(/^(fontSize|lineHeight)$/.test(z)?(q/=100,G="em"):/^scale/.test(z)?(q/=100,G=""):/(Red|Green|Blue)$/i.test(z)&&(q=q/100*255,G="")),/[\/*]/.test(D))G=Y;else if(Y!==G&&0!==M)if(0===q)G=Y;else{n=n||h();var Q=/margin|padding|left|right|width|text|word|letter/i.test(z)||/X$/.test(z)||"x"===z?"x":"y";switch(Y){case"%":M*="x"===Q?n.percentToPxWidth:n.percentToPxHeight;break;case"px":break;default:M*=n[Y+"ToPx"]}switch(G){case"%":M*=1/("x"===Q?n.percentToPxWidth:n.percentToPxHeight);break;case"px":break;default:M*=1/n[G+"ToPx"]}}switch(D){case"+":q=M+q;break;case"-":q=M-q;break;case"*":q=M*q;break;case"/":q=M/q}l[z]={rootPropertyValue:B,startValue:M,currentValue:M,endValue:q,unitType:G,easing:$},b.debug&&console.log("tweensContainer ("+z+"): "+JSON.stringify(l[z]),o)}else b.debug&&console.log("Skipping ["+I+"] due to a lack of browser support.")}l.element=o}l.element&&(S.Values.addClass(o,"velocity-animating"),R.push(l),""===s.queue&&(i(o).tweensContainer=l,i(o).opts=s),i(o).isAnimating=!0,V===w-1?(b.State.calls.push([R,g,s,null,k.resolver]),b.State.isTicking===!1&&(b.State.isTicking=!0,c())):V++)}var n,o=this,s=f.extend({},b.defaults,v),l={};switch(i(o)===a&&b.init(o),parseFloat(s.delay)&&s.queue!==!1&&f.queue(o,s.queue,function(e){b.velocityQueueEntryFlag=!0,i(o).delayTimer={setTimeout:setTimeout(e,parseFloat(s.delay)),next:e}}),s.duration.toString().toLowerCase()){case"fast":s.duration=200;break;case"normal":s.duration=h;break;case"slow":s.duration=600;break;default:s.duration=parseFloat(s.duration)||1}b.mock!==!1&&(b.mock===!0?s.duration=s.delay=1:(s.duration*=parseFloat(b.mock)||1,s.delay*=parseFloat(b.mock)||1)),s.easing=u(s.easing,s.duration),s.begin&&!m.isFunction(s.begin)&&(s.begin=null),s.progress&&!m.isFunction(s.progress)&&(s.progress=null),s.complete&&!m.isFunction(s.complete)&&(s.complete=null),s.display!==a&&null!==s.display&&(s.display=s.display.toString().toLowerCase(),"auto"===s.display&&(s.display=b.CSS.Values.getDisplayType(o))),s.visibility!==a&&null!==s.visibility&&(s.visibility=s.visibility.toString().toLowerCase()),s.mobileHA=s.mobileHA&&b.State.isMobile&&!b.State.isGingerbread,s.queue===!1?s.delay?setTimeout(e,s.delay):e():f.queue(o,s.queue,function(t,r){return r===!0?(k.promise&&k.resolver(g),!0):(b.velocityQueueEntryFlag=!0,void e(t))}),""!==s.queue&&"fx"!==s.queue||"inprogress"===f.queue(o)[0]||f.dequeue(o)}var s,l,d,g,y,v,x=arguments[0]&&(arguments[0].p||f.isPlainObject(arguments[0].properties)&&!arguments[0].properties.names||m.isString(arguments[0].properties));if(m.isWrapped(this)?(s=!1,d=0,g=this,l=this):(s=!0,d=1,g=x?arguments[0].elements||arguments[0].e:arguments[0]),g=o(g)){x?(y=arguments[0].properties||arguments[0].p,v=arguments[0].options||arguments[0].o):(y=arguments[d],v=arguments[d+1]);var w=g.length,V=0;if(!/^(stop|finish)$/i.test(y)&&!f.isPlainObject(v)){var C=d+1;v={};for(var T=C;T<arguments.length;T++)m.isArray(arguments[T])||!/^(fast|normal|slow)$/i.test(arguments[T])&&!/^\d/.test(arguments[T])?m.isString(arguments[T])||m.isArray(arguments[T])?v.easing=arguments[T]:m.isFunction(arguments[T])&&(v.complete=arguments[T]):v.duration=arguments[T]}var k={promise:null,resolver:null,rejecter:null};s&&b.Promise&&(k.promise=new b.Promise(function(e,t){k.resolver=e,k.rejecter=t}));var A;switch(y){case"scroll":A="scroll";break;case"reverse":A="reverse";break;case"finish":case"stop":f.each(g,function(e,t){i(t)&&i(t).delayTimer&&(clearTimeout(i(t).delayTimer.setTimeout),i(t).delayTimer.next&&i(t).delayTimer.next(),delete i(t).delayTimer)});var F=[];return f.each(b.State.calls,function(e,t){t&&f.each(t[1],function(r,n){var o=v===a?"":v;return o===!0||t[2].queue===o||v===a&&t[2].queue===!1?void f.each(g,function(r,a){a===n&&((v===!0||m.isString(v))&&(f.each(f.queue(a,m.isString(v)?v:""),function(e,t){
            m.isFunction(t)&&t(null,!0)}),f.queue(a,m.isString(v)?v:"",[])),"stop"===y?(i(a)&&i(a).tweensContainer&&o!==!1&&f.each(i(a).tweensContainer,function(e,t){t.endValue=t.currentValue}),F.push(e)):"finish"===y&&(t[2].duration=1))}):!0})}),"stop"===y&&(f.each(F,function(e,t){p(t,!0)}),k.promise&&k.resolver(g)),e();default:if(!f.isPlainObject(y)||m.isEmptyObject(y)){if(m.isString(y)&&b.Redirects[y]){var j=f.extend({},v),E=j.duration,H=j.delay||0;return j.backwards===!0&&(g=f.extend(!0,[],g).reverse()),f.each(g,function(e,t){parseFloat(j.stagger)?j.delay=H+parseFloat(j.stagger)*e:m.isFunction(j.stagger)&&(j.delay=H+j.stagger.call(t,e,w)),j.drag&&(j.duration=parseFloat(E)||(/^(callout|transition)/.test(y)?1e3:h),j.duration=Math.max(j.duration*(j.backwards?1-e/w:(e+1)/w),.75*j.duration,200)),b.Redirects[y].call(t,t,j||{},e,w,g,k.promise?k:a)}),e()}var N="Velocity: First argument ("+y+") was not a property map, a known action, or a registered redirect. Aborting.";return k.promise?k.rejecter(new Error(N)):console.log(N),e()}A="start"}var L={lastParent:null,lastPosition:null,lastFontSize:null,lastPercentToPxWidth:null,lastPercentToPxHeight:null,lastEmToPx:null,remToPx:null,vwToPx:null,vhToPx:null},R=[];f.each(g,function(e,t){m.isNode(t)&&n.call(t)});var z,j=f.extend({},b.defaults,v);if(j.loop=parseInt(j.loop),z=2*j.loop-1,j.loop)for(var O=0;z>O;O++){var q={delay:j.delay,progress:j.progress};O===z-1&&(q.display=j.display,q.visibility=j.visibility,q.complete=j.complete),P(g,"reverse",q)}return e()}};b=f.extend(P,b),b.animate=P;var w=t.requestAnimationFrame||g;return b.State.isMobile||r.hidden===a||r.addEventListener("visibilitychange",function(){r.hidden?(w=function(e){return setTimeout(function(){e(!0)},16)},c()):w=t.requestAnimationFrame||g}),e.Velocity=b,e!==t&&(e.fn.velocity=P,e.fn.velocity.defaults=b.defaults),f.each(["Down","Up"],function(e,t){b.Redirects["slide"+t]=function(e,r,n,o,i,s){var l=f.extend({},r),u=l.begin,c=l.complete,p={height:"",marginTop:"",marginBottom:"",paddingTop:"",paddingBottom:""},d={};l.display===a&&(l.display="Down"===t?"inline"===b.CSS.Values.getDisplayType(e)?"inline-block":"block":"none"),l.begin=function(){u&&u.call(i,i);for(var r in p){d[r]=e.style[r];var a=b.CSS.getPropertyValue(e,r);p[r]="Down"===t?[a,0]:[0,a]}d.overflow=e.style.overflow,e.style.overflow="hidden"},l.complete=function(){for(var t in d)e.style[t]=d[t];c&&c.call(i,i),s&&s.resolver(i)},b(e,p,l)}}),f.each(["In","Out"],function(e,t){b.Redirects["fade"+t]=function(e,r,n,o,i,s){var l=f.extend({},r),u={opacity:"In"===t?1:0},c=l.complete;l.complete=n!==o-1?l.begin=null:function(){c&&c.call(i,i),s&&s.resolver(i)},l.display===a&&(l.display="In"===t?"auto":"none"),b(this,u,l)}}),b}(window.jQuery||window.Zepto||window,window,document)}));
var App = (function(){
    "use strict";

    var stage = [];
    var currentStage = $('#app[data-stage]').data('stage') || 'product';

    var debug = true;

    $.widget( "custom.catcomplete", $.ui.autocomplete, {
        _renderMenu: function( ul, items ) {
            var that = this,
                currentCategory = "";
            $.each( items, function( index, item ) {
                if ( item.category !== currentCategory ) {
                    ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
                that._renderItemData( ul, item );
            });
        }
    });

    // Skel.
    skel.breakpoints({
        xlarge: '(max-width: 1680px)',
        large: '(max-width: 1280px)',
        medium: '(max-width: 980px)',
        small: '(max-width: 736px)',
        xsmall: '(max-width: 480px)',
        xxsmall: '(max-width: 360px)',
        short: '(min-aspect-ratio: 16/7)',
        xshort: '(min-aspect-ratio: 16/6)'
    });

    //public API
    return {

        /** @type {function(...*)} */
        log: function() {
            if(debug)
                console.log.apply(console, arguments)
        },
        init: function() {

            stage['cart'] = Cart;
            stage['product'] = Product;

            $('.button-collapse').sideNav({'edge': 'left'});

            this.reinit();
        },

        /*
         * Notify
         *
         * @param {string} msg
         * @param {string} t default "success"
         */
        message: function (msg, t) {
            var type = t ? 'success' : 'danger',
                icon = t ? '<i class="fa fa-check fa-lg" aria-hidden="true"></i> ' : '<i class="fa fa-info-circle fa-lg" aria-hidden="true"></i> ';
            $.notify({
                // options
                message: icon + msg
            },{
                // settings
                type: type,
                delay: 2000,
                animate: {
                    enter: 'animated slideInDown',
                    exit: 'animated slideOutRight'
                }
            });
        },

        reinit: function () {

            stage[currentStage].init();


            $('input[type=number]').stepper({
                type: 'int',       // Allow floating point numbers
                wheel_step:1,       // Wheel increment is 1
                arrow_step: 1,    // Up/Down arrows increment is 0.5
                limit: [1, 100],
                incrementButton: '<i class="fa fa-plus"></i>',
                decrementButton: '<i class="fa fa-minus"></i>',

                onStep: function( val, up )
                {
                    stage[currentStage].update(this, val);
                }
            });
        }
    }
})();

App.init();

$(document).ready(function () {
    //$('[data-toggle="tooltip"]').tooltip();
    if (skel.vars.mobile) {
        var width = $('.yandex-map').width();
        var isLoad = false;
        for(var i = 1000; i < 30 * 1000; i += 1000) {
            (function(i, width, isLoad) {
                if (isLoad) {
                    console.log('loaded');
                    return;
                }
                setInterval(function () {
                    var maps = $('ymaps[id]');
                    if (maps.length) {
                        maps.css({width: width + 'px'});
                        isLoad = true;
                    }
                }, i);
            }) (i, width, isLoad);

        }
    }
});