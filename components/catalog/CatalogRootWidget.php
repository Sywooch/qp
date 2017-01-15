<?php
namespace app\components\catalog;

use app\models\Menu;
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
            'root_ch' => Menu::getRoot()->children(1)->all(),
        ]);
    }
}