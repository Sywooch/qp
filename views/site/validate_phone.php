<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="site-validate_phone">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
