<?php
use yii\grid\GridView;

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['profileLayout'] = true;
$this->title = 'Избранное';
$this->params['breadcrumbs'][] = [
    'label' => 'Личный кабинет',
    'url' => ['profile/index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Личный кабинет</h1>
<h3>Избранное</h3>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'name',
        'price',
        'pic',
        'category_id',
    ],
]); ?>
