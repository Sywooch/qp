<?php

namespace app\components;

use yii\base\Component;
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
        $this->price = 0;
        $this->countProduct = 0;
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
            <a href="javascript:void(0)">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i> '. $this->result
            . '</a>';
    }

    public function render()
    {
        $this->set();
        return '<li class="shopping">' . $this->content . '</li>';
    }
}