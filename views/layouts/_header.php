<?php

use app\components\LoginWidget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

?>

<header class="header">
    <div class="header__inner">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-xs-12 header__logo">
                            <a class="navbar-brand" href="/">Купи</a>
                        </div>
                        <div class="col-xs-12 header__slogan">
                            Интернет-супермаркет
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <section class="search">
                        <form action="/" method="GET" class="form form-search">
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
                <div class="col-md-3">
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

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav nav header__personal'],
        'items' => [
            LoginWidget::widget(),
            Yii::$app->shopping->render(),
        ],
    ]);

    NavBar::end();
    ?>
</header>