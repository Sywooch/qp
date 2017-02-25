<?php

namespace app\modules\backend\controllers;

use Yii;
use yii\web\Controller;
use app\models\Good\Menu;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class MenuController extends Controller
{
    public $defaultAction = 'view';

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

   public function actionView($id = null)
    {
        $id or $id = Menu::getRoot();
        $menu = new Menu;
        $par = Menu::findOneOr404($id);

        if ($menu->load(Yii::$app->request->post())) {
            $menu->appendTo($par);
            return $this->redirect(['view', 'id' => $par->id]);
        }
        return $par->children(1)->all() ?
            $this->render('view', [ 'model' => $par, 'menu' => $menu,]) :
            $this->redirect([ '/backend/good', 'category_id' => $id ]);
    }

    public function actionDelete($id)
    {
        $model = Menu::findOneOr404($id);
        $par_id = $model->parents(1)->one()->id;
        $model->deleteWithChildren();
        return $this->redirect([ 'view', 'id' => $par_id ]);
    }

}
