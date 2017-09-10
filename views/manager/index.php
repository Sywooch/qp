<?php

use app\assets\ManagerAsset;
use app\components\Html;
use app\components\TimeAgoWidget\TimeAgoWidget;
use app\models\Order;
use kartik\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\models\OrderFilterForm */

$this->title = 'Панель менеджера';
$this->params['breadcrumbs'][] = $this->title;


ManagerAsset::register($this);
?>

<div class="row">
    <div class="col-sm-4 col-md-3">
        <div class="filter">
            <?php
            echo $this->render('_filter_form', [
                'model' => $model
            ]);
            ?>
        </div>
    </div>
    <div class="col-sm-8 col-md-9">
        <div class="product__table">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'export' => false,
                'responsive' => true,
                'hover' => true,
                "rowOptions" => function (Order $order) {
                    return [
                        "class" => "order-item",
                        "data-route" => Url::to(['/manager/view-order', 'id' => $order->id])
                    ];
                },
                'columns' => [
                    [
                        'attribute' => 'ref',
                        'format' => 'raw',
                        'label' => '№',
                        'value' => function ($order) {
                            /* @var $order app\models\Order*/
                            return Html::a($order->id, ['view-order', 'id' => $order->id]);
                        }
                    ],
                    'user.email',
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
                    [
                        'attribute' => 'total_price',
                        'format' => 'raw',
                        'value' => function($x) { return Html::unstyled_price($x->total_price); }
                    ],
                    [
                        'attribute' => 'confirmed_price',
                        'format' => 'raw',
                        'value' => function($x) { return Html::unstyled_price($x->confirmed_price); }
                    ],
                    [
                        'attribute' => 'status_str',
                        'format' => 'raw',
                        'width' => '150',
                        'value' => function($x) { return $x->status_str; }
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>


