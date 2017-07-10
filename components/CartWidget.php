<?php

namespace app\components;

use yii\bootstrap\Widget;
use Yii;

class CartWidget extends Widget
{
    public $content;
    public $price;
    public $countProduct;

    public $mobile;

    public $result;

    public function init()
    {
        parent::init();
        $this->set();
        echo $this->content;
    }

    public function set()
    {
        /** @var \yz\shoppingcart\ShoppingCart $cart */
        $cart = Yii::$app->get('cart');
        $this->price = $cart->getCost();
        $this->countProduct = $cart->getCount();
        if($this->countProduct < 1) {
            $this->result = '<span class="cart-number">0</span>';
        } else {
            $price = '<span class="shopping__price">'
                . Html::price($this->price)
                . '</span>';

            $this->result = $this->mobile ?
                        Html::tag(
                            'span',
                            $this->countProduct. ' ' . Html::ending($this->countProduct, ['товар', 'товара', 'товаров']),
                            ['class' => 'counter']
                        ) . " на " . $price
                        : $price. ' ' . Html::tag(
                            'span',
                            $this->countProduct,
                            ['class' => 'counter', 'class'=>"badge", 'data-toggle'=>"visible", 'data-of'=>"totalCount"]
                        );
        }
        $this->content = '
            <a href="/cart">
                <span class="icon lnr lnr-cart"></span> '. $this->result
            . '</a>';
    }
}
