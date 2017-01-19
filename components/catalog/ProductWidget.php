<?php
namespace app\components\catalog;

use app\models\Good\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class ProductWidget extends Widget
{
    public $product;

    public function init()
    {
        parent::init();
    }

    public function run() {
        return $this->render('product', [
            'name' => '<i>Бараны</i>',
            'link' => '/catalog/view?id=' . $this->product->id,
            'img' => '@web/img/catalog/product/1.png',
        ]);
    }
}
