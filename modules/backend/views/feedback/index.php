<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отзывы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-form-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить отзыв', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'email:email',
            'name',
            ['attribute' => 'rating', 'value' => function($model) {
                /* @var $model app\models\ContactForm */
                return $model->getRatingString();
            }],
            'body',
            ['attribute' => 'status', 'format' => 'raw', 'value' => function($model) {
                /* @var $model app\models\ContactForm */
                return '<span class="' .$model->getStatusLabel(). '">' . $model->getStatusString() . '</span>';
            }],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-edit"></i>',
                            $url);
                    },
                    'delete' => function ($url,$model) {
                        return Html::a(
                            '<i class="fa fa-trash"></i>',
                            $url,  ['data' => [
                                'confirm' => 'Вы уверены?',
                                'method' => 'post'
                            ]]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
</div>
