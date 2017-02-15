<?php
namespace app\components\catalog;

use app\models\Good\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class CatalogWidget extends Widget
{
    public $visible;

    public function init()
    {
        parent::init();
    }

    public function run() {
        return $this->render('catalog', [
            'root' => Menu::getRoot(),
            'visible' => $this->visible
        ]);
    }
}
