<?php
/* @var $ordersDataProvider yii\data\ActiveDataProvider */

use app\components\TimeAgoWidget\TimeAgoWidget;
use yii\grid\GridView;
use yii\helpers\Html;

$this->params['profileLayout'] = true;
$this->title = 'Сообщения';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>История покупок</h1>

<div class="product__table">
    <?= GridView::widget([
        'dataProvider' => $ordersDataProvider,
        'columns' => [
            [
                'attribute' => 'created_at',
                'label' => 'Создан',
                'value' => function ($model) {
                    return TimeAgoWidget::widget([
                        'datetime' => $model->created_at
                    ]);
                },
                'format' => 'raw',
                "contentOptions" => [
                    'style' => 'min-width: 140px'
                ],
            ],
            [
                'format' => 'raw',
                'value' => function($model) { return Html::decode($model->text); },
                'label' => 'Сообщение',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-eye"></i>',
                            str_replace('view', 'view-message', $url)
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
