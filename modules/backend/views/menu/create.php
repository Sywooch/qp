<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */
/* @var $parent_name string */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Создание подкатегории в ' . $parent_name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить', [ 'btn btn-primary' ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
