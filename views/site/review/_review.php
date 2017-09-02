<?php
/** @var app\models\ContactForm $item */

use app\components\Html;

?>

<div class="col-sm-8 col-xs-12">
    <div class="review__item row">
        <div class="review__item-feed col-xs-12 col-sm-4">
            <div class="review__item-author">
                <?=$item->name?>
            </div>
            <div class="review__item-date">
                <?=Html::dateRu(date("d m Y", $item->created_at));?>
            </div>
        </div>
        <div class="review__item-content col-xs-12 col-sm-8">
            <?=$item->body?>
        </div>
    </div>
</div>