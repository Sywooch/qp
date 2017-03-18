<?php
use app\components\Html;
use yii\helpers\Url;

$menuShop = [
    ['label' => 'О компании', 'url' => ['/site/about']],
    ['label' => 'Каталог', 'url' => ['/site/about']],
    ['label' => 'Контакты', 'url' => ['/site/about']],
    ['label' => 'Отзывы', 'url' => ['/site/reviews']],
];
$menuUser = [
    ['label' => 'Доставка', 'url' => ['/site/about']],
    ['label' => 'Оплата', 'url' => ['/site/about']],
    ['label' => 'Помощь', 'url' => ['/site/about']],
    ['label' => 'Возврат товара', 'url' => ['/site/about']],
];
?>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <span class="footer-title">
                            Магазин
                        </span>
                        <ul class="footer-nav">
                            <?php
                            foreach ($menuShop as $item) {
                                echo "<li>". Html::a($item['label'], $item['url']) ."</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <span class="footer-title">
                            Покупателям
                        </span>
                        <ul class="footer-nav">
                            <?php
                            foreach ($menuUser as $item) {
                                echo "<li>". Html::a($item['label'], $item['url']) ."</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 hidden-xs">
                <div class="row">
                    <div class="col-md-6">
                        <span class="footer-title">Время работы</span>
                        <div class="footer-hours">ПН-ПТ: 9:00-19:00<br/> СБ-ВС: 9:00-16:00</div>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-phone">
                            <?=isset(Yii::$app->params['phone.manager']) ? Yii::$app->params['phone.manager'] : "Номер телефона"; ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <p class="footer-subscribe-text">Подпишитесь на нашу рассылку и вы будете получать новости о новых поступлениях в нашем магазине</p>
                    </div>
                    <div class="col-md-12">
                        <?= Html::beginForm(Url::to(['/site/subscribe']), 'get', ['class' => 'form form-search col-xs-9 col-md-12']) ?>
                        <div class="input-group">

                            <?= Html::textInput('email', '', ['class' => 'form-control ', 'placeholder' => 'ВАША ЭЛЕКТРОННАЯ ПОЧТА']) ?>
                            <span class="input-group-addon">
                                <?= Html::submitButton('ПОДПИСАТЬСЯ', ['class' => 'btn subscribe__btn']) ?>
                            </span>
                        </div><!-- /input-group -->
                        <?= Html::endForm() ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3">

            </div>
            <div class="col-md-3">

            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-sm-3"><p>&copy; qpvl <?= date('Y') ?></p></div>
                <div class="col-sm-3">
                    <?=Html::a('Пользовательское соглашение', ['/p/rules'])?>
                </div>
            </div>

        </div>
    </div>
</footer>

<div class="drag-target" data-sidenav="nav-mobile" style="left: 0px; touch-action: pan-y; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div>