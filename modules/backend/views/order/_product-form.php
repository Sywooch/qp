<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-product-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'product_c1id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'products_count')->textInput() ?>
    <?= $form->field($model, 'confirmed_count')->textInput() ?>

    <?= $form->field($model, 'provider_order_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
