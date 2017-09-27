<?php

namespace app\controllers;

use yii\web\Controller;

class EmailController extends Controller
{
    public function actions()
    {
        $this->layout = "email/html";
        return parent::actions();
    }

    public function actionIndex()
    {
        return $this->render('linkEmail');
    }
}
