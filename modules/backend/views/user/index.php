<?php

use app\components\TimeAgoWidget\TimeAgoWidget;
use yii\grid\GridView;
use app\components\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

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
