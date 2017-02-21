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
        'columns' => [
            'created_at:datetime',
            [
                'attribute' => 'Номер заказа',
                'format' => 'raw',
                'value' => function ($order) {
                    /* @var $order app\models\Order*/
                    return  '123123';
                }
            ],
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
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-eye"></i>',
                            ['/profile/order/view', 'id' => $model->id]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
