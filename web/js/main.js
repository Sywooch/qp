$(document).ready(function () {
    var $search = $('.search'),
        $searchInput = $('#search-input'),
        $modalSearch = $('.modal-search'),
        $navBarToggle = $('.navbar-toggle');

    function showModalSearch() {
        $search.addClass('visible');
        $searchInput.focus();
        $modalSearch.show();
        $navBarToggle.css('z-index', 0);
    }
    function hideModalSearch() {
        $search.removeClass('visible');
        $modalSearch.hide();
        $navBarToggle.css('z-index', 3);
    }

    function event() {
        $('.search-visible').on('click', function () {
            showModalSearch();
        });
        $('.search-hidden').on('click', function () {
            hideModalSearch();
        });
        $modalSearch.on('click', function () {
            hideModalSearch();
        });
    }

    function init() {
        event();
    }
    init();

});