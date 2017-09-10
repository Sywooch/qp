<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = 'Загрузки';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\UploadZipModel */
/* @var $provider app\modules\backend\models\UploadProvider */
?>

<div class="box box-primary">
    <?php $form1 = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <div class="box-header with-border">
        <h3 class="box-title">Импорт</h3>
    </div>
    <div class="box-body">
        <p>Обновить базу данных из архива.</p>
        <?= $form1->field($model, 'zipFile')->fileInput()->label(false) ?>
        <button class="btn btn-primary">Отправить</button>
    </div>
    <?php ActiveForm::end() ?>
</div>

<div class="box box-primary">
    <?php $form2 = ActiveForm::begin(['action' => ['download-provider']]) ?>
    <div class="box-header with-border">
        <h3 class="box-title">Заявки для поставщиков</h3>
    </div>
    <div class="box-body">
        <p>Выгрузить зявки поставщикам.</p>
        <div class="input-group col-sm-4">
            <div class="form-group">
                <?=
                yii\jui\DatePicker::widget([
                    'name' => 'date',
                    'language' => 'ru',
                    'dateFormat' => 'yyyy-MM-dd',
                    'value' => date('Y-m-d'),
                    'clientOptions' => ['value' => date('Y-m-d')],
                    'options' => ['class' => 'form-control']
                ]) ?>

            </div>
            <span class="input-group-btn">
                <button class="btn btn-primary">Скачать</button>
            </span>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>


<div class="box box-primary">
    <?php $form3 = ActiveForm::begin([
        'action' => ['upload-provider'],
        'options' => ['enctype' => 'multipart/form-data']
    ]) ?>
    <div class="box-header with-border">
        <h3 class="box-title">Ответы поставщиков</h3>
    </div>
    <div class="box-body">
        <p>Загрузить ответы поставщиков.</p>
        <?= $form1->field($model, 'zipFile')->fileInput()->label(false) ?>
        <button class="btn btn-primary">Отправить</button>
    </div>
    <?php ActiveForm::end() ?>
</div>
