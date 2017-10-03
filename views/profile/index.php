<?php
/* @var $ordersDataProvider yii\data\ActiveDataProvider */

use app\components\Html;
use app\components\TimeAgoWidget\TimeAgoWidget;
use app\models\Order;
use kartik\grid\GridView;
use yii\helpers\Url;

$this->params['profileLayout'] = true;
$this->title = 'История покупок';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>История покупок</h1>

<div class="product__table">
    <?= GridView::widget([
        'dataProvider' => $ordersDataProvider,
        'export' => false,
        'responsive' => true,
        'hover' => true,
        "rowOptions" => function (Order $order) {
            return [
                "class" => "order-item",
                "data-route" => Url::to(['/profile/order/view', 'id' => $order->id])
            ];
        },
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
            [
                'attribute' => 'created_at',
                'label' => 'Создан',
                'value' => function ($order) {
                    return TimeAgoWidget::widget([
                        'datetime' => $order->created_at
                    ]);
                },
                'format' => 'raw',
                "contentOptions" => [
                    'style' => 'min-width: 140px'
                ],
            ],
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
