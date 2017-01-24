<?php

namespace app\components;

use yii\base\Component;
use Yii;
use yii\helpers\Html;

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
        $this->price = Yii::$app->cart->getCost();
        $this->countProduct = Yii::$app->cart->getCount();
        if($this->countProduct < 1) {
            $this->result = '<span>Корзина</span>';
        } else {
            $this->result = '
                    <span class="shopping__price">
                        ' . $this->price . ' <i class="fa fa-rub"></i>
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