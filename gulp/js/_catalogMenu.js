var CatalogMenu = (function(){
    "use strict";

    var el = $('.transform');

    var __ = {
        init: function() {
            this.event();
        },
        event: function() {
            var self = this;
            el.on('click', function () {
                self.shown();
            });
        },
        shown: function () {
            if(el.hasClass('shown')){
                el.removeClass('shown');
            } else {
                el.addClass('shown');
            }
        },
    }

    return {
        init: __.init()
    }
})();