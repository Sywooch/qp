<?php

use app\components\Html;
use app\components\LoginWidget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

$itemMenu = [
    'top' => [

    ],
    'bottom' => [
        ['label' => 'Акции', 'url' => ['/', 'is_discount' => 1]],
        ['label' => 'О компании', 'url' => ['/p/about']],
        ['label' => 'Доставка', 'url' => ['/p/delivery']],
        ['label' => 'Оплата', 'url' => ['/p/payment']],
        ['label' => 'Отзывы', 'url' => ['/site/reviews']],
        ['label' => 'Контакты', 'url' => ['/site/contact']],
    ]
];
?>

<header class="header">
    <div class="header__middle">
        <div class="container">
            <div class="row">
                <div class="col-md-2 header__contact visible-md visible-lg">
                    <div>
                        <span class="clock lnr lnr-clock"></span><span class="clock-text">с 10:00 до 19:00</span>
                    </div>
                    <div>
                        <span class="phone lnr lnr-phone-handset"></span>
                        <span class="phone-text"><?=Yii::$app->params['phone.manager']?></span>
                    </div>
                </div>
                <div class="col-md-2 col-xs-12">
                    <section class="search row">
                        <?= $this->render('_search_form', ['text' => '']) ?>
                        <div class="col-xs-3 visible-xs visible-sm mobile-nav-controls">
                            <button class="btn btn-search-modal" data-toggle="modal" data-target=".bs-search-modal-lg">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </section>
                </div>
                <div class="col-md-3 col-xs-6 col-xs-offset-3 col-md-offset-0">
                    <div class="header__logo">
                        <a class="header__logo-link" href="/">
                            <img src="/img/logo-qp.gif">
                        </a>
                        <span class="header__logo-slogan">
                            Интернет-супермаркет
                        </span>
                    </div>

                </div>
                <div class="col-md-3 col-xs-6 col-xs-offset-3 col-md-offset-0  visible-md visible-lg">
                    <?php
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav nav header__personal'],
                        'items' => [
                            LoginWidget::widget(),
                            "<li class=\"header__bookmark\">
                                <a href='/profile/bookmark'>
                                    <span class=\"icon lnr lnr-heart\"></span> 
                                    <span class=\"link bookmark-text\">Избранное</span> 
                                </a>
                            </li>" ,
                            "<li class=\"shopping\">".\app\components\CartWidget::widget()."</li>"
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="header__bottom">
        <div class="header__navbar navbar">
            <div class="container">
                <button type="button" class="navbar-toggle button-collapse" data-toggle="collapse" data-activates="slide-out">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <div class="hidden-xs hidden-sm">
                    <?php
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav nav'],
                        'items' => array_merge([
                            \app\components\catalog\CatalogWidget::widget([
                                'visible' => isset($this->params['catalog']) && $this->params['catalog']
                            ]),
                        ], $itemMenu['bottom'] ),
                    ]);
                    ?>

                </div>
            </div>
        </div>
    </div>

</header>

<?php
/*
 * Mobile navigation
 */
foreach ($itemMenu['top'] as &$item) {
    if($item['url'][0] == '/'.Yii::$app->getRequest()->pathInfo) {
        $item['options'] = ['class' => 'active'];
    }
}
$divider = [ '<li><div class=\'divider\'></div></li>' ];
echo Nav::widget([
    'options' => ['class' => 'side-nav', 'id' => 'slide-out'],
    'items' => array_merge(
        [ LoginWidget::widget(['mobile' => true]) ],
         $divider ,

        $itemMenu['bottom'],
        $itemMenu['top'],
        [ $this->render('mobile/_sidebarLogout') ]
    )
]);
?>

<div class="modal fade bs-search-modal-lg" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <?= $this->render('_search_form', ['text' => '']) ?>
                    <div class="col-xs-2 search__cancel visible-xs visible-sm">
                        <button type="button" class="btn search-hidden" data-dismiss="modal" aria-label="Close">Отмена</button>
                    </div>
                </div>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div class="js-search-result"></div>
            </div>
        </div>
    </div>
</div>
