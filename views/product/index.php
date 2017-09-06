<?php

use app\components\AppliedFiltersWidget\AppliedFiltersWidget;
use app\components\Html;
use yii\helpers\Url;
use yii\caching\TagDependency;
use app\models\Good\Menu;
use yii\widgets\Breadcrumbs;

/* @var $this yii\web\View */
/* @var $category app\models\Good\Menu */
/* @var $mate app\models\Good\Menu */
/* @var $products array of app\models\Good\Good */
/* @var $filters null or array */
/* @var $prices null or array */
/* @var $applied_filters array or null*/
/* @var integer $offset */
$this->title = $category->name;

$this->params['nullLayout'] = true;

$productCount = $category->getProductCount();

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
    <div class="col-sm-4 col-md-3">
        <div class="filter">
            <?=\app\components\catalog\CatalogMateWidget::widget([
                'catalog' => $category
            ])?>
            <h3 class="qp-collapse-handler" data-toggle="filter-box">
                Фильтры
                <span class="arrow"></span>
                <span class="filter-loader"></span>
            </h3>
            <div class="qp-collapse fixed" id="filter-box">
                <?php if(count($filters)) : ?>
                    <?php if(count($prices) > 1) : ?>
                        <div class="filter__item">
                            <span class="filter__item-title">Цена, руб.</span>
                            <div class="text-subline"></div>
                            <div class="range-controls form-inline">
                                <input type="text" id="price_from" class="form-control" data-id="price_from" data-min="<?=Html::rubles(min($prices))?>" data-type="from" placeholder="<?=Html::rubles(min($prices))?>">
                                <span>—</span>
                                <input type="text" id="price_to" class="form-control" data-id="price_to" data-max="<?=Html::rubles(max($prices))?>" data-type="to" placeholder="<?=Html::rubles(max($prices))?>">
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
                        <button class="btn btn-success btn-apply qp-collapse-handler" data-toggle="filter-box">Применить</button>
                        <?=Html::a('Сбросить', ['catalog/view', 'id' => $category->id], ['class' => 'btn btn-default btn-refresh'])?>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <div class="col-sm-8 col-md-9">
        <div class="breadcrumbs hidden-xs hidden-sm">
            <?php
            echo Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]);
            ?>
        </div>
        <div class="good-index">
            <?=AppliedFiltersWidget::widget()?>
            <div class="row">
                <div class="col-xs-7 col-sm-12 col-lg-9 hidden-xs products-title">
                    <h1><?= Html::encode($this->title) ?></h1>
                    <span class="products-count text-nowrap text-ellipsis">
                        <?= $productCount. " " .Html::ending($productCount, ['товар', 'товара', 'товаров'])?>
                    </span>
                </div>
                <div class="col-xs-12 col-sm-12 col-lg-3 toolbar">
                    <?=$this->render("_ordering") ?>
                </div>
            </div>
            <div class="pjax-result row">
            <?=$this->render('_view', [
                'products' => $products,
                'offset' => $offset,
            ]); ?>
            </div>
            <div class="products-more">
                <!-- PRELOADER -->
                <div class="preloader"><div><em></em><em></em><em></em><em></em></div></div>
                <!-- //PRELOADER -->
                <a href="javascript:void(0)"
                   class="btn btn-default js-show-more"
                   data-product-count="<?= $productCount?>">
                    Показать ещё
                </a>
            </div>
        </div>
    </div>
</div>

