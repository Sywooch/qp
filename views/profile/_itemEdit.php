<?php
use yii\helpers\Html;
/** @var array $item */
?>

<div class="row item">
    <div class="col-xs-4 name"><?=$item['label']?></div>
    <div class="col-xs-5 value"><?=$item['value']?></div>
    <div class="col-xs-3 edit">
        <?=Html::a($item['value'] ? 'Изменить' : 'Добавить', $item['url'])?>
    </div>
</div>