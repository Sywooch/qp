<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Good\Good */

$this->title = 'Настройки';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-set_phone">


    <?php
    $form = ActiveForm::begin();
    foreach ($model->attributes() as $f) {
        echo $form->field($model, $f);
    }
    ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        <?= Html::resetButton('Отменить', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>