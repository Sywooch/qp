<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = 'Загрузки';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\UploadZipModel */
/* @var $provider app\modules\backend\models\UploadProvider */
/* @var $arch string */
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
    <div class="box-header with-border">
        <h3 class="box-title">Заявки для поставщиков</h3>
    </div>
    <div class="box-body">
        <p>Выгрузить последний архив. <?=Html::a($arch, ["download-provider", 'arch' => $arch]) ?></p>
        <?=Html::a("Остальные архивы", ["provider-orders"]) ?>
    </div>
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
        <?= $form3->field($provider, 'file')->fileInput()->label(false) ?>
        <button class="btn btn-primary">Отправить</button>
    </div>
    <?php ActiveForm::end() ?>
</div>
