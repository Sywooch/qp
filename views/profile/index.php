<?php
/* @var $ordersDataProvider yii\data\ActiveDataProvider */

use app\components\Html;
use yii\grid\GridView;

$this->params['profileLayout'] = true;
$this->title = 'История покупок';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Личный кабинет</h1>
<h3>История покупок</h3>

<div class="product__table">
    <?= GridView::widget([
        'dataProvider' => $ordersDataProvider,

        // TODO: order/view on click row
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['id' => $model['id'], 'onclick' => 'alert(this.id);'];
        },

        'columns' => [
            [
                'attribute' => 'Номер заказа',
                'format' => 'raw',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return  Html::a($order->public_id, ['/profile/order/view', 'id' => $order->id]);
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
