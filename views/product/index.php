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
/* @var $products array of app\models\Good\Good */
/* @var $filters null or array */
/* @var $prices null or array */

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
<div class="row products-view" data-catalog-id="<?=$category->id?>">
    <div class="col-sm-3">
        <div class="filter">
            <?=\app\components\catalog\CatalogMateWidget::widget([
                    'catalog' => $category
            ])?>
            <h3>Фильтры</h3>
            <?php if(count($filters)) : ?>
                <?php if(count($prices) > 1) : ?>
                <div class="filter__item">
                    <span class="filter__item-title">Цена, руб.</span>
                    <div class="text-subline"></div>
                    <div class="range-controls form-inline">
                        <input type="text" id="price_from" class="form-control" data-id="price_from" data-min="<?=Html::rubles($prices[0])?>" data-type="from" placeholder="<?=Html::rubles($prices[0])?>">
                        <span>—</span>
                        <input type="text" id="price_to" class="form-control" data-id="price_to" data-max="<?=Html::rubles($prices[1])?>" data-type="to" placeholder="<?=Html::rubles($prices[1])?>">
                    </div>
                    <div class="slider-range"></div>
                </div>
                <?php endif; ?>

            <?php
            foreach ($filters as $filter) {
                echo $this->render('_filter', [
                    'filter' => $filter,
                ]);
            }
            ?>
            <div class="filter__item">
                <div class="filter-apply-btn btn btn-success animated">Показать</div>
                <button class="btn btn-success btn-apply">Показать</button>
            </div>
            <?php endif;?>
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
            <div class="pjax-result">

            </div>
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

