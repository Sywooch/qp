<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Menu */
/* @var $parent app\models\Menu  */
/* @var $form yii\widgets\ActiveForm */

foreach($parent->parents()->all() as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['menu/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] = [
    'label' => $parent->name,
    'url' => Url::to(['menu/view', 'id' => $parent->id])
];
$this->title = 'Создание подкатегории';
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить', [ 'btn btn-primary' ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
