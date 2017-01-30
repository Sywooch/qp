<?php

namespace app\components;

use yii\base\Component;
use Yii;
use app\components\Html;

class ShoppingComponent extends Component
{
    public $content;
    public $price;
    public $countProduct;

    public $result;

    public function init()
    {
        parent::init();

    }

    public function set()
    {
        /** @var \yz\shoppingcart\ShoppingCart $cart */
        $cart = Yii::$app->get('cart');
        $this->price = $cart->getCost();
        $this->countProduct = $cart->getCount();
        if($this->countProduct < 1) {
            $this->result = '<span>Корзина</span>';
        } else {
            $this->result = '
                    <span class="shopping__price">
                        ' . Html::price($this->price) . '
                    </span>
                    <span class="badge" data-toggle="visible" data-of="totalCount" style="">
                    '. $this->countProduct .'
                    </span>';
        }
        $this->content = '
            <a href="/cart">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i> '. $this->result
            . '</a>';
    }

    public function render()
    {
        $this->set();
        return $this->content;
    }
}