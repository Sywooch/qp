<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Good\Good */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [ 'attribute' => 'measure', 'value' => $model->getMeasureString() ],
            'c1id',
            'name',
            'pic',
            'price',
            'category_id',
        ]
    ]);
    if ($prop = $model->properties) {
        echo 'Свойства';
        echo DetailView::widget([
            'model' => $model,
            'attributes' => array_map(function($key, $val) {
                return [ 'value' => $val['value'], 'attribute' => $key ];
            }, array_keys($prop), $prop)
        ]);
    }
    ?>
</div>
