<?php
/** @var integer $parentID */
/* @var app\models\Good\Menu $parent */
/* @var app\models\Good\Menu $gran */
use app\components\Html;
use yii\bootstrap\Nav;

?>
<div class="mate">
    <div class="mate__header">
        <?php if($parent->id != $gran->id) : ?>
            <div class="mate__header-back">
                <?= Html::a("<i class='fa fa-arrow-left'></i> " . $gran->name, ['catalog/view', 'id' => $gran->id], ['class' => 'btn btn-default'])?>
            </div>
        <?php endif; ?>

        <span class="mate__header-title">
            <?=$parent->name?>:
        </span>
        <div class="text-subline"></div>
    </div>
    <div id="mate-box">
        <?php
        /** @var array $item */
        echo Nav::widget([
            'options' => ['class' => 'nav nav-stacked mate__nav'],
            'items' => $item,
            'encodeLabels' => false
        ]);
        ?>
    </div>
</div>
