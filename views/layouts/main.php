<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
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
            Yii::$app->user->isGuest ? (
            ['label' => 'Вход и регистрация', 'url' => ['/site/login']]
            ) : (
                '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    'Logout (' . Yii::$app->user->identity->email . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>'
            ),
            Yii::$app->shopping->render(),
        ],
    ]);

    NavBar::end();
    ?>
    </header>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
