<?php
namespace app\components\catalog;

use app\components\Html;
use Yii;
use yii\bootstrap\Widget;
use yii\caching\TagDependency;
use yii\web\NotFoundHttpException;

class CatalogMateWidget extends Widget
{
    public $catalog;
    public $parent;
    public $items;

    public function init()
    {
        parent::init();
        $this->parent = $this->catalog->parents(1)->one();
        $mate = $this->parent->children(1)->all();

        $this->items = [];
        foreach(Yii::$app->db->cache(function ($db) use($mate)
        {
            return $mate;
        }, null, new TagDependency(['tags' => 'cache_table_' . \app\models\Good\Menu::tableName()])) as $par) {
            $this->items[] = [
                'label' => $par->name . ' ' . Html::tag('span', $par->getProductCount(), ['class' => 'counter']),
                'url' => ['catalog/view', 'id' => $par->id]
            ];
        }
    }

    public function run() {
        return $this->render('mate', [
            'item' => $this->items,
            'parent' => $this->parent,
        ]);
    }
}
