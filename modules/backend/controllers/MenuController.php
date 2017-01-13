<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Menu;

/**
 * UserController implements the CRUD actions for User model.
 */
class MenuController extends Controller
{
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->actionView(Menu::getRoot()->id);
    }
   public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($par_id)
    {
        $model = new Menu;
        $par = static::findModel($par_id);

        if ($model->load(Yii::$app->request->post())) {
            $model->appendTo($par);
            return $this->redirect(['view', 'id' => $par->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'parent' => $par,
            ]);
        }
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
