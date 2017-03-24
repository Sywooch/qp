<?php

use app\components\Html;
use app\components\LoginWidget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

$itemMenu = [
    'top' => [
        ['label' => 'Доставка', 'url' => ['/p/delivery']],
        ['label' => 'Оплата', 'url' => ['/p/payment']],
        ['label' => 'О компании', 'url' => ['/p/about']],
        ['label' => 'Контакты', 'url' => ['/site/contact']],
        ['label' => 'Отзывы', 'url' => ['/site/reviews']],
    ],
    'bottom' => [
        ['label' => 'Акции', 'url' => ['/']],
        ['label' => 'Скидки', 'url' => ['/']],
    ]
];
?>

<header class="header">
    <div class="header__top hidden-xs">
        <div class="container">
            <?php
            echo Nav::widget([
                'options' => ['class' => 'nav header__top-navbar'],
                'items' => $itemMenu['top'],
            ]);
            ?>
        </div>
    </div>
    <div class="header__middle">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-xs-6 col-xs-offset-3 col-md-offset-0">
                    <div class="header__logo">
                        <a class="navbar-brand" href="/">Купи</a>
                    </div>
                    <div class="header__slogan">
                        Интернет-супермаркет
                    </div>
                </div>
                <div class="col-xs-3 visible-xs visible-sm mobile-nav-controls">
                    <button class="btn btn-search-modal" data-toggle="modal" data-target=".bs-search-modal-lg">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <div>

                </div>
                <div class="col-md-6 col-xs-12">
                    <section class="search row">
                        <?= $this->render('_search_form', ['text' => '']) ?>
                    </section>
                </div>
                <div class="col-md-3 hidden-xs hidden-sm">
                    <div class="header__phone">
                        <?=Yii::$app->params['phone.manager']?>
                    </div>
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
                    <?php
                    echo Nav::widget([
                        'options' => ['class' => 'navbar-nav nav header__personal'],
                        'items' => [
                            LoginWidget::widget(),
                            "<li class=\"shopping\">".\app\components\CartWidget::widget()."</li>",
                        ],
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
        $divider ,
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

            </div>
        </div>
    </div>
</div>
