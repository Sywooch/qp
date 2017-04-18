<?php

namespace app\controllers;

use app\models\Good\GoodProperty;
use app\models\Good\Menu;
use app\models\Good\Good;
use app\models\Bookmark;
use app\models\Good\PropertyValue;
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
                    'search-data' => ['POST'],
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
        //$this->layout = "products";
        return $this->actionProducts($catalog->id);
    }

    public function applyFilters($filters, $products) {
        $filters = explode(';', $filters);
        $filters = array_filter($filters, function ($f) { return $f !== ''; });

        foreach($filters as $f) {
            list($prop, $values) = explode(':', $f);
            if ($prop == 'p') {
                list($min, $max) = explode('-', $values);
                $products = array_filter($products, function ($prod) use ($min, $max) {
                    return (int)$min <= $prod->price && $prod->price <= (int)$max;
                });
            }
            else if ($prop == 'o') {
                continue;
            }
            else {
                $products = array_filter($products, function ($prod) use ($prop, $values) {
                    $values = explode(',', $values);
                    return isset($prod->properties[$prop]) and in_array($prod->properties[$prop], $values);
                });
            }
        }
        return $products;
    }

    public function getProductFilters($products) {
        $filters = [];
        $prices = [];
        if ($products) {
            $fst_prod = array_shift($products);
            $common_props = $fst_prod->properties;
            foreach ($fst_prod->properties as $name => $pr) {
                $common_props[$name] = [ $common_props[$name] => 1 ];
                $prices = [$fst_prod->price];
            }

            foreach ($products as $prod) {
                foreach ($common_props as $name => &$pr) {
                    if (isset($prod->properties[$name])) {
                        $pr[$prod->properties[$name]] = 1;
                    }
                    else {
                        unset($common_props[$name]);
                    }
                }
                $prices[] = $prod->price;
            }
            foreach ($common_props as $prop => $value) {
                $prop_model = GoodProperty::cachedFindOne($prop);
                $filters[] = [
                    'prop_id' => $prop,
                    'prop_name' => $prop_model->name,
                    'values' => array_map(function ($x) {
                        return [
                            'value_id' => $x,
                            'value_name' => PropertyValue::cachedFindOne($x)->value
                        ];
                    }, array_keys($value))
                ];
            }
        }
        return [$filters, $prices];
    }

    public function actionProducts($cid)
    {
        $get = Yii::$app->request->get();
        $category = Menu::findOneOr404($cid);
        $ordering = Good::ORDERING_PRICE_ACS;
        if (isset($get['f']) && strpos($get['f'], 'o')) {
            list($rest, $ordering) = explode('o', $get['f']);
            $ordering = substr($ordering, 1);
            $ordering = explode(';', $ordering)[0];
        }

        $products = Yii::$app->db->cache(function ($db) use($cid, $ordering)
        {
            static $ordering_to_db_query = [
                Good::ORDERING_PRICE_ACS => ['price' => SORT_ASC],
                Good::ORDERING_PRICE_DESC => ['price' => SORT_DESC],
                Good::ORDERING_NAME => ['name' => SORT_ASC],
            ];
            return Good::find()->joinWith('bookmark')->orderBy($ordering_to_db_query[$ordering])
                ->where([ 'category_id' => $cid, 'status' => Good::STATUS_OK ])->all();
        }, null, new TagDependency([
            'tags'=> [
                'cache_table_' . Good::tableName(),
                'cache_table_' . Bookmark::tableName(),
            ]]));

        if(isset($get['f'])) {
            $filtered_products = $this->applyFilters($get['f'], $products);
            if (isset($get['ajax'])) {
                $this->layout = "_null";
                return $this->render('/product/_view', [
                    'products' => $filtered_products
                ]);
            }
        }
        else {
            $filtered_products = $products;
        }

        list($filters, $prices) = $this->getProductFilters($products);

        return $this->render('/product/index', [
            'products' => $filtered_products,
            'category' => $category,
            'filters' => $filters,
            'prices' => $prices,
        ]);
    }

    public function actionSearchData() {
        $get = Yii::$app->request->post();

        $selector = function($p) { return [ 'id' => $p->id, 'label' => $p->name ]; };
        if (Yii::$app->request->isAjax) {
            $data = [
                'products' => array_map($selector, Good::cachedFindAll(['status' => Good::STATUS_OK])),
                'categories' => array_map($selector, Menu::cachedFindAll()),
            ];
            echo json_encode($data);
        }
    }

    public function actionAddBookmark()
    {
        $get = Yii::$app->request->post();
        if (Yii::$app->request->isAjax) {
            $model = new Bookmark([
                'user_id' => Yii::$app->user->getId(),
                'product_id' => $get['product_id']
            ]);
            if($model->save()) {
                return Bookmark::cachedGetCount(['product_id' => $get['product_id']]) ;
            }
            return "Error: ".$get['product_id'];
        }

        return "Error";
    }

    public function actionDeleteBookmark()
    {
        $get = Yii::$app->request->post();
        if (Yii::$app->request->isAjax) {
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
