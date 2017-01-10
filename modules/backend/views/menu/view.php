<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Menu*/

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div>
<?php
    if ($par = $model->parents(1)->one()) {
        echo '<div>' . Html::a('Назад', ['view', 'id' => $par->id]) . '</div>';
    }

    foreach($model->children(1)->all() as $ch) {
        echo '<div>' . Html::a($ch->name, ['view', 'id' => $ch->id]) . '</div>';
    }
?>
<?= Html::a('<i>Добавить категорию</i>', ['create', 'par_id' => $model->id]) ?>
</div>
