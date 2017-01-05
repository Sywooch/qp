<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<section class="join">
    <div class="row">
        <div class="join-body card col-md-4 col-md-offset-4 col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3">
            <div class="row join-tab">
                <div class="col-xs-6 tab-login active">
                    <?php
                    echo  Html::a(
                        '<i class="fa fa-sign-in hidden-xs"></i> ' . Html::encode($this->title),
                        'javascript::void(0)'
                    );
                    ?>
                </div>
                <div class="col-xs-6 tab-reg">
                    <?php
                    echo  Html::a(
                        '<i class="fa fa-plus hidden-xs"></i> Регистрация',
                        ['site/reg']
                    );
                    ?>
                </div>
            </div>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'fieldConfig' => [
                    'template' => "<div class=\"form-group\">{label}\n{input}\n<div>{error}</div></div>",
                    'labelOptions' => ['class' => 'control-label'],
                ],
            ]); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <div class="row">
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'template' => "<div class=\"col-xs-7 checkbox\">{input} {label}\n<div>{error}</div>",
                    ]) ?>
                        <div class="col-xs-5 join-recovery">
                            <a href="#">Напомнить пароль</a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                        <?= Html::submitButton('Войти', ['class' => 'btn btn-primary  btn-block btn-flat', 'name' => 'login-button']) ?>
                        </div>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

            <!-- .social-auth-links -->
            <div class="social-auth-links text-center">
                <p>- или войти с помощью -</p>
                <ul class="social-list">
                    <li>
                        <a href="#" class="btn btn-block btn-social btn-vk btn-flat">
                            <i class="fa fa-vk"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="btn btn-block btn-social btn-ok btn-flat">
                            <i class="fa fa-odnoklassniki"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="btn btn-block btn-social btn-facebook btn-flat">
                            <i class="fa fa-facebook"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat">
                            <i class="fa fa-google-plus"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- /.social-auth-links -->

        </div>
    </div>
</section>
