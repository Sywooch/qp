<?php

namespace app\controllers;

use app\models\Good\Good;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Order;
use app\models\OrderProduct;
use app\components\CartWidget;

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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['order'],
                'rules' => [[
                    'allow' => true,
                    'actions' => ['order'],
                    'roles' => ['user'],
                ]]
            ]
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
            $pid = $get['product_id'];
            if ($pr = Good::findOkStatus($pid)) {
                Yii::$app->cart->put($pr->getCartPosition(),
                    $get['product_count']);
            }
            else {
                Yii::$app->session->addFlash('error', "Товар с id $pid недоступен.");
            }
        }
        return CartWidget::widget();
    }

    public function actionAddMultiple()
    {
        $get = Yii::$app->request->post();
        if (Yii::$app->request->isAjax) {

            /** @var $cart \yz\shoppingcart\ShoppingCart */
            $cart = Yii::$app->cart;
            foreach ($get['products'] as $item) {
                $pid = $item['id'];
                if ($pr = Good::findOkStatus($pid)){
                    $cart->update($pr->getCartPosition(), $item['count']);
                }
                else {
                    Yii::$app->session->addFlash('error', "Товар с id $pid недоступен.");
                }
            }
        }
        return CartWidget::widget();
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
            foreach($cart->getPositions() as $position) {
                $op = new OrderProduct([
                    'products_count' => $position->getQuantity(),
                    'order_id' => $order->id,
                ]);
                if (!$op->fillWithProduct($position->id)) {
                    continue;
                }
                if (!$op->save()) {
                    Yii::$app->session->addFlash('error',
                        'Ошибка при оформлении заказа. ' . implode(', ', $op->getFirstErrors()));
                }
            }
            $cart->removeAll();

            if ($order->getOrderProducts()) {
                Yii::$app->session->addFlash('success', 'Заказ ' . $order->public_id . ' успешно оформлен.');
                Yii::$app->user->identity->sendMessage($order->getLink() . ' успешно оформлен.');
                return $this->redirect(['/profile/order/view', 'id' => $order->id ]);
            }
            else {
                Yii::$app->session->addFlash('error', 'Не удалось добавить ни одного товара в заказ.');
                $order->delete();
                return $this->redirect(['/index' ]);
            }


        }
        Yii::error('Ошибка при оформлении заказа. ' .
            implode(', ', $order->getFirstErrors()));
    }
}
