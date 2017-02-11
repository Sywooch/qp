<?php
/** @var $root app\models\Good\Menu  */
use yii\helpers\Html;
use app\models\Good\Menu;
use yii\caching\TagDependency;

function traversal($node) {
    /** @var $node app\models\Good\Menu  */
    $chs = Yii::$app->db->cache(function ($db) use($node)
    {
        return $node->children(1)->all();
    }, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()]));
    if ($chs) {
        echo Html::beginTag('li' , ['class' => 'has-child']) . "\n";
        echo Html::a($node->name, ['catalog/view', 'id' => $node->id]);
        echo Html::beginTag('ul') . "\n";
        foreach($chs as $ch) {
            traversal($ch);
        }
        echo Html::endTag('ul') . "\n";
    }
    else {
        echo Html::beginTag('li') . "\n";
        echo Html::a($node->name . '(' . $node->getProductCount() . ')', ['catalog/view', 'id' => $node->id]);
    }
    echo Html::endTag('li') . "\n";
}

?>
<aside class="sidebar">
    <div class="row">
        <div class="col-md-12 categories transform transform-top">
            <span class="sidebar-title hidden-md hidden-lg">
                Каталог
            </span>
            <div class="text-subline hidden-md hidden-lg"></div>
            <ul class="categories-list transform-body">
                <?php
                foreach(Yii::$app->db->cache(function ($db) use($root)
                {
                    return $root->children(1)->all();
                }, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()]))  as $ch) {
                    traversal($ch);
                }

                ?>
            </ul>
        </div>
    </div>
</aside>
