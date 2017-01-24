<?php
/** @var $product app\models\Good\Good */

use yii\helpers\Html;

$img = Html::img([ $product->getImgPath() ],
    ['height'=>204, 'width'=>270, 'class'=>'img-responsive']);

$url = ['product/view', 'id' => $product->id];
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <div class="product card">
        <div class="thumbnail">
            <figure>
                <?=Html::a($img, $url)?>
            </figure>
        </div>
        <div class="caption">
            <div class="product-title">
                <?=Html::a($product->name, $url)?>
            </div>
        </div>
        <div class="product-panel">
            <div class="btn-group">
                <label class="product-price">
                    <?= $product->price ?>
                </label>
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
