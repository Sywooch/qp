<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SetPasswordForm */
/* @var $form ActiveForm */
?>
<div class="site-set_password">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'repeat_password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-set_password -->
