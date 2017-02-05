<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?= Html::beginForm(Url::to(['/site/search']), 'get', ['class' => 'form-inlin']) ?>
<div class="input-group">
    <?= Html::textInput('q', $text, ['class' => 'form-control', 'placeholder' => 'Поиск...']) ?>
    <span class="input-group-btn">
            <?= Html::submitButton('<i class="glyphicon glyphicon-search"></i>', ['class' => 'btn btn-primary']) ?>
          </span>
</div><!-- /input-group -->
<?= Html::endForm() ?>
<br/>