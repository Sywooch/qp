<?php

use app\components\Html;
use yii\web\View;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $product app\models\Good\Good */
/* @var $category app\models\Good\Menu */

$this->title = $product->name;


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
    <div class="row">
        <div class="col-sm-8 col-xs-12">
            <?=Html::img([ $product->getImgPath() ], ['height'=>204, 'width'=>270, 'class'=>'img-responsive'])?>
        </div>
        <div class="col-sm-4 col-xs-12">
            <label class="product-price">
                <?=Html::price($product->price)?>
            </label>
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
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</div>
