<?php
/** @var integer $parentID */
/* @var app\models\Good\Menu $parent */
use app\components\Html;
use yii\bootstrap\Nav;

?>
<div class="mate">
    <div class="mate__header">
        <div class="mate__header-back">
            <?= Html::a("<i class='fa fa-arrow-left'></i> Назад", ['catalog/view', 'id' => $parent->id], ['class' => 'btn btn-default'])?>
        </div>
        <span class="mate__header-title">
            <?=$parent->name?>
        </span>
        <div class="text-subline"></div>
    </div>
    <?php
    /** @var array $item */
    echo Nav::widget([
        'options' => ['class' => 'nav nav-stacked mate__nav'],
        'items' => $item,
        'encodeLabels' => false
    ]);
    ?>
</div>
