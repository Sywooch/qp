<?php
use app\components\Html;
use yii\helpers\Url;

$menuShop = [
    ['label' => 'О компании', 'url' => ['/p/about']],
    ['label' => 'Каталог', 'url' => ['/catalog']],
    ['label' => 'Контакты', 'url' => ['/site/contact']],
    ['label' => 'Отзывы', 'url' => ['/site/reviews']],
];
$menuUser = [
    ['label' => 'Доставка', 'url' => ['/p/delivery']],
    ['label' => 'Оплата', 'url' => ['/p/payment']],
    ['label' => 'Как оформить заказ', 'url' => ['/p/order-individual']],
    ['label' => 'Возврат товара', 'url' => ['/p/purchase-returns']],
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
                        <div class="footer-phone">
                            <?=isset(Yii::$app->params['phone.manager']) ? Yii::$app->params['phone.manager'] : "Номер телефона"; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <span class="footer-title">Способы оплаты</span>
                        <div>
                            <?=Html::img('@web/img/payments.png')?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-sm-3"><p>&copy; qpvl <?= date('Y') ?></p></div>
                <div class="col-sm-3">
                    <?=Html::a('Правовая информация', ['/p/rules'])?>
                </div>
            </div>

        </div>
    </div>
</footer>

<div class="drag-target" data-sidenav="nav-mobile" style="left: 0; touch-action: pan-y; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></div>
<div class="mobile-footer visible-xs visible-sm">
    <ul>
        <li class="shopping"><?=\app\components\CartWidget::widget()?></li>
        <li>
            <a href="#" data-toggle="modal" data-target="#search-modal">
                <span class="icon-mobile lnr lnr-magnifier"></span>
                <span class="link">Поиск</span>
            </a>
        </li>
        <li>
            <a href="<?=Url::to(['/profile/bookmark'])?>">
                <span class="icon-mobile lnr lnr-heart"></span>
                <span class="link bookmark-text">Избранное</span>
            </a>
        </li>
    </ul>
</div>