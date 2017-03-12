<?php

use app\components\Html;
use app\components\LoginWidget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
?>

<header class="header">
    <div class="header__top">
        <div class="container">
            <?php
            echo Nav::widget([
                'options' => ['class' => 'nav header__top-navbar'],
                'items' => [
                    ['label' => 'Доставка', 'url' => ['/p/delivery']],
                    ['label' => 'Оплата', 'url' => ['/p/payment']],
                    ['label' => 'О компании', 'url' => ['/p/about']],
                    ['label' => 'Контакты', 'url' => ['/site/contact']],
                    ['label' => 'Отзывы', 'url' => ['/site/reviews']],
                ],
            ]);
            ?>
        </div>
    </div>
    <div class="header__middle">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-xs-6 col-xs-offset-3 col-md-offset-0">
                    <div class="row">
                        <div class="col-xs-12 header__logo">
                            <a class="navbar-brand" href="/">Купи</a>
                        </div>
                        <div class="col-xs-12 header__slogan">
                            Интернет-супермаркет
                        </div>

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
    <?php
    NavBar::begin([
        'options' => [
            'class' => 'header__navbar navbar',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav nav'],
        'items' => [
            \app\components\catalog\CatalogWidget::widget([
                    'visible' => isset($this->params['catalog']) && $this->params['catalog']
            ]),
            ['label' => 'Акции', 'url' => ['/']],
            ['label' => 'Скидки', 'url' => ['/']],
        ],
    ]);
    ?>

    <?php
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav nav header__personal'],
        'items' => [
            LoginWidget::widget(),
            "<li class=\"shopping\">".Yii::$app->shopping->render()."</li>",
        ],
    ]);

    NavBar::end();
    ?>
    </div>

</header>

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