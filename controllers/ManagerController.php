<?php

namespace app\controllers;

use app\models\OrderProduct;
use Yii;
use app\models\Order;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
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
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find()->joinWith('user'),
        ]);
        Yii::$app->db->cache(function ($db) use ($dataProvider) {
            $dataProvider->prepare();
        }, null, new TagDependency(['tags' => 'cache_table_' . Order::tableName()]));
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionViewOrder($id) {
        $order = Order::cachedFindOne($id);
        if (!$order) {
            throw new NotFoundHttpException();
        }
        $products = Yii::$app->db->cache(function ($db) use ($order) {
            return $order->orderProducts;
        }, null, new TagDependency(['tags' => 'cache_table_' . OrderProduct::tableName()]));

        return $this->render('view-order', [
            'products' => $products,
            'order' => $order,
        ]);
    }

    public function actionOrderPassword($id)
    {
        if ($pass = Yii::$app->request->post('password')) {
            $order = Order::findOneOr404($id);
            if ($order->checkPassword($pass)) {
                Yii::$app->session->setFlash('success', 'Заказ изменил свой статус на <i>выдан</i>.');
                return $this->redirect('index');
            }
            Yii::$app->session->setFlash('error', 'Неверный код подтверждения.');
        }
        return $this->render('order_password', ['id' => $id]);
    }
}
