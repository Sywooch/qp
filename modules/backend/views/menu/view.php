<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Good\Menu */
/* @var $menu app\models\Good\Menu */

$this->title = $model->name;

foreach($model->parents()->all() as $par) {
    $this->params['breadcrumbs'][] =  [
        'label' => $par->name,
        'url' => Url::to(['menu/view', 'id' => $par->id])
    ];
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-sm-12 col-md-6">
        <h4>Добавить новую категорию в этот подраздел</h4>
        <?php $form = ActiveForm::begin([
            'id' => '',
            'options' => [
                'class' => 'form-inline',
            ],
        ]); ?>
        <div class="input-group">
            <?=$form->field($menu, 'name', [
                'inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control transparent']
            ])->textInput()->input('text', ['placeholder' => "Название категории"])->label(false); ?>

            <span class="input-group-btn">
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary' ]) ?>
        </span>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <h4>Список подразделов</h4>
        <table class="table table-striped table-bordered">
            <tr><th>id</th><th>Название</th><th></th></tr>
            <?php foreach($model->children(1)->all() as $ch) : ?>
            <tr>
                <td><?=$ch->id?></td>
                <td><?=Html::a($ch->name, ['view', 'id' => $ch->id])?></td>
                <td>
                    <?=Html::a("<i class='fa fa-trash-o'></i>", ['delete', 'id' => $ch->id],
                        [ 'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить эту и все вложеные категорию?',
                            'method' => 'post',
                        ]]
                    )?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    </div>
</div>

