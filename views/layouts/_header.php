<?php

use app\components\LoginWidget;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
$this->registerJs('
$(".btn-compare").click(function () {
            $.ajax({
                url: "'.\yii\helpers\Url::toRoute(['catalog/add']).'",
                dataType: "html",
                type: "POST",
                data: "product-id="+this.getAttribute(\'data-product-id\')+"&product-count="+this.getAttribute(\'data-product-count\'),
                success: function(data){
                    $(".shopping").html(data);
                },
                error: function () {
                    $("#shopping").html("ERROR");
                }
            });
        });
');
?>

<header class="header">
    <div class="header__inner">
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
                    <button class="btn search-visible">
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
                        <div class="col-xs-2 search__cancel visible-xs visible-sm">
                            <button class=" btn search-hidden">Отмена</button>
                        </div>
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
</header>

<div class="modal-search"></div>