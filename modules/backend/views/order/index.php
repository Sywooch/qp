<?php

use app\components\TimeAgoWidget\TimeAgoWidget;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить заказ', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?php if(YII_DEBUG) : ?>
	    <p>
	        <?= Html::a('Добавить случайный заказ', ['random'], ['class' => 'btn btn-primary']) ?>
	    </p>
    <?php endif; ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'export' => false,
        'responsive' => true,
        'hover' => true,
        'columns' => [
            [
                'attribute' => 'created_at',
                'label' => 'Создан',
                'value' => function ($order) {
                    return TimeAgoWidget::widget([
                        'datetime' => $order->created_at
                    ]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'id',
                'label' => 'Номер заказа',
                'value' => function ($order) {
                    return Html::a(Html::encode("Заказ №" . $order->id),
                        Url::to(['order/update', 'id' => $order->id])) . " от " .
                        Html::a(Html::encode($order->user->email),
                        Url::to(['user/view', 'id' => $order->user->id]));
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'updated_at',
                'label' => 'Обновлён',
                'value' => function ($order) {
                    return TimeAgoWidget::widget([
                        'datetime' => $order->updated_at
                    ]);
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'status',
                'width'=>'150px',
                'value' => function ($order) {
                    return $order->status_str;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-edit"></i>',
                            $url);
                    },
                    'delete' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-trash"></i>',
                            $url,  ['data' => [
                                   'confirm' => 'Вы уверены?',
                                    'method' => 'post'
                            ]]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
