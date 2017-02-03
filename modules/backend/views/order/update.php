<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $order app\models\Order */


$this->title = "Редактирование заказа №$order->id";
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = "Редактирование заказа №$order->id";?>
<div class="order-product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $order,
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'product_c1id',
            'products_count',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::to([ "product-$action", 'id' => $model->id ]);
                },
                'buttons' => [
                    'update' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-edit"></i>',
                            $url);
                    },
                    'delete' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-trash"></i>',
                            $url);
                    },
                ],
            ],
        ],
    ]); ?>
    <p>
        <?= Html::a('Добавить товар', ['product-create', 'order_id' => $order->id], ['class' => 'btn btn-success']) ?>
    </p>
</div>
