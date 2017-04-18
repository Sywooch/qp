<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 01.03.2017
 * Time: 12:56
 */

namespace app\models\Good;


use yii\base\Object;
use yz\shoppingcart\CartPositionInterface;
use yz\shoppingcart\CartPositionTrait;

class ProductCartPosition extends Object implements CartPositionInterface
{
    use CartPositionTrait;
    /**
     * @var $_product Good
     */
    protected $_product;

    public $id;

    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->getProduct()->price;
    }

    /**
     * @return Good
     */
    public function getProduct()
    {
        if ($this->_product === null) {
            $this->_product = Good::findOkStatus($this->id);
        }
        return $this->_product;
    }
}
