<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="col-xs-9 col-md-12">
    <div class="input-group">
        <span class="input-group-addon">
            <button class="search__btn" data-toggle="modal" data-target="#search-modal"><span class="lnr lnr-magnifier"></span></button>
        </span>
        <?= Html::textInput('q', $text, ['class' => 'form-control input-lg', 'placeholder' => 'Поиск...']) ?>

    </div><!-- /input-group -->
</div>