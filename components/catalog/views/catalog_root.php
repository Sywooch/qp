<?php
/** @var $root_ch array of app\models\Good\Menu  */
use yii\helpers\Html;

$depth = 1;
?>
<aside class="sidebar">
    <div class="row">
        <div class="col-md-12 categories transform transform-top"><span class="sidebar-title">Каталог</span>
            <div class="text-subline"></div>
            <ul class="categories-list transform-body">
                <?php
                /** @var $item app\models\Good\Menu  */
                $items = [];
                for($i = 1; $i < count($root_ch); $i++ ) {
                    $cur = $root_ch[$i];
                    $next = $i + 1 > count($root_ch) - 1 ? $cur : $root_ch[$i + 1];
                    if($cur->depth == $depth) {
                        echo Html::endTag('li') . "\n";
                    } else if($cur->depth > $depth) {

                    } else {
                        for($j = $depth - $cur->depth; $j; $j--) {
                            echo Html::endTag('ul') . "\n";
                            echo Html::endTag('li') . "\n";
                        }
                    }
                    if($next->depth > $cur->depth) {
                        echo Html::beginTag('li', ['class' => 'has-child']) . "\n";
                        echo Html::a($cur->name, ['catalog/view', 'id' => $cur->id]);
                        echo Html::beginTag('ul') . "\n";

                    } else {
                        echo Html::beginTag('li') . "\n";
                        echo Html::a($cur->name. " (".$cur->depth . ")", ['catalog/view', 'id' => $cur->id]);
                    }

                    $depth = $cur->depth;
                }
                for($i=$depth; $i; $i--)
                {
                    echo Html::endTag('li')."\n";
                    echo Html::endTag('ul')."\n";
                }
                ?>
            </ul>
        </div>
    </div>
</aside>
