<?php
/* @var $ordersDataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use yii\grid\GridView;

$this->params['profileLayout'] = true;
$this->title = 'История покупок';
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Личный кабинет</h1>
<h3>История покупок</h3>

<?= GridView::widget([
    'dataProvider' => $ordersDataProvider,
    'columns' => [
        'id',
        'created_at:datetime',
    ],
]); ?>
