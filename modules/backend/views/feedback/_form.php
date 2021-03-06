<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\ContactForm;
/* @var $this yii\web\View */
/* @var $model app\models\ContactForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-form-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'rating')->dropDownList(ContactForm::$RATING_TO_STRING) ?>
    <?= $form->field($model, 'body')->textarea(['rows' => '6']) ?>

    <?= $form->field($model, 'status')->dropDownList(ContactForm::$STATUS_TO_STRING) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
