<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?= Html::beginForm(Url::to(['/site/search']), 'get', ['class' => 'form form-search col-xs-9 col-md-12']) ?>
<div class="input-group">
    <span class="input-group-addon">
        <?= Html::submitButton('<i class="fa fa-search"></i>', ['class' => 'search__btn']) ?>
    </span>
    <?= Html::textInput('q', $text, ['class' => 'form-control  input-lg', 'placeholder' => 'Поиск...']) ?>

</div><!-- /input-group -->
<?= Html::endForm() ?>
<br/>