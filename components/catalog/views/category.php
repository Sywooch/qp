<?php
/* @var $item app\models\Menu */
use yii\helpers\Html;

$img = Html::img(['@web/img/catalog/category/1.png'],
    ['height'=>204, 'width'=>270, 'class'=>'img-responsive']);

$url = ['catalog/view', 'id' => $item->id];
?>
<div class='col-md-3 col-sm-4 col-xs-6'>
    <div class="category card">
        <div class="thumbnail">
            <div class="image">
                <?=Html::a($img, $url)?>
            </div>
            <div class="caption">
                <div class="category-title">
                    <?=Html::a($item->name, $url)?>
                </div>
            </div>
        </div>
    </div>
</div>
