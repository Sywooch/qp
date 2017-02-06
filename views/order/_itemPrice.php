<?php
/* @var $product app\models\Good\Good */
use app\components\Html;

?>

<div class="cart-item" data-product-id="<?=$product->id?>" data-product-price="<?=$product->getPrice()/100?>">
    <span class="cart-item__quantity">
        <?=$product->getQuantity()?>
    </span>
    x <?=Html::price($product->getPrice())?>
    <br/>
    <span class="cart-item__sum">
        = <?=Html::price($product->getQuantity() * $product->getPrice())?>
    </span>
</div>
