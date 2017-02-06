<?php

namespace app\controllers;

use app\models\Good\Menu;
use app\models\Good\Good;
use app\models\Bookmark;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use Yii;
use yii\caching\TagDependency;

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
                    $this->redirect('/site/login');
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
        $catalog = isset($id) ? Menu::findOneOr404($id) : Menu::getRoot();
        if(Yii::$app->db->cache(function ($db) use($catalog)
        {
            return $catalog->children(1)->all();
        }, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()]))) {
            return $this->render('view', [ 'catalog' => $catalog ]);
        }
        $this->layout = "products";
        return $this->actionProducts($catalog->id);
    }

    public function actionProducts($cid)
    {
        $products = Yii::$app->db->cache(function ($db) use($cid)
        {
            return Good::find()->joinWith('bookmark')->where([ 'category_id' => $cid ])->all();
        }, null, new TagDependency([
            'tags'=> [
                'cache_table_' . Good::tableName(),
                'cache_table_' . Bookmark::tableName(),
            ]]));
        $common_props = null;
        if ($products) {
            $products_copy = $products;

            $fst_prod = array_shift($products_copy);
            $common_props = $fst_prod->properties;
            foreach ($fst_prod->properties as $name => $pr) {
                $common_props[$name]['value'] = [ $common_props[$name]['value'] ];
            }

            foreach ($products_copy as $prod) {
                foreach ($common_props as $name => &$pr) {
                    if (isset($prod->properties[$name])) {
                        array_push($pr['value'], $prod->properties[$name]['value']);
                    }
                    else {
                        unset($common_props[$name]);
                    }
                }
            }
        }

        return $this->render('/product/index', [
            'products' => $products,
            'category' => Menu::findOneOr404($cid),
            'filters' => $common_props,
        ]);
    }

    public function actionAddBookmark()
    {
        $get = Yii::$app->request->post();
        if (isset($get['_csrf'])) {
            $model = new Bookmark([
                'user_id' => Yii::$app->user->getId(),
                'product_id' => $get['product_id']
            ]);
            if($model->save()) {
                return "Add";
            }
            return "Error: ".$get['product_id'];
        }

        return "Error";
    }

    public function actionDeleteBookmark()
    {
        $get = Yii::$app->request->post();
        if (isset($get['_csrf'])) {
            if (($model = Bookmark::cachedFindOne([
                    'user_id' => Yii::$app->user->id,
                    'product_id' => $get['product_id'],
                ]))
                && $model->delete()) {
                return "Delete";
            }
            return "Error";
        }

        return "Error";
    }
}
