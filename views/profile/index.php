<?php
/* @var $ordersDataProvider yii\data\ActiveDataProvider */

use app\components\Html;
use yii\grid\GridView;

$this->params['profileLayout'] = true;
$this->title = 'История покупок';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>История покупок</h1>

<div class="product__table">
    <?= GridView::widget([
        'dataProvider' => $ordersDataProvider,
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return  Html::a($order->id, ['/profile/order/view', 'id' => $order->id]);
                },
                'enableSorting'=>TRUE,
            ],
            'created_at:datetime',
            'totalPriceHtml',
            'confirmedPriceHtml',
            [
                'attribute' => 'status',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return  $order->status_str;
                },
                'enableSorting'=>TRUE,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{order-repeat}',
                'buttons' => [
                    'order-repeat' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-refresh"></i>',
                            $url, ['title' => 'Повторить заказ']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
