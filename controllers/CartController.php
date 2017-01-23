<?php

namespace app\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;

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
        $array = Yii::$app->cart->getPositions();
//        var_dump(array_values(Yii::$app->cart->getPositions()));exit;
        $dataProvider = new ArrayDataProvider([ 'allModels' => $array ]);
        return $this->render('/cart', [
            'dataProvider' => $dataProvider,
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
}
