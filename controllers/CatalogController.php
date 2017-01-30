<?php

namespace app\controllers;

use app\models\Good\Menu;
use app\models\Good\Good;
use Yii;

class CatalogController extends \yii\web\Controller
{
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
        if (isset($get['product-id'])) {
            Yii::$app->cart->put(Good::findByIdOr404($get['product-id']), $get['product-count']);
        }
        return Yii::$app->shopping->render;
    }
}
