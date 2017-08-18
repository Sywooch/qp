<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="input-group" id="js-search-wrap">
    <span class="input-group-addon">
        <button class="search__btn" data-toggle="modal" data-target="#search-modal"><span class="lnr lnr-magnifier"></span></button>
    </span>
    <?= Html::textInput('q', $text, ['class' => 'form-control input-lg', 'id'=> 'js-search-input','placeholder' => 'Поиск...']) ?>

</div><!-- /input-group -->