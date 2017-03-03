<?php

use app\components\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product__table">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'Номер заказа',
                'format' => 'raw',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return
                        Html::a($order->public_id, ['/profile/order/view', 'id' => $order->id]);
                }
            ],
            'created_at:datetime',
            [
                'attribute' => 'Сумма',
                'format' => 'raw',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return  Html::price($order->getTotalPrice());
                }
            ],
            [
                'attribute' => 'Статус',
                'format' => 'raw',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return  'Выполнен';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{order-password}',
                'buttons' => [
                    'order-password' => function ($url, $model) {
                        return Html::a(
                            '<i class="fa fa-vcard-o"></i>',
                            $url, ['title' => 'Ввести пароль']);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
