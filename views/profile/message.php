<?php
/* @var $ordersDataProvider yii\data\ActiveDataProvider */

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
            'created_at:datetime',
            [
                'format' => 'raw',
                'value' => function($model) { return Html::decode($model->text); },
                'label' => Yii::t('app', 'Some Label')
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
