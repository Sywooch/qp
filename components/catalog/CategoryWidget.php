<?php
/** @var $catalog_item app\models\Menu  */

namespace app\components\catalog;

use app\models\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class CategoryWidget extends Widget
{
    public $item;

    public function init()
    {
        parent::init();
    }

    public function run() {
        return $this->render('category', [
            'item' => $this->item,
        ]);
    }
}
