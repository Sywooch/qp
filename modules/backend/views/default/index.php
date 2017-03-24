<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$this->title = 'Панель администратора'
/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\UploadZipModel */
/* @var $par app\models\Good\Menu */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
<?= $form->field($model, 'zipFile')->fileInput()->label('Обновить базу данных из архива.') ?>
    <button>Отправить</button>
<?php ActiveForm::end() ?>


<?php $form = ActiveForm::begin(['action' => ['provider-order']]) ?>
    <button>Выгрузить заказы</button>
<?php ActiveForm::end() ?>
