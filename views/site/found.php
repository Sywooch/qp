<?php
use yii\helpers\Html;
use app\components\catalog\ProductWidget;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $productDataProvider yii\data\ActiveDataProvider */
/* @var $categoryDataProvider yii\data\ActiveDataProvider */
/* @var $query string */

$this->title = "Результаты поиска";
$this->params['breadcrumbs'][] = $this->title;

?>
<h1>Результат поиска по запросу: "<?=$query?>"</h1>
<h2>Товары</h2>
<div class="row">
    <?= ListView::widget([
        'dataProvider' => $productDataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a($model->name, ['product/view', 'id' => $model->id]);
        },
    ]) ?>
    <h2>Категории</h2>
    <?= ListView::widget([
        'dataProvider' => $categoryDataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a($model->name, ['catalog/view', 'id' => $model->id]);
        },
    ]) ?>
</div>