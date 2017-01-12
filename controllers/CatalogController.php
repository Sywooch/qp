<?php

namespace app\controllers;

use app\models\Menu;
use yii\web\NotFoundHttpException;

class CatalogController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (!($catalog = Menu::find()->roots()->one())) {
            $catalog = new Menu([ 'name' => 'Категории товаров' ]);
            $catalog->makeRoot();
        }

        return $this->render('index', [
            'catalog' => $this->findModel(1),
        ]);
    }

    public function actionView($id)
    {
        if (!($catalog = Menu::find()->roots()->one())) {
            $catalog = new Menu([ 'name' => 'Категории товаров' ]);
            $catalog->makeRoot();
        }

        return $this->render('view', [
            'catalog' => $this->findModel($id),
        ]);
    }

    protected static function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
