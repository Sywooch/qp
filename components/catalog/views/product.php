<?php
/** @var $product app\models\Good\Good */

use yii\helpers\Html;
$img = Html::img(['@web/img/catalog/good/' . $product->pic ],
    ['height'=>204, 'width'=>270, 'class'=>'img-responsive']);

$url = ['catalog/view', 'id' => 1];         // change to good view
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
            <form class="form-inline">
                <div class="btn-group">
                    <label class="product-price">
                        <?= $product->price ?>
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
