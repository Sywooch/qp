<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Good\Menu;
use yii\filters\VerbFilter;
use app\modules\backend\models\UploadZipModel;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class MenuController extends Controller
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

   public function actionView($id)
    {
        $menu = new Menu;
        $par = Menu::findById($id);

        if ($menu->load(Yii::$app->request->post())) {
            $menu->appendTo($par);
            return $this->redirect(['view', 'id' => $par->id]);
        }

        return $this->render('view', [
            'model' => Menu::findById($id),
            'menu' => $menu,
        ]);
    }

    public function actionCreate($par_id)
    {
        $model = new Menu;
        $par = Menu::findById($par_id);

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

    public function actionDelete($id)
    {
        $model = Menu::findById($id);
        $par_id = $model->parents(1)->one()->id;
        $model->deleteWithChildren();
        return $this->redirect([ 'view', 'id' => $par_id ]);
    }

    public function actionIndex()
    {
        $model = new UploadZipModel();

        if (Yii::$app->request->isPost) {
            $model->zipFile = UploadedFile::getInstance($model, 'zipFile');
            if ($model->upload()) {
                yii::$app->session->setFlash('success', 'Архив принят на обработку');
                return $this->refresh();
            }
        }
        return $this->render('index', ['model' => $model, 'par' => Menu::getRoot()]);
    }
}
