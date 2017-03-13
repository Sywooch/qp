<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
/* @var $feedbacks array of app\models\ContactForm */

$this->params['catalog'] = true;

$this->title = 'Отзывы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <h1><?=$this->title?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Благодарим за отзыв. Наши администраторы ответят на Ваше сообщение в ближайшее время.
        </div>
    <?php else: ?>
        <?=$this->render('_form', ['model' => $model])?>
        <div class="review">
            <?php
            foreach ($feedbacks as $feedback) {
                echo $this->render('_review', ['item' => $feedback]);
            }
            ?>
        </div>
    <?php endif; ?>
</div>
