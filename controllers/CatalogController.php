<?php

namespace app\controllers;

use app\models\Good\Menu;
use yii\web\NotFoundHttpException;

class CatalogController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('view', [
            'catalog' => Menu::getRoot(),
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'catalog' => Menu::findById($id),
        ]);
    }
}
