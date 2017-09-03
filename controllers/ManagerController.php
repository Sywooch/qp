<?php

namespace app\controllers;

use app\models\OrderFilterForm;
use app\models\OrderProduct;
use app\models\Profile\Message;
use Yii;
use app\models\Order;
use yii\caching\TagDependency;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class ManagerController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['manager'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'order-ready' => ['POST'],
                    'secret' => ['POST'],
                    'get-orders-json' => ['GET']
                ],
            ],
        ];
    }

    public function actionSecret()
    {
        if ($pass = Yii::$app->request->post('password')) {
            if (!$order = Order::findOne([ 'password' => $pass ])) {
                Yii::$app->session->setFlash('error', 'Неверный секретный ключ.');
                return $this->redirect([ '/manager' ]);
            }
            if ($order->status != Order::STATUS_DELIVERED) {
                Yii::$app->session->setFlash('error', 'Заказ ' . $order->id  . ' не готов к выдаче.');
                return $this->redirect([ 'view-order', 'id' => $order->id ]);
            }
            else {
                $order->status = Order::STATUS_DONE;
                $order->save();
                Yii::$app->session->setFlash('success', 'Заказ ' . $order->id  . ' выдан.');
                return $this->redirect([ 'view-order', 'id' => $order->id ]);
            }
        }
        return $this->redirect([ '/manager' ]);
    }

    public function actionIndex()
    {
        $model = new OrderFilterForm();
        $model->load(Yii::$app->request->get(), '');
        $model->validate();
        return $this->render('index', [
            'dataProvider' => $model->getOrders(),
            'model' => $model,
        ]);

    }


    public static function order2array($order) {
        return [
            'id_order' => $order->id,
            'email' => $order->user->email,
            'created' => date('d-m-Y H:i:s', $order->created_at),
            'status' => $order->status_str,
            'price' => $order->confirmed_price / 100,
        ];
    }

    public function actionGetOrdersJson() {
        $model = new OrderFilterForm();
        $model->load(Yii::$app->request->get(), '');
        $model->validate();
        return json_encode(array_map('self::order2array', $model->getOrders()->models));
    }

    private function getOrderContent($id) {
        $order = Order::findOneOr404($id);
        $products = Yii::$app->db->cache(function ($db) use ($order) {
            return $order->orderProducts;
        }, null, new TagDependency(['tags' => 'cache_table_' . OrderProduct::tableName()]));

        return [
            'products' => $products,
            'order' => $order,
        ];
    }

    public function actionGetOrderContentJson($id) {
        $content = $this->getOrderContent($id);
        $content['order'] = self::order2array($content['order']);

        $content['total_price'] = 0;
        $content['total_products'] = 0;
        $content['products'] = array_map(function ($x) use(&$content){
            $price_sum = $x->old_price * $x->confirmed_count/ 100;
            $content['total_price'] += $price_sum;
            $content['total_products'] += $x->confirmed_count;
            return [
                'product_name' => $x->product_name,
                'price' => $x->old_price / 100,
                'products_count' => $x->confirmed_count,
                'price_sum' => $price_sum,
        ];}, $content['products']);

        return json_encode($content);
    }

    public function actionViewOrder($id) {
        return $this->render('view-order', $this->getOrderContent($id));
    }

    public function actionOrderReady($id) {
        /* @var $order Order */
        $order = Order::findOneOr404($id);
        $products = Yii::$app->db->cache(function ($db) use ($order) {
            return $order->orderProducts;
        }, null, new TagDependency(['tags' => 'cache_table_' . OrderProduct::tableName()]));

        if ($order->status == Order::STATUS_ORDERED) {
            $order->status = Order::STATUS_DELIVERED;
            $order->generatePassword();
        }
        else {
            Yii::$app->session->addFlash('error', 'Неверный текущий статус заказа: ' .
                Order::$STATUS_TO_STRING[$order->status]);
            return $this->render('view-order', [
                'products' => $products,
                'order' => $order,
            ]);
        }

        if ($order->save()) {
            $message = new Message([
                'user_id' => $order->user_id,
                'text' => "Вы можете забрать ваш " . $order->getLink() .
                    ".\n\nс 10.00 до 20.00 по адресу: " . Yii::$app->params['deliveryAddress'] .
                    ".<br>Стоимость заказа: $order->confirmedPriceHtml Заявка будет храниться 14 дней" .
                    ".<br>Секретный ключ: $order->password."
            ]);
            $message->sendEmail();
            $message->save();
        }
        else {
            Yii::$app->session->addFlash('error', 'Ошибка при изменении статуса заказа: ' . $order->firstErrors);
        }

        return $this->render('view-order', [
            'products' => $products,
            'order' => $order,
        ]);
    }

}
