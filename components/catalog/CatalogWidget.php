<?php
namespace app\components\catalog;

use app\models\Menu;
use yii\bootstrap\Widget;
use yii\web\NotFoundHttpException;

class CatalogWidget extends Widget
{
    public $catalog;

    public function init()
    {
        parent::init();
        if (!($this->catalog = Menu::find()->roots()->one())) {
            $this->catalog = new Menu([ 'name' => 'Категории товаров' ]);
            $this->catalog->makeRoot();
        }
    }

    public function run() {
        return $this->render('catalog', [
            'catalog' => $this->findModel(1),
        ]);
    }

    protected static function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}