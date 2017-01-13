<?php
/** @var
 * $name string
 * $link string
 * $img string
 */
use yii\helpers\Html;
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <div class="product text-center">
        <a href=<?=$link?>>
            <div class="product-images">
                <?=Html::img([$img],
                    ['height'=>204, 'width'=>270, 'class'=>'img-responsive'])?>
            </div>
            <div class="product-title">
                <div class="h7 text-sbold">
                    <?=$name?>
                </div>
            </div>
        </a>
        <div class="product-panel">
            <form class="form-inline">
                <div class="btn-group">
                    <label class="product-price">
                        80.00
                    </label>
                    <input type="number" min="1" value="1">
                </div>
                <a href="#" class="btn btn-icon btn-icon-left btn-success">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                </a>
            </form>

        </div>
    </div>
</div>
