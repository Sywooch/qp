<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Good\Good */
/* @var $category app\models\Good\Menu */

$this->title = $model->name;


foreach($category->parents()->all() as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['catalog/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] =  [
    'label' => $category->name,
    'url' => Url::to(['catalog/view', 'id' => $category->id])
];
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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'measure',
            'c1id',
            'name',
            'pic',
            'price',
            'category_id',
            'properties',
        ],
    ]) ?>

</div>
