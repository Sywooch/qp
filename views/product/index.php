<?php

use app\components\Html;
use yii\bootstrap\Nav;
use yii\helpers\Url;
use app\components\catalog\ProductWidget;
use app\models\Good\Good;
use yii\caching\TagDependency;
use app\models\Good\Menu;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $category app\models\Good\Menu */
/* @var $mate app\models\Good\Menu */
/* @var parentID integer */
/* @var $products array of app\models\Good\Good */
/* @var $filters null or array [ prop_name => [ 'value' => [values], 'type' => type ]] */

$this->title = $category->name;

$this->params['nullLayout'] = true;

foreach(Yii::$app->db->cache(function ($db) use($category)
{
    return $category->parents()->all();
}, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()])) as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['catalog/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-sm-3">
        <div class="filter">
            <?=\app\components\catalog\CatalogMateWidget::widget([
                    'catalog' => $category
            ])?>
            <h3>Фильтры</h3>
            <div class="filter__item">
                <span class="filter__item-title">Цена, руб.</span>
                <div class="text-subline"></div>
                <div class="range-controls form-inline">
                    <input type="text" id="price_from" class="form-control" data-id="price_from" data-min="0" data-type="from" placeholder="0">
                    <span>—</span>
                    <input type="text" id="price_to" class="form-control" data-id="price_to" data-max="500" data-type="to" placeholder="500">
                </div>
                <div class="slider-range"></div>
            </div>

            <?php
            foreach ($filters as $key => $value) {
                echo $this->render('_filter', [
                    'title' => $key,
                    'options' => $value,
                ]);
            }
            ?>
        </div>
    </div>
    <div class="col-sm-9">
        <?php
        echo Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]);
        ?>
        <div class="good-index">

            <h1><?= Html::encode($this->title) ?></h1>

            <?php
            foreach ($products as $product) {
                echo ProductWidget::widget([
                    'product' => $product,
                ]);
            }
            ?>
        </div>
    </div>
</div>

