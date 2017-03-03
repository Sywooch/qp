<?php

namespace app\controllers;

use Yii;
use app\models\Order;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class ManagerController extends Controller
{
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
