<?php
/** @var $catalog_item app\models\Good\Menu  */

namespace app\components\catalog;

use app\models\Good\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class CategoryWidget extends Widget
{
    public $catalog_item;
    public $img;
    public $link;

    public function init()
    {
        parent::init();
    }

    public function run() {
        return $this->render('category', [
            'name' => $this->catalog_item->name,
            'link' => '/catalog/view?id=' . $this->catalog_item->id,
            'img' => '@web/img/catalog/category/1.png',
        ]);
    }
}
