<?php

use app\components\Html;
use yii\web\View;
use yii\helpers\Url;
use yii\caching\TagDependency;
use app\models\Good\Menu;

/* @var $this yii\web\View */
/* @var $product app\models\Good\Good */
/* @var $category app\models\Good\Menu */

$this->title = $product->name;

$this->params['sidebarLayout'] = true;

foreach(Yii::$app->db->cache(function() use($category) {
    return $category->parents()->all();
}, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()])) as $par) {
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
<div class="product-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-sm-5 col-xs-12">
            <div class="thumbnails">
                <div class="product-image">
                    <?=Html::img([ $product->getImgPath() ], ['height'=>204, 'width'=>270, 'class'=>'img-responsive', 'data-product-id'=>$product->id])?>
                </div>
            </div>
        </div>
        <div class="col-sm-7 col-xs-12 product-detail">

            <div class="price">
                <?=Html::price($product->price)?>
            </div>
            <div class="product-panel">
                <div class="btn-group">
                    <input type="number" min="1" value="1"
                           name="product_count"
                           class="product_count"
                           data-product-id="<?= $product->id ?>">
                    <input type="hidden" name="product_id" value=<?= $product->id ?>>
                </div>
                <button class="btn btn-icon btn-icon-left btn-success btn-compare"
                        data-product-id="<?= $product->id ?>"
                        data-product-count="1">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i> Добавить в корзину
                </button>
            </div>

            <div>
                <h3>Характеристики:</h3>
                <table class="table product-info">
                    <?php
                    foreach ($product->properties as $key => $value) {
                        echo "<tr><td>" . $key . "</td><td>" . $value['value'] . "</td></tr>";
                    }
                    ?>
                </table>
            </div>


        </div>
    </div>
</div>
