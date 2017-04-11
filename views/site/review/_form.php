<?php
use app\components\Html;
use app\models\Bookmark;
use app\models\ContactForm;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $model app\models\ContactForm */
?>
<button type="button" class="btn btn-primary review__add-btn" data-toggle="modal" data-target=".review__form">
    <i class="fa fa-plus"></i> Добавить отзыв
</button>

<div class="modal fade review__form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Новый отзыв</h4>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <div class="modal-body">
                <p>
                    Все отзывы доходят до администрации, а недорозумения быстро устраняются. <br/>
                    Ваше мнение очень важно для нас. Оставьте отзыв о нашей работе, и мы ответим в ближайшее время.
                </p>

                <?= $form->field($model, 'email')->textInput() ?>
                <?= $form->field($model, 'name')->textInput() ?>
                <?= $form->field($model, 'rating')->dropDownList(ContactForm::$RATING_TO_STRING); ?>
                <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>
                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                ]) ?>

            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Отправить отзыв', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

