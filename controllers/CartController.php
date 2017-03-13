<?php

namespace app\controllers;

use app\models\Good\Good;
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
                    'add' => ['POST'],
                    'add-multiple' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex() {
        /** @var $cart \yz\shoppingcart\ShoppingCart */
        $cart = Yii::$app->cart;
        $array = $cart->getPositions();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $array
        ]);
        return $this->render('/cart', [
            'dataProvider' => $dataProvider,
            'cart' => $cart
        ]);
    }

    public function actionAdd()
    {
        $get = Yii::$app->request->post();
        if (Yii::$app->request->isAjax) {
            Yii::$app->cart->put(Good::findOneOr404($get['product_id'])->getCartPosition(),
                $get['product_count']);
        }
        return Yii::$app->shopping->render();
    }

    public function actionAddMultiple()
    {
        $get = Yii::$app->request->post();
        if (Yii::$app->request->isAjax) {

            /** @var $cart \yz\shoppingcart\ShoppingCart */
            $cart = Yii::$app->cart;
            foreach ($get['products'] as $item) {
                $cart->update(Good::findOneOr404($item['id'])->getCartPosition(), $item['count']);
            }
        }
        return Yii::$app->shopping->render();
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
        /** @var $user \app\models\User */
        /** @var $cart \yz\shoppingcart\ShoppingCart */
        $cart = Yii::$app->cart;
        if ($cart->isEmpty) {
            return $this->redirect('index');
        }
        $user = Yii::$app->user->identity;
        $order = new Order([
            'user_id' => $user->id,
            'public_id' => $user->id . '-' . $user->order_counter
        ]);
        if ($order->save()) {
            $user->order_counter++;
            $user->save();
            foreach($cart->getPositions() as $product) {
                $op = new OrderProduct([
                    'products_count' => $product->getQuantity(),
                    'product_c1id' => $product->getProduct()->c1id,
                    'order_id' => $order->id,
                    'product_name' => $product->getProduct()->name,
                    'old_price' => $product->getProduct()->price,
                ]);
                if (!$op->save()) {
                    Yii::error('Ошибка при оформлении заказа. ' .
                        implode(', ', $op->getFirstErrors()));
                }
            }
            $cart->removeAll();
            Yii::$app->session->setFlash('success', 'Заказ ' . $order->public_id . ' успешно оформлен.');
            return $this->redirect('/profile/');
        }
        Yii::error('Ошибка при оформлении заказа. ' .
            implode(', ', $order->getFirstErrors()));
    }
}
