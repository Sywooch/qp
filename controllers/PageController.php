<?php

namespace app\controllers;

use yii\web\Controller;

class PageController extends Controller
{
    public function actionIndex($view)
    {
        return $this->render($view);
    }
}
