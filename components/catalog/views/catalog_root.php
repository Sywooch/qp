<?php
/** @var $root app\models\Good\Menu  */
use yii\helpers\Html;

function traversal($node) {
    /** @var $node app\models\Good\Menu  */
    if ($chs = $node->children(1)->all()) {
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
        echo Html::a($node->name . "(кол-во товара)", ['catalog/view', 'id' => $node->id]);
    }
    echo Html::endTag('li') . "\n";
}

?>
<aside class="sidebar">
    <div class="row">
        <div class="col-md-12 categories transform transform-top"><span class="sidebar-title">Каталог</span>
            <div class="text-subline"></div>
            <ul class="categories-list transform-body">
                <?php
                foreach($root->children(1)->all() as $ch) {
                    traversal($ch);
                }

                ?>
            </ul>
        </div>
    </div>
</aside>
