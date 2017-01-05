<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->params['profileLayout'] = true;
$this->title = 'Смена телефона';

$this->params['breadcrumbs'][] = [
    'label' => 'Личный кабинет',
    'url' => ['profile/index']
];
$this->params['breadcrumbs'][] = [
    'label' => 'Настройки профиля',
    'url' => ['profile/edit']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-set_phone">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone') ?>

    <div class="form-group">
        <?= Html::a('Отмена', ['profile/edit'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
