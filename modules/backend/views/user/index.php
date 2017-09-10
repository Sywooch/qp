<?php

use app\components\TimeAgoWidget\TimeAgoWidget;
use kartik\grid\GridView;
use app\components\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $sort = Yii::$app->getRequest()->get('sort'); ?>

    <p>
        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(
            'Импортировать в Excel',
            ['excel-export' . ($sort ? "?sort=$sort" : '')],
            ['class' => 'btn btn-primary pull-right']
        ) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'export' => false,
        'responsive' => true,
        'hover' => true,
        'columns' => [
            'id',
            'email:email',
            [ 'attribute' => 'status', 'value' => function($model) {
                /* @var $model app\models\User */
                return $model->getStatusString();
            } ],
            [ 'attribute' => 'role', 'value' => function($model) {
                /* @var $model app\models\User */
                return $model->getRole();
            }],
            [ 'attribute' => 'phone', 'value' => function($model) {
                /* @var $model app\models\User */
                return $model->getPhone();
            }],
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
                'attribute' => 'payment_sum',
                'value' => function($model) {
                    /* @var $model app\models\Good\Good */
                    return Html::unstyled_price($model->payment_sum);
                },
                'enableSorting'=>TRUE,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-eye"></i>',
                            $url);
                    },
                    'update' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-edit"></i>',
                            $url);
                    },
                    'delete' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-trash"></i>',
                            $url,  [
                                'data' => [
                                    'confirm' => 'Вы уверены?',
                                    'method' => 'post',
                                ]
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
