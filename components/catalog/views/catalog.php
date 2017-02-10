<?php
/** @var $root app\models\Good\Menu  */
use yii\helpers\Html;
use app\models\Good\Menu;
use yii\caching\TagDependency;

function traversalNode($node, $depth) {
    /** @var $node app\models\Good\Menu  */
    $chs = Yii::$app->db->cache(function ($db) use($node)
    {
        return $node->children(1)->all();
    }, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()]));

    if ($chs) {
        echo Html::beginTag('li' , ['class' => 'has-child']) . "\n";
        echo Html::a($node->name . '(' . $node->getProductCount() . ')', ['catalog/view', 'id' => $node->id]);
        echo Html::beginTag('ul') . "\n";
        if($depth < 1) {
            foreach($chs as $ch) {
                traversalNode($ch, $depth + 1);
            }
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
<li class="catalog">
    <a href="javascript:void(0)"> <i class="fa fa-bars" aria-hidden="true"></i> Каталог</a>
    <ul class="items">

                <?php

                $chs = Yii::$app->db->cache(function ($db) use($root) {
                    return $root->children(1)->all();
                }, null, new TagDependency(['tags' => 'cache_table_' . Menu::tableName()]));

                $i = 0;
                $n = round(count($chs) / 2);

                foreach($chs as $ch) {


                    traversalNode($ch, 0);
                }

                ?>


    </ul>
</li>

