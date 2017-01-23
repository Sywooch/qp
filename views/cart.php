<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */

$this->title = 'Корзина';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'measure',
            'name',
            'pic',

            ['attribute' => 'Количество', 'value' => function ($model) {
                /* @var $model app\models\Good\Good */
                return  $model->getQuantity();
            }],
            ['attribute' => 'Сумма', 'value' => function ($model) {
                /* @var $model app\models\Good\Good */
                return  $model->getQuantity() * $model->getPrice();
            }],
            // 'price',
            // 'category_id',
            // 'properties',

            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [ 'update' => false, 'view' => false],
            ],
        ],
    ]) ?>

    <p>
        <?= Html::a('Очистить корзину', ['clear'], [
            'class' => 'btn btn-danger',
            'data-confirm' => 'Вы уверены, что хотите очистить корзину?'
        ]) ?>
        <?= Html::a('Купить', [''], [
            'class' => 'btn btn-success ',
        ]) ?>
    </p>
</div>
