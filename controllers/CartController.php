<?php

namespace app\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use app\models\Order;
use app\models\OrderProduct;

class CartController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        /** @var $cart \yz\shoppingcart\ShoppingCart */
        $cart = Yii::$app->cart;
        $array = $cart->getPositions();
        $dataProvider = new ArrayDataProvider([ 'allModels' => $array ]);
        return $this->render('/cart', [
            'dataProvider' => $dataProvider,
            'cart' => $cart
        ]);
    }

    public function actionDelete($id)
    {
        Yii::$app->cart->removeById($id);

        return $this->redirect(['index']);
    }

    public function actionClear()
    {
        Yii::$app->cart->removeAll();

        return $this->redirect(['index']);
    }

    public function actionOrder()
    {
        $order = new Order([ 'user_id' => Yii::$app->user->id ]);
        if ($order->save()) {
            /** @var $cart \yz\shoppingcart\ShoppingCart */
            $cart = Yii::$app->cart;
            foreach($cart->getPositions() as $product) {
                $op = new OrderProduct([
                    'products_count' => $product->getQuantity(),
                    'product_c1id' => $product->c1id,
                    'order_id' => $order->id,
                ]);
                if (!$op->save()) {
                    Yii::error('Ошибка при оформлении заказа. ' .
                        implode(', ', $op->getFirstErrors()));
                }
            }
            $cart->removeAll();
            return $this->render('/order', [ 'order' => $order ]);
        }

        Yii::error('Ошибка при оформлении заказа. ' .
            implode(', ', $order->getFirstErrors()));
    }
}
