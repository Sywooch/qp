<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SetPasswordForm */
/* @var $form ActiveForm */

$this->params['profileLayout'] = true;
$this->title = 'Смена пароля';

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
<div class="site-set_password">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'repeat_password')->passwordInput() ?>

        <div class="form-group">
            <?= Html::a('Отмена', ['profile/edit'], ['class' => 'btn btn-default']) ?>
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- site-set_password -->
