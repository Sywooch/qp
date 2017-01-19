<?php
/* @var $model app\models\Profile\RegForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

echo Yii::$app->session->getFlash("success");
echo Yii::$app->session->getFlash("error");

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;

?>
<section class="join">
    <div class="row">
        <div class="join-body card col-md-4 col-md-offset-4 col-xs-10 col-xs-offset-1 col-sm-6 col-sm-offset-3">
            <div class="row join-tab">
                <div class="col-xs-6 tab-login">
                    <?php
                    echo  Html::a(
                        '<i class="fa fa-sign-in hidden-xs"></i> Вход',
                        ['site/login']
                    );
                    ?>
                </div>
                <div class="col-xs-6 tab-reg active">
                    <?php
                    echo  Html::a(
                        '<i class="fa fa-plus hidden-xs"></i> Регистрация',
                        'javascript::void(0)'
                    );
                    ?>
                </div>
            </div>

                <?php $form = ActiveForm::begin([
                    'id' => 'reg-form',
                    'action' => ['site/reg'],
                    'fieldConfig' => [
                        'template' => "<div class=\"form-group\">{label}\n{input}\n<div>{error}</div></div>",
                        'labelOptions' => ['class' => 'control-label'],
                    ],
                ]); ?>

                <?= $form->field($model, 'email')->textInput() ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'repeat_password')->passwordInput() ?>

                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary  btn-block btn-flat', 'name' => 'reg-button']) ?>
                        </div>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>

            <!-- .social-auth-links -->
            <div class="social-auth-links text-center">
                <p>- или войти с помощью -</p>
                <?php echo \nodge\eauth\Widget::widget(['action' => 'site/login']); ?>
            </div>
            <!-- /.social-auth-links -->
            </div>
        </div>
    </div>
</section>
