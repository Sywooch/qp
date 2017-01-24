<?php
/* @var $item app\models\Good\Menu */
use yii\helpers\Html;

$img = Html::img(['@web/img/catalog/category/1.png'],
    ['height'=>204, 'width'=>270, 'class'=>'img-responsive']);

$url = ['catalog/view', 'id' => $item->id];
?>
<div class='col-md-3 col-sm-4 col-xs-6'>
    <div class="category card">
        <?=Html::a($img, $url, ['class' => 'thumbnail'])?>
        <div class="caption">
            <div class="category-title">
                <?=Html::a($item->name, $url)?>
            </div>
        </div>
    </div>
</div>
