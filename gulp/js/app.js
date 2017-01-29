$(document).ready(function () {
    var App = (function(){
        "use strict";

        //public API
        return {
            init: function() {
                Search.init();
                CatalogMenu.init();
                Cart.init();
            }
        }
    })();

    App.init();
});