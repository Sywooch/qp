<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?= Html::beginForm(Url::to(['/site/search']), 'get', ['class' => 'form form-search col-xs-9 col-md-12']) ?>
<div class="input-group">
    <span class="input-group-addon">
        <?= Html::submitButton('<span class="lnr lnr-magnifier"></span>', ['class' => 'search__btn']) ?>
    </span>
    <?= Html::textInput('q', $text, ['class' => 'form-control input-lg js-search', 'placeholder' => 'Поиск...', 'id' => 'search-input']) ?>

</div><!-- /input-group -->
<?= Html::endForm() ?>