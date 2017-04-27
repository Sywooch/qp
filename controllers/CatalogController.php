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

    public function applyFilters($filters, $products, &$offset) {
        array_walk($products, function(&$x) use(&$offset) {
            $x->offset =  $offset++;
        });
        if (!$filters) {
            return $products;
        }

        $filters = explode(';', $filters);
        $filters = array_filter($filters, function ($f) { return $f !== ''; });

        foreach($filters as $f) {
            list($prop, $values) = explode(':', $f);
            if ($prop == 'p') {
                list($min, $max) = explode('-', $values);
                $products = array_filter($products, function ($prod) use ($min, $max) {
                    return ((int)$min - 100) <= $prod->price && $prod->price <= ((int)$max + 100);
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

    static $ordering_to_db_query = [
        Good::ORDERING_PRICE_ACS => ['price' => SORT_ASC],
        Good::ORDERING_PRICE_DESC => ['price' => SORT_DESC],
        Good::ORDERING_NAME => ['name' => SORT_ASC],
    ];

    public function actionProducts($cid)
    {
        $limit = 24;
        $get = Yii::$app->request->get();
        $category = Menu::findOneOr404($cid);

        $query = Good::find()->joinWith('bookmark')
            ->where([ 'category_id' => $cid, 'status' => Good::STATUS_OK ]);

        $filter = isset($get['f']) ? $get['f'] : null;

        if (strpos($filter, 'o')) {
            list($rest, $ordering) = explode('o', $filter);
            $ordering = substr($ordering, 1);
            $ordering = explode(';', $ordering)[0];
        }
        else {
            $ordering = Good::ORDERING_PRICE_ACS;
        }

        $offset = isset($get['offset']) ? $get['offset'] : 0;

        if (isset($get['ajax'])) {
            $filtered_products = [];

            while(count($filtered_products) < $limit) {
                $products = Yii::$app->db->cache(function ($db) use($query, $ordering, $offset, $limit)
                {
                    return $query->orderBy(self::$ordering_to_db_query[$ordering])->offset($offset)->limit($limit)->all();
                }, null, new TagDependency([
                    'tags'=> [
                        'cache_table_' . Good::tableName(),
                        'cache_table_' . Bookmark::tableName(),
                    ]]));
                if (empty($products)) {
                    break;
                }
                $filtered_products += $this->applyFilters($filter, $products, $offset);
            }


            $this->layout = "_null";
            return $this->render('/product/_view', [
                'products' => array_slice($filtered_products, 0, $limit),
                'offset' => end($filtered_products)->offset + 1,
            ]);
        }
        else {
            $products = Yii::$app->db->cache(function ($db) use ($query, $ordering) {
                return $query->orderBy(self::$ordering_to_db_query[$ordering])->all();
            }, null, new TagDependency([
                'tags' => [
                    'cache_table_' . Good::tableName(),
                    'cache_table_' . Bookmark::tableName(),
                ]]));

            list($filters, $prices) = $this->getProductFilters($products);
            $filtered_products = $this->applyFilters($filter, $products, $offset);
            return $this->render('/product/index', [
                'products' => array_slice($filtered_products, 0, $limit),
                'category' => $category,
                'filters' => $filters,
                'prices' => $prices,
                'offset' => end($filtered_products)->offset + 1,
            ]);
        }
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
