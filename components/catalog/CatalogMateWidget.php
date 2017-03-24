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
    public $gran;
    public $items;
    /* If depth = 1 then
     *     btn-back = parent
     * else
     *     btn-back = gran
     */
    public $depth;

    public function init()
    {
        parent::init();
        $this->parent = $this->catalog->parents(1)->one();

        $gran = $this->catalog->parents(1)->one()->parents(1)->one();
        $this->gran = count($gran)  ? $gran : $this->parent;

        $mate = $this->parent->children(1)->all();

        $this->items = [];
        foreach(Yii::$app->db->cache(function ($db) use($mate)
        {
            return $mate;
        }, null, new TagDependency(['tags' => 'cache_table_' . \app\models\Good\Menu::tableName()])) as $par) {
            $this->items[] = [
                'label' => $par->name . ' ' . Html::tag(
                        'span',
                            $par->getProductCount()
                            . ' '
                            . Html::ending($par->getProductCount(), ['товар', 'товара', 'товаров']),
                        ['class' => 'counter']
                    ),
                'url' => ['catalog/view', 'id' => $par->id]
            ];
        }
    }

    public function run() {
        return $this->render('mate', [
            'item' => $this->items,
            'parent' => $this->parent,
            'gran' => $this->gran,
            'depth' => $this->depth,
        ]);
    }
}
