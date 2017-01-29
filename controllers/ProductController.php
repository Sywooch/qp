<?php

namespace app\controllers;

use Yii;
use app\models\Good\Good;
use app\models\Good\Menu;
use yii\web\Controller;

/**
 * ProductController implements the CRUD actions for Good model.
 */
class ProductController extends Controller
{
    public function actionIndex($cid)
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

        return $this->render('index', [
            'products' => Good::findAll([ 'category_id' => $cid ]),
            'category' => Menu::findByIdOr404($cid),
            'filters' => $common_props,
        ]);
    }

    /**
     * Displays a single Good model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = Good::findByIdOr404($id);
        return $this->render('view', [
            'model' => $model,
            'category' => Menu::findByIdOr404($model->category_id)
        ]);
    }
}
