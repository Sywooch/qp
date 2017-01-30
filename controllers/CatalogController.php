<?php

namespace app\controllers;

use app\models\Good\Menu;
use app\models\Good\Good;
use app\models\Bookmark;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use Yii;

class CatalogController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['add-bookmark', 'delete-bookmark'],
                'denyCallback' => function($role, $action) {
                    Yii::$app->session->setFlash('warning', 'Необходимо авторизоваться.');
                    $this->goBack();
                },
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['user'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'add-bookmark' => ['POST'],
                    'delete-bookmark' => ['POST'],
                ],
            ],
        ];
    }
    public $defaultAction = 'view';

    public function actionView($id = null)
    {
        $catalog = isset($id) ? Menu::findByIdOr404($id) : Menu::getRoot();
        return $catalog->children(1)->all() ?
            $this->render('view', [ 'catalog' => $catalog ]) :
            $this->redirect([ '/product/index', 'cid' => $catalog->id ]);
    }

    public function actionAdd()
    {
        $get = Yii::$app->request->post();
        if (isset($get['product-id'])) {
            Yii::$app->cart->put(Good::findByIdOr404($get['product-id']), $get['product-count']);
        }
        return Yii::$app->shopping->render();
    }

    public function actionAddBookmark()
    {
        $model = new Bookmark();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Товар добавлен в избранное.');
        }
        return $this->goBack();
    }

    public function actionDeleteBookmark()
    {
        $post = Yii::$app->request->post('Bookmark');
        if (($model = Bookmark::findOne([
            'user_id' => $post['user_id'],
            'product_id' => $post['product_id'],
            ]))
            && $model->delete()) {

            Yii::$app->session->setFlash('success', 'Товар удалён из избранного.');
        }
        return $this->goBack();
    }
}
