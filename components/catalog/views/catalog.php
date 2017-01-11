<?php
/** @var $catalog app\models\Menu  */
use yii\helpers\Html;
?>
<aside class="sidebar">
    <div class="row">
        <div class="col-md-12 categories"><span class="sidebar-title">Каталог</span>
            <div class="text-subline"></div>
            <ul class="categories-list">
                <?php
                foreach($catalog->children(1)->all() as $ch) {
                    echo '<li>' . Html::a($ch->name, ['catalog/view', 'id' => $ch->id]) . '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
</aside>