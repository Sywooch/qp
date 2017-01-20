<?php
/* @var $model app\models\Profile\ValidatePhoneForm */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="site-validate_phone">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key') ?>

    <div class="form-group">
        <?= Html::a('Отмена', ['profile/edit'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
