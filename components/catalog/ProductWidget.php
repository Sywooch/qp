<?php
namespace app\components\catalog;

use app\models\Menu;
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
            'product' => $this->product
        ]);
    }
}
