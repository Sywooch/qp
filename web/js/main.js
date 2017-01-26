$(document).ready(function () {
    /**
     * Search panel
     */
    var $search = $('.search'),
        $searchInput = $('#search-input'),
        $modalSearch = $('.modal-search'),
        $navBarToggle = $('.navbar-toggle'),
        $productCount = $('.product_count'),
        $transform = $('.transform');

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
        $transform.on('click', function () {

            if($(this).hasClass('shown')){
                $(this).removeClass('shown');
            } else {
                $(this).addClass('shown');
            }

        });
        $modalSearch.on('click', function () {
            hideModalSearch();
        });
        $productCount.on('change', function () {
            $console.log(('btn-compare').data('productId'));
        })
    }

    function init() {
        event();
    }
    init();
    
    
});