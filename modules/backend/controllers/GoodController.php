<?php

namespace app\modules\backend\controllers;

use Yii;
use app\models\Good\Good;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * GoodController implements the CRUD actions for Good model.
 */
class GoodController extends Controller
{
    /**
     * @inheritdoc
     */
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

    /**
     * Lists all Good models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Good::find(),
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
            'model' => Good::findOneOr404($id),
        ]);
    }

    /**
     * Creates a new Good model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Good();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Good model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = Good::findOneOr404($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Deletes an existing Good model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Good::findOneOr404($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionCheckStatus()
    {
        $ok_status = 0;
        $error_status = 0;
        foreach (Good::findAll(['status' => Good::STATUS_ERROR]) as $product) {
            $product->status = Good::STATUS_OK;
            if ($product->validate() && $product->save()) {
                $ok_status++;
            }
            else {
                Yii::$app->session->addFlash('error', implode(', ', $product->getFirstErrors()));
                $error_status++;
            }
        }
        if ($ok_status) {
            Yii::$app->session->addFlash('success', "$ok_status товаров получили статус ОК");
        }

        if ($error_status) {
            Yii::$app->session->addFlash('error', "$error_status товаров всё еще имеют статус ОШИБКА");
        }
        return $this->redirect(['index']);
    }
}
