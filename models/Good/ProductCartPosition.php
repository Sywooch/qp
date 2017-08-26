<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 01.03.2017
 * Time: 12:56
 */

namespace app\models\Good;


use Yii;
use yii\base\Object;
use yz\shoppingcart\CartPositionInterface;
use yz\shoppingcart\CartPositionTrait;
use yii\web\NotFoundHttpException;

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
            $this->_product = Good::findOne($this->id);
        }

        if (!$this->_product){
            /** @var $cart \yz\shoppingcart\ShoppingCart */
            $cart = Yii::$app->cart;
            $cart->remove($this);
            throw new NotFoundHttpException("Товар с ИД $this->id несуществует.");
        }
        return $this->_product;
    }
}
