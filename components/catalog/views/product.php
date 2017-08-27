<?php
/** @var $product app\models\Good\Good */

use app\models\Bookmark;
use app\components\Html;

$img = Html::img([ $product->getImgPath() ],
    ['height'=>204, 'width'=>270, 'class'=>'img-responsive', 'data-product-id'=>$product->id]);

$url = ['product/view', 'id' => $product->id];
$bookmark = Bookmark::cachedFindOne([
    'product_id' => $product->id,
    'user_id' => Yii::$app->user->getId()
]);
if (!$bookmark) {
    $bookmark = new Bookmark([
        'user_id' => Yii::$app->user->getId(),
        'product_id' => $product->getId(),
    ]);
}
?>
<div class='col-md-4 col-sm-6 col-xs-12'>
    <div class="product">
        <?=Html::a($img, $url, ['class' => 'thumbnail'])?>
        <?=Html::a($product->name, $url, ['class' => 'product-name'])?>
        <div class="product-price">
            <?=Html::price($product->price)?>
        </div>
        <div class="product-action">
            <button class="btn product-to-bookmark btn-default bookmark <?=$bookmark->isNewRecord ? '' : 'active'?>"
                    data-product-id="<?= $product->id ?>"
                    data-placement="top"
                    title="<?=$bookmark->isNewRecord ? 'В избранное' : 'В избранном'?>">
                <span class="icon lnr lnr-heart"></span>
                <span class="counter"><?= $product->getBookmarksCount() ? $product->getBookmarksCount() : '' ?></span>
            </button>

            <?php if($product->readyToSale()) : ?>
                <button class="btn product-to-cart btn-success btn-compare"
                        data-product-id="<?= $product->id ?>"
                        data-product-count="1"
                        data-active="1">
                    Купить
                </button>
            <?php else: ?>
                <span class="product-disabled">Нет в наличии</span>
            <?php endif; ?>
        </div>

    </div>
</div>
