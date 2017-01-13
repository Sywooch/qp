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
    </div>
</div>
