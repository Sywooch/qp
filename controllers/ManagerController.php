<?php

namespace app\controllers;

use app\models\OrderProduct;
use app\models\Profile\Message;
use DateTime;
use Yii;
use app\models\Order;
use yii\base\Object;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
                Yii::$app->session->setFlash('error', 'Заказ ' . $order->public_id  . ' не готов к выдаче.');
                return $this->redirect([ 'view-order', 'id' => $order->id ]);
            }
            else {
                $order->status = Order::STATUS_DONE;
                $order->save();
                Yii::$app->session->setFlash('success', 'Заказ ' . $order->public_id  . ' выдан.');
                return $this->redirect([ 'view-order', 'id' => $order->id ]);
            }
        }
        return $this->redirect([ '/manager' ]);
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => $this->getOrders()
        ]);
    }

    public function getOrders() {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()
                ->select('order.id, order.status, order.created_at, order.public_id, order.user_id, user.email,
                    sum(order_product.products_count * old_price) as total_price,
                    sum(order_product.confirmed_count * old_price) as confirmed_price'
                )->groupBy('order.id')
                ->join('RIGHT JOIN', 'order_product', 'order.id=order_product.order_id')
                ->joinWith('user')
            ,
            'sort' => [
                'attributes' => [
                    'created_at',
                    'total_price',
                    'confirmed_price',
                    'user.email',
                    'status_str' => [
                        'asc' => ['status' => SORT_ASC],
                        'desc' => ['status' => SORT_DESC],
                        'default' => SORT_DESC,
                    ],
                    'ref' => [
                        'asc' => ['public_id' => SORT_ASC],
                        'desc' => ['public_id' => SORT_DESC],
                        'default' => SORT_DESC,
                    ]
                ]
            ]
        ]);
        $get = Yii::$app->request->get();
        if (isset($get['after'])) {
            $dataProvider->query->andFilterWhere(['>=', 'order.created_at', strtotime($get['after'])]);
        }
        if (isset($get['before'])) {
            $dataProvider->query->andFilterWhere(['<=', 'order.created_at', strtotime($get['before'])]);
        }
        if (isset($get['status'])) {
            $dataProvider->query->andFilterWhere(['in', 'order.status', explode(',', $get['status'])]);
        }

        Yii::$app->db->cache(function ($db) use ($dataProvider) {
            $dataProvider->prepare();
        }, null, new TagDependency(['tags' => [
            'cache_table_' . Order::tableName(),
            'cache_table_' . OrderProduct::tableName(),
        ]]));

        return $dataProvider;
    }

    public function getOrdersJson() {
        $dataProvider = $this->getOrders();
        $items = [];
        foreach ($dataProvider->models as $item) {
            $ts = $item["created_at"];
            $fdf = new DateTime("@$ts");
            array_push($items, [
                'id_order' => $item['public_id'],
                'email' => $item['user']['email'],
                'status' => $item['public_id'],
                'price' => $item['status_str'],
                'created' => $fdf->format('d-m-Y H:i:s')
            ]);
        }
        return json_encode($items);
    }

    public function actionViewOrder($id) {
        $order = Order::findOneOr404($id);
        $products = Yii::$app->db->cache(function ($db) use ($order) {
            return $order->orderProducts;
        }, null, new TagDependency(['tags' => 'cache_table_' . OrderProduct::tableName()]));

        return $this->render('view-order', [
            'products' => $products,
            'order' => $order,
        ]);
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
                'text' => "Вы можете забрать ваш заказ " . $order->getLink() .
                    ".\nс 10.00 до 20.00 по адресу: " . Yii::$app->params['deliveryAddress'] .
                    ".\nСтоимость заказа: $order->confirmedPriceHtml Заявка будет храниться 14 дней" .
                    ".\nСекретный ключ: $order->password."
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
