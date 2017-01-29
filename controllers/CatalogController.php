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
}
