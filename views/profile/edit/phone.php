<?php
use yii\widgets\MaskedInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\SetPhoneForm $model */

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
    <?=$form->field($model, 'phone')->widget(MaskedInput::className(), [
        'mask' => '+7 999 999-99-99',
    ]) ?>
    <div class="form-group">
        <?= Html::a('Отмена', ['profile/edit'], ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
