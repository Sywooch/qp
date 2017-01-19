<?php
/** @var $product app\models\Menu */
use yii\helpers\Html;
$img = Html::img(['@web/img/catalog/product/1.png'],
    ['height'=>204, 'width'=>270, 'class'=>'img-responsive']);

$url = ['catalog/view', 'id' => 1];
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <div class="product card">
        <div class="thumbnail">
            <div class="image">
                <?=Html::a($img, $url)?>
            </div>
        </div>
        <div class="caption">
            <div class="product-title">
                <?=Html::a('Бараны', $url)?>
            </div>
        </div>
        <div class="product-panel">
            <form class="form-inline">
                <div class="btn-group">
                    <label class="product-price">
                        80.00
                    </label>
                    <input type="number" min="1" value="1">
                </div>
                <button class="btn btn-icon btn-icon-left btn-success">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </button>
            </form>

        </div>
    </div>
</div>
