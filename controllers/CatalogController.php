<?php

namespace app\controllers;

use app\models\Good\Menu;

class CatalogController extends \yii\web\Controller
{
    public $defaultAction = 'view';

    public function actionView($id = null)
    {
        $get = Yii::$app->request->get();
        if (isset($get['product_id'])) {
            Yii::$app->cart->put(Good::findByIdOr404($get['product_id']), $get['product_count']);
        }
        $catalog = isset($id) ? Menu::findByIdOr404($id) : Menu::getRoot();
        return $catalog->children(1)->all() ?
            $this->render('view', [ 'catalog' => $catalog ]) :
            $this->redirect([ '/product/index', 'category_id' => $catalog->id ]);
    }
}
