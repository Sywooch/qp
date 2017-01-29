<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->input('email') ?>

    <?= $form->field($model, 'status')->dropDownList([
        User::$STATUS_TO_STRING
    ]) ?>

    <?php
    $model->role = $model->getRole();
    echo $form->field($model, 'role')->dropDownList([
        array_combine(User::getRolesNames(), User::getRolesNames())
    ]) ?>

    <?= $form->field($model, 'phone')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
