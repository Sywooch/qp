<?php
namespace app\components\catalog;

use app\models\Good\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class CatalogRootWidget extends Widget
{
    public $catalog;

    public function init()
    {
        parent::init();
    }

    public function run() {
        return $this->render('catalog_root', [
            'root' => Menu::getRoot(),
        ]);
    }
}
