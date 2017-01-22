<?php

namespace app\controllers;

use Yii;
use app\models\Good\Good;
use yii\data\ActiveDataProvider;
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
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Good model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => Good::findByIdOr404($id),
        ]);
    }
}
