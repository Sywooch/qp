<?php

use app\assets\ManagerAsset;
use app\components\Html;
use yii\grid\GridView;

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
                'columns' => [
                    [
                        'attribute' => 'ref',
                        'format' => 'raw',
                        'value' => function ($order) {
                            /* @var $order app\models\Order*/
                            return Html::a($order->public_id, ['view-order', 'id' => $order->id]);
                        }
                    ],
                    'user.email',
                    'created_at:datetime',
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
                    'status_str',
                ],
            ]); ?>
        </div>
    </div>
</div>


