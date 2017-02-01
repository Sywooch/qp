<?php

namespace app\controllers;

use Yii;
use app\models\Good\Good;
use app\models\Good\Menu;
use yii\web\Controller;

/**
 * ProductController implements the CRUD actions for Good model.
 */
class ProductController extends Controller
{
    public function actionIndex()
    {
        return [];
    }

    /**
     * Displays a single Good model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = Good::findByIdOr404($id);

        return $this->render('view', [
            'product' => $model,
            'category' => Menu::findByIdOr404($model->category_id)
        ]);
    }
}
