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
    /**
     * Lists all Good models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Good::find()->where(Yii::$app->request->get()),
        ]);
        return $this->render('index', [
            'products' => Good::findAll([ 'category_id' => $cid ]),
        ]);
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
            'model' => $model,
            'category' => Menu::findByIdOr404($model->category_id)
        ]);
    }
}
