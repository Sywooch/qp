<?php
use app\components\Html;
$this->title = 'Панель администратора';

/**
 * @var $this yii\web\View
 * @var int $userTotal
 * @var int $orderTotal
 * @var int $goodTotal
 * @var int $salesTotal
 * @var $provider app\modules\backend\models\UploadProvider
 */

$infoBoxes = [
    [
        'icon' => 'lnr lnr-cart',
        'title' => 'Всего заказов',
        'number' => $orderTotal,
        'url' => ['/backend/order']
    ], [
        'icon' => 'fa fa-credit-card',
        'title' => 'Всего продаж',
        'number' => Html::price($salesTotal),
        'url' => ['/backend/default/report']
    ], [
        'icon' => 'lnr lnr-users',
        'title' => 'Пользователей',
        'number' => $userTotal,
        'url' => ['/backend/user']
    ], [
        'icon' => 'lnr lnr-layers',
        'title' => 'Товаров',
        'number' => $goodTotal,
        'url' => ['/backend/good']
    ]
]
?>

<div class="row">
    <?php foreach ($infoBoxes as $box) : ?>
        <div class="col-md-6 col-lg-3">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="<?= $box['icon']?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"><?= $box['title']?></span>
                    <span class="info-box-number"><?= $box['number']?></span>
                </div>
                <div class="info-box-footer">
                    <?= Html::a('Подробней...', $box['url'], ['style' => 'padding: 10px; font-size: 13px;'])?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
