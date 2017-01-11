<?php
namespace app\components\product;

use app\models\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class ProductWidget extends Widget
{
    public $product;

    public function init()
    {
        parent::init();
        if (!($this->product === null)) {
            $this->product = [];
        }
    }

    public function run() {
        return $this->render('product', [
            'product' => $this->product,
        ]);
    }
}