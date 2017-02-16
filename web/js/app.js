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
                    if(action) {
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
            sum.text(parseInt(val) * price);
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
                var min = parseInt(arguments[0] || $from.data('min')),
                    max = parseInt(arguments[1] || $to.data('max'));
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
                return $from.val() + "-" + $to.val();
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
        // log(serialize2(data));
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

            $.ajax({
                url:     url + '?ajax=1',
                success: function(data){
                    $content.html(data);
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

    const $inputCount = $('.product-count'),
        $compare = $('.btn-compare'),
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
(function($){

    "use strict";

    var $open = $('.btn-search-modal'),
        $input = $('#search-input-mobile');

    var Search = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            $open.on('click', function () {
                setTimeout(self.openSearch, 800);
            });
        },
        openSearch: function () {
            $input.focus();
        }
    };

    Search.init();

})(jQuery);
var App = (function(){
    "use strict";

    //public API
    return {
        init: function() {

            var stage = [];

            var currentStage = $('#app[data-stage]').data('stage') || 'product';

            stage['cart'] = Cart;
            stage['product'] = Product;

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
                    enter: 'animated fadeInDown',
                    exit: 'animated fadeOutUp'
                }
            });
        }
    }
})();

App.init();

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});