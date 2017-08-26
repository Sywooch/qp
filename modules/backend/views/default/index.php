<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = 'Панель администратора'

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\UploadZipModel */
/* @var $provider app\modules\backend\models\UploadProvider */
?>
<?php $form1 = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
<?= $form1->field($model, 'zipFile')->fileInput()->label('Обновить базу данных из архива.') ?>
    <button>Отправить</button>
<?php ActiveForm::end() ?>

<br>
<?php $form2 = ActiveForm::begin(['action' => ['download-provider']]) ?>
<label class="control-label">Выгрузить зявки поставщикам</label> <br>
    <?=
        yii\jui\DatePicker::widget([
        'name' => 'date',
        'language' => 'ru',
        'dateFormat' => 'yyyy-MM-dd',
        'value' => date('Y-m-d'),
        'clientOptions' => ['value' => date('Y-m-d')],
    ]) ?>
    <button>Скачать</button>
<?php ActiveForm::end() ?>

<br>
<?php $form3 = ActiveForm::begin([
    'action' => ['upload-provider'],
    'options' => ['enctype' => 'multipart/form-data']
]) ?>
<?= $form3->field($provider, 'file')->fileInput()->label('Загрузить ответы поставщиков.') ?>
    <button>Отправить</button>
<?php ActiveForm::end() ?>
