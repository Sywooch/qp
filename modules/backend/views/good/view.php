<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Good\PropertyValue;
use app\models\Good\GoodProperty;

/* @var $this yii\web\View */
/* @var $model app\models\Good\Good */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены что хотите удалить этот товар?',
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
            'vendor',
            'provider',
            [ 'attribute' => 'status', 'value' => $model::$STATUS_TO_STRING[$model->status] ],
        ]
    ]);
    if ($prop = $model->properties) {
        echo 'Свойства';
        echo DetailView::widget([
            'model' => $model,
            'attributes' => array_map(function($key, $val) {
                return [
                    'value' => PropertyValue::cachedFindOne($val)->value,
                    'attribute' => GoodProperty::cachedFindOne($key)->name,
                ];
            }, array_keys($prop), $prop)
        ]);
    }
    ?>
</div>
