<?php
namespace app\components\catalog;

use app\models\Good\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class CatalogWidget extends Widget
{
    public function init()
    {
        parent::init();
    }

    public function run() {
        return $this->render('catalog', [
            'root' => Menu::getRoot(),
        ]);
    }
}
