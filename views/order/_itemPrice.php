<?php
/* @var $cart_position app\models\Good\ProductCartPosition */
use app\components\Html;

?>

<div class="cart-item" data-product-id="<?=$cart_position->id?>" data-product-price="<?=$cart_position->getPrice()/100?>">
    <span class="cart-item__quantity">
        <?=$cart_position->getQuantity()?>
    </span>
    x <?=Html::price($cart_position->getPrice())?>
    <br/>
    <span class="cart-item__sum">
        = <?=Html::price($cart_position->getQuantity() * $cart_position->getPrice())?>
    </span>
</div>
