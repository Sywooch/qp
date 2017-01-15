<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\backend\models\UploadZipModel */
/* @var $par app\models\Menu */
?>



<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<?= $form->field($model, 'zipFile')->fileInput() ?>

    <button>Submit</button>

<?php ActiveForm::end() ?>

<div class="row">
    <div class="col-sm-12">
        <h4>Список подразделов</h4>
        <table class="table table-striped table-bordered">
            <tr><th>id</th><th>Название</th><th></th></tr>
            <?php foreach($par->children(1)->all() as $ch) : ?>
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
                        <?=Html::a("<i class='fa fa-pencil'></i>", ['update', 'id' => $ch->id])?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>
</div>

