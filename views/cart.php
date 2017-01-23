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
            [
                'format' => 'html',
                'value' => function ($product) {
                    /* @var $product app\models\Good\Good */
                    return  Html::img([ $product->getImgPath() ],
                        [ 'height'=>100, 'width'=>100, 'class'=>'img-responsive' ]
                    );
                }
            ],
            'name',
            ['attribute' => 'Количество', 'value' => function ($product) {
                /* @var $product app\models\Good\Good */
                return  $product->getQuantity();
            }],
            'price',
            ['attribute' => 'Сумма', 'value' => function ($product) {
                /* @var $model app\models\Good\Good */
                return  $product->getQuantity() * $product->getPrice();
            }],

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
