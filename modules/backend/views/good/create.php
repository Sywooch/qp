<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Good\Good */

$this->title = 'Добавить товар';
$this->params['breadcrumbs'][] = ['label' => 'Goods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="good-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
