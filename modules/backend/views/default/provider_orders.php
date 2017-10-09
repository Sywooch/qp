<?php

use app\components\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Архивы заказов поставщикам';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="provider-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => function ($data) {
            return Html::a($data, ['download-provider', 'arch' => $data]);
        },
    ]); ?>
</div>
