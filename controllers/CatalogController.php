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
        if($catalog->children(1)->all()) {
            return $this->render('view', [ 'catalog' => $catalog ]);
        }
        $this->layout = "products";
        return $this->actionProducts($catalog->id);
    }

    public function actionProducts($cid)
    {
        $products = Good::findAll([ 'category_id' => $cid ]);
        $fst_prod = array_shift($products);
        $common_props = $fst_prod->properties;
        foreach ($fst_prod->properties as $name => $pr) {
            $common_props[$name]['value'] = [ $common_props[$name]['value'] ];
        }

        foreach ($products as $prod) {
            foreach ($common_props as $name => &$pr) {
                if (isset($prod->properties[$name])) {
                    array_push($pr['value'], $prod->properties[$name]['value']);
                }
                else {
                    unset($common_props[$name]);
                }
            }
        }

        return $this->render('/product/index', [
            'products' => Good::findAll([ 'category_id' => $cid ]),
            'category' => Menu::findByIdOr404($cid),
            'filters' => $common_props,
        ]);
    }

    public function actionAdd()
    {
        $get = Yii::$app->request->post();
        if (isset($get['product_id'])) {
            Yii::$app->cart->put(Good::findByIdOr404($get['product_id']), $get['product_count']);
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
