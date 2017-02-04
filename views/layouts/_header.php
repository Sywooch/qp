<?php

use app\components\LoginWidget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
?>

<header class="header">
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
                <div class="col-md-6 col-xs-12">
                    <section class="search row">
                        <form action="/" method="GET" class="form form-search col-xs-9 col-md-12">
                            <div class="input-group">
                            <span class="input-group-addon">
                                <button class="search__btn">
                                    <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search icons</span>
                                </button>
                            </span>
                                <input id="search-input" class="form-control input-lg" placeholder="Поиск среди более 10 000 товаров" autocomplete="off" spellcheck="false" autocorrect="off" tabindex="1">
                            </div>
                        </form>
                    </section>
                </div>
                <div class="col-md-3 hidden-xs hidden-sm">
                    <div class="header__phone">
                        8 (800) 123-12-12
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    NavBar::begin([
        'options' => [
            'class' => 'header__navbar navbar',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav nav'],
        'items' => [
            ['label' => 'Главная', 'url' => ['/site/index']],
            ['label' => 'Доставка и оплата', 'url' => ['/site/about']],
            ['label' => 'О компании', 'url' => ['/site/about']],
            ['label' => 'Контакты', 'url' => ['/site/contact']],
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

    <div class="header__bottom">

    </div>

</header>

<div class="modal fade bs-search-modal-lg" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="searchLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <form action="/" method="GET" class="form form-search col-xs-9">
                        <div class="input-group">
                        <span class="input-group-addon">
                            <button class="search__btn">
                                <i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Search icons</span>
                            </button>
                        </span>
                            <input id="search-input-mobile" class="form-control input-lg search-input-mobile" placeholder="Поиск среди более 10 000 товаров" autocomplete="off" spellcheck="false" autocorrect="off" tabindex="1">
                        </div>
                    </form>
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